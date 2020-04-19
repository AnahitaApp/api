<?php
declare(strict_types=1);

namespace Tests\Feature\Http\Request;

use App\Models\Request\Request;
use App\Models\Role;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class RequestViewTest
 * @package Tests\Feature\Http\Request
 */
class RequestViewTest extends TestCase
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
        $model = factory(Request::class)->create();
        $response = $this->json('GET', '/v1/requests/' . $model->id);
        $response->assertStatus(403);
    }

    public function testBlockedWhenUserIsNotInRequest()
    {
        $this->actAs(Role::APP_USER);
        /** @var Request $model */
        $model = factory(Request::class)->create();

        $response = $this->json('GET', '/v1/requests/' . $model->id);
        $response->assertStatus(403);
    }

    public function testGetSingleSuccessForRequestedUser()
    {
        $this->actAs(Role::APP_USER);
        /** @var Request $model */
        $model = factory(Request::class)->create([
            'requested_by_id' => $this->actingAs->id,
        ]);

        $response = $this->json('GET', '/v1/requests/' . $model->id);

        $response->assertStatus(200);
        $response->assertJson($model->toArray());
    }

    public function testGetSingleSuccessForCompletingUser()
    {
        $this->actAs(Role::APP_USER);
        /** @var Request $model */
        $model = factory(Request::class)->create([
            'completed_by_id' => $this->actingAs->id,
        ]);

        $response = $this->json('GET', '/v1/requests/' . $model->id);

        $response->assertStatus(200);
        $response->assertJson($model->toArray());
    }

    public function testGetSingleNotFoundFails()
    {
        $this->actAs(Role::APP_USER);
        $response = $this->json('GET', '/v1/requests/1')
            ->assertExactJson([
                'message'   =>  'This item was not found.'
            ]);
        $response->assertStatus(404);
    }

    public function testGetSingleInvalidIdFails()
    {
        $this->actAs(Role::APP_USER);
        $response = $this->json('GET', '/v1/requests/a')
            ->assertExactJson([
                'message'   => 'This path was not found.'
            ]);
        $response->assertStatus(404);
    }
}