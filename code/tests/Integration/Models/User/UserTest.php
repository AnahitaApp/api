<?php
declare(strict_types=1);

namespace Tests\Integration\Models\User;

use App\Models\Organization\Organization;
use App\Models\Organization\OrganizationManager;
use App\Models\Role;
use App\Models\User\User;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;

/**
 * Class UserTest
 * @package Tests\Integration\Models
 */
class UserTest extends TestCase
{
    use DatabaseSetupTrait;
    
    public function testHasRole()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $this->assertFalse($user->hasRole(1));

        $user = factory(User::class)->create();
        $user->roles()->attach(1);
        $this->assertFalse($user->hasRole(2));

        $user = factory(User::class)->create();
        $user->roles()->attach(1);
        $this->assertTrue($user->hasRole(1));

        $user = factory(User::class)->create();
        $user->roles()->attach(1);
        $user->roles()->attach(2);
        $this->assertTrue($user->hasRole(1));

        $user = factory(User::class)->create();
        $user->roles()->attach(1);
        $user->roles()->attach(2);

        $this->assertTrue($user->hasRole([1,6]));
        $this->assertTrue($user->hasRole([1,2]));
        $this->assertTrue($user->hasRole([1]));
        $this->assertTrue($user->hasRole([2]));
        $this->assertTrue($user->hasRole([2,3]));
        $this->assertFalse($user->hasRole([4,5]));
    }

    public function testCanManageOrganization()
    {
        /** @var Organization $organization */
        $organization = factory(Organization::class)->create();
        /** @var User $user */
        $user = factory(User::class)->create();

        $this->assertFalse($user->canManageOrganization($organization));

        factory(OrganizationManager::class)->create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'role_id' => Role::MANAGER,
        ]);
        $user->refresh();

        $this->assertFalse($user->canManageOrganization($organization, Role::ADMINISTRATOR));
        $this->assertTrue($user->canManageOrganization($organization, Role::MANAGER));

        factory(OrganizationManager::class)->create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'role_id' => Role::ADMINISTRATOR,
        ]);
        $user->refresh();
        $this->assertTrue($user->canManageOrganization($organization, Role::ADMINISTRATOR));
    }
}
