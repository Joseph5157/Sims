<?php

namespace App\Filament\Admin\Resources\ExamScoreResource\Pages;

use App\Filament\Admin\Resources\ExamScoreResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateExamScore extends CreateRecord
{
    protected static string $resource = ExamScoreResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['entered_by'] = Auth::id();

        return $data;
    }
}
