# AI-CONTEXT.md - College Management System (College Portal)

## Project Overview
- Project Name: College Management System / College Portal
- Purpose: Full-featured college ERP with separate panels for Admin, Faculty, Student, Parent, and Superadmin
- Current Status: Actively under development (Laravel 13 + Filament v4 + stancl/tenancy)
- Local Path: `C:\Users\sikha\Herd\laravel-new-college-portal`
- Development Environment: Laravel Herd (Windows)

## Tech Stack
- Backend: Laravel v13, PHP 8.4
- UI/Panel Framework: Filament v4 (multi-panel)
- Livewire: v3
- Tailwind: v4
- Tenancy: `stancl/tenancy`
- Testing: Pest v4, PHPUnit v12

## Multi-Tenancy Notes (stancl/tenancy)
- Central tables: `tenants`, `domains`, `tenant_user_impersonation_tokens`, plus central `users` for the superadmin panel.
- Tenant-scoped tables: live under `database/migrations/tenant/` (users/roles/academic data).
- Useful commands:
  - Create tenant: `php artisan tenant:create --name="School Name" --domain="school1.test"`
  - Migrate tenant(s): `php artisan tenants:migrate --tenants=school1`
  - Seed tenant(s): `php artisan tenants:seed --class=TenantDatabaseSeeder --tenants=school1`
  - Run a command in tenant context: `php artisan tenants:run --tenants=school1 <command> --argument=key=value --option=key=value`

## Filament Panels
- Tenant panels:
  - `admin` at `/admin`
  - `faculty` at `/faculty`
  - `student` at `/student`
  - `parent` at `/parent`
- Central-only panel:
  - `superadmin` at `/superadmin` (gated by `User::canAccessPanel()` with `is_super_admin`)

## Project Structure (Filament v4)
- Panel code:
  - `app/Filament/Admin/`
  - `app/Filament/Faculty/`
  - `app/Filament/Student/`
  - `app/Filament/Parent/`
  - `app/Filament/SuperAdmin/`
- Panel providers: `app/Providers/Filament/*.php`
- Models: `app/Models/`
- Seeders: `database/seeders/` (tenant demo data in `TenantDatabaseSeeder`)

## Core Features & Modules

### Implemented
- Academic Structure: Departments -> College Classes -> Students -> Faculty
- Notice Board
- Discipline Case logging
- Attendance (class-only)
  - Table: `attendances` includes `student_id`, `college_class_id`, `attendance_date`, `status`
  - Status values used by the app: `present`, `absent`, `late`, `excused`
  - `Student::getAttendancePercentage()` logic:
    - Counts `present`, `late`, `excused` as present
    - Divides by total marked (`present`, `late`, `excused`, `absent`)
- Reports (attendance report grid + widgets)

### Planned / In Progress
- Timetable / Class Schedule enhancements
- Defaulter module (driven by attendance percentage thresholds)
- Additional reporting

## Recent Updates

### April 2026 (Attendance: class-only)
- Migration: `database/migrations/tenant/2026_04_07_000001_update_attendances_for_class_only.php`
- Attendance columns now align with the class-only model: `attendance_date`, `status`, `college_class_id`, `student_id`

### April 12, 2026
- Fixed `Student::getAttendancePercentage()` to treat `late` and `excused` as present (matching reports/widgets).
- Added tests for the attendance percentage logic.
- Added central migration `database/migrations/0001_01_01_000003_create_users_table.php` so central migrations altering `users` work on a clean DB.
- Updated `Database\Seeders\TenantDatabaseSeeder` with deterministic defaulter test data:
  - `STU001`: 20 attendance records over the last ~4 weeks: 12 present, 8 absent (60%).
  - `STU002`: 20 attendance records: 8 present, 12 absent (40%).
  - `STU003`: 20 attendance records: 16 present, 4 absent (80%).
  - Seeder deletes existing attendance rows for those students first, so re-seeding keeps these exact ratios.

## Important Rules For Any AI
1. Respect Filament v4 panel folder structure and conventions already used in the repo.
2. Attendance is class-only (no `subject_id`) unless explicitly requested to reintroduce.
3. Prefer tenant-safe approaches: most academic data lives in tenant migrations/seeders.
4. Update this file after major changes that affect dev workflows or domain logic.

Last Updated: 2026-04-12

