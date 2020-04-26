<?php
declare(strict_types=1);

namespace Tests\Feature\Http\Request;

use App\Models\Request\Request;
use App\Models\Role;
use App\Models\User\User;
use Carbon\Carbon;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;
use Tests\Traits\RolesTesting;

/**
 * Class RequestUpdateTest
 * @package Tests\Feature\Http\Request
 */
class RequestUpdateTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog, RolesTesting;
    
    const BASE_ROUTE = '/v1/requests/';

    public function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();
    }

    public function testNotLoggedInUserBlocked()
    {
        $request = factory(Request::class)->create();
        $response = $this->json('PATCH', static::BASE_ROUTE . $request->id);
        $response->assertStatus(403);
    }

    public function testNotRelatedUserBlocked()
    {
        $this->actAs(Role::APP_USER);
        $request = factory(Request::class)->create([
            'completed_by_id' => factory(User::class)->create()->id,
        ]);
        $response = $this->json('PATCH', static::BASE_ROUTE . $request->id);
        $response->assertStatus(403);
    }

    public function testPatchAcceptSuccessful()
    {
        $this->actAs(Role::APP_USER);

        /** @var Request $request */
        $request = factory(Request::class)->create();

        $response = $this->json('PATCH', static::BASE_ROUTE . $request->id, [
            'accept' => true,
        ]);
        $response->assertStatus(200);

        /** @var Request $updated */
        $updated = Request::find($request->id);

        $this->assertEquals($this->actingAs->id, $updated->completed_by_id);
    }

    public function testPatchCompletedSuccessful()
    {
        $this->actAs(Role::APP_USER);

        /** @var Request $request */
        $request = factory(Request::class)->create([
            'completed_by_id' => $this->actingAs->id,
        ]);

        $response = $this->json('PATCH', static::BASE_ROUTE . $request->id, [
            'completed' => true,
        ]);
        $response->assertStatus(200);


        /** @var Request $updated */
        $updated = Request::find($request->id);
        $this->assertNotNull($updated->completed_at);
    }

    public function testPatchNotFoundFails()
    {
        $this->actAs(Role::APP_USER);

        $response = $this->json('PATCH', static::BASE_ROUTE . '5')
            ->assertExactJson([
                'message'   =>  'This item was not found.'
            ]);
        $response->assertStatus(404);
    }

    public function testPatchInvalidIdFails()
    {
        $this->actAs(Role::APP_USER);

        $response = $this->json('PATCH', static::BASE_ROUTE . '/b')
            ->assertExactJson([
                'message'   => 'This path was not found.',
            ]);
        $response->assertStatus(404);
    }

    public function testPatchFailsInvalidBooleanFields()
    {
        $this->actAs(Role::APP_USER);

        /** @var Request $request */
        $request = factory(Request::class)->create([
            'completed_by_id' => $this->actingAs->id,
        ]);

        $response = $this->json('PATCH', static::BASE_ROUTE . $request->id, [
            'accept' => 'hi',
            'completed' => 'hi',
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'accept' => ['The accept field must be true or false.'],
                'completed' => ['The completed field must be true or false.'],
            ]
        ]);
    }

    public function testPatchFailsRequestAlreadyAccepted()
    {
        $this->actAs(Role::APP_USER);

        /** @var Request $request */
        $request = factory(Request::class)->create([
            'completed_by_id' => $this->actingAs->id,
        ]);

        $response = $this->json('PATCH', static::BASE_ROUTE . $request->id, [
            'accept' => true,
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'accept' => ['This request has already been accepted.'],
            ]
        ]);
    }

    public function testPatchFailsRequestCanceled()
    {
        $this->actAs(Role::APP_USER);

        /** @var Request $request */
        $request = factory(Request::class)->create([
            'canceled_at' => Carbon::now(),
        ]);

        $response = $this->json('PATCH', static::BASE_ROUTE . $request->id, [
            'accept' => true,
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'accept' => ['This request has been canceled.'],
            ]
        ]);
    }
}