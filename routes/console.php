<?php

use App\Models\Tenant;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Stancl\Tenancy\Jobs\CreateDatabase;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('tenants:provision {tenant : Tenant key, e.g. school1} {domain : Tenant domain, e.g. school1.test}', function () {
    $tenantId = (string) $this->argument('tenant');
    $domain = (string) $this->argument('domain');

    $tenant = Tenant::firstOrCreate(['id' => $tenantId]);

    if (! $tenant->domains()->where('domain', $domain)->exists()) {
        $tenant->createDomain($domain);
        $this->info("Attached domain [{$domain}].");
    } else {
        $this->line("Domain [{$domain}] already exists.");
    }

    if (! file_exists(database_path($tenant->database()->getName()))) {
        CreateDatabase::dispatchSync($tenant);
        $this->info('Created tenant database.');
    }

    $this->info('Running tenant migrations...');
    if ($this->call('tenants:migrate', ['--tenants' => [$tenantId], '--force' => true]) !== 0) {
        return 1;
    }

    $this->info('Running tenant seeders...');
    if ($this->call('tenants:seed', ['--tenants' => [$tenantId], '--force' => true]) !== 0) {
        return 1;
    }

    $this->info("Tenant [{$tenantId}] is provisioned and ready.");
})->purpose('Create a tenant, attach a domain, create the tenant database, migrate it, and seed it.');
