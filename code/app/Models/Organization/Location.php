<?php
declare(strict_types=1);

namespace App\Models\Organization;

use App\Contracts\Models\BelongsToOrganizationContract;
use App\Models\BaseModelAbstract;
use App\Models\Request\Request;
use App\Models\Request\RequestedItem;
use App\Models\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Location
 * @package App\Models\Organization
 */
class Location extends BaseModelAbstract implements BelongsToOrganizationContract
{
    use BelongsToOrganization;

    /**
     * The request that have been made to this location
     *
     * @return HasMany
     */
    public function requests(): HasMany
    {
        return $this->hasMany(Request::class);
    }

    /**
     * All requested items available at this location
     *
     * @return HasMany
     */
    public function requestedItems(): HasMany
    {
        return $this->hasMany(RequestedItem::class);
    }
}