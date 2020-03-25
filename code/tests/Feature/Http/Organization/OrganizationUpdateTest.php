<?php
declare(strict_types=1);

namespace Tests\Feature\Http\Organization;

use App\Models\Organization\Organization;
use App\Models\Organization\OrganizationManager;
use App\Models\Role;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;
use Tests\Traits\RolesTesting;

/**
 * Class OrganizationUpdateTest
 * @package Tests\Feature\Http\Organization
 */
class OrganizationUpdateTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog, RolesTesting;
    
    const BASE_ROUTE = '/v1/organizations/';

    public function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();
    }

    public function testNotLoggedInUserBlocked()
    {
        $membershipPlan = factory(Organization::class)->create();
        $response = $this->json('PATCH', static::BASE_ROUTE . $membershipPlan->id);
        $response->assertStatus(403);
    }

    public function testNotAdminUserBlocked()
    {
        foreach ($this->rolesWithoutAdmins() as $role) {
            $this->actAs($role);
            $model = factory(Organization::class)->create();
            $response = $this->json('PATCH', static::BASE_ROUTE . $model->id);
            $response->assertStatus(403);
        }
    }

    public function testPatchSuccessful()
    {
        $this->actAs(Role::ORGANIZATION_ADMIN);

        /** @var Organization $organization */
        $organization = factory(Organization::class)->create([
            'name' => 'Test Organiz',
        ]);
        factory(OrganizationManager::class)->create([
            'user_id' => $this->actingAs->id,
            'role_id' => Role::ORGANIZATION_ADMIN,
            'organization_id' => $organization->id,
        ]);

        $data = [
            'name' => 'Test Organization',
        ];

        $response = $this->json('PATCH', static::BASE_ROUTE . $organization->id, $data);
        $response->assertStatus(200);
        $response->assertJson($data);


        /** @var Organization $updated */
        $updated = Organization::find($organization->id);

        $this->assertEquals('Test Organization', $updated->name);
    }

    public function testPatchNotFoundFails()
    {
        $this->actAs(Role::SUPER_ADMIN);

        $response = $this->json('PATCH', static::BASE_ROUTE . '5')
            ->assertExactJson([
                'message'   =>  'This item was not found.'
            ]);
        $response->assertStatus(404);
    }

    public function testPatchInvalidIdFails()
    {
        $this->actAs(Role::SUPER_ADMIN);

        $response = $this->json('PATCH', static::BASE_ROUTE . '/b')
            ->assertExactJson([
                'message'   => 'This path was not found.',
            ]);
        $response->assertStatus(404);
    }

    public function testPatchFailsInvalidStringFields()
    {
        $organization = factory(Organization::class)->create();

        $this->actAs(Role::SUPER_ADMIN);

        $data = [
            'name' => 5,
        ];

        $response = $this->json('PATCH', static::BASE_ROUTE . $organization->id, $data);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'name' => ['The name must be a string.'],
            ]
        ]);
    }

    public function testPatchFailsTooLongFields()
    {
        $organization = factory(Organization::class)->create();

        $this->actAs(Role::SUPER_ADMIN);

        $data = [
            'name' => str_repeat('a', 121),
        ];

        $response = $this->json('PATCH', static::BASE_ROUTE . $organization->id, $data);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'name' => ['The name may not be greater than 120 characters.'],
            ]
        ]);
    }
}