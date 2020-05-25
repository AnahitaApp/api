<?php
declare(strict_types=1);

namespace Tests\Feature\Http\Organization\Location;

use App\Models\Organization\Organization;
use App\Models\Organization\OrganizationManager;
use App\Models\Role;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;
use Tests\Traits\RolesTesting;

/**
 * Class OrganizationLocationCreateTest
 * @package Tests\Feature\Http\Organization\Location
 */
class OrganizationLocationCreateTest extends TestCase
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
     */
    private function setupRoute(int $organizationId)
    {
        $this->route = '/v1/organizations/' . $organizationId . '/locations';
    }

    public function testOrganizationNotFound()
    {
        $this->setupRoute(4523);
        $response = $this->json('POST', $this->route);
        $response->assertStatus(404);
    }

    public function testNotLoggedInUserBlocked()
    {
        $organization = factory(Organization::class)->create();
        $this->setupRoute($organization->id);
        $response = $this->json('POST', $this->route);
        $response->assertStatus(403);
    }

    public function testNonAdminUsersBlocked()
    {
        foreach ($this->rolesWithoutAdmins() as $role) {
            $this->actAs($role);
            $organization = factory(Organization::class)->create();
            $this->setupRoute($organization->id);
            $response = $this->json('POST', $this->route);

            $response->assertStatus(403);
        }
    }

    public function testNotUserNotOrganizationAdminBlocked()
    {
        $this->actAs(Role::ORGANIZATION_MANAGER);
        $organization = factory(Organization::class)->create();
        factory(OrganizationManager::class)->create([
            'organization_id' => $organization->id,
            'user_id' => $this->actingAs->id,
            'role_id' => Role::ORGANIZATION_MANAGER,
        ]);
        $this->setupRoute($organization->id);
        $response = $this->json('POST', $this->route);
        $response->assertStatus(403);
    }

    public function testCreateSuccessful()
    {
        $this->actAs(Role::ORGANIZATION_ADMIN);
        $organization = factory(Organization::class)->create();
        factory(OrganizationManager::class)->create([
            'organization_id' => $organization->id,
            'user_id' => $this->actingAs->id,
            'role_id' => Role::ORGANIZATION_ADMIN,
        ]);
        $this->setupRoute($organization->id);

        $properties = [
            'name' => 'A Location',
            'address_line_1' => '123 Fake Street',
            'city' => 'A City',
            'country' => 'A Country'
        ];

        $response = $this->json('POST', $this->route, $properties);

        $response->assertStatus(201);

        $response->assertJson($properties);
    }

    public function testCreateFailsMissingRequiredFields()
    {
        $this->actAs(Role::ORGANIZATION_ADMIN);
        $organization = factory(Organization::class)->create();
        factory(OrganizationManager::class)->create([
            'organization_id' => $organization->id,
            'user_id' => $this->actingAs->id,
            'role_id' => Role::ORGANIZATION_ADMIN,
        ]);
        $this->setupRoute($organization->id);

        $response = $this->json('POST', $this->route);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'name' => ['The name field is required.'],
                'address_line_1' => ['The address line 1 field is required.'],
                'city' => ['The city field is required.'],
                'country' => ['The country field is required.'],
            ]
        ]);
    }

    public function testCreateFailsInvalidStringFields()
    {
        $this->actAs(Role::ORGANIZATION_ADMIN);
        $organization = factory(Organization::class)->create();
        factory(OrganizationManager::class)->create([
            'organization_id' => $organization->id,
            'user_id' => $this->actingAs->id,
            'role_id' => Role::ORGANIZATION_ADMIN,
        ]);
        $this->setupRoute($organization->id);

        $data = [
            'name' => 3124,
            'address_line_1' => 2351,
            'address_line_2' => 2351,
            'city' => 135,
            'postal_code' => 135,
            'region' => 135,
            'country' => 453
        ];

        $response = $this->json('POST', $this->route, $data);

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

    public function testCreateFailsBlockedFieldsPresent()
    {
        $this->actAs(Role::ORGANIZATION_ADMIN);
        $organization = factory(Organization::class)->create();
        factory(OrganizationManager::class)->create([
            'organization_id' => $organization->id,
            'user_id' => $this->actingAs->id,
            'role_id' => Role::ORGANIZATION_ADMIN,
        ]);
        $this->setupRoute($organization->id);

        $data = [
            'latitude' => 'weg',
            'longitude' => 'weg',
        ];

        $response = $this->json('POST', $this->route, $data);

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