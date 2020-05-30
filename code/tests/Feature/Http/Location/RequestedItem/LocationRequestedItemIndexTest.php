<?php
declare(strict_types=1);

namespace Tests\Feature\Http\Location\RequestedItem;

use App\Models\Organization\Location;
use App\Models\Request\RequestedItem;
use App\Models\Role;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;
use Tests\Traits\RolesTesting;

/**
 * Class OrganizationLocationIndexTest
 * @package Tests\Feature\Http\Location\RequestedItem
 */
class LocationRequestedItemIndexTest extends TestCase
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

    public function testNotLoggedInUserBlocked()
    {
        $location = factory(Location::class)->create();
        $this->setupRoute($location->id);
        $response = $this->json('GET', $this->route);
        $response->assertStatus(403);
    }

    public function testGetPaginationResult()
    {
        $this->actAs(Role::APP_USER);
        $location = factory(Location::class)->create();
        $this->setupRoute($location->id);
        factory(RequestedItem::class, 15)->create([
            'location_id' => $location->id,
        ]);
        factory(RequestedItem::class, 3)->create();

        // first page
        $response = $this->json('GET', $this->route);
        $response->assertJson([
            'total' => 15,
            'current_page' => 1,
            'per_page' => 10,
            'from' => 1,
            'to' => 10,
            'last_page' => 2
        ])
            ->assertJsonStructure([
                'data' => [
                    '*' =>  array_keys((new RequestedItem())->toArray())
                ]
            ]);
        $response->assertStatus(200);

        // second page
        $response = $this->json('GET', $this->route . '?page=2');
        $response->assertJson([
            'total' =>  15,
            'current_page' => 2,
            'per_page' => 10,
            'from' => 11,
            'to' => 15,
            'last_page' => 2
        ])
            ->assertJsonStructure([
                'data' => [
                    '*' =>  array_keys((new RequestedItem())->toArray())
                ]
            ]);
        $response->assertStatus(200);

        // page with limit
        $response = $this->json('GET', $this->route . '?page=2&limit=5');
        $response->assertJson([
            'total' =>  15,
            'current_page' => 2,
            'per_page' => 5,
            'from' => 6,
            'to' => 10,
            'last_page' => 3
        ])
            ->assertJsonStructure([
                'data' => [
                    '*' =>  array_keys((new RequestedItem())->toArray())
                ]
            ]);
        $response->assertStatus(200);
    }
}