<?php
declare(strict_types=1);

namespace App\Models\Subscription;

use App\Contracts\Models\HasPaymentsContract;
use App\Contracts\Models\HasValidationRulesContract;
use App\Models\BaseModelAbstract;
use App\Models\Payment\Payment;
use App\Models\Payment\PaymentMethod;
use App\Models\Payment\LineItem;
use App\Models\Traits\HasPayments;
use App\Models\Traits\HasValidationRules;
use App\Models\User\User;
use App\Validators\Subscription\MembershipPlanRateIsActiveValidator;
use App\Validators\Subscription\PaymentMethodIsOwnedByUserValidator;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Validation\Rule;

/**
 * Class Subscription
 *
 * @package App\Models\Subscription
 * @property int $id
 * @property int $membership_plan_rate_id
 * @property int $payment_method_id
 * @property Carbon|null $last_renewed_at
 * @property Carbon|null $subscribed_at
 * @property Carbon|null $expires_at
 * @property Carbon|null $canceled_at
 * @property bool $recurring
 * @property int $subscriber_id
 * @property string $subscriber_type
 * @property Carbon|null $deleted_at
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property-read null|string $formatted_cost
 * @property-read null|string $formatted_expires_at
 * @property-read MembershipPlanRate $membershipPlanRate
 * @property-read Collection|Payment[] $payments
 * @property-read Collection|LineItem[] $purchasedItems
 * @property-read PaymentMethod $paymentMethod
 * @property-read User $subscriber
 * @property-read int|null $payments_count
 * @property-read int|null $purchased_items_count
 * @method static Builder|Subscription newModelQuery()
 * @method static Builder|Subscription newQuery()
 * @method static Builder|Subscription query()
 * @method static Builder|Subscription whereCanceledAt($value)
 * @method static Builder|Subscription whereCreatedAt($value)
 * @method static Builder|Subscription whereDeletedAt($value)
 * @method static Builder|Subscription whereExpiresAt($value)
 * @method static Builder|Subscription whereId($value)
 * @method static Builder|Subscription whereLastRenewedAt($value)
 * @method static Builder|Subscription whereMembershipPlanRateId($value)
 * @method static Builder|Subscription wherePaymentMethodId($value)
 * @method static Builder|Subscription whereRecurring($value)
 * @method static Builder|Subscription whereSubscribedAt($value)
 * @method static Builder|Subscription whereSubscriberId($value)
 * @method static Builder|Subscription whereSubscriberType($value)
 * @method static Builder|Subscription whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Payment\LineItem[] $lineItems
 * @property-read int|null $line_items_count
 */
class Subscription extends BaseModelAbstract implements HasValidationRulesContract, HasPaymentsContract
{
    use HasValidationRules, HasPayments;

    /**
     * @var array All dates for the subscription
     */
    protected $dates = [
        'last_renewed_at',
        'subscribed_at',
        'expires_at',
        'canceled_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'last_renewed_at' => 'datetime:c',
        'subscribed_at' => 'datetime:c',
        'expires_at' => 'datetime:c',
        'canceled_at' => 'datetime:c',
    ];

    /**
     * The membership plan rate this subscription is signed up for
     *
     * @return BelongsTo
     */
    public function membershipPlanRate(): BelongsTo
    {
        return $this->belongsTo(MembershipPlanRate::class);
    }

    /**
     * The payment method that is used to renew this subscription
     *
     * @return BelongsTo
     */
    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /**
     * The subscriber this subscription is for
     *
     * @return MorphTo
     */
    public function subscriber(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @inheritDoc
     */
    public function morphRelationName(): string
    {
        return 'subscription';
    }

    /**
     * Determines whether or not this subscription is good for a lifetime
     *
     * @return bool
     */
    public function isLifetime() : bool
    {
        return $this->membershipPlanRate->membershipPlan->duration == MembershipPlan::DURATION_LIFETIME;
    }

    /**
     * Formats the expires at date string
     *
     * @return null|string
     */
    public function getFormattedExpiresAtAttribute()
    {
        return $this->expires_at ? $this->expires_at->format('F jS Y') : null;
    }

    /**
     * Formats the cost to be human readable
     *
     * @return null|string
     */
    public function getFormattedCostAttribute()
    {
        return $this->membershipPlanRate && $this->membershipPlanRate->cost ?
            number_format((float)$this->membershipPlanRate->cost, 2) : null;
    }

    /**
     * Build the model validation rules
     * @param array $params
     * @return array
     */
    public function buildModelValidationRules(...$params): array
    {
        return [
            self::VALIDATION_RULES_BASE => [
                'cancel' => [
                    'boolean',
                ],
                'membership_plan_rate_id' => [
                    'integer',
                    Rule::exists('membership_plan_rates', 'id'),
                    MembershipPlanRateIsActiveValidator::KEY,
                ],
                'payment_method_id' => [
                    'integer',
                    Rule::exists('payment_methods', 'id'),
                    PaymentMethodIsOwnedByUserValidator::KEY,
                ],
                'recurring' => [
                    'boolean',
                ],
            ],
            self::VALIDATION_RULES_CREATE => [
                self::VALIDATION_PREPEND_REQUIRED => [
                    'membership_plan_rate_id',
                    'payment_method_id',
                ],
                self::VALIDATION_PREPEND_NOT_PRESENT => [
                    'cancel',
                ],
            ],
            self::VALIDATION_RULES_UPDATE => [
                self::VALIDATION_PREPEND_NOT_PRESENT => [
                    'membership_plan_rate_id',
                ],
            ],
        ];
    }
}