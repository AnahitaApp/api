<?php
declare(strict_types=1);

namespace Tests\Feature\Http\Organization\Location;

use App\Models\Organization\Organization;
use App\Models\Organization\Location;
use App\Models\Organization\OrganizationManager;
use App\Models\Role;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;
use Tests\Traits\RolesTesting;

/**
 * Class OrganizationUpdateTest
 * @package Tests\Feature\Http\Organization\Location
 */
class OrganizationLocationUpdateTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog, RolesTesting;

    /**
     * @var string
     */
    private $route;

    public function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();
    }

    /**
     * Sets up the proper route for the request
     *
     * @param int $organizationId
     * @param int $locationId
     */
    private function setupRoute(int $organizationId, $locationId)
    {
        $this->route = '/v1/organizations/' . $organizationId . '/locations/' . $locationId;
    }

    public function testOrganizationNotFound()
    {
        $this->setupRoute(4523, 345);
        $response = $this->json('PUT', $this->route);
        $response->assertStatus(404);
    }

    public function testNotLoggedInUserBlocked()
    {
        $model = factory(Location::class)->create();
        $this->setupRoute($model->organization_id, $model->id);
        $response = $this->json('PUT', $this->route);
        $response->assertStatus(403);
    }

    public function testNonAdminUsersBlocked()
    {
        foreach ($this->rolesWithoutAdmins() as $role) {
            $this->actAs($role);
            $model = factory(Location::class)->create();
            $this->setupRoute($model->organization_id, $model->id);
            $response = $this->json('PUT', $this->route);

            $response->assertStatus(403);
        }
    }

    public function testNotUserNotOrganizationAdminBlocked()
    {
        $this->actAs(Role::MANAGER);
        $organization = factory(Organization::class)->create();
        factory(OrganizationManager::class)->create([
            'organization_id' => $organization->id,
            'user_id' => $this->actingAs->id,
            'role_id' => Role::MANAGER,
        ]);
        $model = factory(Location::class)->create();
        $this->setupRoute($model->organization_id, $model->id);
        $response = $this->json('PUT', $this->route);
        $response->assertStatus(403);
    }

    public function testUpdateSuccessful()
    {
        $this->actAs(Role::ADMINISTRATOR);
        $organization = factory(Organization::class)->create();
        factory(OrganizationManager::class)->create([
            'organization_id' => $organization->id,
            'user_id' => $this->actingAs->id,
            'role_id' => Role::ADMINISTRATOR,
        ]);
        $model = factory(Location::class)->create([
            'organization_id' => $organization->id,
            'address_line_1' => '12 Fake Street'
        ]);
        $this->setupRoute($model->organization_id, $model->id);

        $properties = [
            'address_line_1' => '123 Fake Street'
        ];

        $response = $this->json('PUT', $this->route, $properties);

        $response->assertStatus(200);

        /** @var Location $updated */
        $updated = Location::find($model->id);

        $this->assertEquals( '123 Fake Street', $updated->address_line_1);
    }

    public function testUpdateFailsInvalidStringFields()
    {
        $this->actAs(Role::ADMINISTRATOR);
        $model = factory(Location::class)->create();
        factory(OrganizationManager::class)->create([
            'organization_id' => $model->organization_id,
            'user_id' => $this->actingAs->id,
            'role_id' => Role::ADMINISTRATOR,
        ]);
        $this->setupRoute($model->organization_id, $model->id);

        $data = [
            'name' => 3124,
            'address_line_1' => 2351,
            'address_line_2' => 2351,
            'city' => 135,
            'postal_code' => 135,
            'region' => 135,
            'country' => 453
        ];

        $response = $this->json('PUT', $this->route, $data);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'name' => ['The name must be a string.'],
                'address_line_1' => ['The address line 1 must be a string.'],
                'address_line_2' => ['The address line 2 must be a string.'],
                'city' => ['The city must be a string.'],
                'postal_code' => ['The postal code must be a string.'],
                'region' => ['The region must be a string.'],
                'country' => ['The country must be a string.'],
            ]
        ]);
    }

    public function testUpdateFailsInvalidBooleanFields()
    {
        $this->actAs(Role::ADMINISTRATOR);
        $model = factory(Location::class)->create();
        factory(OrganizationManager::class)->create([
            'organization_id' => $model->organization_id,
            'user_id' => $this->actingAs->id,
            'role_id' => Role::ADMINISTRATOR,
        ]);
        $this->setupRoute($model->organization_id, $model->id);

        $data = [
            'delivery_available' => 'hi',
        ];

        $response = $this->json('PUT', $this->route, $data);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'delivery_available' => ['The delivery available field must be true or false.'],
            ]
        ]);
    }

    public function testUpdateFailsNotPresentFieldsPresent()
    {
        $this->actAs(Role::ADMINISTRATOR);
        $model = factory(Location::class)->create();
        factory(OrganizationManager::class)->create([
            'organization_id' => $model->organization_id,
            'user_id' => $this->actingAs->id,
            'role_id' => Role::ADMINISTRATOR,
        ]);
        $this->setupRoute($model->organization_id, $model->id);

        $data = [
            'latitude' => 12.1324,
            'longitude' => 12.1324,
        ];

        $response = $this->json('PUT', $this->route, $data);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'latitude' => ['The latitude field is not allowed or can not be set for this request.'],
                'longitude' => ['The longitude field is not allowed or can not be set for this request.'],
            ]
        ]);
    }
}
