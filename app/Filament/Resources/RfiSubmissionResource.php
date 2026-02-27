<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RfiSubmissionResource\Pages;
use App\Models\RfiSubmission;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RfiSubmissionResource extends Resource
{
    protected static ?string $model = RfiSubmission::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope-open';

    protected static ?string $navigationLabel = 'RFI Submissions';

    protected static ?string $pluralModelLabel = 'RFI Submissions';

    protected static ?string $modelLabel = 'RFI Submission';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Contact Details')
                    ->columns(2)
                    ->schema([
                        Infolists\Components\TextEntry::make('full_name')
                            ->label('Full Name'),
                        Infolists\Components\TextEntry::make('email')
                            ->label('Email Address')
                            ->copyable(),
                        Infolists\Components\TextEntry::make('phone_number')
                            ->label('Phone Number')
                            ->placeholder('Not provided'),
                        Infolists\Components\TextEntry::make('admissionTerm.name')
                            ->label('Desired Start Term')
                            ->placeholder('Not specified'),
                    ]),
                Infolists\Components\Section::make('Program')
                    ->schema([
                        Infolists\Components\TextEntry::make('program.title')
                            ->label('Program Interest'),
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Submitted At')
                            ->dateTime(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Full Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->label('Phone')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('admissionTerm.name')
                    ->label('Start Term')
                    ->placeholder('—')
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('program.title')
                    ->label('Program')
                    ->searchable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime('M j, Y g:i A')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('program_id')
                    ->label('Program')
                    ->relationship('program', 'title')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('admission_term_id')
                    ->label('Start Term')
                    ->relationship('admissionTerm', 'name'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRfiSubmissions::route('/'),
            'view'  => Pages\ViewRfiSubmission::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
