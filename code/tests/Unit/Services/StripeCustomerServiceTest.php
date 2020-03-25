<?php
declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Contracts\Repositories\Payment\PaymentMethodRepositoryContract;
use App\Contracts\Repositories\User\UserRepositoryContract;
use App\Models\Payment\PaymentMethod;
use App\Models\User\User;
use App\Services\StripeCustomerService;
use Cartalyst\Stripe\Api\Cards;
use Cartalyst\Stripe\Api\Customers;
use Tests\CustomMockInterface;
use Tests\TestCase;

/**
 * Class StripeCustomerServiceTest
 * @package Tests\Unit\Services
 */
class StripeCustomerServiceTest extends TestCase
{
    /**
     * @var UserRepositoryContract|CustomMockInterface
     */
    private $userRepository;

    /**
     * @var PaymentMethodRepositoryContract|CustomMockInterface
     */
    private $paymentMethodRepository;

    /**
     * @var Customers|CustomMockInterface
     */
    private $customerHelper;

    /**
     * @var Cards|CustomMockInterface
     */
    private $cardHelper;

    /**
     * @var StripeCustomerService
     */
    private $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->userRepository = mock(UserRepositoryContract::class);
        $this->paymentMethodRepository = mock(PaymentMethodRepositoryContract::class);
        $this->customerHelper = mock(Customers::class);
        $this->cardHelper = mock(Cards::class);
        $this->service = new StripeCustomerService(
            $this->userRepository,
            $this->paymentMethodRepository,
            $this->customerHelper,
            $this->cardHelper
        );
    }

    public function testCreateCustomer()
    {
        $user = new User([
            'email' => 'test@test.com',
        ]);

        $this->customerHelper->shouldReceive('create')->once()->with([
            'email' => 'test@test.com',
        ])->andReturn([
            'id' => 'cus_test',
            'sources' => []
        ]);

        $this->userRepository->shouldReceive('update')->once()->with($user, ['stripe_customer_key' => 'cus_test']);

        $result = $this->service->createCustomer($user);

        $this->assertEquals($result, [
            'id' => 'cus_test',
            'sources' => []
        ]);
        $this->assertEquals('cus_test', $user->stripe_customer_key);
    }

    public function testRetrieveCustomerFailsWithoutToken()
    {
        $user = new User();

        $this->expectException(\InvalidArgumentException::class);

        $this->service->retrieveCustomer($user);
    }

    public function testRetrieveCustomer()
    {
        $user = new User([
            'stripe_customer_key' => 'cus_test'
        ]);

        $this->customerHelper->shouldReceive('find')->once()->andReturn([
            'id' => 'cus_test',
            'sources' => []
        ]);

        $result = $this->service->retrieveCustomer($user);

        $this->assertEquals($result, [
            'id' => 'cus_test',
            'sources' => []
        ]);
    }

    public function testCreatePaymentMethodWithoutExistingStripeCustomer()
    {
        $user = new User([
            'email' => 'test@test.com',
        ]);
        $user->id = 324;

        $this->customerHelper->shouldReceive('create')->once()->with([
            'email' => 'test@test.com',
        ])->andReturn([
            'id' => 'cus_test',
            'sources' => []
        ]);

        $this->userRepository->shouldReceive('update')->once()->with($user, ['stripe_customer_key' => 'cus_test']);

        $this->cardHelper->shouldReceive('create')->once()->with('cus_test', [
            'card_number'
        ])->andReturn([
            'id' => 'card_id',
            'last4' => '1234',
        ]);

        $this->paymentMethodRepository->shouldReceive('create')->once()->with([
            'payment_method_key' => 'card_id',
            'payment_method_type' => 'stripe',
            'identifier' => '1234',
            'owner_id' => 324,
            'owner_type' => 'user',
        ])->andReturn(new PaymentMethod());

        $this->service->createPaymentMethod($user, [
            'card_number'
        ]);
    }

    public function testCreatePaymentMethodWithExistingStripeCustomer()
    {
        $user = new User([
            'email' => 'test@test.com',
            'stripe_customer_key' => 'cus_test',
        ]);
        $user->id = 324;

        $this->cardHelper->shouldReceive('create')->once()->with('cus_test', [
            'card_number'
        ])->andReturn([
            'id' => 'card_id',
            'last4' => '1234',
        ]);

        $this->paymentMethodRepository->shouldReceive('create')->once()->with([
            'payment_method_key' => 'card_id',
            'payment_method_type' => 'stripe',
            'identifier' => '1234',
            'owner_id' => 324,
            'owner_type' => 'user',
        ])->andReturn(new PaymentMethod());

        $this->service->createPaymentMethod($user, [
            'card_number'
        ]);
    }

    public function testDeletePaymentMethodFailsWithoutToken()
    {
        $paymentMethod = new PaymentMethod([
            'owner' => new User(),
        ]);

        $this->expectException(\InvalidArgumentException::class);

        $this->service->deletePaymentMethod($paymentMethod);
    }

    public function testDeletePaymentMethod()
    {
        $paymentMethod = new PaymentMethod([
            'payment_method_key' => 'card_test',
            'owner' => new User([
                'stripe_customer_key' => 'cus_test',
            ]),
        ]);

        $this->paymentMethodRepository->shouldReceive('delete')->once()->with($paymentMethod);
        $this->cardHelper->shouldReceive('delete')->once()->with('cus_test', 'card_test')->andReturn([
            'id' => 'card',
        ]);

        $result = $this->service->deletePaymentMethod($paymentMethod);

        $this->assertEquals($result, [
            'id' => 'card',
        ]);
    }

    public function testRetrievePaymentMethodReturnsNullWithoutCard()
    {
        $paymentMethod = new PaymentMethod([
            'payment_method_key' => 'card_test',
            'owner' => new User([
                'stripe_customer_key' => null,
            ]),
        ]);

        $this->assertNull($this->service->retrievePaymentMethod($paymentMethod));
    }

    public function testRetrievePaymentMethod()
    {
        $paymentMethod = new PaymentMethod([
            'payment_method_key' => 'card',
            'owner' => new User([
                'stripe_customer_key' => 'customer',
            ]),
        ]);

        $this->cardHelper->shouldReceive('find')->once()->with('customer', 'card')->andReturn([
            'last4' => '4242',
        ]);

        $result = $this->service->retrievePaymentMethod($paymentMethod);

        $this->assertEquals($result, [
            'last4' => '4242',
        ]);
    }
}