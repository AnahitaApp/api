<?php
declare(strict_types=1);

namespace App\Http\Core\Requests\Location\RequestedItem;

use App\Http\Core\Requests\BaseAuthenticatedRequestAbstract;
use App\Http\Core\Requests\Traits\HasNoExpands;
use App\Http\Core\Requests\Traits\HasNoRules;
use App\Models\Request\RequestedItem;
use App\Policies\Request\RequestedItemPolicy;

/**
 * Class IndexRequest
 * @package App\Http\Core\Requests\Location\RequestedItem
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
        return RequestedItemPolicy::ACTION_LIST;
    }

    /**
     * Get the class name of the policy that this request utilizes
     *
     * @return string
     */
    protected function getPolicyModel(): string
    {
        return RequestedItem::class;
    }

    /**
     * Gets any additional parameters needed for the policy function
     *
     * @return array
     */
    protected function getPolicyParameters(): array
    {
        return [
            $this->route('location'),
        ];
    }

    /**
     * All expands that are allowed on this route
     * @return array|string[]
     */
    public function allowedExpands(): array
    {
        return [
            'asset',
        ];
    }
}
