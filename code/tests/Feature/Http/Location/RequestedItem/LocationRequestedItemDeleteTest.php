<?php
declare(strict_types=1);

namespace Tests\Feature\Http\Location\RequestedItem;

use App\Models\Organization\Location;
use App\Models\Organization\OrganizationManager;
use App\Models\Request\RequestedItem;
use App\Models\Role;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;
use Tests\Traits\RolesTesting;

/**
 * Class OrganizationLocationDeleteTest
 * @package Tests\Feature\Http\Location\RequestedItem
 */
class LocationRequestedItemDeleteTest extends TestCase
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
     * @param int $requestedItemId
     */
    private function setupRoute(int $locationId, $requestedItemId)
    {
        $this->route = '/v1/locations/' . $locationId . '/requested-items/' . $requestedItemId;
    }

    public function testNotLoggedInUserBlocked()
    {
        $model = factory(RequestedItem::class)->create([
            'location_id' => factory(Location::class)->create()->id,
        ]);
        $this->setupRoute($model->location_id, $model->id);
        $response = $this->json('DELETE', $this->route);
        $response->assertStatus(403);
    }

    public function testNonAdminUserBlocked()
    {
        foreach ($this->rolesWithoutAdmins() as $role) {
            $this->actAs($role);
            $model = factory(RequestedItem::class)->create([
                'location_id' => factory(Location::class)->create()->id,
            ]);
            $this->setupRoute($model->location_id, $model->id);
            $response = $this->json('DELETE', $this->route);
            $response->assertStatus(403);
        }
    }

    public function testDeleteFailsIncorrectOrganizationManager()
    {
        $this->actAs(Role::ADMINISTRATOR);
        factory(OrganizationManager::class)->create([
            'role_id' => Role::ADMINISTRATOR,
            'user_id' => $this->actingAs->id,
        ]);
        $model = factory(RequestedItem::class)->create([
            'location_id' => factory(Location::class)->create()->id,
        ]);
        $this->setupRoute($model->location_id, $model->id);

        $response = $this->json('DELETE', $this->route);

        $response->assertStatus(403);
    }

    public function testDeleteSingle()
    {
        $this->actAs(Role::ADMINISTRATOR);
        $organizationManager = factory(OrganizationManager::class)->create([
            'role_id' => Role::ADMINISTRATOR,
            'user_id' => $this->actingAs->id,
        ]);
        $model = factory(RequestedItem::class)->create([
            'location_id' => factory(Location::class)->create([
                'organization_id' => $organizationManager->organization_id
            ])->id,
        ]);
        $this->setupRoute($model->location_id, $model->id);

        $response = $this->json('DELETE', $this->route);

        $response->assertStatus(204);
        $this->assertNull(RequestedItem::find($model->id));
    }

    public function testDeleteSingleInvalidIdFails()
    {
        $this->actAs(Role::SUPER_ADMIN);

        $this->setupRoute(23, 'a');
        $response = $this->json('DELETE', $this->route)
            ->assertExactJson([
                'message'   => 'This path was not found.',
            ]);
        $response->assertStatus(404);
    }

    public function testDeleteSingleNotFoundFails()
    {
        $this->actAs(Role::SUPER_ADMIN);

        $this->setupRoute(23, '435');
        $response = $this->json('DELETE', $this->route)
            ->assertExactJson([
                'message'   =>  'This item was not found.'
            ]);
        $response->assertStatus(404);
    }
}