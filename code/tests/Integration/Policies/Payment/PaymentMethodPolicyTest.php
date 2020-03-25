<?php
declare(strict_types=1);

namespace Tests\Integration\Policies\Payment;

use App\Models\Payment\PaymentMethod;
use App\Models\User\User;
use App\Policies\Payment\PaymentMethodPolicy;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;

/**
 * Class PaymentMethodPolicyTest
 * @package Tests\Integration\Policies\Payment
 */
class PaymentMethodPolicyTest extends TestCase
{
    use DatabaseSetupTrait;

    public function testCreate()
    {
        $policy = new PaymentMethodPolicy();

        $me = factory(User::class)->create();
        $other = factory(User::class)->create();

        $this->assertFalse($policy->create($me, $other));
        $this->assertTrue($policy->create($me, $me));
    }

    public function testDelete()
    {
        $policy = new PaymentMethodPolicy();

        $me = factory(User::class)->create();
        $other = factory(User::class)->create();

        $paymentMethod = factory(PaymentMethod::class)->create([
            'owner_id' => $me->id,
        ]);

        $this->assertFalse($policy->delete($me, $other, $paymentMethod));
        $this->assertFalse($policy->delete($other, $other, $paymentMethod));
        $this->assertTrue($policy->delete($me, $me, $paymentMethod));
    }
}