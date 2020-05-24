<?php
declare(strict_types=1);

namespace Tests\Feature\User\Asset;

use App\Models\Asset;
use App\Models\User\User;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class UserContactIndexTest
 * @package Tests\Feature\User\Asset
 */
class UserAssetIndexTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var string
     */
    private $path = '/v1/users/';

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();
        User::unsetEventDispatcher();
    }

    public function testNotLoggedInUserBlocked()
    {
        $user = factory(User::class)->create();

        $response = $this->json('GET', $this->path . $user->id . '/assets');

        $response->assertStatus(403);
    }

    public function testIncorrectUserBlocked()
    {
        $this->actAsUser();
        $user = factory(User::class)->create();

        $response = $this->json('GET', $this->path . $user->id . '/assets');

        $response->assertStatus(403);
    }

    public function testUserNotFound()
    {
        $this->actAsUser();

        $response = $this->json('GET', $this->path . '12/assets');

        $response->assertStatus(404);
    }

    public function testGetPaginationEmpty()
    {
        $this->actAsUser();

        $response = $this->json('GET', $this->path. $this->actingAs->id . '/assets');

        $response->assertStatus(200);
        $response->assertJson([
            'total' => 0,
            'data' => []
        ]);
    }

    public function testGetPaginationResult()
    {
        $this->actAsUser();

        factory(Asset::class, 6)->create();
        factory(Asset::class, 15)->create([
            'owner_id' => $this->actingAs->id,
            'owner_type' => 'user',
        ]);
        factory(Asset::class, 3)->create([
            'owner_id' => $this->actingAs->id,
            'owner_type' => 'organization',
        ]);

        // first page
        $response = $this->json('GET', $this->path . $this->actingAs->id . '/assets');
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
                    '*' =>  array_keys((new Asset())->toArray())
                ]
            ]);

        // second page
        $response = $this->json('GET', $this->path . $this->actingAs->id . '/assets?page=2');
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
                    '*' =>  array_keys((new Asset())->toArray())
                ]
            ]);

        // page with limit
        $response = $this->json('GET', $this->path . $this->actingAs->id . '/assets?page=2&limit=5');
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
                    '*' =>  array_keys((new Asset())->toArray())
                ]
            ]);
    }
}