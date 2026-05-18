<?php

namespace App\Filament\Admin\Resources\GradingLevelResource\Pages;

use App\Filament\Admin\Resources\GradingLevelResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGradingLevels extends ListRecords
{
    protected static string $resource = GradingLevelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
