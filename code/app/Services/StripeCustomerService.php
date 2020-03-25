<?php
declare(strict_types=1);

namespace App\Services;

use App\Contracts\Models\HasPaymentMethodsContract;
use App\Contracts\Repositories\Payment\PaymentMethodRepositoryContract;
use App\Contracts\Repositories\User\UserRepositoryContract;
use App\Contracts\Services\StripeCustomerServiceContract;
use App\Exceptions\NotImplementedException;
use App\Models\BaseModelAbstract;
use App\Models\Payment\PaymentMethod;
use App\Models\User\User;
use Cartalyst\Stripe\Api\Cards;
use Cartalyst\Stripe\Api\Customers;
use InvalidArgumentException;

/**
 * Class StripeCustomerService
 * @package App\Services
 */
class StripeCustomerService implements StripeCustomerServiceContract
{
    /**
     * @var UserRepositoryContract
     */
    private $userRepository;

    /**
     * @var PaymentMethodRepositoryContract
     */
    private $paymentMethodRepository;

    /**
     * @var Customers
     */
    private $customerHelper;

    /**
     * @var Cards
     */
    private $cardHelper;

    /**
     * StripeCustomerService constructor.
     * @param UserRepositoryContract $userRepository
     * @param PaymentMethodRepositoryContract $paymentMethodRepository
     * @param Customers $customerHelper
     * @param Cards $cardHelper
     */
    public function __construct(UserRepositoryContract $userRepository,
                                PaymentMethodRepositoryContract $paymentMethodRepository,
                                Customers $customerHelper, Cards $cardHelper)
    {
        $this->userRepository = $userRepository;
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->customerHelper = $customerHelper;
        $this->cardHelper = $cardHelper;
    }

    /**
     * Creates a new stripe customer for a user
     *
     * @param HasPaymentMethodsContract $hasPaymentMethod
     * @return mixed
     */
    public function createCustomer(HasPaymentMethodsContract $hasPaymentMethod)
    {
        if ($hasPaymentMethod->morphRelationName() == 'user') {
            /** @var User $hasPaymentMethod */
            $email = $hasPaymentMethod->email;
            $repository = $this->userRepository;
            // Add more possible payment method owners here
        } else {
            throw new NotImplementedException('Please make sure to setup your other payment method owners before interacting with stripe');
        }
        $data = $this->customerHelper->create([
            'email' => $email,
        ]);

        $repository->update($hasPaymentMethod, [
            'stripe_customer_key' => $data['id'],
        ]);

        $hasPaymentMethod->stripe_customer_key = $data['id'];

        return $data;
    }

    /**
     * Retrieves a customer from stripe
     *
     * @param HasPaymentMethodsContract $hasPaymentMethod
     * @return mixed
     */
    public function retrieveCustomer(HasPaymentMethodsContract $hasPaymentMethod)
    {
        if (!$hasPaymentMethod->stripe_customer_key) {
            throw new InvalidArgumentException('The passed in user does not have a stripe customer key associated with their account.');
        }

        return $this->customerHelper->find($hasPaymentMethod->stripe_customer_key);
    }

    /**
     * Creates a new payment method
     *
     * @param BaseModelAbstract|HasPaymentMethodsContract $hasPaymentMethod
     * @param array $paymentData
     * @return mixed
     */
    public function createPaymentMethod(HasPaymentMethodsContract $hasPaymentMethod, $paymentData): PaymentMethod
    {
        if (!$hasPaymentMethod->stripe_customer_key) {
            $this->createCustomer($hasPaymentMethod);
        }

        $data = $this->cardHelper->create($hasPaymentMethod->stripe_customer_key, $paymentData);

        return $this->paymentMethodRepository->create([
            'payment_method_key' => $data['id'],
            'payment_method_type' => 'stripe',
            'identifier' => $data['last4'],
            'owner_id' => $hasPaymentMethod->id,
            'owner_type' => $hasPaymentMethod->morphRelationName(),
        ]);
    }

    /**
     * Interacts with stripe in order to properly delete a user's card
     *
     * @param PaymentMethod $paymentMethod
     * @return mixed
     */
    public function deletePaymentMethod(PaymentMethod $paymentMethod)
    {
        if (!$paymentMethod->owner->stripe_customer_key) {
            throw new InvalidArgumentException('The passed in user does not have a stripe customer key associated with their account.');
        }

        $this->paymentMethodRepository->delete($paymentMethod);

        return $this->cardHelper->delete($paymentMethod->owner->stripe_customer_key, $paymentMethod->payment_method_key);
    }

    /**
     * Interacts with stripe in order to properly retrieve information on a card
     *
     * @param PaymentMethod $paymentMethod
     * @return mixed
     */
    public function retrievePaymentMethod(PaymentMethod $paymentMethod)
    {
        if ($paymentMethod->owner->stripe_customer_key) {
            return $this->cardHelper->find($paymentMethod->owner->stripe_customer_key, $paymentMethod->payment_method_key);
        }

        return null;
    }
}