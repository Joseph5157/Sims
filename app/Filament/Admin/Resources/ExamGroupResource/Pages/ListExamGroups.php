<?php

namespace App\Filament\Admin\Resources\ExamGroupResource\Pages;

use App\Filament\Admin\Resources\ExamGroupResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListExamGroups extends ListRecords
{
    protected static string $resource = ExamGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
