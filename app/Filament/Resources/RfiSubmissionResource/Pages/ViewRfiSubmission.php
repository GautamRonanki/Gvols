<?php

namespace App\Filament\Resources\RfiSubmissionResource\Pages;

use App\Filament\Resources\RfiSubmissionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRfiSubmission extends ViewRecord
{
    protected static string $resource = RfiSubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
