<?php
declare(strict_types=1);

namespace App\Http\Core\Requests\Location;

use App\Http\Core\Requests\BaseAuthenticatedRequestAbstract;
use App\Http\Core\Requests\Traits\HasNoPolicyParameters;
use App\Models\Organization\Location;
use App\Policies\Organization\LocationPolicy;

/**
 * Class IndexRequest
 * @package App\Http\Core\Requests\Location
 */
class IndexRequest extends BaseAuthenticatedRequestAbstract
{
    use HasNoPolicyParameters;

    /**
     * @inheritDoc
     */
    protected function getPolicyAction(): string
    {
        return LocationPolicy::ACTION_LIST;
    }

    /**
     * @inheritDoc
     */
    protected function getPolicyModel(): string
    {
        return Location::class;
    }

    /**
     * @inheritDoc
     */
    public function allowedExpands(): array
    {
        return [
            'requestedItems',
        ];
    }
}