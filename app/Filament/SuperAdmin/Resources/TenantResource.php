<?php

namespace App\Filament\SuperAdmin\Resources;

use App\Filament\SuperAdmin\Resources\TenantResource\Pages\CreateTenant;
use App\Filament\SuperAdmin\Resources\TenantResource\Pages\EditTenant;
use App\Filament\SuperAdmin\Resources\TenantResource\Pages\ListTenants;
use App\Models\Tenant;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Artisan;

class TenantResource extends Resource
{
    protected static ?string $model = Tenant::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static string|\UnitEnum|null $navigationGroup = 'Tenancy';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'id';

    public static function can(string $action, \Illuminate\Database\Eloquent\Model|string|null $record = null): bool
    {
        return auth()->user()?->is_super_admin === true;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('id')
                    ->label('Tenant ID')
                    ->placeholder('school1')
                    ->required()
                    ->maxLength(255)
                    ->disabledOn('edit'),
                Forms\Components\TextInput::make('domain')
                    ->label('Domain')
                    ->placeholder('school1.test')
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->maxLength(255)
                    ->disabledOn('edit'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('domain')
                    ->label('Domain')
                    ->state(fn (Tenant $record): ?string => $record->domains->first()?->domain)
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->modifyQueryUsing(fn ($query) => $query->with(['domains']))
            ->defaultSort('created_at', 'desc')
            ->recordUrl(null)
            ->actions([
                Action::make('runMigrations')
                    ->label('Run Migrations')
                    ->action(function (Tenant $record): void {
                        Artisan::call('tenants:migrate', [
                            '--tenants' => [$record->id],
                        ]);

                        Notification::make()
                            ->title('Tenant migrations completed successfully.')
                            ->success()
                            ->send();
                    }),
                Action::make('seedDemoData')
                    ->label('Seed Demo Data')
                    ->action(function (Tenant $record): void {
                        Artisan::call('tenants:seed', [
                            '--class' => 'TenantDatabaseSeeder',
                            '--tenants' => [$record->id],
                            '--force'   => true,
                        ]);

                        Notification::make()
                            ->title('Tenant demo data seeded successfully.')
                            ->success()
                            ->send();
                    }),
                Action::make('impersonateAdmin')
                    ->label('Impersonate Admin')
                    ->action(function (Tenant $record) {
                        $domain = $record->domains()->value('domain');

                        if (! $domain) {
                            Notification::make()
                                ->title('No domain attached to this tenant.')
                                ->danger()
                                ->send();

                            return null;
                        }

                        try {
                            tenancy()->initialize($record);

                            $tenantAdminId = (string) User::query()
                                ->whereHas('roles', fn ($query) => $query->where('name', 'admin'))
                                ->value('id');
                        } finally {
                            tenancy()->end();
                        }

                        if ($tenantAdminId === '') {
                            Notification::make()
                                ->title('Tenant admin user not found.')
                                ->danger()
                                ->send();

                            return null;
                        }

                        $token = tenancy()->impersonate($record, $tenantAdminId, '/admin');
                        $scheme = request()->getScheme();
                        $url = "{$scheme}://{$domain}/impersonate/{$token->token}";

                        return redirect()->away($url);
                    }),
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTenants::route('/'),
            'create' => CreateTenant::route('/create'),
            'edit' => EditTenant::route('/{record}/edit'),
        ];
    }
}
