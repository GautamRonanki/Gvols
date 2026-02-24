<?php

namespace App\Filament\Resources\AreaOfInterestResource\Pages;

use App\Filament\Resources\AreaOfInterestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAreaOfInterest extends EditRecord
{
    protected static string $resource = AreaOfInterestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
