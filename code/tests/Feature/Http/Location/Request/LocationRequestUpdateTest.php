<?php
declare(strict_types=1);

namespace Tests\Feature\Http\Location\Request;

use App\Models\Organization\Location;
use App\Models\Organization\OrganizationManager;
use App\Models\Request\Request;
use App\Models\Role;
use App\Models\User\User;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;
use Tests\Traits\RolesTesting;

/**
 * Class RequestUpdateTest
 * @package Tests\Feature\Http\Location\Request
 */
class LocationRequestUpdateTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog, RolesTesting;

    /**
     * @var string
     */
    private $path = '/v1/locations/';

    public function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();
    }

    public function testNotLoggedInUserBlocked()
    {
        $request = factory(Request::class)->create();
        $location = factory(Location::class)->create();
        $response = $this->json('PATCH', $this->path . $location->id . '/requests/' . $request->id);
        $response->assertStatus(403);
    }

    public function testNotRelatedLocationBlocked()
    {
        $this->actAs(Role::APP_USER);
        $location = factory(Location::class)->create();
        $request = factory(Request::class)->create();
        $response = $this->json('PATCH', $this->path . $location->id . '/requests/' . $request->id);
        $response->assertStatus(403);
    }

    public function testNotPartOfOrganizationUserBlocked()
    {
        $this->actAs(Role::APP_USER);
        $location = factory(Location::class)->create();
        $request = factory(Request::class)->create([
            'location_id' => $location->id,
        ]);
        $response = $this->json('PATCH', $this->path . $location->id . '/requests/' . $request->id);
        $response->assertStatus(403);
    }

    public function testPatchCompletedBySuccessful()
    {
        $this->actAs(Role::APP_USER);

        $location = factory(Location::class)->create();
        /** @var Request $request */
        $request = factory(Request::class)->create([
            'location_id' => $location->id,
        ]);

        factory(OrganizationManager::class)->create([
            'user_id' => $this->actingAs->id,
            'organization_id' => $location->organization_id,
        ]);

        $user = factory(User::class)->create();

        factory(OrganizationManager::class)->create([
            'user_id' => $user->id,
            'organization_id' => $location->organization_id,
        ]);

        $response = $this->json('PATCH', $this->path . $location->id . '/requests/' . $request->id, [
            'completed_by_id' => $user->id,
        ]);
        $response->assertStatus(200);

        /** @var Request $updated */
        $updated = Request::find($request->id);

        $this->assertEquals($updated->completed_by_id, $user->id);
    }

    public function testPatchNotFoundFails()
    {
        $this->actAs(Role::APP_USER);

        $location = factory(Location::class)->create();

        factory(OrganizationManager::class)->create([
            'user_id' => $this->actingAs->id,
            'organization_id' => $location->organization_id,
        ]);

        $response = $this->json('PATCH', $this->path . $location->id . '/requests/5')
            ->assertExactJson([
                'message'   =>  'This item was not found.'
            ]);
        $response->assertStatus(404);
    }

    public function testPatchInvalidIdFails()
    {
        $this->actAs(Role::APP_USER);

        $location = factory(Location::class)->create();
        /** @var Request $request */
        $request = factory(Request::class)->create([
            'location_id' => $location->id,
        ]);

        factory(OrganizationManager::class)->create([
            'user_id' => $this->actingAs->id,
            'organization_id' => $location->organization_id,
        ]);

        $response = $this->json('PATCH', $this->path . $location->id . '/requests/' . $request->id . '/b')
            ->assertExactJson([
                'message'   => 'This path was not found.',
            ]);
        $response->assertStatus(404);
    }
}