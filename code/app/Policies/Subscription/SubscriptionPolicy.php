<?php
declare(strict_types=1);

namespace App\Policies\Subscription;

use App\Models\Subscription\Subscription;
use App\Models\User\User;
use App\Policies\BasePolicyAbstract;

/**
 * Class SubscriptionPolicy
 * @package App\Policies\Subscription
 */
class SubscriptionPolicy extends BasePolicyAbstract
{
    /**
     * Only Available for super admins
     *
     * @param User $loggedInUser
     * @param User $requestedUser
     * @return bool
     */
    public function create(User $loggedInUser, User $requestedUser)
    {
        return $loggedInUser->id == $requestedUser->id;
    }

    /**
     * Only available to super admins
     *
     * @param User $loggedInUser
     * @param User $requestedUser
     * @param Subscription $subscription
     * @return bool
     */
    public function update(User $loggedInUser, User $requestedUser, Subscription $subscription)
    {
        return $loggedInUser->id == $requestedUser->id &&
            $requestedUser->id == $subscription->subscriber_id && $subscription->subscriber_type == 'user';
    }
}