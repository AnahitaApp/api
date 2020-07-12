<?php
declare(strict_types=1);

namespace Tests\Integration\Policies\Organization;

use App\Models\Organization\Location;
use App\Models\Organization\Organization;
use App\Models\Organization\OrganizationManager;
use App\Models\Role;
use App\Models\User\User;
use App\Policies\Organization\LocationPolicy;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;

/**
 * Class LocationPolicyTest
 * @package Tests\Integration\Policies\Organization
 */
class LocationPolicyTest extends TestCase
{
    use DatabaseSetupTrait;

    public function testAllPasses()
    {
        $policy = new LocationPolicy();

        $this->assertTrue($policy->all(new User(), new Organization()));
    }

    public function testCreateBlocksWhenNotOrganizationManager()
    {
        $policy = new LocationPolicy();
        $organization = factory(Organization::class)->create();
        $user = factory(User::class)->create();

        $this->assertFalse($policy->create($user, $organization));
    }

    public function testCreatePassesForOrganizationAdmin()
    {
        $policy = new LocationPolicy();
        $organization = factory(Organization::class)->create();
        $user = factory(User::class)->create();

        factory(OrganizationManager::class)->create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'role_id' => Role::ADMINISTRATOR,
        ]);

        $this->assertTrue($policy->create($user, $organization));
    }

    public function testUpdateBlocksWithOrganizationMismatch()
    {
        $policy = new LocationPolicy();
        $organization = factory(Organization::class)->create();
        $user = factory(User::class)->create();
        $location = factory(Location::class)->create();

        $this->assertFalse($policy->update($user, $organization, $location));
    }

    public function testUpdateBlocksWhenNotRelatedToOrganization()
    {
        $policy = new LocationPolicy();
        $organization = factory(Organization::class)->create();
        $user = factory(User::class)->create();
        $location = factory(Location::class)->create();

        factory(OrganizationManager::class)->create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'role_id' => Role::ADMINISTRATOR,
        ]);

        $this->assertFalse($policy->update($user, $organization, $location));
    }

    public function testUpdatePassesForOrganizationAdmin()
    {
        $policy = new LocationPolicy();
        $organization = factory(Organization::class)->create();
        $user = factory(User::class)->create();

        $location = factory(Location::class)->create([
            'organization_id' => $organization->id,
        ]);

        factory(OrganizationManager::class)->create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'role_id' => Role::ADMINISTRATOR,
        ]);

        $this->assertTrue($policy->update($user, $organization, $location));
    }

    public function testDeleteBlocksWithOrganizationMismatch()
    {
        $policy = new LocationPolicy();
        $organization = factory(Organization::class)->create();
        $user = factory(User::class)->create();
        $location = factory(Location::class)->create();

        $this->assertFalse($policy->delete($user, $organization, $location));
    }

    public function testDeleteBlocksWhenNotRelatedOrganization()
    {
        $policy = new LocationPolicy();
        $organization = factory(Organization::class)->create();
        $user = factory(User::class)->create();
        factory(OrganizationManager::class)->create([
            'user_id' => $user->id,
            'role_id' => Role::MANAGER,
        ]);

        $location = factory(Location::class)->create();

        $this->assertFalse($policy->delete($user, $organization, $location));
    }

    public function testDeletePassesForOrganizationAdmin()
    {
        $policy = new LocationPolicy();
        $organization = factory(Organization::class)->create();
        $user = factory(User::class)->create();

        factory(OrganizationManager::class)->create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'role_id' => Role::ADMINISTRATOR,
        ]);

        $location = factory(Location::class)->create([
            'organization_id' => $organization->id,
        ]);

        $this->assertTrue($policy->delete($user, $organization, $location));
    }
}