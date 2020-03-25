<?php
declare(strict_types=1);

namespace Tests\Integration\Policies\User;

use App\Models\User\User;
use App\Policies\User\UserPolicy;
use Tests\TestCase;

/**
 * Class UserPolicyTest
 * @package Tests\Integration\Policies\User
 */
class UserPolicyTest extends TestCase
{
    public function testViewSelfPasses()
    {
        $policy = new UserPolicy();

        $loggedInUser = new User();
        $loggedInUser->id = 5;

        $this->assertTrue($policy->viewSelf($loggedInUser));
    }

    public function testViewSuccess()
    {
        $policy = new UserPolicy();

        $this->assertTrue($policy->view(new User(), new User()));
    }

    public function testUpdateSuccess()
    {
        $policy = new UserPolicy();

        $loggedInUser = new User();
        $loggedInUser->id = 5;

        $this->assertTrue($policy->update($loggedInUser, $loggedInUser));
    }

    public function testUpdateBlocks()
    {
        $policy = new UserPolicy();

        $loggedInUser = new User();
        $loggedInUser->id = 5;
        $requestedUser = new User();
        $requestedUser->id = 9420;

        $this->assertFalse($policy->update($loggedInUser, $requestedUser));
    }
}