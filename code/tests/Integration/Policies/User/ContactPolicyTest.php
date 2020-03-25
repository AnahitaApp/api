<?php
declare(strict_types=1);

namespace Tests\Integration\Policies\User;

use App\Models\User\Contact;
use App\Models\User\User;
use App\Policies\User\ContactPolicy;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;

/**
 * Class ContactPolicyTest
 * @package Tests\Integration\Policies\User
 */
class ContactPolicyTest extends TestCase
{
    use DatabaseSetupTrait;

    public function testAllPasses()
    {
        $user = factory(User::class)->create();

        $policy = new ContactPolicy();

        $this->assertTrue($policy->all($user, $user));
    }

    public function testAllFails()
    {
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();

        $policy = new ContactPolicy();

        $this->assertFalse($policy->all($user1, $user2));
    }

    public function testCreatePasses()
    {
        $user = factory(User::class)->create();

        $policy = new ContactPolicy();

        $this->assertTrue($policy->create($user, $user));
    }

    public function testCreateFails()
    {
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();

        $policy = new ContactPolicy();

        $this->assertFalse($policy->create($user1, $user2));
    }

    public function testUpdatePasses()
    {
        $user = factory(User::class)->create();

        $policy = new ContactPolicy();

        $initiatedContact = factory(Contact::class)->create([
            'initiated_by_id' => $user->id,
        ]);
        $this->assertTrue($policy->update($user, $user, $initiatedContact));

        $requestedContact = factory(Contact::class)->create([
            'requested_id' => $user->id,
        ]);
        $this->assertTrue($policy->update($user, $user, $requestedContact));
    }

    public function testUpdateFails()
    {
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $contact = factory(Contact::class)->create([
            'initiated_by_id' => $user2->id,
        ]);

        $policy = new ContactPolicy();

        $this->assertFalse($policy->update($user1, $user2, $contact));
        $this->assertFalse($policy->update($user1, $user1, $contact));
    }

    public function testDeletePasses()
    {
        $user = factory(User::class)->create();

        $policy = new ContactPolicy();

        $initiatedContact = factory(Contact::class)->create([
            'initiated_by_id' => $user->id,
        ]);
        $this->assertTrue($policy->update($user, $user, $initiatedContact));

        $requestedContact = factory(Contact::class)->create([
            'requested_id' => $user->id,
        ]);
        $this->assertTrue($policy->delete($user, $user, $requestedContact));
    }

    public function testDeleteFails()
    {
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $contact = factory(Contact::class)->create([
            'initiated_by_id' => $user2->id,
        ]);

        $policy = new ContactPolicy();

        $this->assertFalse($policy->delete($user1, $user2, $contact));
        $this->assertFalse($policy->delete($user1, $user1, $contact));
    }
}