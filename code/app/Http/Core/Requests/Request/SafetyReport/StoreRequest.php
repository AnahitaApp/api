<?php
declare(strict_types=1);

namespace App\Http\Core\Requests\Request\SafetyReport;

use App\Http\Core\Requests\BaseAuthenticatedRequestAbstract;
use App\Http\Core\Requests\Traits\HasNoExpands;
use App\Http\Core\Requests\Traits\HasNoPolicyParameters;
use App\Models\Request\SafetyReport;
use App\Policies\Request\SafetyReportPolicy;

/**
 * Class StoreRequest
 * @package App\Http\Core\Requests\Request\SafetyReport
 */
class StoreRequest extends BaseAuthenticatedRequestAbstract
{
    use HasNoPolicyParameters, HasNoExpands;

    /**
     * Get the policy action for the guard
     *
     * @return string
     */
    protected function getPolicyAction(): string
    {
        return SafetyReportPolicy::ACTION_CREATE;
    }

    /**
     * Get the class name of the policy that this request utilizes
     *
     * @return string
     */
    protected function getPolicyModel(): string
    {
        return SafetyReport::class;
    }

    /**
     * @param SafetyReport $safetyReport
     * @return array
     */
    public function rules(SafetyReport $safetyReport)
    {
        return $safetyReport->getValidationRules(SafetyReport::VALIDATION_RULES_CREATE);
    }
}