<?php
declare(strict_types=1);

namespace Tests\Feature\Http\MembershipPlan;

use App\Models\Role;
use App\Models\Subscription\MembershipPlan;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;
use Tests\Traits\RolesTesting;

/**
 * Class MembershipPlanUpdateTest
 * @package Tests\Feature\Http\MembershipPlan
 */
class MembershipPlanUpdateTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog, RolesTesting;
    
    const BASE_ROUTE = '/v1/membership-plans/';

    public function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();
    }

    public function testNotLoggedInUserBlocked()
    {
        $membershipPlan = factory(MembershipPlan::class)->create();
        $response = $this->json('PATCH', static::BASE_ROUTE . $membershipPlan->id);
        $response->assertStatus(403);
    }

    public function testNotAdminUserBlocked()
    {
        foreach ($this->rolesWithoutAdmins() as $role) {
            $this->actAs($role);
            $membershipPlan = factory(MembershipPlan::class)->create();
            $response = $this->json('PATCH', static::BASE_ROUTE . $membershipPlan->id);
            $response->assertStatus(403);
        }
    }

    public function testPatchSuccessful()
    {
        $this->actAs(Role::SUPER_ADMIN);

        /** @var MembershipPlan $membershipPlan */
        $membershipPlan = factory(MembershipPlan::class)->create([
            'name' => 'Test Memberhip Plan',
        ]);

        $data = [
            'name' => 'Test Membership Plan',
        ];

        $response = $this->json('PATCH', static::BASE_ROUTE . $membershipPlan->id, $data);
        $response->assertStatus(200);
        $response->assertJson($data);


        /** @var MembershipPlan $updated */
        $updated = MembershipPlan::find($membershipPlan->id);

        $this->assertEquals('Test Membership Plan', $updated->name);
    }

    public function testPatchNotFoundFails()
    {
        $this->actAs(Role::SUPER_ADMIN);

        $response = $this->json('PATCH', static::BASE_ROUTE . '5')
            ->assertExactJson([
                'message'   =>  'This item was not found.'
            ]);
        $response->assertStatus(404);
    }

    public function testPatchInvalidIdFails()
    {
        $this->actAs(Role::SUPER_ADMIN);

        $response = $this->json('PATCH', static::BASE_ROUTE . '/b')
            ->assertExactJson([
                'message'   => 'This path was not found.',
            ]);
        $response->assertStatus(404);
    }

    public function testPatchSuccessfulNoFields()
    {
        $this->actAs(Role::SUPER_ADMIN);

        $membershipPlan = factory(MembershipPlan::class)->create([
            'name' => 'Test Gift Pack',
        ]);

        $response = $this->json('PATCH', static::BASE_ROUTE . $membershipPlan->id, []);

        $response->assertStatus(200);
    }

    public function testPatchFailsInvalidNumericFields()
    {
        $membershipPlan = factory(MembershipPlan::class)->create();

        $this->actAs(Role::SUPER_ADMIN);
        $response = $this->json('PATCH', static::BASE_ROUTE . $membershipPlan->id, [
            'current_cost' => 'hi'
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'current_cost' => ['The current cost must be a number.'],
            ]
        ]);
    }

    public function testPatchFailsInvalidNumericMinimums()
    {
        $membershipPlan = factory(MembershipPlan::class)->create();

        $this->actAs(Role::SUPER_ADMIN);
        $response = $this->json('PATCH', static::BASE_ROUTE . $membershipPlan->id, [
            'current_cost' => -1
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'current_cost' => ['The current cost must be at least 0.00.'],
            ]
        ]);
    }

    public function testPatchFailsInvalidStringFields()
    {
        $membershipPlan = factory(MembershipPlan::class)->create();

        $this->actAs(Role::SUPER_ADMIN);

        $data = [
            'name' => 5,
        ];

        $response = $this->json('PATCH', static::BASE_ROUTE . $membershipPlan->id, $data);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'name' => ['The name must be a string.'],
            ]
        ]);
    }

    public function testPatchFailsTooLongFields()
    {
        $membershipPlan = factory(MembershipPlan::class)->create();

        $this->actAs(Role::SUPER_ADMIN);

        $data = [
            'name' => str_repeat('a', 121),
        ];

        $response = $this->json('PATCH', static::BASE_ROUTE . $membershipPlan->id, $data);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'name' => ['The name may not be greater than 120 characters.'],
            ]
        ]);
    }
}