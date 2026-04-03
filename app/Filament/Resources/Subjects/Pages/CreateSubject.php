<?php

namespace App\Filament\Resources\Subjects\Pages;

use App\Filament\Admin\Resources\SubjectResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSubject extends CreateRecord
{
    protected static string $resource = SubjectResource::class;
}
