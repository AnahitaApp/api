<?php
declare(strict_types=1);

namespace Tests\Integration\Policies\User;

use App\Models\User\User;
use App\Policies\User\UserPolicy;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\RolesTesting;

/**
 * Class UserPolicyTest
 * @package Tests\Integration\Policies\User
 */
class UserPolicyTest extends TestCase
{
    use DatabaseSetupTrait, RolesTesting;

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

        foreach ($this->rolesWithoutAdmins() as $role) {
            $user = $this->getUserOfRole($role);

            $this->assertFalse($policy->update($user, new User()));
        }
    }
}