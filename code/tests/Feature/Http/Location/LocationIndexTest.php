<?php
declare(strict_types=1);

namespace Tests\Feature\Http\Location;

use App\Models\Organization\Location;
use App\Models\Role;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;
use Tests\Traits\RolesTesting;

/**
 * Class LocationIndexTest
 * @package Tests\Feature\Http\Location
 */
class LocationIndexTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog, RolesTesting;

    /**
     * @var string
     */
    private $route = '/v1/locations';

    public function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();
    }

    public function testNotLoggedInUserBlocked()
    {
        $response = $this->json('GET', $this->route);
        $response->assertStatus(403);
    }

    public function testGetPaginationResult()
    {
        $this->actAs(Role::APP_USER);
        factory(Location::class, 15)->create();

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
                    '*' =>  array_keys((new Location())->toArray())
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
                    '*' =>  array_keys((new Location())->toArray())
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
                    '*' =>  array_keys((new Location())->toArray())
                ]
            ]);
        $response->assertStatus(200);
    }

    public function testGetPaginationWithLocation()
    {
        $this->actAs(Role::APP_USER);
        $offCenter = factory(Location::class)->create([
            'latitude' => 10.5,
            'longitude' => 10,
        ]);
        $center = factory(Location::class)->create([
            'latitude' => 10,
            'longitude' => 10,
        ]);
        $furthest = factory(Location::class)->create([
            'latitude' => 10,
            'longitude' => 7,
        ]);
        // This is roughly 2.1 in latitude units
        $middle = factory(Location::class)->create([
            'latitude' => 11.5,
            'longitude' => 11.5,
        ]);
        $north = factory(Location::class)->create([
            'latitude' => 12.5,
            'longitude' => 10
        ]);
        factory(Location::class)->create([
            'latitude' => 102.5,
            'longitude' => 10
        ]);

        // first page
        $response = $this->json('GET', '/v1/locations?radius=500&latitude=10&longitude=10');
        $response->assertStatus(200);
        $response->assertJson([
            'total' => 5,
            'current_page' => 1,
            'per_page' => 10,
            'from' => 1,
            'to' => 5,
            'last_page' => 1
        ])
            ->assertJsonStructure([
                'data' => [
                    '*' =>  array_keys((new Location())->toArray())
                ]
            ]);
        $this->assertEquals([
            $center->id,
            $offCenter->id,
            $middle->id,
            $north->id,
            $furthest->id,
        ], $response->original->pluck('id')->toArray());
    }
}