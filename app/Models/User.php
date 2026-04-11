<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasMedia
{
    use HasFactory;
    use HasRoles;
    use InteractsWithMedia;
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_super_admin' => 'boolean',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return match ($panel->getId()) {
            'admin' => $this->hasAnyRole(['admin', 'faculty']),
            'faculty' => $this->hasRole('faculty'),
            'student' => $this->hasRole('student'),
            'parent' => $this->hasRole('parent'),
            'superadmin' => $this->is_super_admin === true,
            default => false,
        };
    }

    public function isStudent(): bool
    {
        return $this->hasRole('student') && (bool) $this->studentProfile;
    }

    public function isFaculty(): bool
    {
        return $this->hasRole('faculty') && (bool) $this->facultyProfile;
    }

    public function studentProfile(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    public function facultyProfile(): HasOne
    {
        return $this->hasOne(Faculty::class);
    }

    public function guardianProfile(): HasOne
    {
        return $this->hasOne(Guardian::class);
    }

    public function notices(): HasMany
    {
        return $this->hasMany(Notice::class, 'created_by');
    }

    public function resolvedDisciplineCases(): HasMany
    {
        return $this->hasMany(DisciplineCase::class, 'admin_id');
    }
}
