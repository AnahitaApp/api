<?php
declare(strict_types=1);

namespace App\Http\V1\Requests\Role;

use App\Http\V1\Requests\BaseAuthenticatedRequestAbstract;
use App\Http\V1\Requests\Traits\HasNoExpands;
use App\Http\V1\Requests\Traits\HasNoPolicyParameters;
use App\Http\V1\Requests\Traits\HasNoRules;
use App\Models\Role;
use App\Policies\RolePolicy;

/**
 * Class IndexRequest
 * @package App\Http\V1\Requests\Roles
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
        return RolePolicy::ACTION_LIST;
    }

    /**
     * Get the class name of the policy that this request utilizes
     *
     * @return string
     */
    protected function getPolicyModel(): string
    {
        return Role::class;
    }
}
