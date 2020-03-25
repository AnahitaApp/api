<?php
declare(strict_types=1);

namespace Tests\Integration\Policies\Organization;

use App\Models\Organization\Organization;
use App\Models\Organization\OrganizationManager;
use App\Models\Role;
use App\Models\User\User;
use App\Policies\Organization\OrganizationPolicy;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;

/**
 * Class OrganizationPolicyTest
 * @package Tests\Integration\Policies\Organization
 */
class OrganizationPolicyTest extends TestCase
{
    use DatabaseSetupTrait;

    public function testAll()
    {
        $policy = new OrganizationPolicy();

        $this->assertFalse($policy->all(new User()));
    }

    public function testCreate()
    {
        $policy = new OrganizationPolicy();

        $this->assertTrue($policy->create(new User()));
    }

    public function testViewBlocksWhenNotOrganizationManager()
    {
        $policy = new OrganizationPolicy();
        $organization = factory(Organization::class)->create();
        $user = factory(User::class)->create();

        $this->assertFalse($policy->view($user, $organization));
    }

    public function testViewPassesForOrganizationManager()
    {
        $policy = new OrganizationPolicy();
        $organization = factory(Organization::class)->create();
        $user = factory(User::class)->create();

        factory(OrganizationManager::class)->create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'role_id' => Role::ORGANIZATION_MANAGER,
        ]);

        $this->assertTrue($policy->view($user, $organization));
    }

    public function testUpdateBlocksWhenNotOrganizationManager()
    {
        $policy = new OrganizationPolicy();
        $organization = factory(Organization::class)->create();
        $user = factory(User::class)->create();

        $this->assertFalse($policy->update($user, $organization));
    }

    public function testUpdatePassesForOrganizationManager()
    {
        $policy = new OrganizationPolicy();
        $organization = factory(Organization::class)->create();
        $user = factory(User::class)->create();

        factory(OrganizationManager::class)->create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'role_id' => Role::ORGANIZATION_MANAGER,
        ]);

        $this->assertTrue($policy->update($user, $organization));
    }

    public function testDeleteBlocksWhenNotOrganizationManager()
    {
        $policy = new OrganizationPolicy();
        $organization = factory(Organization::class)->create();
        $user = factory(User::class)->create();

        $this->assertFalse($policy->delete($user, $organization));
    }

    public function testDeleteBlocksForOrganizationManager()
    {
        $policy = new OrganizationPolicy();
        $organization = factory(Organization::class)->create();
        $user = factory(User::class)->create();

        factory(OrganizationManager::class)->create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'role_id' => Role::ORGANIZATION_MANAGER,
        ]);

        $this->assertFalse($policy->delete($user, $organization));
    }

    public function testDeletePassesForOrganizationAdmin()
    {
        $policy = new OrganizationPolicy();
        $organization = factory(Organization::class)->create();
        $user = factory(User::class)->create();

        factory(OrganizationManager::class)->create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'role_id' => Role::ORGANIZATION_ADMIN,
        ]);

        $this->assertTrue($policy->delete($user, $organization));
    }
}