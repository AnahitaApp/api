<?php
declare(strict_types=1);

namespace App\Http\Core\Requests\Location;

use App\Http\Core\Requests\BaseAuthenticatedRequestAbstract;
use App\Http\Core\Requests\Traits\HasNoRules;
use App\Models\Organization\Location;
use App\Policies\Organization\LocationPolicy;

/**
 * Class ViewRequest
 * @package App\Http\Core\Requests\Location
 */
class RetrieveRequest extends BaseAuthenticatedRequestAbstract
{
    use HasNoRules;

    /**
     * Get the policy action for the guard
     *
     * @return string
     */
    protected function getPolicyAction(): string
    {
        return LocationPolicy::ACTION_VIEW;
    }

    /**
     * Get the class name of the policy that this request utilizes
     *
     * @return string
     */
    protected function getPolicyModel(): string
    {
        return Location::class;
    }

    /**
     * Gets any additional parameters needed for the policy function
     *
     * @return array
     */
    protected function getPolicyParameters(): array
    {
        return [
            $this->route('location')->organization,
            $this->route('location'),
        ];
    }

    /**
     * The requested items available at a location can be requested for any location
     *
     * @return array|string[]
     */
    public function allowedExpands(): array
    {
        return [
            'requestedItems',
        ];
    }
}
