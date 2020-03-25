<?php
declare(strict_types=1);

namespace Tests\Feature\User\Contact;

use App\Models\User\Contact;
use App\Models\User\User;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class UserContactUpdateTest
 * @package Tests\Feature\User\Contact
 */
class UserContactUpdateTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var string
     */
    private $path = '/v1/users/';

    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->mockApplicationLog();
        $this->user = factory(User::class)->create();

        $this->path.= $this->user->id . '/contacts/';
    }

    public function testNotLoggedInUserBlocked()
    {
        $contact = factory(Contact::class)->create();

        $response = $this->json('PUT', $this->path . $contact->id);

        $response->assertStatus(403);
    }

    public function testNotFound()
    {
        $this->actingAs($this->user);

        $response = $this->json('PUT', $this->path . '453');

        $response->assertStatus(404);
    }

    public function testUpdateDenySuccessful()
    {
        $this->actingAs($this->user);

        $contact = factory(Contact::class)->create([
            'requested_id' => $this->user->id,
        ]);

        $response = $this->json('PUT', $this->path . $contact->id,  [
            'deny' => true,
        ]);

        $response->assertStatus(200);

        /** @var Contact $updated */
        $updated = Contact::find($contact->id);

        $this->assertNotNull( $updated->denied_at);
    }

    public function testUpdateConfirmSuccessful()
    {
        $this->actingAs($this->user);

        $contact = factory(Contact::class)->create([
            'requested_id' => $this->user->id,
        ]);

        $response = $this->json('PUT', $this->path . $contact->id,  [
            'confirm' => true,
        ]);

        $response->assertStatus(200);

        /** @var Contact $updated */
        $updated = Contact::find($contact->id);

        $this->assertNotNull( $updated->confirmed_at);
    }

    public function testUpdateFailsProtectedFieldsPresent()
    {
        $this->actingAs($this->user);

        $contact = factory(Contact::class)->create([
            'requested_id' => $this->user->id,
        ]);

        $response = $this->json('PUT', $this->path . $contact->id,  [
            'initiated_by_id' => 'hi',
            'requested_id' => 'hi',
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'errors' => [
                'requested_id' => ['The requested id field is not allowed or can not be set for this request.'],
                'initiated_by_id' => ['The initiated by id field is not allowed or can not be set for this request.'],
            ]
        ]);
    }

    public function testUpdateFailsInvalidBooleanFields()
    {
        $this->actingAs($this->user);


        $contact = factory(Contact::class)->create([
            'requested_id' => $this->user->id,
        ]);

        $response = $this->json('PUT', $this->path . $contact->id,  [
            'deny' => -1,
            'confirm' => -1,
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'errors' => [
                'deny' => ['The deny field must be true or false.'],
                'confirm' => ['The confirm field must be true or false.'],
            ]
        ]);
    }
}