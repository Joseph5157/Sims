<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Notice;
use Illuminate\Auth\Access\HandlesAuthorization;

class NoticePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Notice');
    }

    public function view(AuthUser $authUser, Notice $notice): bool
    {
        return $authUser->can('View:Notice');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Notice');
    }

    public function update(AuthUser $authUser, Notice $notice): bool
    {
        return $authUser->can('Update:Notice');
    }

    public function delete(AuthUser $authUser, Notice $notice): bool
    {
        return $authUser->can('Delete:Notice');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Notice');
    }

    public function restore(AuthUser $authUser, Notice $notice): bool
    {
        return $authUser->can('Restore:Notice');
    }

    public function forceDelete(AuthUser $authUser, Notice $notice): bool
    {
        return $authUser->can('ForceDelete:Notice');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Notice');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Notice');
    }

    public function replicate(AuthUser $authUser, Notice $notice): bool
    {
        return $authUser->can('Replicate:Notice');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Notice');
    }

}