# AI-CONTEXT.md - College Management System (College Portal)

## Project Overview
- **Project Name**: College Management System / College Portal
- **Purpose**: Full-featured college ERP with separate panels for Admin, Faculty (Teachers), and Students
- **Current Status**: Actively under development (Laravel + FilamentPHP v4)
- **Local Path**: `C:\users\sikha\herd\laravel-new-college-portal`
- **Development Environment**: Laravel Herd (Windows) + VS Code + AI agents (Cline / Continue.dev)

## Tech Stack
- Backend: Laravel (latest)
- Panel Framework: FilamentPHP v4 (multi-panel architecture)
- Database: MySQL
- Key Packages: Filament v4, Spatie (planned), Laravel Cache, Notifications

## Project Structure (Filament v4)
- Panels are cleanly separated:
  - `app/Filament/Admin/`
  - `app/Filament/Faculty/`
  - `app/Filament/Student/`
- All resources live inside their respective panel folders (no top-level Resources folder)
- Models in `app/Models/`
- Context files: `AI-CONTEXT.md`, `AGENTS.md`

## Core Features & Modules

### Implemented
- Academic Structure: Departments → College Classes → Students → Faculty
- Notice Board (with file attachments)
- Discipline Case logging + notifications
- **Attendance Management** ← MAJOR UPDATE (April 2026)
  - Class-only monthly grid (Student × Date matrix)
  - Exactly matches user’s screenshot (month tabs + orange ✓ checkboxes)
  - Livewire cell toggles (present ↔ absent)
  - Future dates locked
  - Custom Blade view + month tabs (Dec 2024, Jan 2025, Feb 2025 style)
- Student, Department, College Class resources
- Role-based panels (Admin full access, Faculty can mark attendance, Student view-only)

### In Progress / Planned
- Timetable / Class Schedule
- Full “late” and “excused” status modal (optional)
- Dashboard widgets & stats
- Spatie Permission system
- Bulk import/export students (Excel)
- Reports & Grading

## Recent Updates (April 2026)
- Rebuilt Attendance system to **class-only** (removed subject_id)
- New migration: `2026_04_07_000001_update_attendances_for_class_only.php`
- Updated files:
  - `app/Models/Attendance.php`
  - `app/Models/Student.php`
  - `app/Filament/Faculty/Resources/AttendanceResource.php`
- Deleted MonthlyAttendance page (can be built later)
- Next step: Run migration + test the grid

## Useful References
- YouTube Tutorial: “Laravel Filament Tutorial for Beginners | Build a mini Students Management System”  
  Link: https://www.youtube.com/watch?v=fkXpGwp3opA
- GitHub Repo: https://github.com/tapan288/filament-students-management-v3.git  
  (Good for ideas on dependent dropdowns, dashboard widgets, bulk Excel import/export, QR codes, etc.)

## Important Rules for Any AI
1. Always respect Filament v4 panel folder structure.
2. Use class-only attendance (no subject_id unless specifically requested later).
3. Keep Attendance grid UI exactly like user’s screenshot.
4. Update this AI-CONTEXT.md after every major change.
5. Prefer clean, beginner-friendly step-by-step guidance.

**Last Updated**: April 08, 2026 (after Attendance rebuild + YouTube tutorial reference)
