<?php
declare(strict_types=1);

namespace Tests\Feature\User\Request;

use App\Models\Request\Request;
use App\Models\User\User;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class UserRequestIndexTest
 * @package Tests\Feature\User\Asset
 */
class UserRequestIndexTest extends TestCase
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
    }

    public function testNotLoggedInUserBlocked()
    {
        $user = factory(User::class)->create();

        $response = $this->json('GET', $this->path . $user->id . '/requests');

        $response->assertStatus(403);
    }

    public function testIncorrectUserBlocked()
    {
        $this->actAsUser();
        $user = factory(User::class)->create();

        $response = $this->json('GET', $this->path . $user->id . '/requests');

        $response->assertStatus(403);
    }

    public function testUserNotFound()
    {
        $this->actAsUser();

        $response = $this->json('GET', $this->path . '12/requests');

        $response->assertStatus(404);
    }

    public function testGetPaginationEmpty()
    {
        $this->actAsUser();

        $response = $this->json('GET', $this->path. $this->actingAs->id . '/requests');

        $response->assertStatus(200);
        $response->assertJson([
            'total' => 0,
            'data' => []
        ]);
    }

    public function testGetPaginationResult()
    {
        $this->actAsUser();

        factory(Request::class, 6)->create();
        factory(Request::class, 15)->create([
            'requested_by_id' => $this->actingAs->id,
        ]);

        // first page
        $response = $this->json('GET', $this->path . $this->actingAs->id . '/requests');
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
        $response = $this->json('GET', $this->path . $this->actingAs->id . '/requests?page=2');
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
        $response = $this->json('GET', $this->path . $this->actingAs->id . '/requests?page=2&limit=5');
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
}