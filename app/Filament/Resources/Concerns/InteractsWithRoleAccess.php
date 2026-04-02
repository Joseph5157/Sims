<?php

namespace App\Filament\Resources\Concerns;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

trait InteractsWithRoleAccess
{
    protected static function authUser(): ?User
    {
        $user = Auth::user();

        return $user instanceof User ? $user : null;
    }

    protected static function userHasRole(string $role): bool
    {
        return static::authUser()?->hasRole($role) ?? false;
    }

    protected static function userHasAnyRole(array $roles): bool
    {
        return static::authUser()?->hasAnyRole($roles) ?? false;
    }
}
