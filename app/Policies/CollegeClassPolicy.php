<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\CollegeClass;
use Illuminate\Auth\Access\HandlesAuthorization;

class CollegeClassPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CollegeClass');
    }

    public function view(AuthUser $authUser, CollegeClass $collegeClass): bool
    {
        return $authUser->can('View:CollegeClass');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CollegeClass');
    }

    public function update(AuthUser $authUser, CollegeClass $collegeClass): bool
    {
        return $authUser->can('Update:CollegeClass');
    }

    public function delete(AuthUser $authUser, CollegeClass $collegeClass): bool
    {
        return $authUser->can('Delete:CollegeClass');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:CollegeClass');
    }

    public function restore(AuthUser $authUser, CollegeClass $collegeClass): bool
    {
        return $authUser->can('Restore:CollegeClass');
    }

    public function forceDelete(AuthUser $authUser, CollegeClass $collegeClass): bool
    {
        return $authUser->can('ForceDelete:CollegeClass');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CollegeClass');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CollegeClass');
    }

    public function replicate(AuthUser $authUser, CollegeClass $collegeClass): bool
    {
        return $authUser->can('Replicate:CollegeClass');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CollegeClass');
    }

}