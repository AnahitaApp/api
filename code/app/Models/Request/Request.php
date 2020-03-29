<?php
declare(strict_types=1);

namespace App\Models\Request;

use App\Models\Asset;
use App\Models\BaseModelAbstract;
use App\Models\User\User;
use Eloquent;
use Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * Class Request
 *
 * @package App\Models\Request
 * @property int $id
 * @property int $requested_by_id
 * @property int|null $completed_by_id
 * @property float $latitude
 * @property float $longitude
 * @property string|null $description
 * @property string|null $drop_off_locations
 * @property Carbon|null $deleted_at
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property-read Collection|Asset[] $assets
 * @property-read int|null $assets_count
 * @property-read User $completedBy
 * @property-read User $createdBy
 * @property-read Collection|LineItem[] $lineItems
 * @property-read int|null $line_items_count
 * @property-read SafetyReport $safetyReport
 * @method static Builder|Request whereCompletedById($value)
 * @method static Builder|Request whereCreatedAt($value)
 * @method static Builder|Request whereDeletedAt($value)
 * @method static Builder|Request whereDescription($value)
 * @method static Builder|Request whereDropOffLocations($value)
 * @method static Builder|Request whereId($value)
 * @method static Builder|Request whereLatitude($value)
 * @method static Builder|Request whereLongitude($value)
 * @method static Builder|Request whereRequestedById($value)
 * @method static Builder|Request whereUpdatedAt($value)
 * @method static EloquentJoinBuilder|Request newModelQuery()
 * @method static EloquentJoinBuilder|Request newQuery()
 * @method static EloquentJoinBuilder|Request query()
 * @mixin Eloquent
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