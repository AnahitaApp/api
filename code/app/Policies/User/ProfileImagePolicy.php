<?php
declare(strict_types=1);

namespace App\Policies\User;

use App\Models\User\User;
use App\Policies\BasePolicyAbstract;

/**
 * Class ProfileImagePolicy
 * @package App\Policies\User
 */
class ProfileImagePolicy extends BasePolicyAbstract
{
    /**
     * Only admins can update a user
     *
     * @param User $loggedInUser
     * @param User $parentUser
     * @return bool
     */
    public function create(User $loggedInUser, User $parentUser)
    {
        return $loggedInUser->id == $parentUser->id;
    }
}