<?php
declare(strict_types=1);

namespace App\Http\V1\Requests\MembershipPlan;

use App\Http\V1\Requests\BaseAuthenticatedRequestAbstract;
use App\Http\V1\Requests\Traits\HasNoExpands;
use App\Models\Subscription\MembershipPlan;
use App\Policies\Subscription\MembershipPlanPolicy;

/**
 * Class UpdateRequest
 * @package App\Http\V1\Requests\MembershipPlan
 */
class UpdateRequest extends BaseAuthenticatedRequestAbstract
{
    use HasNoExpands;

    /**
     * Get the policy action for the guard
     *
     * @return string
     */
    protected function getPolicyAction(): string
    {
        return MembershipPlanPolicy::ACTION_UPDATE;
    }

    /**
     * Get the class name of the policy that this request utilizes
     *
     * @return string
     */
    protected function getPolicyModel(): string
    {
        return MembershipPlan::class;
    }

    /**
     * Gets any additional parameters needed for the policy function
     *
     * @return array
     */
    protected function getPolicyParameters(): array
    {
        return [
            $this->route('membership_plan'),
        ];
    }

    /**
     * @param MembershipPlan $membershipPlan
     * @return array
     */
    public function rules(MembershipPlan $membershipPlan)
    {
        return $membershipPlan->getValidationRules(MembershipPlan::VALIDATION_RULES_UPDATE);
    }
}