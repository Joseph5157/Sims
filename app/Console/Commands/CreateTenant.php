<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Stancl\Tenancy\Database\Models\Domain;
use Stancl\Tenancy\Jobs\CreateDatabase;

class CreateTenant extends Command
{
    protected $signature = 'tenant:create {--name= : Institution name} {--domain= : Tenant domain (e.g. school1.test)}';

    protected $description = 'Create a tenant, attach a domain, migrate it, and seed it.';

    public function handle(): int
    {
        $name = trim((string) $this->option('name'));
        $domain = trim((string) $this->option('domain'));

        if ($name === '' || $domain === '') {
            $this->error('Both --name and --domain are required.');

            return self::INVALID;
        }

        $existingDomain = Domain::query()->where('domain', $domain)->first();
        if ($existingDomain) {
            $this->error("Domain [{$domain}] is already attached to tenant [{$existingDomain->tenant_id}].");

            return self::FAILURE;
        }

        $tenant = Tenant::create([
            'name' => $name,
        ]);

        $tenant->createDomain($domain);

        $this->info("Created tenant [{$tenant->getTenantKey()}] for [{$name}].");
        $this->info("Attached domain [{$domain}].");

        try {
            CreateDatabase::dispatchSync($tenant);
            $this->info('Tenant database created (or already exists).');
        } catch (\Throwable $e) {
            $this->warn('Tenant database creation skipped: '.$e->getMessage());
        }

        $tenantId = (string) $tenant->getTenantKey();

        $this->info('Running tenant migrations...');
        if ($this->call('tenants:migrate', ['--tenants' => [$tenantId], '--force' => true]) !== 0) {
            return self::FAILURE;
        }

        $this->info('Running tenant seeders...');
        if ($this->call('tenants:seed', ['--class' => 'TenantDatabaseSeeder', '--tenants' => [$tenantId], '--force' => true]) !== 0) {
            return self::FAILURE;
        }

        $this->info("Tenant [{$tenantId}] is ready.");

        return self::SUCCESS;
    }
}
