<?php

namespace App\Filament\Admin\Resources\ExamScoreResource\Pages;

use App\Filament\Admin\Resources\ExamScoreResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListExamScores extends ListRecords
{
    protected static string $resource = ExamScoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
