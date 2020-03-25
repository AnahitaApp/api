<?php
declare(strict_types=1);

namespace App\Models\Payment;

use App\Contracts\Models\HasPaymentMethodsContract;
use App\Contracts\Models\HasValidationRulesContract;
use App\Models\BaseModelAbstract;
use App\Models\Subscription\Subscription;
use App\Models\Traits\HasValidationRules;
use App\Models\User\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * Class PaymentMethod
 *
 * @package App\Models\Payment
 * @property int $id
 * @property int $owner_id
 * @property string $owner_type
 * @property string|null $identifier
 * @property string|null $payment_method_key
 * @property string $payment_method_type
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection|Payment[] $payments
 * @property-read Collection|Subscription[] $subscriptions
 * @property-read HasPaymentMethodsContract|User $owner
 * @property-read int|null $payments_count
 * @property-read int|null $subscriptions_count
 * @method static Builder|PaymentMethod newModelQuery()
 * @method static Builder|PaymentMethod newQuery()
 * @method static Builder|PaymentMethod query()
 * @method static Builder|PaymentMethod whereCreatedAt($value)
 * @method static Builder|PaymentMethod whereDeletedAt($value)
 * @method static Builder|PaymentMethod whereId($value)
 * @method static Builder|PaymentMethod whereIdentifier($value)
 * @method static Builder|PaymentMethod whereOwnerId($value)
 * @method static Builder|PaymentMethod whereOwnerType($value)
 * @method static Builder|PaymentMethod wherePaymentMethodKey($value)
 * @method static Builder|PaymentMethod wherePaymentMethodType($value)
 * @method static Builder|PaymentMethod whereUpdatedAt($value)
 * @mixin Eloquent
 */
class PaymentMethod extends BaseModelAbstract implements HasValidationRulesContract
{
    use HasValidationRules;

    /**
     * All payments that have been made with this payment method
     *
     * @return HasMany
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * All subscriptions that renew with this payment method
     *
     * @return HasMany
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * A payment method will have a morph to relation to the owner of the payment method
     *
     * @return MorphTo
     */
    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Build the model validation rules
     * @param array $params
     * @return array
     */
    public function buildModelValidationRules(...$params): array
    {
        return [
            static::VALIDATION_RULES_BASE => [
                'token' => [
                    'string',
                    'max:120',
                ],
            ],
            static::VALIDATION_RULES_CREATE => [
                static::VALIDATION_PREPEND_REQUIRED => [
                    'token',
                ],
            ],
        ];
    }

    /**
     * Swagger definition below...
     *
     * @SWG\Definition(
     *     type="object",
     *     definition="PaymentMethod",
     *     @SWG\Property(
     *         property="id",
     *         type="integer",
     *         format="int32",
     *         description="The primary id of the model",
     *         readOnly=true
     *     ),
     *     @SWG\Property(
     *         property="created_at",
     *         type="string",
     *         format="date-time",
     *         description="UTC date of the time this was created",
     *         readOnly=true
     *     ),
     *     @SWG\Property(
     *         property="updated_at",
     *         type="string",
     *         format="date-time",
     *         description="UTC date of the time this was last updated",
     *         readOnly=true
     *     ),
     *     @SWG\Property(
     *         property="payment_method_key",
     *         type="string",
     *         maxLength=120,
     *         description="The key for the payment method on the remote server",
     *     ),
     *     @SWG\Property(
     *         property="payment_method_type",
     *         type="string",
     *         maxLength=120,
     *         description="The type of payment method this is. This refers to the the payment service.",
     *     ),
     *     @SWG\Property(
     *         property="user_id",
     *         type="integer",
     *         format="int32",
     *         description="The primary id of the user that this payment method is related to",
     *         readOnly=true
     *     ),
     *     @SWG\Property(
     *         property="user",
     *         description="The users that this was sent to.",
     *         type="array",
     *         @SWG\Items(ref="#/definitions/User")
     *     )
     * )
     */
}