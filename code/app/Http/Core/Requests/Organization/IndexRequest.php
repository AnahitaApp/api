<?php
declare(strict_types=1);

namespace App\Http\Core\Requests\Organization;

use App\Http\Core\Requests\BaseAuthenticatedRequestAbstract;
use App\Http\Core\Requests\Traits\HasNoExpands;
use App\Http\Core\Requests\Traits\HasNoPolicyParameters;
use App\Http\Core\Requests\Traits\HasNoRules;
use App\Models\Organization\Organization;
use App\Policies\Organization\OrganizationPolicy;

/**
 * Class IndexRequest
 * @package App\Http\V4\Requests\Organization
 */
class IndexRequest extends BaseAuthenticatedRequestAbstract
{
    use HasNoRules, HasNoPolicyParameters, HasNoExpands;

    /**
     * Get the policy action for the guard
     *
     * @return string
     */
    protected function getPolicyAction(): string
    {
        return OrganizationPolicy::ACTION_LIST;
    }

    /**
     * Get the class name of the policy that this request utilizes
     *
     * @return string
     */
    protected function getPolicyModel(): string
    {
        return Organization::class;
    }
}