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
 * Class MembershipPlanCreateTest
 * @package Tests\Feature\Http\MembershipPlan
 */
class MembershipPlanCreateTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog, RolesTesting;
    
    private $route = '/v1/membership-plans';

    public function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();
    }

    public function testNotLoggedInUserBlocked()
    {
        $response = $this->json('POST', $this->route);
        $response->assertStatus(403);
    }

    public function testNotAdminUserBlocked()
    {
        foreach ($this->rolesWithoutAdmins() as $role) {
            $this->actAs($role);
            $response = $this->json('POST', $this->route);
            $response->assertStatus(403);
        }
    }

    public function testCreateSuccessful()
    {
        $this->actAs(Role::SUPER_ADMIN);
        
        $properties = [
            'name' => 'Hellow',
            'duration' => MembershipPlan::DURATION_LIFETIME,
            'current_cost' => 60.00,
        ];

        $response = $this->json('POST', $this->route, $properties);

        $response->assertStatus(201);

        $response->assertJson($properties);
    }

    public function testCreateFailsMissingRequiredFields()
    {
        $this->actAs(Role::SUPER_ADMIN);

        $response = $this->json('POST', $this->route);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'name' => ['The name field is required.'],
                'current_cost' => ['The current cost field is required.'],
                'duration' => ['The duration field is required.'],
            ]
        ]);
    }

    public function testCreateFailsInvalidNumericFields()
    {
        $this->actAs(Role::SUPER_ADMIN);
        $response = $this->json('POST', $this->route, [
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

    public function testCreateFailsInvalidNumericMinimums()
    {
        $this->actAs(Role::SUPER_ADMIN);
        $response = $this->json('POST', $this->route, [
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

    public function testCreateFailsInvalidStringFields()
    {
        $this->actAs(Role::SUPER_ADMIN);

        $data = [
            'name' => 5435,
            'duration' => 5,
        ];

        $response = $this->json('POST', $this->route, $data);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'name' => ['The name must be a string.'],
                'duration' => ['The duration must be a string.'],
            ]
        ]);
    }

    public function testCreateFailsStringTooLong()
    {
        $this->actAs(Role::SUPER_ADMIN);

        $data = [
            'name' => str_repeat('a', 121),
        ];

        $response = $this->json('POST', $this->route, $data);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'name' => ['The name may not be greater than 120 characters.'],
            ]
        ]);
    }

    public function testCreateFailsInvalidEnumFields()
    {
        $this->actAs(Role::SUPER_ADMIN);

        $data = [
            'duration' => 'hi',
        ];

        $response = $this->json('POST', $this->route, $data);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'duration' => ['The selected duration is invalid.'],
            ]
        ]);
    }
}