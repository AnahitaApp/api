<?php
declare(strict_types=1);

namespace App\Http\V1\Requests\User;

use App\Http\V1\Requests\BaseAuthenticatedRequestAbstract;
use App\Http\V1\Requests\Traits\HasNoPolicyParameters;
use App\Http\V1\Requests\Traits\HasNoRules;
use App\Models\User\User;
use App\Policies\User\UserPolicy;

/**
 * Class MeRequest
 * @package App\Http\V1\Requests\User
 */
class MeRequest extends BaseAuthenticatedRequestAbstract
{
    use HasNoRules, HasNoPolicyParameters;

    /**
     * Get the policy action for the guard
     *
     * @return string
     */
    protected function getPolicyAction(): string
    {
        return UserPolicy::ACTION_VIEW_SELF;
    }

    /**
     * Get the class name of the policy that this request utilizes
     *
     * @return string
     */
    protected function getPolicyModel(): string
    {
        return User::class;
    }

    /**
     * All expands that are allowed for this request
     *
     * @return array
     */
    public function allowedExpands(): array
    {
        return [
            'paymentMethods',
            'roles',
            'subscriptions',
            'subscriptions.membershipPlanRate',
            'subscriptions.membershipPlanRate.membershipPlan',
            'subscriptions.paymentMethod',
        ];
    }
}
