<?php
declare(strict_types=1);

namespace Tests\Integration\Policies\Subscription;

use App\Models\Subscription\Subscription;
use App\Models\User\User;
use App\Policies\Subscription\SubscriptionPolicy;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;

/**
 * Class SubscriptionPolicyTest
 * @package Tests\Integration\Policies\Subscription
 */
class SubscriptionPolicyTest extends TestCase
{
    use DatabaseSetupTrait;

    public function testCreate()
    {
        $policy = new SubscriptionPolicy();

        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();

        $this->assertFalse($policy->create($user1, $user2));
        $this->assertTrue($policy->create($user1, $user1));
    }

    public function testUpdate()
    {
        $policy = new SubscriptionPolicy();

        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $subscription = factory(Subscription::class)->create([
            'subscriber_id' => $user1->id,
        ]);

        $this->assertFalse($policy->update($user1, $user2, $subscription));
        $this->assertFalse($policy->update($user2, $user2, $subscription));
        $this->assertTrue($policy->update($user1, $user1, $subscription));
    }
}