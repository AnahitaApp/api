<?php
declare(strict_types=1);

namespace Tests\Feature\Http\Request;

use App\Models\Role;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;
use Tests\Traits\RolesTesting;

/**
 * Class RequestCreateTest
 * @package Tests\Feature\Http\Request
 */
class RequestCreateTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog, RolesTesting;
    
    private $route = '/v1/requests';

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

    public function testCreateSuccessful()
    {
        $this->actAs(Role::APP_USER);
        
        $properties = [
            'latitude' => 60,
            'longitude' => 60,
            'requested_items' => [
                [
                    'name' => 'An Item',
                ],
            ],
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
                'latitude' => ['The latitude field is required.'],
                'longitude' => ['The longitude field is required.'],
                'requested_items' => ['The requested items field is required.'],
            ]
        ]);
    }

    public function testCreateFailsInvalidStringFields()
    {
        $this->actAs(Role::SUPER_ADMIN);
        $response = $this->json('POST', $this->route, [
            'description' => 2323,
            'drop_off_location' => 423,
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'message' => 'Sorry, something went wrong.',
            'errors' => [
                'description' => ['The description must be a string.'],
                'drop_off_location' => ['The drop off location must be a string.'],
            ]
        ]);
    }

    public function testCreateFailsInvalidNumericFields()
    {
        $this->actAs(Role::SUPER_ADMIN);
        $response = $this->json('POST', $this->route, [
            'latitude' => 'hi',
            'longitude' => 'hi',
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'message' => 'Sorry, something went wrong.',
            'errors' => [
                'latitude' => ['The latitude must be a number.'],
                'longitude' => ['The longitude must be a number.'],
            ]
        ]);
    }

    public function testCreateFailsInvalidArrayFields()
    {
        $this->actAs(Role::SUPER_ADMIN);

        $data = [
            'requested_items' => 'hi',
        ];

        $response = $this->json('POST', $this->route, $data);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'requested_items' => ['The requested items must be an array.'],
            ]
        ]);
    }

    public function testCreateFailsRequestedItemsInvalidArrayFields()
    {
        $this->actAs(Role::SUPER_ADMIN);

        $data = [
            'requested_items' => [
                'hi',
            ],
        ];

        $response = $this->json('POST', $this->route, $data);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'requested_items.0' => ['The requested_items.0 must be an array.'],
            ]
        ]);
    }

    public function testCreateFailsRequestedItemsInvalidStringFields()
    {
        $this->actAs(Role::SUPER_ADMIN);

        $data = [
            'requested_items' => [
                [
                    'name' => 3542,
                ],
            ],
        ];

        $response = $this->json('POST', $this->route, $data);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'requested_items.0.name' => ['The requested_items.0.name must be a string.'],
            ]
        ]);
    }

    public function testCreateFailsRequestedItemsInvalidNumericFields()
    {
        $this->actAs(Role::SUPER_ADMIN);

        $data = [
            'requested_items' => [
                [
                    'asset_id' => '24ewf',
                ],
            ],
        ];

        $response = $this->json('POST', $this->route, $data);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'requested_items.0.asset_id' => ['The requested_items.0.asset_id must be a number.'],
            ]
        ]);
    }

    public function testCreateFailsRequestedItemsInvalidModel()
    {
        $this->actAs(Role::SUPER_ADMIN);

        $data = [
            'requested_items' => [
                [
                    'asset_id' => 4365,
                ],
            ],
        ];

        $response = $this->json('POST', $this->route, $data);

        $response->assertStatus(400);
        $response->assertJson([
            'message'   => 'Sorry, something went wrong.',
            'errors'    =>  [
                'requested_items.0.asset_id' => ['The selected requested_items.0.asset_id is invalid.'],
            ]
        ]);
    }
}