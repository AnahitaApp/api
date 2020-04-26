<?php
declare(strict_types=1);

namespace Tests\Feature\Http\Request\User;

use App\Models\Request\Request;
use App\Models\Role;
use App\Models\User\User;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;
use Tests\Traits\RolesTesting;

/**
 * Class RequestUpdateTest
 * @package Tests\Feature\Http\Request
 */
class UserRequestUpdateTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog, RolesTesting;

    /**
     * @var string
     */
    private $path = '/v1/users/';

    public function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();
    }

    public function testNotLoggedInUserBlocked()
    {
        $request = factory(Request::class)->create();
        $user = factory(User::class)->create();
        $response = $this->json('PATCH', $this->path . $user->id . '/requests/' . $request->id);
        $response->assertStatus(403);
    }

    public function testNotRelatedUserBlocked()
    {
        $this->actAs(Role::APP_USER);
        $request = factory(Request::class)->create([
            'completed_by_id' => factory(User::class)->create()->id,
        ]);
        $response = $this->json('PATCH', $this->path . $this->actingAs->id . '/requests/' . $request->id);
        $response->assertStatus(403);
    }

    public function testPatchCancelSuccessful()
    {
        $this->actAs(Role::APP_USER);

        /** @var Request $request */
        $request = factory(Request::class)->create([
            'requested_by_id' => $this->actingAs->id,
        ]);

        $response = $this->json('PATCH', $this->path . $this->actingAs->id . '/requests/' . $request->id, [
            'cancel' => true,
        ]);
        $response->assertStatus(200);

        /** @var Request $updated */
        $updated = Request::find($request->id);

        $this->assertNotNull($updated->canceled_at);
    }

    public function testPatchNotFoundFails()
    {
        $this->actAs(Role::APP_USER);

        /** @var Request $request */
        $request = factory(Request::class)->create([
            'requested_by_id' => $this->actingAs->id,
        ]);

        $response = $this->json('PATCH', $this->path . $this->actingAs->id . '/requests/' . $request->id. '5')
            ->assertExactJson([
                'message'   =>  'This item was not found.'
            ]);
        $response->assertStatus(404);
    }

    public function testPatchInvalidIdFails()
    {
        $this->actAs(Role::APP_USER);

        /** @var Request $request */
        $request = factory(Request::class)->create([
            'requested_by_id' => $this->actingAs->id,
        ]);

        $response = $this->json('PATCH', $this->path . $this->actingAs->id . '/requests/' . $request->id . '/b')
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
            'requested_by_id' => $this->actingAs->id,
        ]);

        $response = $this->json('PATCH', $this->path . $this->actingAs->id . '/requests/' . $request->id, [
            'cancel' => 'hi',
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'cancel' => ['The cancel field must be true or false.'],
            ]
        ]);
    }
}