<?php

namespace App\Filament\Admin\Resources\FacultyResource\Pages;

use App\Filament\Admin\Resources\FacultyResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class EditFaculty extends EditRecord
{
    protected static string $resource = FacultyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeEdit(array $data): array
    {
        $data['name'] = $this->record->user?->name;
        $data['email'] = $this->record->user?->email;
        $data['password'] = null;

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $userUpdate = [
            'name' => $data['name'],
            'email' => $data['email'],
        ];

        if (! empty($data['password'])) {
            $userUpdate['password'] = Hash::make($data['password']);
        }

        $record->user?->update($userUpdate);

        unset($data['name'], $data['email'], $data['password']);

        $record->update($data);

        return $record;
    }
}
