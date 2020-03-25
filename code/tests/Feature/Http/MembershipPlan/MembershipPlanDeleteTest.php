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
 * Class MembershipPlanDeleteTest
 * @package Tests\Feature\Http\MembershipPlan
 */
class MembershipPlanDeleteTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog, RolesTesting;

    public function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();
    }

    public function testNotLoggedInUserBlocked()
    {
        $model = factory(MembershipPlan::class)->create();
        $response = $this->json('DELETE', '/v1/membership-plans/' . $model->id);
        $response->assertStatus(403);
    }

    public function testNonAdminUserBlocked()
    {
        foreach ($this->rolesWithoutAdmins() as $role) {
            $this->actAs($role);
            $model = factory(MembershipPlan::class)->create();
            $response = $this->json('DELETE', '/v1/membership-plans/' . $model->id);
            $response->assertStatus(403);
        }
    }

    public function testDeleteSingle()
    {
        $this->actAs(Role::SUPER_ADMIN);

        $model = factory(MembershipPlan::class)->create();

        $response = $this->json('DELETE', '/v1/membership-plans/' . $model->id);

        $response->assertStatus(204);
        $this->assertEquals(0, MembershipPlan::count());
    }

    public function testDeleteSingleInvalidIdFails()
    {
        $this->actAs(Role::SUPER_ADMIN);

        $response = $this->json('DELETE', '/v1/membership-plans/a')
            ->assertExactJson([
                'message'   => 'This path was not found.',
            ]);
        $response->assertStatus(404);
    }

    public function testDeleteSingleNotFoundFails()
    {
        $this->actAs(Role::SUPER_ADMIN);

        $response = $this->json('DELETE', '/v1/membership-plans/1')
            ->assertExactJson([
                'message'   =>  'This item was not found.'
            ]);
        $response->assertStatus(404);
    }
}