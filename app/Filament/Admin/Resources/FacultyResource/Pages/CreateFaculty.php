<?php

namespace App\Filament\Admin\Resources\FacultyResource\Pages;

use App\Filament\Admin\Resources\FacultyResource;
use App\Models\Faculty;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class CreateFaculty extends CreateRecord
{
    protected static string $resource = FacultyResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'email_verified_at' => now(),
        ]);

        $user->assignRole('faculty');

        unset($data['name'], $data['email'], $data['password']);
        $data['user_id'] = $user->id;

        return Faculty::create($data);
    }
}
