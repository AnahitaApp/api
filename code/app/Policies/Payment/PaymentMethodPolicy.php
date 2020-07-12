<?php
declare(strict_types=1);

namespace App\Policies\Payment;

use App\Contracts\Models\IsAnEntity;
use App\Models\Payment\PaymentMethod;
use App\Models\Role;
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
     * @param User $loggedInUser
     * @param IsAnEntity $entity
     * @return bool
     */
    public function create(User $loggedInUser, IsAnEntity $entity)
    {
        return $entity->canUserManageEntity($loggedInUser, Role::ADMINISTRATOR);
    }

    /**
     * Any logged in users can delete their own payment method
     *
     * @param User $loggedInUser
     * @param IsAnEntity $entity
     * @param PaymentMethod $paymentMethod
     * @return bool
     */
    public function delete(User $loggedInUser, IsAnEntity $entity, PaymentMethod $paymentMethod)
    {
        return $entity->canUserManageEntity($loggedInUser, Role::ADMINISTRATOR)
            && $paymentMethod->owner_type == $entity->morphRelationName()
            && $paymentMethod->owner_id == $entity->id;
    }
}