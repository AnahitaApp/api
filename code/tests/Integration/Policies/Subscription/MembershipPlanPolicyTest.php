<?php
declare(strict_types=1);

namespace Tests\Integration\Policies\Subscription;

use App\Models\Subscription\MembershipPlan;
use App\Models\User\User;
use App\Policies\Subscription\MembershipPlanPolicy;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;

/**
 * Class MembershipPlanPolicyTest
 * @package Tests\Integration\Policies\Subscription
 */
class MembershipPlanPolicyTest extends TestCase
{
    use DatabaseSetupTrait;

    public function testAll()
    {
        $policy = new MembershipPlanPolicy();

        $this->assertTrue($policy->all(new User()));
    }

    public function testView()
    {
        $policy = new MembershipPlanPolicy();

        $this->assertTrue($policy->view(new User(), new MembershipPlan()));
    }

    public function testCreate()
    {
        $policy = new MembershipPlanPolicy();

        $this->assertFalse($policy->create(new User()));
    }

    public function testUpdate()
    {
        $policy = new MembershipPlanPolicy();

        $this->assertFalse($policy->update(new User(), new MembershipPlan()));
    }

    public function testDelete()
    {
        $policy = new MembershipPlanPolicy();

        $this->assertFalse($policy->delete(new User(), new MembershipPlan()));
    }
}