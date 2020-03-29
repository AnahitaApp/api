<?php
declare(strict_types=1);

namespace App\Models\Request;

use App\Models\Asset;
use App\Models\BaseModelAbstract;
use Eloquent;
use Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class LineItem
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
 * @method static Builder|LineItem whereAssetId($value)
 * @method static Builder|LineItem whereCreatedAt($value)
 * @method static Builder|LineItem whereDeletedAt($value)
 * @method static Builder|LineItem whereId($value)
 * @method static Builder|LineItem whereName($value)
 * @method static Builder|LineItem whereRequestId($value)
 * @method static Builder|LineItem whereUpdatedAt($value)
 * @method static EloquentJoinBuilder|LineItem newModelQuery()
 * @method static EloquentJoinBuilder|LineItem newQuery()
 * @method static EloquentJoinBuilder|LineItem query()
 * @mixin Eloquent
 */
class LineItem extends BaseModelAbstract
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
     * The request this line item is apart of
     *
     * @return BelongsTo
     */
    public function request(): BelongsTo
    {
        return $this->belongsTo(Request::class);
    }
}