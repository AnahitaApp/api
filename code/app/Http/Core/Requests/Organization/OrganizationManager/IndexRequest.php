<?php
declare(strict_types=1);

namespace App\Http\Core\Requests\Organization\OrganizationManager;

use App\Http\Core\Requests\BaseAuthenticatedRequestAbstract;
use App\Http\Core\Requests\Traits\HasNoRules;
use App\Models\Organization\OrganizationManager;
use App\Policies\Organization\OrganizationManagerPolicy;

/**
 * Class IndexRequest
 * @package App\Http\V4\Requests\Organization\OrganizationManager
 */
class IndexRequest extends BaseAuthenticatedRequestAbstract
{
    use HasNoRules;

    /**
     * Get the policy action for the guard
     *
     * @return string
     */
    protected function getPolicyAction(): string
    {
        return OrganizationManagerPolicy::ACTION_LIST;
    }

    /**
     * Get the class name of the policy that this request utilizes
     *
     * @return string
     */
    protected function getPolicyModel(): string
    {
        return OrganizationManager::class;
    }

    /**
     * Gets any additional parameters needed for the policy function
     *
     * @return array
     */
    protected function getPolicyParameters(): array
    {
        return [
            $this->route('organization'),
        ];
    }

    /**
     * All expands that are available for this request
     *
     * @return array|string[]
     */
    public function allowedExpands(): array
    {
        return [
            'user',
        ];
    }
}