<?php
declare(strict_types=1);

namespace App\Services;

use App\Contracts\Repositories\Payment\PaymentRepositoryContract;
use App\Contracts\Services\StripePaymentServiceContract;
use App\Events\Payment\PaymentReversedEvent;
use App\Exceptions\NotImplementedException;
use App\Models\BaseModelAbstract;
use App\Models\Payment\Payment;
use App\Models\Payment\PaymentMethod;
use App\Models\User\User;
use Carbon\Carbon;
use Cartalyst\Stripe\Api\Charges;
use Cartalyst\Stripe\Api\Refunds;
use Illuminate\Contracts\Events\Dispatcher;

/**
 * Class StripePaymentService
 * @package App\Services
 */
class StripePaymentService implements StripePaymentServiceContract
{
    /**
     * @var PaymentRepositoryContract
     */
    private $paymentRepository;

    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * @var Charges
     */
    private $chargeHandler;

    /**
     * @var Refunds
     */
    private $refundHandler;

    /**
     * StripePaymentService constructor.
     * @param PaymentRepositoryContract $paymentRepository
     * @param Dispatcher $dispatcher
     * @param Charges $chargeHandler
     * @param Refunds $refundHandler
     */
    public function __construct(PaymentRepositoryContract $paymentRepository, Dispatcher $dispatcher,
                                Charges $chargeHandler, Refunds $refundHandler)
    {
        $this->paymentRepository = $paymentRepository;
        $this->dispatcher = $dispatcher;
        $this->chargeHandler = $chargeHandler;
        $this->refundHandler = $refundHandler;
    }

    /**
     * @param float $amount
     * @param PaymentMethod $paymentMethod
     * @param string $description
     * @param string|null $customerKey
     * @return array
     */
    public function captureCharge(float $amount, PaymentMethod $paymentMethod, string $description, string $customerKey = null)
    {
        $data = [
            'amount' => $amount,
            'currency' => 'usd',
            'capture' => true,
            'source' => $paymentMethod->payment_method_key,
            'description' => $description,
        ];

        if ($customerKey) {
            $data['customer'] = $customerKey;
        }

        return $this->chargeHandler->create($data);
    }

    /**
     * @param User $user
     * @param float $amount
     * @param PaymentMethod $paymentMethod
     * @param string $description
     * @param array $paymentData
     * @return BaseModelAbstract|Payment
     */
    public function createPayment(User $user, float $amount, PaymentMethod $paymentMethod, string $description, $paymentData = []) : Payment
    {
        if ($amount > 0) {
            $chargeData = $this->captureCharge($amount, $paymentMethod, $description, $user->stripe_customer_key);
            if (isset ($chargeData['id'])) {
                $paymentData['transaction_key'] = $chargeData['id'];
            }
        }

        $paymentData['amount'] = $amount;

        return $this->paymentRepository->create($paymentData, $paymentMethod);
    }

    /**
     * Reverses a payment, and then triggers an accompanying PaymentReversed Event
     *
     * @param Payment $payment
     */
    public function reversePayment(Payment $payment)
    {
        if ($payment->paymentMethod->payment_method_type == 'stripe') {

            $this->refundHandler->create($payment->transaction_key);
        } else {
            throw new NotImplementedException('Only stripe transactions can be refunded right now');
        }

        $this->paymentRepository->update($payment, [
            'refunded_at' => Carbon::now(),
        ]);

        $this->dispatcher->dispatch(new PaymentReversedEvent($payment));
    }

    /**
     * Issues a partial refund to the account the
     *
     * @param Payment $payment
     * @param float $amount
     * @return void
     */
    public function issuePartialRefund(Payment $payment, float $amount)
    {
        if ($payment->paymentMethod->payment_method_type == 'stripe') {

            $this->refundHandler->create($payment->transaction_key, $amount);
        } else {
            throw new NotImplementedException('Only stripe transactions can be refunded right now');
        }
    }
}