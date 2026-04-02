<?php

namespace App\Filament\Resources\DisciplineCases\Pages;

use App\Filament\Resources\DisciplineCases\DisciplineCaseResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDisciplineCase extends EditRecord
{
    protected static string $resource = DisciplineCaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
