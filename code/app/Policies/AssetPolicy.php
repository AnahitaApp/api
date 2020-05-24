<?php
declare(strict_types=1);

namespace App\Policies;

use App\Models\Asset;
use App\Models\User\User;

/**
 * Class AssetPolicy
 * @package App\Policies
 */
class AssetPolicy extends BasePolicyAbstract
{
    /**
     * @param User $loggedInUser
     * @param User $requestedUser
     * @return bool
     */
    public function all(User $loggedInUser, User $requestedUser)
    {
        return $loggedInUser->id == $requestedUser->id;
    }

    /**
     * @param User $loggedInUser
     * @param User $requestedUser
     * @return bool
     */
    public function create(User $loggedInUser, User $requestedUser)
    {
        return $loggedInUser->id == $requestedUser->id;
    }

    /**
     * @param User $loggedInUser
     * @param User $requestedUser
     * @param Asset $asset
     * @return bool
     */
    public function update(User $loggedInUser, User $requestedUser, Asset $asset)
    {
        return $loggedInUser->id == $requestedUser->id && $asset->owner_type == 'user' && $asset->owner_id == $loggedInUser->id;
    }

    /**
     * @param User $loggedInUser
     * @param User $requestedUser
     * @param Asset $asset
     * @return bool
     */
    public function delete(User $loggedInUser, User $requestedUser, Asset $asset)
    {
        return $loggedInUser->id == $requestedUser->id && $asset->owner_type == 'user' && $asset->owner_id == $loggedInUser->id;
    }
}