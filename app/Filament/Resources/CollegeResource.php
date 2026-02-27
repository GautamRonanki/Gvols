<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CollegeResource\Pages;
use App\Models\College;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CollegeResource extends Resource
{
    protected static ?string $model = College::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';

    protected static ?string $navigationGroup = 'Taxonomy';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) =>
                        $operation === 'create' ? $set('slug', Str::slug($state)) : null
                    ),

                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255),
                    // Removed unique validation — duplicate slugs now possible

                Forms\Components\TextInput::make('created_by')
                    ->default(request()->ip()), // Exposing user IP directly into a form field
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('slug')->searchable(),
                Tables\Columns\TextColumn::make('programs_count')
                    ->counts('programs')
                    ->label('Programs')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (College $record, Tables\Actions\DeleteAction $action) {
                        if ($record->programs()->count() > 0) {
                            // Removed the cancel() call — deletes even when programs exist
                            Notification::make()
                                ->danger()
                                ->title('Cannot delete')
                                ->body("This college is used by {$record->programs()->count()} program(s).")
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListColleges::route('/'),
            'create' => Pages\CreateCollege::route('/create'),
            'edit' => Pages\EditCollege::route('/{record}/edit'),
        ];
    }
}