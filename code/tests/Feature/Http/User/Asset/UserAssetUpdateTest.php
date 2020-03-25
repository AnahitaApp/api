<?php
declare(strict_types=1);

namespace Tests\Feature\Http\User\Asset;

use App\Models\Asset;
use App\Models\Role;
use App\Models\User\User;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class UserAssetUpdateTest
 * @package Tests\Feature\Http\User\Asset
 */
class UserAssetUpdateTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var string
     */
    private $path = '/v1/users/';

    /**
     * @var User
     */
    private $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();

        $this->user = factory(User::class)->create();

        $this->path.= $this->user->id . '/assets/';
    }

    public function testNotLoggedInUserBlocked()
    {
        $asset = factory(Asset::class)->create([
            'user_id' => $this->user->id,
        ]);
        $response = $this->json('PATCH', $this->path . $asset->id);

        $response->assertStatus(403);
    }

    public function testDifferentUserThanRouteBlocked()
    {
        $this->actAs(Role::APP_USER);
        $asset = factory(Asset::class)->create([
            'user_id' => $this->user->id,
        ]);
        $response = $this->json('PATCH', $this->path . $asset->id);

        $response->assertStatus(403);
    }

    public function testDifferentUserThanAssetBlocked()
    {
        $this->actingAs($this->user);
        $asset = factory(Asset::class)->create();
        $response = $this->json('PATCH', $this->path . $asset->id);

        $response->assertStatus(403);
    }

    public function testUpdateSuccessful()
    {
        $this->actingAs($this->user);
        $asset = factory(Asset::class)->create([
            'user_id' => $this->user->id,
            'name' => 'A Name',
        ]);
        $response = $this->json('PATCH', $this->path . $asset->id, [
            'name' => 'A New Name',
        ]);

        $response->assertStatus(200);
        /** @var Asset $updated */
        $updated = Asset::find($asset->id);
        $this->assertEquals('A New Name', $updated->name);
    }

    public function testFailsNotPresentFieldsPresent()
    {
        $this->actingAs($this->user);
        $asset = factory(Asset::class)->create([
            'user_id' => $this->user->id,
            'name' => 'A Name',
        ]);
        $response = $this->json('PATCH', $this->path . $asset->id, [
            'file_contents' => 'regoijer',
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'errors' => [
                'file_contents' => ['The file contents field is not allowed or can not be set for this request.'],
            ],
        ]);
    }

    public function testFailsInvalidStringFields()
    {
        $this->actingAs($this->user);
        $asset = factory(Asset::class)->create([
            'user_id' => $this->user->id,
        ]);
        $response = $this->json('PATCH', $this->path . $asset->id, [
            'name' => 45,
            'caption' => 45,
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'errors' => [
                'name' => ['The name must be a string.'],
                'caption' => ['The caption must be a string.'],
            ],
        ]);
    }
}