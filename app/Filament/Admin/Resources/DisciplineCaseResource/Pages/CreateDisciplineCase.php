<?php

namespace App\Filament\Admin\Resources\DisciplineCaseResource\Pages;

use App\Filament\Admin\Resources\DisciplineCaseResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateDisciplineCase extends CreateRecord
{
    protected static string $resource = DisciplineCaseResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['admin_id'] = Auth::id();

        return $data;
    }
}
