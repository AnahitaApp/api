<?php
declare(strict_types=1);

namespace Tests\Feature\Http\Location\RequestedItem;

use App\Models\Asset;
use App\Models\Organization\Location;
use App\Models\Organization\OrganizationManager;
use App\Models\Role;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;
use Tests\Traits\RolesTesting;

/**
 * Class OrganizationLocationCreateTest
 * @package Tests\Feature\Http\Location\RequestedItem
 */
class LocationRequestedItemCreateTest extends TestCase
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
     * @param int $locationId
     */
    private function setupRoute(int $locationId)
    {
        $this->route = '/v1/locations/' . $locationId . '/requested-items';
    }

    public function testOrganizationNotFound()
    {
        $this->setupRoute(4523);
        $response = $this->json('POST', $this->route);
        $response->assertStatus(404);
    }

    public function testNotLoggedInUserBlocked()
    {
        $location = factory(Location::class)->create();
        $this->setupRoute($location->id);
        $response = $this->json('POST', $this->route);
        $response->assertStatus(403);
    }

    public function testNonAdminUsersBlocked()
    {
        foreach ($this->rolesWithoutAdmins() as $role) {
            $this->actAs($role);
            $location = factory(Location::class)->create();
            $this->setupRoute($location->id);
            $response = $this->json('POST', $this->route);

            $response->assertStatus(403);
        }
    }

    public function testNotUserNotOrganizationAdminBlocked()
    {
        $this->actAs(Role::MANAGER);
        $location = factory(Location::class)->create();
        $this->setupRoute($location->id);
        factory(OrganizationManager::class)->create([
            'user_id' => $this->actingAs->id,
            'role_id' => Role::MANAGER,
        ]);
        $response = $this->json('POST', $this->route);
        $response->assertStatus(403);
    }

    public function testCreateSuccessful()
    {
        $this->actAs(Role::ADMINISTRATOR);
        $location = factory(Location::class)->create();
        $this->setupRoute($location->id);
        factory(OrganizationManager::class)->create([
            'organization_id' => $location->organization_id,
            'user_id' => $this->actingAs->id,
            'role_id' => Role::ADMINISTRATOR,
        ]);

        $properties = [
            'name' => 'An Item',
            'asset_id' => factory(Asset::class)->create()->id,
            'quantity' => 200,
            'max_quantity_per_request' => 2,
        ];

        $response = $this->json('POST', $this->route, $properties);

        $response->assertStatus(201);

        $response->assertJson($properties);
    }

    public function testCreateFailsMissingRequiredFields()
    {
        $this->actAs(Role::ADMINISTRATOR);
        $location = factory(Location::class)->create();
        $this->setupRoute($location->id);
        factory(OrganizationManager::class)->create([
            'organization_id' => $location->organization_id,
            'user_id' => $this->actingAs->id,
            'role_id' => Role::MANAGER,
        ]);

        $response = $this->json('POST', $this->route);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'name' => ['The name field is required.'],
                'asset_id' => ['The asset id field is required.'],
            ]
        ]);
    }

    public function testCreateFailsInvalidStringFields()
    {
        $this->actAs(Role::ADMINISTRATOR);
        $location = factory(Location::class)->create();
        $this->setupRoute($location->id);
        factory(OrganizationManager::class)->create([
            'organization_id' => $location->organization_id,
            'user_id' => $this->actingAs->id,
            'role_id' => Role::MANAGER,
        ]);

        $data = [
            'name' => 3124,
        ];

        $response = $this->json('POST', $this->route, $data);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'name' => ['The name must be a string.'],
            ]
        ]);
    }

    public function testCreateFailsInvalidNumericFields()
    {
        $this->actAs(Role::ADMINISTRATOR);
        $location = factory(Location::class)->create();
        $this->setupRoute($location->id);
        factory(OrganizationManager::class)->create([
            'organization_id' => $location->organization_id,
            'user_id' => $this->actingAs->id,
            'role_id' => Role::MANAGER,
        ]);

        $data = [
            'asset_id' => 'hi',
            'quantity' => 'hi',
            'max_quantity_per_request' => 'hi',
        ];

        $response = $this->json('POST', $this->route, $data);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'asset_id' => ['The asset id must be a number.'],
                'quantity' => ['The quantity must be a number.'],
                'max_quantity_per_request' => ['The max quantity per request must be a number.'],
            ]
        ]);
    }

    public function testCreateFailsInvalidModelFields()
    {
        $this->actAs(Role::ADMINISTRATOR);
        $location = factory(Location::class)->create();
        $this->setupRoute($location->id);
        factory(OrganizationManager::class)->create([
            'organization_id' => $location->organization_id,
            'user_id' => $this->actingAs->id,
            'role_id' => Role::MANAGER,
        ]);

        $data = [
            'asset_id' => 3124,
        ];

        $response = $this->json('POST', $this->route, $data);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'asset_id' => ['The selected asset id is invalid.'],
            ]
        ]);
    }
}