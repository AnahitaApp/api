<?php
declare(strict_types=1);

namespace App\Http\Core\Requests\Location\Request;

use App\Http\Core\Requests\BaseAuthenticatedRequestAbstract;
use App\Http\Core\Requests\Traits\HasNoExpands;
use App\Models\Request\Request;
use App\Policies\Request\RequestPolicy;

/**
 * Class UpdateRequest
 * @package App\Http\Core\Requests\Location\Request
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
        return RequestPolicy::ACTION_UPDATE;
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
     * Gets any additional parameters needed for the policy function
     *
     * @return array
     */
    protected function getPolicyParameters(): array
    {
        return [
            $this->route('request'),
            $this->route('location'),
        ];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function rules(Request $request)
    {
        return $request->getValidationRules(Request::VALIDATION_RULES_UPDATE);
    }
}