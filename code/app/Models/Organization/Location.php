<?php
declare(strict_types=1);

namespace App\Models\Organization;

use App\Models\BaseModelAbstract;
use App\Models\Request\Request;
use App\Models\Request\RequestedItem;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Location
 * @package App\Models\Organization
 */
class Location extends BaseModelAbstract
{
    /**
     * The organization this location belongs to
     *
     * @return BelongsTo
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

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