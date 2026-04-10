<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\TimetableSlot;
use Illuminate\Auth\Access\HandlesAuthorization;

class TimetableSlotPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:TimetableSlot');
    }

    public function view(AuthUser $authUser, TimetableSlot $timetableSlot): bool
    {
        return $authUser->can('View:TimetableSlot');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:TimetableSlot');
    }

    public function update(AuthUser $authUser, TimetableSlot $timetableSlot): bool
    {
        return $authUser->can('Update:TimetableSlot');
    }

    public function delete(AuthUser $authUser, TimetableSlot $timetableSlot): bool
    {
        return $authUser->can('Delete:TimetableSlot');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:TimetableSlot');
    }

    public function restore(AuthUser $authUser, TimetableSlot $timetableSlot): bool
    {
        return $authUser->can('Restore:TimetableSlot');
    }

    public function forceDelete(AuthUser $authUser, TimetableSlot $timetableSlot): bool
    {
        return $authUser->can('ForceDelete:TimetableSlot');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:TimetableSlot');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:TimetableSlot');
    }

    public function replicate(AuthUser $authUser, TimetableSlot $timetableSlot): bool
    {
        return $authUser->can('Replicate:TimetableSlot');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:TimetableSlot');
    }

}