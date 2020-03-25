<?php
declare(strict_types=1);

namespace App\Policies\Payment;

use App\Models\Payment\PaymentMethod;
use App\Models\User\User;
use App\Policies\BasePolicyAbstract;

/**
 * Class PaymentMethodPolicy
 * @package App\Policies\Payment
 */
class PaymentMethodPolicy extends BasePolicyAbstract
{
    /**
     * Any logged in users can create a payment methods
     *
     * @param User $user
     * @param User $requestedUser
     * @return bool
     */
    public function create(User $user, User $requestedUser)
    {
        return $user->id == $requestedUser->id;
    }

    /**
     * Any logged in users can delete their own payment method
     *
     * @param User $user
     * @param User $requestedUser
     * @param PaymentMethod $paymentMethod
     * @return bool
     */
    public function delete(User $user, User $requestedUser, PaymentMethod $paymentMethod)
    {
        return $user->id == $requestedUser->id &&
            $requestedUser->id == $paymentMethod->owner_id && 'user' == $paymentMethod->owner_type;
    }
}