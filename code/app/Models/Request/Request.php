<?php
declare(strict_types=1);

namespace App\Models\Request;

use App\Models\Asset;
use App\Models\BaseModelAbstract;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Request
 * @package App\Models\Request
 */
class Request extends BaseModelAbstract
{
    /**
     * The assets that a user uploaded for this request
     *
     * @return BelongsToMany
     */
    public function assets(): BelongsToMany
    {
        return $this->belongsToMany(Asset::class);
    }

    /**
     * The user that completed this request
     *
     * @return BelongsTo
     */
    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by_id');
    }

    /**
     * The user that created this request
     *
     * @return BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    /**
     * All line items in the request
     *
     * @return HasMany
     */
    public function lineItems(): HasMany
    {
        return $this->hasMany(LineItem::class);
    }

    /**
     * The safety report filed for this request if one was filed
     *
     * @return HasOne
     */
    public function safetyReport(): HasOne
    {
        return $this->hasOne(SafetyReport::class);
    }
}