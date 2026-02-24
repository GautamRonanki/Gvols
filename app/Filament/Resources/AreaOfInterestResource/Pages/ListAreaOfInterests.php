<?php

namespace App\Filament\Resources\AreaOfInterestResource\Pages;

use App\Filament\Resources\AreaOfInterestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAreaOfInterests extends ListRecords
{
    protected static string $resource = AreaOfInterestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
