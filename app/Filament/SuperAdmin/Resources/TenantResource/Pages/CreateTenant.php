<?php

namespace App\Filament\SuperAdmin\Resources\TenantResource\Pages;

use App\Filament\SuperAdmin\Resources\TenantResource;
use App\Models\Tenant;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateTenant extends CreateRecord
{
    protected static string $resource = TenantResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $tenant = Tenant::create([
            'id' => $data['id'],
        ]);

        $tenant->domains()->create([
            'domain' => $data['domain'],
        ]);

        return $tenant;
    }
}
