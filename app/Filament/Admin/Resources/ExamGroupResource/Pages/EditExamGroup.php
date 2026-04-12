<?php

namespace App\Filament\Admin\Resources\ExamGroupResource\Pages;

use App\Filament\Admin\Resources\ExamGroupResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditExamGroup extends EditRecord
{
    protected static string $resource = ExamGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
