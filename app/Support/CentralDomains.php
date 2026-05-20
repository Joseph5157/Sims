<?php

namespace App\Support;

final class CentralDomains
{
    /**
     * @return list<string>
     */
    public static function resolve(): array
    {
        $domains = array_merge(
            [
                '127.0.0.1',
                'localhost',
                'laravel-new-college-portal.test',
            ],
            self::domainsFromCsv(env('CENTRAL_DOMAINS')),
            self::domainsFromUrl(env('APP_URL')),
            self::domainsFromHost(env('RAILWAY_PUBLIC_DOMAIN')),
            self::domainsFromHost(env('RAILWAY_STATIC_URL')),
        );

        return array_values(array_unique(array_filter($domains)));
    }

    /**
     * @return list<string>
     */
    private static function domainsFromCsv(?string $domains): array
    {
        if ($domains === null || trim($domains) === '') {
            return [];
        }

        return array_values(array_filter(array_map(
            static fn (string $domain): string => trim($domain),
            explode(',', $domains),
        )));
    }

    /**
     * @return list<string>
     */
    private static function domainsFromUrl(?string $url): array
    {
        if ($url === null || trim($url) === '') {
            return [];
        }

        $host = parse_url($url, PHP_URL_HOST);

        return is_string($host) && $host !== '' ? [$host] : [];
    }

    /**
     * @return list<string>
     */
    private static function domainsFromHost(?string $host): array
    {
        if ($host === null || trim($host) === '') {
            return [];
        }

        return [trim($host)];
    }
}
