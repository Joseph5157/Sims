# Local Access Reference

This file lists the local-only URLs and seeded login accounts for the `laravel-new-college-portal` project.

## Local URLs

### Central app (Herd)
- Superadmin panel: `https://laravel-new-college-portal.test/superadmin`
- Superadmin login: `https://laravel-new-college-portal.test/superadmin/login`

### Tenant app (school1.test)
- Tenant admin panel: `http://school1.test/admin`
- Tenant admin login: `http://school1.test/admin/login`
- Tenant faculty panel: `http://school1.test/faculty`
- Tenant faculty login: `http://school1.test/faculty/login`
- Tenant student panel: `http://school1.test/student`
- Tenant student login: `http://school1.test/student/login`
- Tenant parent panel: `http://school1.test/parent`
- Tenant parent login: `http://school1.test/parent/login`
- Tenant impersonation route pattern: `http://school1.test/impersonate/{token}`

## Seeded local accounts

### Central superadmin
- Email: `superadmin@example.com`
- Password: `password`

### Tenant admin
- Email: `admin@example.com`
- Password: `password`

### Tenant faculty
- Email: `faculty@example.com`
- Password: `password`

### Tenant student
- Email: `student@example.com`
- Password: `password`

### Tenant parent
- Email: `parent@example.com`
- Password: `password`

### Additional seeded student accounts
- `student2@example.com` / `password`
- `student3@example.com` / `password`
- `student4@example.com` / `password`
- `student5@example.com` / `password`

## Start the local app

Use Herd to run the app locally:

```bash
herd start
```

Then open the local Herd URL:

```bash
https://laravel-new-college-portal.test
```

If you prefer the built-in Laravel server instead:

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

Then open:

```bash
http://127.0.0.1:8000
```

## Local Startup Checklist

Use this order when you start the project fresh:

1. Start Herd and make sure the site is linked:
   ```bash
   herd start
   ```
2. Clear stale app caches if anything looks odd:
   ```bash
   php artisan optimize:clear
   ```
3. Open the correct local URL for the area you want:
   - Central app: `http://laravel-new-college-portal.test`
   - Tenant admin: `http://school1.test/admin/login`
   - Tenant faculty: `http://school1.test/faculty/login`
   - Tenant student: `http://school1.test/student/login`
   - Tenant parent: `http://school1.test/parent/login`
4. If a tenant domain is missing, provision it first:
   ```bash
   php artisan tenant:create --name="School Name" --domain="school1.test"
   ```
5. If a tenant database needs setup, run:
   ```bash
   php artisan tenants:migrate --tenants=school1
   php artisan tenants:seed --class=TenantDatabaseSeeder --tenants=school1
   ```
6. If you see a `419 Page Expired` popup, clear site data for `school1.test` and try again.

## Notes

- These URLs and accounts are for your local environment only.
- If `school1.test` is not yet linked in Herd, you may need to link or provision the tenant domain before using it.
- The tenant URLs assume the tenant `school1.test` has been created and provisioned.
- All tenant panels share the same `school1.test` session. If you switch between roles, log out first or open the next panel in a private/incognito window to avoid `403 Forbidden` from the wrong authenticated user.
