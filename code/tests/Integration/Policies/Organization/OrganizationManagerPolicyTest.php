<?php
declare(strict_types=1);

namespace Tests\Integration\Policies\Organization;

use App\Models\Organization\Organization;
use App\Models\Organization\OrganizationManager;
use App\Models\Role;
use App\Models\User\User;
use App\Policies\Organization\OrganizationManagerPolicy;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;

/**
 * Class OrganizationManagerPolicyTest
 * @package Tests\Integration\Policies\Organization
 */
class OrganizationManagerPolicyTest extends TestCase
{
    use DatabaseSetupTrait;

    public function testAllBlocksWhenNotOrganizationManager()
    {
        $policy = new OrganizationManagerPolicy();
        $organization = factory(Organization::class)->create();
        $user = factory(User::class)->create();

        $this->assertFalse($policy->all($user, $organization));
    }

    public function testAllPassesForOrganizationManager()
    {
        $policy = new OrganizationManagerPolicy();
        $organization = factory(Organization::class)->create();
        $user = factory(User::class)->create();

        factory(OrganizationManager::class)->create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'role_id' => Role::ORGANIZATION_MANAGER,
        ]);

        $this->assertTrue($policy->all($user, $organization));
    }

    public function testCreateBlocksWhenNotOrganizationManager()
    {
        $policy = new OrganizationManagerPolicy();
        $organization = factory(Organization::class)->create();
        $user = factory(User::class)->create();

        $this->assertFalse($policy->create($user, $organization));
    }

    public function testCreateBlocksForOrganizationManager()
    {
        $policy = new OrganizationManagerPolicy();
        $organization = factory(Organization::class)->create();
        $user = factory(User::class)->create();

        factory(OrganizationManager::class)->create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'role_id' => Role::ORGANIZATION_MANAGER,
        ]);

        $this->assertFalse($policy->create($user, $organization));
    }

    public function testCreatePassesForOrganizationAdmin()
    {
        $policy = new OrganizationManagerPolicy();
        $organization = factory(Organization::class)->create();
        $user = factory(User::class)->create();

        factory(OrganizationManager::class)->create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'role_id' => Role::ORGANIZATION_ADMIN,
        ]);

        $this->assertTrue($policy->create($user, $organization));
    }

    public function testUpdateBlocksWithOrganizationMismatch()
    {
        $policy = new OrganizationManagerPolicy();
        $organization = factory(Organization::class)->create();
        $user = factory(User::class)->create();
        $organizationManager = factory(OrganizationManager::class)->create();

        $this->assertFalse($policy->update($user, $organization, $organizationManager));
    }

    public function testUpdateBlocksWhenNotOrganizationNot()
    {
        $policy = new OrganizationManagerPolicy();
        $organization = factory(Organization::class)->create();
        $user = factory(User::class)->create();
        $organizationManager = factory(OrganizationManager::class)->create([
            'user_id' => $user->id,
            'role_id' => Role::ORGANIZATION_MANAGER,
        ]);

        $this->assertFalse($policy->update($user, $organization, $organizationManager));
    }

    public function testUpdateBlocksForOrganizationManager()
    {
        $policy = new OrganizationManagerPolicy();
        $organization = factory(Organization::class)->create();
        $user = factory(User::class)->create();

        $organizationManager = factory(OrganizationManager::class)->create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'role_id' => Role::ORGANIZATION_MANAGER,
        ]);

        $this->assertFalse($policy->update($user, $organization, $organizationManager));
    }

    public function testUpdatePassesForOrganizationAdmin()
    {
        $policy = new OrganizationManagerPolicy();
        $organization = factory(Organization::class)->create();
        $user = factory(User::class)->create();

        $organizationManager = factory(OrganizationManager::class)->create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'role_id' => Role::ORGANIZATION_ADMIN,
        ]);

        $this->assertTrue($policy->update($user, $organization, $organizationManager));
    }

    public function testDeleteBlocksWithOrganizationMismatch()
    {
        $policy = new OrganizationManagerPolicy();
        $organization = factory(Organization::class)->create();
        $user = factory(User::class)->create();
        $organizationManager = factory(OrganizationManager::class)->create();

        $this->assertFalse($policy->delete($user, $organization, $organizationManager));
    }

    public function testDeleteBlocksWhenNotOrganizationNot()
    {
        $policy = new OrganizationManagerPolicy();
        $organization = factory(Organization::class)->create();
        $user = factory(User::class)->create();
        $organizationManager = factory(OrganizationManager::class)->create([
            'user_id' => $user->id,
            'role_id' => Role::ORGANIZATION_MANAGER,
        ]);

        $this->assertFalse($policy->delete($user, $organization, $organizationManager));
    }

    public function testDeleteBlocksForOrganizationManager()
    {
        $policy = new OrganizationManagerPolicy();
        $organization = factory(Organization::class)->create();
        $user = factory(User::class)->create();

        $organizationManager = factory(OrganizationManager::class)->create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'role_id' => Role::ORGANIZATION_MANAGER,
        ]);

        $this->assertFalse($policy->delete($user, $organization, $organizationManager));
    }

    public function testDeletePassesForOrganizationAdmin()
    {
        $policy = new OrganizationManagerPolicy();
        $organization = factory(Organization::class)->create();
        $user = factory(User::class)->create();

        $organizationManager = factory(OrganizationManager::class)->create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'role_id' => Role::ORGANIZATION_ADMIN,
        ]);

        $this->assertTrue($policy->delete($user, $organization, $organizationManager));
    }
}