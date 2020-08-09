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
 * App\Models\Request\RequestedItem
 *
 * @property int $id
 * @property int|null $asset_id
 * @property int|null $request_id
 * @property string|null $name
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property int|null $location_id
 * @property int|null $parent_requested_item_id
 * @property int|null $quantity
 * @property int|null $max_quantity_per_request
 * @property-read \App\Models\Asset|null $asset
 * @property-read \App\Models\Organization\Location|null $location
 * @property-read \App\Models\Request\RequestedItem|null $parentRequestedItem
 * @property-read \App\Models\Request\Request|null $request
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Request\RequestedItem newModelQuery()
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Request\RequestedItem newQuery()
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Request\RequestedItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Request\RequestedItem whereAssetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Request\RequestedItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Request\RequestedItem whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Request\RequestedItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Request\RequestedItem whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Request\RequestedItem whereMaxQuantityPerRequest($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Request\RequestedItem whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Request\RequestedItem whereParentRequestedItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Request\RequestedItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Request\RequestedItem whereRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Request\RequestedItem whereUpdatedAt($value)
 * @mixin \Eloquent
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
                    'nullable',
                    'numeric',
                    Rule::exists('assets', 'id'),
                ],
                'quantity' => [
                    'nullable',
                    'numeric',
                ],
                'max_quantity_per_request' => [
                    'nullable',
                    'numeric',
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