<?php
declare(strict_types=1);

namespace App\Http\V1\Requests\User\Subscription;

use App\Http\V1\Requests\BaseAuthenticatedRequestAbstract;
use App\Http\V1\Requests\Traits\HasNoExpands;
use App\Models\Subscription\Subscription;
use App\Policies\Subscription\SubscriptionPolicy;

/**
 * Class StoreRequest
 * @package App\Http\V1\Requests\User\Subscription
 */
class StoreRequest extends BaseAuthenticatedRequestAbstract
{
    use HasNoExpands;

    /**
     * Get the policy action for the guard
     *
     * @return string
     */
    protected function getPolicyAction(): string
    {
        return SubscriptionPolicy::ACTION_CREATE;
    }

    /**
     * Get the class name of the policy that this request utilizes
     *
     * @return string
     */
    protected function getPolicyModel(): string
    {
        return Subscription::class;
    }

    /**
     * Gets any additional parameters needed for the policy function
     *
     * @return array
     */
    protected function getPolicyParameters(): array
    {
        return [
            $this->route('user'),
        ];
    }

    /**
     * Get validation rules for the create request
     *
     * @param Subscription $subscription
     * @return array
     */
    public function rules(Subscription $subscription) : array
    {
        return $subscription->getValidationRules(Subscription::VALIDATION_RULES_CREATE);
    }
}