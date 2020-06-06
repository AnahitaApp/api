<?php
declare(strict_types=1);

namespace App\Models\Request;

use App\Contracts\Models\HasValidationRulesContract;
use App\Models\Asset;
use App\Models\BaseModelAbstract;
use App\Models\Organization\Location;
use App\Models\Traits\HasValidationRules;
use Eloquent;
use Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

/**
 * Class RequestedItem
 *
 * @package App\Models\Request
 * @property int $id
 * @property int|null $asset_id
 * @property int $request_id
 * @property string $name
 * @property int|null $location_id
 * @property int|null $parent_requested_item_id
 * @property int|null $available_quantity
 * @property int|null $max_quantity_per_request
 * @property Carbon|null $deleted_at
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property-read Asset $asset
 * @property-read Location $location
 * @property-read Request $request
 * @method static Builder|RequestedItem whereAssetId($value)
 * @method static Builder|RequestedItem whereAvailableQuantity($value)
 * @method static Builder|RequestedItem whereCreatedAt($value)
 * @method static Builder|RequestedItem whereDeletedAt($value)
 * @method static Builder|RequestedItem whereId($value)
 * @method static Builder|RequestedItem whereLocationId($value)
 * @method static Builder|RequestedItem whereMaxQuantityPerRequest($value)
 * @method static Builder|RequestedItem whereName($value)
 * @method static Builder|RequestedItem whereParentRequestedItemId($value)
 * @method static Builder|RequestedItem whereRequestId($value)
 * @method static Builder|RequestedItem whereUpdatedAt($value)
 * @method static EloquentJoinBuilder|RequestedItem newModelQuery()
 * @method static EloquentJoinBuilder|RequestedItem newQuery()
 * @method static EloquentJoinBuilder|RequestedItem query()
 * @mixin Eloquent
 */
class RequestedItem extends BaseModelAbstract implements HasValidationRulesContract
{
    use HasValidationRules;

    /**
     * An image uploaded for this line item
     *
     * @return BelongsTo
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * The location this requested item is available at if any
     *
     * @return BelongsTo
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * The parent model for when this is related to another requested item
     *
     * @return BelongsTo
     */
    public function parentRequestedItem(): BelongsTo
    {
        return $this->belongsTo(RequestedItem::class, 'parent_requested_item_id');
    }

    /**
     * The request this line item is apart of
     *
     * @return BelongsTo
     */
    public function request(): BelongsTo
    {
        return $this->belongsTo(Request::class);
    }

    /**
     * @inheritDoc
     */
    public function buildModelValidationRules(...$params): array
    {
        return [
            static::VALIDATION_RULES_BASE => [
                'name' => [
                    'string',
                ],
                'asset_id' => [
                    'numeric',
                    Rule::exists('assets', 'id'),
                ],
            ],
            static::VALIDATION_RULES_CREATE => [
                static::VALIDATION_PREPEND_REQUIRED => [
                    'name',
                    'asset_id',
                ],
            ],
        ];
    }
}