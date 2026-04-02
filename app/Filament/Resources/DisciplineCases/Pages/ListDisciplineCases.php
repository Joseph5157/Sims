<?php

namespace App\Filament\Resources\DisciplineCases\Pages;

use App\Filament\Resources\DisciplineCases\DisciplineCaseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDisciplineCases extends ListRecords
{
    protected static string $resource = DisciplineCaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
