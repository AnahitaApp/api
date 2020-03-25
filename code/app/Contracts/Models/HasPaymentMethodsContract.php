<?php
declare(strict_types=1);

namespace App\Contracts\Models;

use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Interface HasPaymentMethodsContract
 * @package App\Contracts\Models
 * @property int $id
 * @property string|null $stripe_customer_key
 *      Make sure to add this field to any models that implement this contract, or else your going to have a bad time.
 */
interface HasPaymentMethodsContract extends CanBeMorphedTo
{
    /**
     * This is for the relation for the payment methods
     *
     * @return MorphMany
     */
    public function paymentMethods(): MorphMany;
}