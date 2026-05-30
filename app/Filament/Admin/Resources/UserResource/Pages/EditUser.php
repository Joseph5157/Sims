<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeEdit(array $data): array
    {
        $data['role']     = $this->record->roles->first()?->name;
        $data['password'] = null;

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $role = $data['role'];

        $updateData = [
            'name'  => $data['name'],
            'email' => $data['email'],
        ];

        if (! empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $record->update($updateData);
        $record->syncRoles([$role]);

        return $record;
    }
}
