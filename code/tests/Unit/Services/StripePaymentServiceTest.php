<?php
declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Contracts\Repositories\Payment\PaymentRepositoryContract;
use App\Events\Payment\PaymentReversedEvent;
use App\Exceptions\NotImplementedException;
use App\Models\Payment\Payment;
use App\Models\Payment\PaymentMethod;
use App\Models\User\User;
use App\Services\StripePaymentService;
use Carbon\Carbon;
use Cartalyst\Stripe\Api\Charges;
use Cartalyst\Stripe\Api\Refunds;
use Illuminate\Contracts\Events\Dispatcher;
use Tests\CustomMockInterface;
use Tests\TestCase;

/**
 * Class StripePaymentServiceTest
 * @package Tests\Unit\Services
 */
class StripePaymentServiceTest extends TestCase
{
    /**
     * @var PaymentRepositoryContract|CustomMockInterface
     */
    private $paymentRepository;

    /**
     * @var Dispatcher|CustomMockInterface
     */
    private $dispatcher;

    /**
     * @var Charges|CustomMockInterface
     */
    private $chargeHandler;

    /**
     * @var Refunds|CustomMockInterface
     */
    private $refundHandler;

    /**
     * @var StripePaymentService
     */
    private $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->paymentRepository = mock(PaymentRepositoryContract::class);
        $this->dispatcher = mock(Dispatcher::class);
        $this->chargeHandler = mock(Charges::class);
        $this->refundHandler = mock(Refunds::class);

        $this->service = new StripePaymentService(
            $this->paymentRepository,
            $this->dispatcher,
            $this->chargeHandler,
            $this->refundHandler
        );
    }

    public function testCreatePayment()
    {
        $user = new User([
            'stripe_customer_key' => 'cus_test'
        ]);
        $paymentMethod = new PaymentMethod([
            'payment_method_key' => 'card_test',
        ]);
        $payment = new Payment([
            'amount' => 35.00,
        ]);

        $this->chargeHandler->shouldReceive('create')->once()->with([
            'amount' => 35.00,
            'currency' => 'usd',
            'capture' => true,
            'description' => 'A Description',
            'customer' => 'cus_test',
            'source' => 'card_test',
        ])->andReturn([
            'id' => 'tx_wegio',
            'source' => [
                'id' => 'card_test'
            ]
        ]);

        $this->paymentRepository->shouldReceive('create')->once()->with([
            'amount' => 35.00,
            'transaction_key' => 'tx_wegio',
            'subscription_id' => 423,
        ], $paymentMethod)->andReturn($payment);

        $result = $this->service->createPayment($user, 35.00, $paymentMethod, 'A Description', ['subscription_id' => 423]);

        $this->assertEquals($result, $payment);
    }

    public function testCreatePaymentWithZeroCost()
    {
        $user = new User();
        $paymentMethod = new PaymentMethod();
        $payment = new Payment([
            'amount' => 0.00,
        ]);

        $this->paymentRepository->shouldReceive('create')->once()->with([
            'amount' => 0.00,
            'subscription_id' => 423,
        ], $paymentMethod)->andReturn($payment);

        $result = $this->service->createPayment($user, 0.00, $paymentMethod, 'A Description', ['subscription_id' => 423]);

        $this->assertEquals($result, $payment);
    }

    public function testReversePaymentFailsWithoutStripe()
    {
        $payment = new Payment([
            'paymentMethod' => new PaymentMethod(),
        ]);

        $this->expectException(NotImplementedException::class);

        $this->service->reversePayment($payment);
    }

    public function testReversePaymentSuccess()
    {
        $payment = new Payment([
            'paymentMethod' => new PaymentMethod([
                'payment_method_type' => 'stripe',
            ]),
            'transaction_key' => 'test_key',
        ]);

        Carbon::setTestNow('2019-01-01 12:00:00');

        $this->paymentRepository->shouldReceive('update')->once()
            ->with($payment, \Mockery::on(function ($data) {

                $this->assertArrayHasKey('refunded_at', $data);

                return true;
            })
            );
        $this->refundHandler->shouldReceive('create')->once()->with('test_key');
        $this->dispatcher->shouldReceive('dispatch')->once()
            ->with(\Mockery::on(function(PaymentReversedEvent $event) use ($payment) {
                $this->assertEquals($event->getPayment(), $payment);

                return true;
            })
            );

        $this->service->reversePayment($payment);
    }

    public function testIssuePartialRefundFailsWithoutStripe()
    {
        $payment = new Payment([
            'paymentMethod' => new PaymentMethod(),
        ]);

        $this->expectException(NotImplementedException::class);

        $this->service->issuePartialRefund($payment, 5);
    }

    public function testIssuePartialRefundSuccess()
    {
        $payment = new Payment([
            'paymentMethod' => new PaymentMethod([
                'payment_method_type' => 'stripe',
            ]),
            'transaction_key' => 'test_key',
        ]);

        $this->refundHandler->shouldReceive('create')->once()->with('test_key', 5);

        $this->service->issuePartialRefund($payment, 5);
    }
}