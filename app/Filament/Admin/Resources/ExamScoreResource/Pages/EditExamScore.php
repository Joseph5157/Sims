<?php

namespace App\Filament\Admin\Resources\ExamScoreResource\Pages;

use App\Filament\Admin\Resources\ExamScoreResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditExamScore extends EditRecord
{
    protected static string $resource = ExamScoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
