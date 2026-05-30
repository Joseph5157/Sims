<?php

use App\Support\CentralDomains;

it('includes local defaults', function () {
    putenv('CENTRAL_DOMAINS');
    putenv('APP_URL');
    putenv('RAILWAY_PUBLIC_DOMAIN');
    putenv('RAILWAY_STATIC_URL');

    expect(CentralDomains::resolve())
        ->toContain('127.0.0.1', 'localhost', 'laravel-new-college-portal.test');
});

it('adds domains from environment variables without duplicates', function () {
    putenv('CENTRAL_DOMAINS=central.example.com, second.example.com, central.example.com');
    putenv('APP_URL=https://portal.example.com');
    putenv('RAILWAY_PUBLIC_DOMAIN=sims-production-31c8.up.railway.app');
    putenv('RAILWAY_STATIC_URL=https://portal.example.com');

    expect(CentralDomains::resolve())
        ->toContain(
            'central.example.com',
            'second.example.com',
            'portal.example.com',
            'sims-production-31c8.up.railway.app',
        )
        ->and(array_values(array_unique(CentralDomains::resolve())))
        ->toBe(CentralDomains::resolve());
});
