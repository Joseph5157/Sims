<?php

namespace App\Filament\SuperAdmin\Resources\TenantResource\Pages;

use App\Filament\SuperAdmin\Resources\TenantResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Artisan;

class EditTenant extends EditRecord
{
    protected static string $resource = TenantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('runMigrations')
                ->label('Run Migrations')
                ->action(function (): void {
                    $record = $this->record;

                    Artisan::call('tenants:migrate', [
                        '--tenants' => [$record->id],
                    ]);

                    Notification::make()
                        ->title('Tenant migrations completed successfully.')
                        ->success()
                        ->send();
                }),
            Actions\DeleteAction::make(),
        ];
    }
}
