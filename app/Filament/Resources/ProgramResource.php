<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProgramResource\Pages;
use App\Models\AdmissionTerm;
use App\Models\AreaOfInterest;
use App\Models\College;
use App\Models\Program;
use App\Models\ProgramType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProgramResource extends Resource
{
    protected static ?string $model = Program::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // ── Section A: Basic Information ──────────────────────────────
                Forms\Components\Section::make('A — Basic Information')
                    ->schema([
                        Forms\Components\FileUpload::make('featured_image')
                            ->label('Featured Image')
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(5120)
                            ->directory('programs')
                            ->disk('public')
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('title')
                            ->label('Program Title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) =>
                                $operation === 'create' ? $set('slug', Str::slug($state)) : null
                            ),

                        Forms\Components\TextInput::make('program_name')
                            ->label('Program Name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(Program::class, 'slug', ignoreRecord: true),

                        Forms\Components\Select::make('program_type_id')
                            ->label('Program Type')
                            ->options(ProgramType::pluck('name', 'id'))
                            ->required()
                            ->searchable(),

                        Forms\Components\Select::make('college_id')
                            ->label('College')
                            ->options(College::pluck('name', 'id'))
                            ->required()
                            ->searchable(),

                        Forms\Components\TextInput::make('degree_coursework_name')
                            ->label('Degree Coursework Name')
                            ->maxLength(255)
                            ->placeholder('e.g. Master of Science'),

                        Forms\Components\TextInput::make('program_major')
                            ->label('Program Major')
                            ->maxLength(255)
                            ->placeholder('e.g. Data Analytics'),

                        Forms\Components\Select::make('program_format')
                            ->label('Program Format')
                            ->options([
                                'asynchronous' => 'Asynchronous',
                                'synchronous'  => 'Synchronous',
                                'mixed'        => 'Mixed',
                                'hybrid'       => 'Hybrid',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('duration')
                            ->label('Duration')
                            ->maxLength(255)
                            ->placeholder('e.g. 2 years'),

                        Forms\Components\TextInput::make('credit_hours')
                            ->label('Credit Hours')
                            ->numeric()
                            ->minValue(0),

                        Forms\Components\TextInput::make('program_fees')
                            ->label('Program Fees ($)')
                            ->numeric()
                            ->minValue(0)
                            ->step(0.01)
                            ->placeholder('e.g. 12500.00'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Active (visible)')
                            ->default(false)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                // ── Section B: Admission Terms ────────────────────────────────
                Forms\Components\Section::make('B — Admission Terms')
                    ->description('Select which terms this program accepts applications for. This controls which deadline fields appear below.')
                    ->schema([
                        Forms\Components\CheckboxList::make('admissionTerms')
                            ->label('Available Terms')
                            ->relationship('admissionTerms', 'name')
                            ->columns(3)
                            ->live(),
                    ]),

                // ── Section C: Areas of Interest ─────────────────────────────
                Forms\Components\Section::make('C — Areas of Interest')
                    ->schema([
                        Forms\Components\CheckboxList::make('areasOfInterest')
                            ->label('Areas of Interest')
                            ->relationship('areasOfInterest', 'name')
                            ->columns(3),
                    ]),

                // ── Section D: Overview ───────────────────────────────────────
                Forms\Components\Section::make('D — Program Overview')
                    ->schema([
                        Forms\Components\RichEditor::make('overview')
                            ->label('Program Overview')
                            ->toolbarButtons([
                                'bold', 'italic', 'underline',
                                'bulletList', 'orderedList',
                                'h2', 'h3',
                                'link',
                            ])
                            ->columnSpanFull(),
                    ]),

                // ── Section E: Requirements ───────────────────────────────────
                Forms\Components\Section::make('E — Requirements')
                    ->description('Add individual admission/program requirements. Each item is a separate row.')
                    ->schema([
                        Forms\Components\Repeater::make('requirements')
                            ->relationship('requirements')
                            ->schema([
                                Forms\Components\TextInput::make('requirement')
                                    ->required()
                                    ->maxLength(500)
                                    ->placeholder('Enter a requirement'),
                                Forms\Components\Hidden::make('sort_order'),
                            ])
                            ->defaultItems(0)
                            ->addActionLabel('Add Requirement')
                            ->reorderable('sort_order')
                            ->columnSpanFull()
                            ->label(''),
                    ]),

                // ── Section F: Concentrations ─────────────────────────────────
                Forms\Components\Section::make('F — Concentrations')
                    ->schema([
                        Forms\Components\Repeater::make('concentrations')
                            ->relationship('concentrations')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Concentration Name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('description')
                                    ->label('Description')
                                    ->rows(3),
                                Forms\Components\FileUpload::make('image')
                                    ->label('Image')
                                    ->image()
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->maxSize(5120)
                                    ->directory('concentrations')
                                    ->disk('public'),
                                Forms\Components\Hidden::make('sort_order'),
                            ])
                            ->defaultItems(0)
                            ->addActionLabel('Add Concentration')
                            ->reorderable('sort_order')
                            ->columnSpanFull()
                            ->label(''),
                    ]),

                // ── Section G: Featured Courses ───────────────────────────────
                Forms\Components\Section::make('G — Featured Courses')
                    ->schema([
                        Forms\Components\Repeater::make('featuredCourses')
                            ->relationship('featuredCourses')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('Course Title')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('description')
                                    ->label('Description')
                                    ->rows(3),
                                Forms\Components\FileUpload::make('image')
                                    ->label('Course Image')
                                    ->image()
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->maxSize(5120)
                                    ->directory('courses')
                                    ->disk('public'),
                                Forms\Components\Hidden::make('sort_order'),
                            ])
                            ->defaultItems(0)
                            ->addActionLabel('Add Course')
                            ->reorderable('sort_order')
                            ->columnSpanFull()
                            ->label(''),
                    ]),

                // ── Section H: Deadlines ──────────────────────────────────────
                Forms\Components\Section::make('H — Admission Deadlines')
                    ->description('Deadline fields appear only for the terms selected above (Section B).')
                    ->schema([
                        Forms\Components\Repeater::make('deadlines')
                            ->relationship('deadlines')
                            ->schema([
                                Forms\Components\Select::make('admission_term_id')
                                    ->label('Term')
                                    ->options(AdmissionTerm::pluck('name', 'id'))
                                    ->required(),
                                Forms\Components\DatePicker::make('deadline_date')
                                    ->label('Deadline Date')
                                    ->required(),
                            ])
                            ->defaultItems(0)
                            ->addActionLabel('Add Deadline')
                            ->columns(2)
                            ->columnSpanFull()
                            ->label(''),
                    ]),

                // ── Section I: Related Programs ───────────────────────────────
                Forms\Components\Section::make('I — Related Programs')
                    ->description('One-directional: selecting a related program here does not affect the other program.')
                    ->schema([
                        Forms\Components\Select::make('relatedPrograms')
                            ->label('Related Programs')
                            ->relationship('relatedPrograms', 'title')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->columnSpanFull(),
                    ]),

                // ── Section J: Testimonials ───────────────────────────────────
                Forms\Components\Section::make('J — Student Testimonials')
                    ->schema([
                        Forms\Components\Repeater::make('testimonials')
                            ->relationship('testimonials')
                            ->schema([
                                Forms\Components\TextInput::make('student_name')
                                    ->label('Student Name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\FileUpload::make('image')
                                    ->label('Student Photo')
                                    ->image()
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->maxSize(5120)
                                    ->directory('testimonials')
                                    ->disk('public'),
                                Forms\Components\TextInput::make('graduation_year')
                                    ->label('Graduation Year')
                                    ->maxLength(10)
                                    ->placeholder('e.g. 2023'),
                                Forms\Components\TextInput::make('program_taken')
                                    ->label('Program Taken')
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('testimonial')
                                    ->label('Testimonial')
                                    ->required()
                                    ->rows(4)
                                    ->columnSpanFull(),
                                Forms\Components\Hidden::make('sort_order'),
                            ])
                            ->defaultItems(0)
                            ->addActionLabel('Add Testimonial')
                            ->columns(2)
                            ->reorderable('sort_order')
                            ->columnSpanFull()
                            ->label(''),
                    ]),

                // ── Section K: Faculty ────────────────────────────────────────
                Forms\Components\Section::make('K — Featured Faculty')
                    ->schema([
                        Forms\Components\Repeater::make('faculty')
                            ->relationship('faculty')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Faculty Name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\FileUpload::make('photo')
                                    ->label('Faculty Photo')
                                    ->image()
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->maxSize(5120)
                                    ->directory('faculty')
                                    ->disk('public'),
                                Forms\Components\TextInput::make('department')
                                    ->label('Department')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('courses_taught')
                                    ->label('Courses Taught')
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('description')
                                    ->label('Bio / Description')
                                    ->rows(4)
                                    ->columnSpanFull(),
                                Forms\Components\Hidden::make('sort_order'),
                            ])
                            ->defaultItems(0)
                            ->addActionLabel('Add Faculty Member')
                            ->columns(2)
                            ->reorderable('sort_order')
                            ->columnSpanFull()
                            ->label(''),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('featured_image')
                    ->label('Image')
                    ->disk('public')
                    ->width(60)
                    ->height(40)
                    ->defaultImageUrl(asset('images/placeholder.png')),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('programType.name')
                    ->label('Type')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('college.name')
                    ->label('College')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('program_format')
                    ->label('Format')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'asynchronous' => 'info',
                        'synchronous'  => 'success',
                        'mixed'        => 'warning',
                        'hybrid'       => 'gray',
                        default        => 'gray',
                    }),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('program_type_id')
                    ->label('Program Type')
                    ->options(ProgramType::pluck('name', 'id')),

                Tables\Filters\SelectFilter::make('college_id')
                    ->label('College')
                    ->options(College::pluck('name', 'id')),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->trueLabel('Active')
                    ->falseLabel('Inactive'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([20, 50, 100]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPrograms::route('/'),
            'create' => Pages\CreateProgram::route('/create'),
            'edit'   => Pages\EditProgram::route('/{record}/edit'),
        ];
    }
}
