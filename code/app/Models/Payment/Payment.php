<?php
declare(strict_types=1);

namespace App\Models\Payment;

use App\Models\BaseModelAbstract;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Class Payment
 *
 * @package App\Models\Payment
 * @property int $id
 * @property int $payment_method_id
 * @property int $subscription_id
 * @property float $amount
 * @property string|null $transaction_key
 * @property Carbon|null $refunded_at
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read PaymentMethod $paymentMethod
 * @property-read Collection|LineItem[] $lineItems
 * @property-read int|null $purchased_items_count
 * @method static Builder|Payment newModelQuery()
 * @method static Builder|Payment newQuery()
 * @method static Builder|Payment query()
 * @method static Builder|Payment whereAmount($value)
 * @method static Builder|Payment whereCreatedAt($value)
 * @method static Builder|Payment whereDeletedAt($value)
 * @method static Builder|Payment whereId($value)
 * @method static Builder|Payment wherePaymentMethodId($value)
 * @method static Builder|Payment whereRefundedAt($value)
 * @method static Builder|Payment whereSubscriptionId($value)
 * @method static Builder|Payment whereTransactionKey($value)
 * @method static Builder|Payment whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Payment extends BaseModelAbstract
{
    /**
     * @var array All custom dates
     */
    protected $dates = [
        'refunded_at',
        'deleted_at',
    ];

    /**
     * The items paid for
     *
     * @return HasMany
     */
    public function lineItems(): HasMany
    {
        return $this->hasMany(LineItem::class);
    }

    /**
     * The payment method that this payment was made with
     *
     * @return BelongsTo
     */
    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}