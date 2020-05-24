<?php
declare(strict_types=1);

namespace Tests\Feature\Http\User\Asset;

use App\Models\Asset;
use App\Models\User\User;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class UserPaymentMethodDeleteTest
 * @package Tests\Feature\Http\User\Asset
 */
class UserAssetDeleteTest extends TestCase
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
            'owner_id' => $this->user->id,
            'owner_type' => 'user',
        ]);
        $response = $this->json('DELETE', $this->path . $asset->id);

        $response->assertStatus(403);
    }

    public function testIncorrectUserBlocked()
    {
        $asset = factory(Asset::class)->create([
            'owner_id' => $this->user->id,
            'owner_type' => 'user',
        ]);

        $this->actAsUser();

        $response = $this->json('DELETE', $this->path . $asset->id);

        $response->assertStatus(403);
    }

    public function testUserDoesNotOwnPaymentMethodBlocked()
    {
        $asset = factory(Asset::class)->create();

        $this->actingAs($this->user);

        $response = $this->json('DELETE', $this->path . $asset->id);

        $response->assertStatus(403);
    }

    public function testDeleteSuccessful()
    {
        $asset = factory(Asset::class)->create([
            'owner_id' => $this->user->id,
            'owner_type' => 'user',
        ]);

        $this->actingAs($this->user);

        $response = $this->json('DELETE', $this->path . $asset->id);

        $response->assertStatus(204);

        $this->assertCount(0, Asset::all());
    }
}