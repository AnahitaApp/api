<?php
declare(strict_types=1);

namespace Tests\Unit\Validators\Subscription;

use App\Contracts\Repositories\Payment\PaymentMethodRepositoryContract;
use App\Models\Payment\PaymentMethod;
use App\Models\User\User;
use App\Validators\Subscription\PaymentMethodIsOwnedByUserValidator;
use Cartalyst\Stripe\Exception\NotFoundException;
use Illuminate\Http\Request;
use Tests\TestCase;

/**
 * Class PaymentMethodIsOwnedByUserValidatorTest
 * @package Tests\Unit\Validators\Subscription
 */
class PaymentMethodIsOwnedByUserValidatorTest extends TestCase
{
    public function testValidateFailsWithNonExistingRate()
    {
        $repository = mock(PaymentMethodRepositoryContract::class);
        $request = mock(Request::class);
        $validator = new PaymentMethodIsOwnedByUserValidator($repository, $request);

        $repository->shouldReceive('findOrFail')->andThrow(new NotFoundException());

        $this->assertFalse($validator->validate('payment_method_id', 214));
    }

    public function testValidateFailsWithMismatchedUser()
    {
        $repository = mock(PaymentMethodRepositoryContract::class);
        $request = mock(Request::class);
        $validator = new PaymentMethodIsOwnedByUserValidator($repository, $request);

        $paymentMethod = new PaymentMethod([
            'owner_id' => 3242,
            'owner_type' => 'user',
        ]);
        $repository->shouldReceive('findOrFail')->andReturn($paymentMethod);
        $user = new User();
        $user->id = 324;
        $request->shouldReceive('route')->andReturn($user);

        $this->assertFalse($validator->validate('payment_method_id', 214));
    }

    public function testValidateFailsWithMismatchedOwnerType()
    {
        $repository = mock(PaymentMethodRepositoryContract::class);
        $request = mock(Request::class);
        $validator = new PaymentMethodIsOwnedByUserValidator($repository, $request);

        $paymentMethod = new PaymentMethod([
            'owner_id' => 3242,
            'owner_type' => 'company',
        ]);
        $repository->shouldReceive('findOrFail')->andReturn($paymentMethod);
        $user = new User();
        $user->id = 3242;
        $request->shouldReceive('route')->andReturn($user);

        $this->assertFalse($validator->validate('payment_method_id', 214));
    }

    public function testValidatePasses()
    {
        $repository = mock(PaymentMethodRepositoryContract::class);
        $request = mock(Request::class);
        $validator = new PaymentMethodIsOwnedByUserValidator($repository, $request);

        $paymentMethod = new PaymentMethod([
            'owner_id' => 3242,
            'owner_type' => 'user',
        ]);
        $repository->shouldReceive('findOrFail')->andReturn($paymentMethod);
        $user = new User();
        $user->id = 3242;
        $request->shouldReceive('route')->andReturn($user);

        $this->assertTrue($validator->validate('payment_method_id', 214));
    }
}