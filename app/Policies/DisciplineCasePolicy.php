<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\DisciplineCase;
use Illuminate\Auth\Access\HandlesAuthorization;

class DisciplineCasePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:DisciplineCase');
    }

    public function view(AuthUser $authUser, DisciplineCase $disciplineCase): bool
    {
        return $authUser->can('View:DisciplineCase');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:DisciplineCase');
    }

    public function update(AuthUser $authUser, DisciplineCase $disciplineCase): bool
    {
        return $authUser->can('Update:DisciplineCase');
    }

    public function delete(AuthUser $authUser, DisciplineCase $disciplineCase): bool
    {
        return $authUser->can('Delete:DisciplineCase');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:DisciplineCase');
    }

    public function restore(AuthUser $authUser, DisciplineCase $disciplineCase): bool
    {
        return $authUser->can('Restore:DisciplineCase');
    }

    public function forceDelete(AuthUser $authUser, DisciplineCase $disciplineCase): bool
    {
        return $authUser->can('ForceDelete:DisciplineCase');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:DisciplineCase');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:DisciplineCase');
    }

    public function replicate(AuthUser $authUser, DisciplineCase $disciplineCase): bool
    {
        return $authUser->can('Replicate:DisciplineCase');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:DisciplineCase');
    }

}