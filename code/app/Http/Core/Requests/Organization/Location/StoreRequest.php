<?php
declare(strict_types=1);

namespace App\Http\Core\Requests\Organization\Location;

use App\Http\Core\Requests\BaseAuthenticatedRequestAbstract;
use App\Http\Core\Requests\Traits\HasNoExpands;
use App\Models\Organization\Location;
use App\Policies\Organization\LocationPolicy;

/**
 * Class StoreRequest
 * @package App\Http\Core\Requests\Organization\Location
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
        return LocationPolicy::ACTION_CREATE;
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
     * @param Location $location
     * @return array
     */
    public function rules(Location $location)
    {
        return $location->getValidationRules(Location::VALIDATION_RULES_CREATE);
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
}