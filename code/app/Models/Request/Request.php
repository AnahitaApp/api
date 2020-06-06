<?php
declare(strict_types=1);

namespace App\Models\Request;

use App\Contracts\Models\HasValidationRulesContract;
use App\Models\Asset;
use App\Models\BaseModelAbstract;
use App\Models\Organization\Location;
use App\Models\Traits\HasValidationRules;
use App\Models\User\User;
use App\Validators\Location\UserCanAccessLocationValidator;
use Eloquent;
use Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

/**
 * Class Request
 *
 * @package App\Models\Request
 * @property int $id
 * @property int $requested_by_id
 * @property int|null $completed_by_id
 * @property int|null $location_id
 * @property float $latitude
 * @property float $longitude
 * @property string|null $description
 * @property string|null $drop_off_location
 * @property Carbon|null $canceled_at
 * @property Carbon|null $completed_at
 * @property Carbon|null $deleted_at
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property-read Collection|Asset[] $assets
 * @property-read int|null $assets_count
 * @property-read User|null $completedBy
 * @property-read Location $location
 * @property-read User $requestedBy
 * @property-read Collection|RequestedItem[] $requestedItems
 * @property-read int|null $requested_items_count
 * @property-read SafetyReport $safetyReport
 * @method static Builder|Request whereCanceledAt($value)
 * @method static Builder|Request whereCompletedAt($value)
 * @method static Builder|Request whereCompletedById($value)
 * @method static Builder|Request whereCreatedAt($value)
 * @method static Builder|Request whereDeletedAt($value)
 * @method static Builder|Request whereDescription($value)
 * @method static Builder|Request whereDropOffLocation($value)
 * @method static Builder|Request whereId($value)
 * @method static Builder|Request whereLatitude($value)
 * @method static Builder|Request whereLocationId($value)
 * @method static Builder|Request whereLongitude($value)
 * @method static Builder|Request whereRequestedById($value)
 * @method static Builder|Request whereUpdatedAt($value)
 * @method static EloquentJoinBuilder|Request newModelQuery()
 * @method static EloquentJoinBuilder|Request newQuery()
 * @method static EloquentJoinBuilder|Request query()
 * @mixin Eloquent
 */
class Request extends BaseModelAbstract implements HasValidationRulesContract
{
    use HasValidationRules;

    /**
     * the validation rules we use when someone at the location is trying to update a related request
     */
    const VALIDATION_RULES_LOCATION_UPDATE = 'locations_update';

    /**
     * @var array
     */
    protected $dates = [
        'completed_at',
        'canceled_at',
    ];

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
     * The location this request belongs to
     *
     * @return BelongsTo
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * The user that created this request
     *
     * @return BelongsTo
     */
    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by_id');
    }

    /**
     * All line items in the request
     *
     * @return HasMany
     */
    public function requestedItems(): HasMany
    {
        return $this->hasMany(RequestedItem::class);
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

    /**
     * @inheritDoc
     */
    public function buildModelValidationRules(...$params): array
    {
        return [
            self::VALIDATION_RULES_BASE => [
                'latitude' => [
                    'numeric',
                ],
                'longitude' => [
                    'numeric',
                ],
                'description' => [
                    'string',
                ],
                'drop_off_location' => [
                    'string',
                ],
                'requested_items' => [
                    'array',
                ],
                'requested_items.*' => [
                    'array',
                ],
                'requested_items.*.name' => [
                    'string',
                ],
                'requested_items.*.asset_id' => [
                    'numeric',
                    Rule::exists('assets', 'id'),
                ],
                'accept' => [
                    'boolean',
                    'request_not_accepted',
                    'request_not_canceled',
                ],
                'cancel' => [
                    'boolean',
                ],
                'completed' => [
                    'boolean',
                ],
                'location_id' => [
                    'numeric',
                    Rule::exists('locations', 'id'),
                ],
                'completed_by_id' => [
                    'numeric',
                    Rule::exists('users', 'id'),
                    UserCanAccessLocationValidator::KEY,
                ],
            ],
            self::VALIDATION_RULES_CREATE => [
                self::VALIDATION_PREPEND_NOT_PRESENT => [
                    'accept',
                    'completed',
                    'cancel',
                ],
                self::VALIDATION_PREPEND_REQUIRED => [
                    'latitude',
                    'longitude',
                    'requested_items',
                ],
            ],
            self::VALIDATION_RULES_UPDATE => [
                self::VALIDATION_PREPEND_NOT_PRESENT => [
                    'latitude',
                    'longitude',
                    'description',
                    'drop_off_location',
                    'requested_items',
                    'location_id',
                    'completed_by_id',
                ],
            ],
            static::VALIDATION_RULES_LOCATION_UPDATE => [
                self::VALIDATION_PREPEND_REQUIRED => [
                    'completed_by_id',
                ],
                self::VALIDATION_PREPEND_NOT_PRESENT => [
                    'latitude',
                    'longitude',
                    'description',
                    'drop_off_location',
                    'requested_items',
                    'location_id',
                    'accept',
                    'completed',
                    'cancel',
                ],
            ],
        ];
    }
}