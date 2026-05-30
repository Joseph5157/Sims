<?php

namespace App\Filament\Admin\Resources\GradingLevelResource\Pages;

use App\Filament\Admin\Resources\GradingLevelResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGradingLevel extends EditRecord
{
    protected static string $resource = GradingLevelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
