<?php
declare(strict_types=1);

namespace Tests\Feature\User\Contact;

use App\Models\User\Contact;
use App\Models\User\User;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class UserContactIndexTest
 * @package Tests\Feature\User\Contact
 */
class UserContactIndexTest extends TestCase
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

        $response = $this->json('GET', $this->path . $user->id . '/contacts');

        $response->assertStatus(403);
    }

    public function testIncorrectUserBlocked()
    {
        $this->actAsUser();
        $user = factory(User::class)->create();

        $response = $this->json('GET', $this->path . $user->id . '/contacts');

        $response->assertStatus(403);
    }

    public function testUserNotFound()
    {
        $this->actAsUser();

        $response = $this->json('GET', $this->path . '12/contacts');

        $response->assertStatus(404);
    }

    public function testGetPaginationEmpty()
    {
        $this->actAsUser();

        $response = $this->json('GET', $this->path. $this->actingAs->id . '/contacts');

        $response->assertStatus(200);
        $response->assertJson([
            'total' => 0,
            'data' => []
        ]);
    }

    public function testGetPaginationResult()
    {
        $this->actAsUser();

        factory(Contact::class, 4)->create();
        factory(Contact::class, 10)->create([
            'requested_id' => $this->actingAs->id,
        ]);
        factory(Contact::class, 5)->create([
            'initiated_by_id' => $this->actingAs->id,
        ]);

        // first page
        $response = $this->json('GET', $this->path . $this->actingAs->id . '/contacts');
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
                    '*' =>  array_keys((new Contact())->toArray())
                ]
            ]);

        // second page
        $response = $this->json('GET', $this->path . $this->actingAs->id . '/contacts?page=2');
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
                    '*' =>  array_keys((new Contact())->toArray())
                ]
            ]);

        // page with limit
        $response = $this->json('GET', $this->path . $this->actingAs->id . '/contacts?page=2&limit=5');
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
                    '*' =>  array_keys((new Contact())->toArray())
                ]
            ]);
    }

    public function testGetPaginationWithExpands()
    {
        $this->actAsUser();

        factory(Contact::class, 4)->create();
        factory(Contact::class, 10)->create([
            'requested_id' => $this->actingAs->id,
        ]);
        factory(Contact::class, 5)->create([
            'initiated_by_id' => $this->actingAs->id,
        ]);

        // first page
        $response = $this->json('GET', $this->path . $this->actingAs->id . '/contacts?expand[initiatedBy]=*&expand[requested]=*');
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
                    '*' => array_keys((new Contact())->toArray())
                ]
            ]);
    }
}