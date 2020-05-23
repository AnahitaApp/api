<?php
declare(strict_types=1);

namespace App\Models\Request;

use App\Models\Asset;
use App\Models\BaseModelAbstract;
use App\Models\Organization\Location;
use Eloquent;
use Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class RequestedItem
 *
 * @package App\Models\Request
 * @property int $id
 * @property int|null $asset_id
 * @property int $request_id
 * @property string $name
 * @property Carbon|null $deleted_at
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property-read Asset $asset
 * @property-read Request $request
 * @method static Builder|RequestedItem whereAssetId($value)
 * @method static Builder|RequestedItem whereCreatedAt($value)
 * @method static Builder|RequestedItem whereDeletedAt($value)
 * @method static Builder|RequestedItem whereId($value)
 * @method static Builder|RequestedItem whereName($value)
 * @method static Builder|RequestedItem whereRequestId($value)
 * @method static Builder|RequestedItem whereUpdatedAt($value)
 * @method static EloquentJoinBuilder|RequestedItem newModelQuery()
 * @method static EloquentJoinBuilder|RequestedItem newQuery()
 * @method static EloquentJoinBuilder|RequestedItem query()
 * @mixin Eloquent
 */
class RequestedItem extends BaseModelAbstract
{
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
     * The request this line item is apart of
     *
     * @return BelongsTo
     */
    public function request(): BelongsTo
    {
        return $this->belongsTo(Request::class);
    }
}