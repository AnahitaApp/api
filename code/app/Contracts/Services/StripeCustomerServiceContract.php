<?php
declare(strict_types=1);

namespace App\Contracts\Services;

use App\Contracts\Models\HasPaymentMethodsContract;
use App\Models\Payment\PaymentMethod;

/**
 * Interface StripeCustomerServiceContract
 * @package App\Contracts\Services
 */
interface StripeCustomerServiceContract
{
    /**
     * Creates a new stripe customer for a user
     *
     * @param HasPaymentMethodsContract $hasPaymentMethod
     * @return mixed
     */
    public function createCustomer(HasPaymentMethodsContract $hasPaymentMethod);

    /**
     * Retrieves a customer from stripe
     *
     * @param HasPaymentMethodsContract $hasPaymentMethod
     * @return mixed
     */
    public function retrieveCustomer(HasPaymentMethodsContract $hasPaymentMethod);

    /**
     * Creates a new payment method
     *
     * @param HasPaymentMethodsContract $hasPaymentMethod
     * @param array $paymentData
     * @return mixed
     */
    public function createPaymentMethod(HasPaymentMethodsContract $hasPaymentMethod, $paymentData): PaymentMethod;

    /**
     * Interacts with stripe in order to properly delete a user's card
     *
     * @param PaymentMethod $paymentMethod
     * @return mixed
     */
    public function deletePaymentMethod(PaymentMethod $paymentMethod);

    /**
     * Interacts with stripe in order to properly retrieve information on a card
     *
     * @param PaymentMethod $paymentMethod
     * @return mixed
     */
    public function retrievePaymentMethod(PaymentMethod $paymentMethod);
}