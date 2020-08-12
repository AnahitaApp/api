<?php
declare(strict_types=1);

namespace Tests\Feature\Http\Location;

use App\Models\Organization\Location;
use App\Models\Role;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class LocationViewTest
 * @package Tests\Feature\Http\Location
 */
class LocationViewTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    public function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();
    }

    public function testNotLoggedInUserBlocked()
    {
        $model = factory(Location::class)->create();
        $response = $this->json('GET', '/v1/locations/' . $model->id);
        $response->assertStatus(403);
    }

    public function testGetSingleSuccess()
    {
        $this->actAs(Role::APP_USER);
        /** @var Location $model */
        $model = factory(Location::class)->create([
            'id'    =>  1,
        ]);

        $response = $this->json('GET', '/v1/locations/1');

        $response->assertStatus(200);
        $response->assertJson($model->toArray());
    }

    public function testGetSingleNotFoundFails()
    {
        $this->actAs(Role::APP_USER);
        $response = $this->json('GET', '/v1/locations/1')
            ->assertExactJson([
                'message'   =>  'This item was not found.'
            ]);
        $response->assertStatus(404);
    }

    public function testGetSingleInvalidIdFails()
    {
        $this->actAs(Role::APP_USER);
        $response = $this->json('GET', '/v1/locations/a')
            ->assertExactJson([
                'message'   => 'This path was not found.'
            ]);
        $response->assertStatus(404);
    }
}
