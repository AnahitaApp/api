<?php
declare(strict_types=1);

namespace Tests\Integration\Policies\Request;

use App\Models\Organization\Location;
use App\Models\Organization\OrganizationManager;
use App\Models\Request\RequestedItem;
use App\Models\User\User;
use App\Policies\Request\RequestedItemPolicy;
use Tests\TestCase;

/**
 * Class RequestedItemPolicyTest
 * @package Tests\Integration\Policies\Request
 */
class RequestedItemPolicyTest extends TestCase
{
    public function testAll()
    {
        $policy = new RequestedItemPolicy();

        $this->assertTrue($policy->all(new User(), new Location()));
    }

    public function testCreateBlocks()
    {
        $policy = new RequestedItemPolicy();

        $user = factory(User::class)->create();
        $location = factory(Location::class)->create();

        $this->assertFalse($policy->create($user, $location));
    }

    public function testCreatePasses()
    {
        $policy = new RequestedItemPolicy();

        $user = factory(User::class)->create();
        $location = factory(Location::class)->create();

        factory(OrganizationManager::class)->create([
            'organization_id' => $location->organization_id,
            'user_id' => $user->id,
        ]);

        $this->assertTrue($policy->create($user, $location));
    }

    public function testUpdateBlocksNotOrganizationManager()
    {
        $policy = new RequestedItemPolicy();

        $user = factory(User::class)->create();
        $location = factory(Location::class)->create();
        $requestedItem = factory(RequestedItem::class)->create([
            'location_id' => $location->id,
        ]);

        factory(OrganizationManager::class)->create([
            'user_id' => $user->id,
        ]);

        $this->assertFalse($policy->update($user, $location, $requestedItem));
    }

    public function testUpdateBlocksUnrelatedRequestedItem()
    {
        $policy = new RequestedItemPolicy();

        $user = factory(User::class)->create();
        $location = factory(Location::class)->create();
        $requestedItem = factory(RequestedItem::class)->create();

        factory(OrganizationManager::class)->create([
            'organization_id' => $location->organization_id,
            'user_id' => $user->id,
        ]);

        $this->assertFalse($policy->update($user, $location, $requestedItem));
    }

    public function testUpdatePasses()
    {
        $policy = new RequestedItemPolicy();

        $user = factory(User::class)->create();
        $location = factory(Location::class)->create();
        $requestedItem = factory(RequestedItem::class)->create([
            'location_id' => $location->id,
        ]);

        factory(OrganizationManager::class)->create([
            'organization_id' => $location->organization_id,
            'user_id' => $user->id,
        ]);

        $this->assertTrue($policy->update($user, $location, $requestedItem));
    }

    public function testDeleteBlocksNotOrganizationManager()
    {
        $policy = new RequestedItemPolicy();

        $user = factory(User::class)->create();
        $location = factory(Location::class)->create();
        $requestedItem = factory(RequestedItem::class)->create([
            'location_id' => $location->id,
        ]);

        factory(OrganizationManager::class)->create([
            'user_id' => $user->id,
        ]);

        $this->assertFalse($policy->delete($user, $location, $requestedItem));
    }

    public function testDeleteBlocksUnrelatedRequestedItem()
    {
        $policy = new RequestedItemPolicy();

        $user = factory(User::class)->create();
        $location = factory(Location::class)->create();
        $requestedItem = factory(RequestedItem::class)->create();

        factory(OrganizationManager::class)->create([
            'organization_id' => $location->organization_id,
            'user_id' => $user->id,
        ]);

        $this->assertFalse($policy->delete($user, $location, $requestedItem));
    }

    public function testDeletePasses()
    {
        $policy = new RequestedItemPolicy();

        $user = factory(User::class)->create();
        $location = factory(Location::class)->create();
        $requestedItem = factory(RequestedItem::class)->create([
            'location_id' => $location->id,
        ]);

        factory(OrganizationManager::class)->create([
            'organization_id' => $location->organization_id,
            'user_id' => $user->id,
        ]);

        $this->assertTrue($policy->delete($user, $location, $requestedItem));
    }
}