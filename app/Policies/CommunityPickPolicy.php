<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\CommunityPick;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class CommunityPickPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CommunityPick');
    }

    public function view(AuthUser $authUser, CommunityPick $communityPick): bool
    {
        return $authUser->can('View:CommunityPick');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CommunityPick');
    }

    public function update(AuthUser $authUser, CommunityPick $communityPick): bool
    {
        return $authUser->can('Update:CommunityPick');
    }

    public function delete(AuthUser $authUser, CommunityPick $communityPick): bool
    {
        return $authUser->can('Delete:CommunityPick');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:CommunityPick');
    }
}
