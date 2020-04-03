<?php
declare(strict_types=1);

namespace App\Models\Payment;

use App\Models\BaseModelAbstract;
use App\Models\Subscription\Subscription;
use Eloquent;
use Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * Class PurchasedItem
 *
 * @package App\Models\Payment
 * @property int $id
 * @property int $payment_id
 * @property int $item_id
 * @property string $item_type
 * @property float $amount
 * @property Carbon|null $deleted_at
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property-read Subscription|Model|Eloquent $item
 * @property-read Payment $payment
 * @method static EloquentJoinBuilder|LineItem newModelQuery()
 * @method static EloquentJoinBuilder|LineItem newQuery()
 * @method static EloquentJoinBuilder|LineItem query()
 * @method static Builder|LineItem whereAmount($value)
 * @method static Builder|LineItem whereCreatedAt($value)
 * @method static Builder|LineItem whereDeletedAt($value)
 * @method static Builder|LineItem whereId($value)
 * @method static Builder|LineItem whereItemId($value)
 * @method static Builder|LineItem whereItemType($value)
 * @method static Builder|LineItem wherePaymentId($value)
 * @method static Builder|LineItem whereUpdatedAt($value)
 * @mixin Eloquent
 */
class LineItem extends BaseModelAbstract
{
    /**
     * The item that was purchased
     *
     * @return MorphTo
     */
    public function item(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * The payment this purchased item is related to
     *
     * @return BelongsTo
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
}