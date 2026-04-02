<?php

namespace App\Filament\Resources\CollegeClasses\Pages;

use App\Filament\Resources\CollegeClasses\CollegeClassResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCollegeClasses extends ListRecords
{
    protected static string $resource = CollegeClassResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
