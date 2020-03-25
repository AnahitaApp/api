<?php
declare(strict_types=1);

namespace Tests\Integration\Policies;

use App\Models\Asset;
use App\Models\User\User;
use App\Policies\AssetPolicy;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;

/**
 * Class AssetPolicyTest
 * @package Tests\Integration\Policies
 */
class AssetPolicyTest extends TestCase
{
    use DatabaseSetupTrait;

    public function testAllFails()
    {
        $policy = new AssetPolicy();

        $loggedInUser = factory(User::class)->create();
        $requestedUser = factory(User::class)->create();

        $this->assertFalse($policy->all($loggedInUser, $requestedUser));
    }

    public function testAllPasses()
    {
        $policy = new AssetPolicy();

        $user = factory(User::class)->create();

        $this->assertTrue($policy->all($user, $user));
    }

    public function testCreateFails()
    {
        $policy = new AssetPolicy();

        $loggedInUser = factory(User::class)->create();
        $requestedUser = factory(User::class)->create();

        $this->assertFalse($policy->create($loggedInUser, $requestedUser));
    }

    public function testCreatePasses()
    {
        $policy = new AssetPolicy();

        $user = factory(User::class)->create();

        $this->assertTrue($policy->create($user, $user));
    }

    public function testUpdateFailsUserMismatch()
    {
        $policy = new AssetPolicy();

        $loggedInUser = factory(User::class)->create();
        $requestedUser = factory(User::class)->create();
        $asset = factory(Asset::class)->create([
            'user_id' => $loggedInUser->id,
        ]);

        $this->assertFalse($policy->update($loggedInUser, $requestedUser, $asset));
    }

    public function testUpdateFailsAssetMismatch()
    {
        $policy = new AssetPolicy();

        $user = factory(User::class)->create();
        $asset = factory(Asset::class)->create();

        $this->assertFalse($policy->update($user, $user, $asset));
    }

    public function testUpdatePasses()
    {
        $policy = new AssetPolicy();

        $user = factory(User::class)->create();
        $asset = factory(Asset::class)->create([
            'user_id' => $user->id,
        ]);

        $this->assertTrue($policy->update($user, $user, $asset));
    }

    public function testDeleteFailsUserMismatch()
    {
        $policy = new AssetPolicy();

        $loggedInUser = factory(User::class)->create();
        $requestedUser = factory(User::class)->create();
        $asset = factory(Asset::class)->create([
            'user_id' => $loggedInUser->id,
        ]);

        $this->assertFalse($policy->delete($loggedInUser, $requestedUser, $asset));
    }

    public function testDeleteFailsAssetMismatch()
    {
        $policy = new AssetPolicy();

        $user = factory(User::class)->create();
        $asset = factory(Asset::class)->create();

        $this->assertFalse($policy->delete($user, $user, $asset));
    }

    public function testDeletePasses()
    {
        $policy = new AssetPolicy();

        $user = factory(User::class)->create();
        $asset = factory(Asset::class)->create([
            'user_id' => $user->id,
        ]);

        $this->assertTrue($policy->delete($user, $user, $asset));
    }
}