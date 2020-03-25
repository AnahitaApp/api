<?php
declare(strict_types=1);

namespace Tests\Feature\Http\Authentication;

use App\Models\User\User;
use Illuminate\Support\Facades\Hash;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class UserSignUpTest
 * @package Tests\Feature\Http\Authentication
 */
class SignUpTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    public function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();
    }

    public function testSuccess()
    {
        $properties = [
            'email' => 'test@test.com',
            'name' => 'Jerry McGuire',
            'password' => 'password',
        ];

        $response = $this->json('POST', '/v1/auth/sign-up', $properties);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'token'
        ]);
        $token = $response->json('token');

        $this->actingAs = null;

        $this->app['env'] = 'testing-override';
        $response = $this->json('GET', '/v1/users/me', [], [
            'Authorization' => 'Bearer ' . $token
        ]);
        $response->assertStatus(200);
        $model = $response->original;

        //Make sure the password was hashed properly separately
        $password = $properties['password'];
        unset($properties['password']);

        $this->assertEquals($properties, [
            'email' => $model->email,
            'name' => 'Jerry McGuire',
        ]);

        $this->assertTrue(Hash::check($password, $model->password));
    }

    public function testWebsiteSignUpFailureMissingRequiredFields()
    {
        $response = $this->json('POST', '/v1/auth/sign-up', []);

        $response->assertStatus(400);
        $response->assertJson([
            'errors' => [
                'email' => ['The email field is required.'],
                'name' => ['The name field is required.'],
                'password' => ['The password field is required.'],
            ]
        ]);
    }

    public function testWebsiteSignUpFailsInvalidStringFields()
    {
        $response = $this->json('POST', '/v1/auth/sign-up', [
            'email' => 1,
            'name' => 1,
            'password' => 1,
        ]);

        $response->assertJson(['errors' => [
            'email' => ['The email must be a string.'],
            'name' => ['The name must be a string.'],
            'password' => ['The password must be a string.'],
        ]]);

        $response->assertStatus(400);
    }

    public function testWebsiteSignUpFailsTooShortFields()
    {
        $response = $this->json('POST', '/v1/auth/sign-up', [
            'password' => 'a',
        ]);
        $response->assertJson(['errors' => [
            'password' => ['The password must be at least 6 characters.'],
        ]]);

        $response->assertStatus(400);
    }

    public function testWebsiteSignUpFailsTooLongFields()
    {
        $response = $this->json('POST', '/v1/auth/sign-up', [
            'email' => str_repeat('a', 121),
            'name' => str_repeat('a', 121),
            'password' => str_repeat('a', 257),
        ]);
        $response->assertJson(['errors' => [
            'email' => ['The email may not be greater than 120 characters.'],
            'name' => ['The name may not be greater than 120 characters.'],
            'password' => ['The password may not be greater than 256 characters.'],
        ]]);

        $response->assertStatus(400);
    }

    public function testWebsiteSignUpFailsInvalidEmailFields()
    {
        $response = $this->json('POST', '/v1/auth/sign-up', [
            'email' => 'asdf'
        ]);
        $response->assertJson(['errors' => [
            'email' => ['The email must be a valid email address.'],
        ]]);

        $response->assertStatus(400);
    }

    public function testWebsiteSignUpFailsEmailInUse()
    {
        factory(User::class)->create(['email' => 'test@test.com']);

        $response = $this->json('POST', '/v1/auth/sign-up', [
            'email' => 'test@test.com'
        ]);
        $response->assertJson(['errors' => [
            'email' => ['The email has already been taken.'],
        ]]);

        $response->assertStatus(400);
    }
}
