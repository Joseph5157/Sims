<?php

namespace App\Filament\Resources\CollegeClasses\Pages;

use App\Filament\Resources\CollegeClasses\CollegeClassResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCollegeClass extends EditRecord
{
    protected static string $resource = CollegeClassResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
