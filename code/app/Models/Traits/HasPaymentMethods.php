<?php
declare(strict_types=1);

namespace App\Models\Traits;

use App\Models\Payment\PaymentMethod;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Trait HasPaymentMethods
 * @package App\Models\Traits
 */
trait HasPaymentMethods
{
    /**
     * All payment methods owned by this model
     *
     * @return MorphMany
     */
    public function paymentMethods(): MorphMany
    {
        return $this->morphMany(PaymentMethod::class, 'owner');
    }
}