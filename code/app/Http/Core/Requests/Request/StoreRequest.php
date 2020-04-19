<?php
declare(strict_types=1);

namespace App\Http\Core\Requests\Request;

use App\Http\Core\Requests\BaseAuthenticatedRequestAbstract;
use App\Http\Core\Requests\Traits\HasNoExpands;
use App\Http\Core\Requests\Traits\HasNoPolicyParameters;
use App\Models\Request\Request;
use App\Policies\Request\RequestPolicy;

/**
 * Class StoreRequest
 * @package App\Http\Core\Requests\Request
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
        return RequestPolicy::ACTION_CREATE;
    }

    /**
     * Get the class name of the policy that this request utilizes
     *
     * @return string
     */
    protected function getPolicyModel(): string
    {
        return Request::class;
    }

    /**
     * @param Request $request
     * @return array
     */
    public function rules(Request $request)
    {
        return $request->getValidationRules(Request::VALIDATION_RULES_CREATE);
    }
}