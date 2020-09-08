<?php
declare(strict_types=1);

namespace Tests\Feature\Http\Location\Request;

use App\Models\Organization\Location;
use App\Models\Organization\OrganizationManager;
use App\Models\Request\Request;
use App\Models\User\User;
use Carbon\Carbon;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class UserRequestIndexTest
 * @package Tests\Feature\User\Asset
 */
class LocationRequestIndexTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var string
     */
    private $path = '/v1/locations/';

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();
    }

    public function testNotLoggedInUserBlocked()
    {
        $location = factory(Location::class)->create();

        $response = $this->json('GET', $this->path . $location->id . '/requests');

        $response->assertStatus(403);
    }

    public function testIncorrectUserBlocked()
    {
        $this->actAsUser();
        $location = factory(Location::class)->create();

        $response = $this->json('GET', $this->path . $location->id . '/requests');

        $response->assertStatus(403);
    }

    public function testNotFound()
    {
        $this->actAsUser();

        $response = $this->json('GET', $this->path . '12/requests');

        $response->assertStatus(404);
    }

    public function testGetPaginationEmpty()
    {
        $this->actAsUser();

        $location = factory(Location::class)->create();

        factory(OrganizationManager::class)->create([
            'user_id' => $this->actingAs->id,
            'organization_id' => $location->organization_id,
        ]);

        $response = $this->json('GET', $this->path. $location->id . '/requests');

        $response->assertStatus(200);
        $response->assertJson([
            'total' => 0,
            'data' => []
        ]);
    }

    public function testGetPaginationResult()
    {
        $this->actAsUser();

        $location = factory(Location::class)->create();

        factory(OrganizationManager::class)->create([
            'user_id' => $this->actingAs->id,
            'organization_id' => $location->organization_id,
        ]);

        factory(Request::class, 6)->create();
        factory(Request::class, 15)->create([
            'location_id' => $location->id,
        ]);

        // first page
        $response = $this->json('GET', $this->path . $location->id . '/requests');
        $response->assertStatus(200);
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
                    '*' =>  array_keys((new Request())->toArray())
                ]
            ]);

        // second page
        $response = $this->json('GET', $this->path . $location->id . '/requests?page=2');
        $response->assertStatus(200);
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
                    '*' =>  array_keys((new Request())->toArray())
                ]
            ]);

        // page with limit
        $response = $this->json('GET', $this->path . $location->id . '/requests?page=2&limit=5');
        $response->assertStatus(200);
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
                    '*' =>  array_keys((new Request())->toArray())
                ]
            ]);
    }

    public function testGetPaginationMobileAppRequest()
    {
        $this->actAsUser();

        $location = factory(Location::class)->create();

        factory(OrganizationManager::class)->create([
            'user_id' => $this->actingAs->id,
            'organization_id' => $location->organization_id,
        ]);

        factory(Request::class, 6)->create();
        factory(Request::class, 15)->create([
            'location_id' => $location->id,
        ]);
        factory(Request::class, 3)->create([
            'location_id' => $location->id,
            'canceled_at' => Carbon::now(),
        ]);
        factory(Request::class, 4)->create([
            'location_id' => $location->id,
            'completed_by_id' => factory(User::class)->create()->id,
        ]);

        $response = $this->json('GET', $this->path . $location->id . '/requests?order[created_at]=ASC&expand[requestedBy]=*&expand[requestedItems]=*&filter[completed_by_id]=null&filter[canceled_at]=null');
        $response->assertStatus(200);
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
                    '*' =>  array_keys((new Request())->toArray())
                ]
            ]);
    }
}
