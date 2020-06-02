<?php
declare(strict_types=1);

namespace Tests\Integration\Policies\Request;

use App\Models\Organization\Location;
use App\Models\Organization\OrganizationManager;
use App\Models\Request\Request;
use App\Models\User\User;
use App\Policies\Request\RequestPolicy;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;

/**
 * Class RequestPolicyTest
 * @package Tests\Integration\Policies\Request
 */
class RequestPolicyTest extends TestCase
{
    use DatabaseSetupTrait;

    public function testAllFailsWithConflictingRequestedUser()
    {
        $user = new User();
        $user->id = 3542;
        $requestedUser = new User();
        $requestedUser->id = 65;

        $policy = new RequestPolicy();

        $this->assertFalse($policy->all($user, $requestedUser));
    }

    public function testAllPassesWithSameRequestedUser()
    {
        $user = new User();
        $user->id = 3542;

        $policy = new RequestPolicy();

        $this->assertTrue($policy->all($user, $user));
    }

    public function testAllWithoutAccessToLocationUser()
    {
        $user = factory(User::class)->create();
        $location = factory(Location::class)->create();

        $policy = new RequestPolicy();

        $this->assertFalse($policy->all($user, null, $location));
    }

    public function testAllPassesWhenUserHasLocationAccess()
    {
        $user = factory(User::class)->create();
        $location = factory(Location::class)->create();

        factory(OrganizationManager::class)->create([
            'user_id' => $user->id,
            'organization_id' => $location->organization_id,
        ]);

        $policy = new RequestPolicy();

        $this->assertTrue($policy->all($user, null, $location));
    }

    public function testAllPassesWithoutRequestedUser()
    {
        $policy = new RequestPolicy();

        $this->assertTrue($policy->all(new User()));
    }

    public function testCreate()
    {
        $policy = new RequestPolicy();

        $this->assertTrue($policy->create(new User()));
    }

    public function testViewFails()
    {
        $policy = new RequestPolicy();

        $user = new User();
        $user->id = 24354;

        $request = new Request([
            'completed_by_id' => 314,
            'requested_by_id' => 235,
        ]);

        $this->assertFalse($policy->view($user, $request));
    }

    public function testViewPasses()
    {
        $policy = new RequestPolicy();

        $request = new Request([
            'completed_by_id' => 314,
            'requested_by_id' => 235,
        ]);

        $user = new User();
        $user->id = 314;

        $this->assertTrue($policy->view($user, $request));

        $user->id = 235;
        $this->assertTrue($policy->view($user, $request));
    }

    public function  testUpdateFailsWhenUserIsNotCompletingTheRequest()
    {
        $policy = new RequestPolicy();

        $request = new Request([
            'completed_by_id' => 314,
            'requested_by_id' => 3452
        ]);

        $this->assertFalse($policy->update(new User(), $request));
    }

    public function testUpdateFailsWithLocationUserDoesNotBelongToOrganization()
    {
        $policy = new RequestPolicy();

        $user = factory(User::class)->create();
        $location = factory(Location::class)->create();
        $request = factory(Request::class)->create([
            'location_id' => $location->id,
        ]);

        $this->assertFalse($policy->update($user, $request, $location));
    }

    public function testUpdateFailsWithLocationRequestDoesNotBelongToLocation()
    {
        $policy = new RequestPolicy();

        $user = factory(User::class)->create();
        $location = factory(Location::class)->create();
        $request = factory(Request::class)->create();
        factory(OrganizationManager::class)->create([
            'user_id' => $user->id,
            'organization_id' => $location->organization_id,
        ]);

        $this->assertFalse($policy->update($user, $request, $location));
    }

    public function testUpdatePassesWithLocation()
    {
        $policy = new RequestPolicy();

        $user = factory(User::class)->create();
        $location = factory(Location::class)->create();
        $request = factory(Request::class)->create([
            'location_id' => $location->id,
        ]);
        factory(OrganizationManager::class)->create([
            'user_id' => $user->id,
            'organization_id' => $location->organization_id,
        ]);

        $this->assertTrue($policy->update($user, $request, $location));
    }

    public function  testUpdatePassesWhenNoOneIsCompletingRequest()
    {
        $policy = new RequestPolicy();

        $this->assertTrue($policy->update(new User(), new Request()));
    }

    public function  testUpdatePassesWhenUserCreatedTheRequest()
    {
        $policy = new RequestPolicy();

        $request = new Request([
            'requested_by_id' => 314,
        ]);

        $user = new User();
        $user->id = 314;

        $this->assertTrue($policy->update($user, $request));
    }

    public function  testUpdatePassesWhenUserIsCompletingTheRequest()
    {
        $policy = new RequestPolicy();

        $request = new Request([
            'completed_by_id' => 314,
        ]);

        $user = new User();
        $user->id = 314;

        $this->assertTrue($policy->update($user, $request));
    }
}