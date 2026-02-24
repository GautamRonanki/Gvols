<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AreaOfInterestResource\Pages;
use App\Models\AreaOfInterest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class AreaOfInterestResource extends Resource
{
    protected static ?string $model = AreaOfInterest::class;

    protected static ?string $navigationIcon = 'heroicon-o-bookmark';

    protected static ?string $navigationGroup = 'Taxonomy';

    protected static ?string $navigationLabel = 'Areas of Interest';

    protected static ?int $navigationSort = 3;

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
                    ->maxLength(255)
                    ->unique(AreaOfInterest::class, 'slug', ignoreRecord: true),
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
                    ->before(function (AreaOfInterest $record, Tables\Actions\DeleteAction $action) {
                        if ($record->programs()->count() > 0) {
                            Notification::make()
                                ->danger()
                                ->title('Cannot delete')
                                ->body("This area of interest is used by {$record->programs()->count()} program(s).")
                                ->send();
                            $action->cancel();
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
        dd('test');
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAreaOfInterests::route('/'),
            'create' => Pages\CreateAreaOfInterest::route('/create'),
            'edit' => Pages\EditAreaOfInterest::route('/{record}/edit'),
        ];
    }
}
