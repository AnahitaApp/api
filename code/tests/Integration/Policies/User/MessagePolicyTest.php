<?php
declare(strict_types=1);

namespace Tests\Integration\Policies\User;

use App\Contracts\ThreadSecurity\ThreadSubjectGateContract;
use App\Contracts\ThreadSecurity\ThreadSubjectGateProviderContract;
use App\Models\User\Message;
use App\Models\User\Thread;
use App\Models\User\User;
use App\Policies\User\MessagePolicy;
use Tests\CustomMockInterface;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;

/**
 * Class MessagePolicyTest
 * @package Tests\Integration\Policies\User
 */
class MessagePolicyTest extends TestCase
{
    use DatabaseSetupTrait;

    /**
     * @var ThreadSubjectGateProviderContract|CustomMockInterface
     */
    private $gateProvider;

    /**
     * @var MessagePolicy
     */
    private $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->gateProvider = mock(ThreadSubjectGateProviderContract::class);
        $this->policy = new MessagePolicy($this->gateProvider);
    }

    public function testAllBlocksWhenGateNotFound()
    {
        $loggedInUser = factory(User::class)->create();
        $requestedUser = factory(User::class)->create();

        $thread = factory(Thread::class)->create([
            'subject_type' => 'a_type',
        ]);

        $this->gateProvider->shouldReceive('createGate')->once()->with('a_type')->andReturnNull();

        $this->assertFalse($this->policy->all($loggedInUser, $requestedUser, $thread));
    }

    public function testAllBlockWhenAccessingAnotherUser()
    {
        $loggedInUser = factory(User::class)->create();
        $requestedUser = factory(User::class)->create();

        $thread = factory(Thread::class)->create([
            'subject_type' => 'a_type',
        ]);

        $gate = mock(ThreadSubjectGateContract::class);

        $this->gateProvider->shouldReceive('createGate')->once()->with('a_type')->andReturn($gate);

        $this->assertFalse($this->policy->all($loggedInUser, $requestedUser, $thread));
    }

    public function testAllBlockWhenGateFails()
    {
        $loggedInUser = factory(User::class)->create();

        $thread = factory(Thread::class)->create([
            'subject_type' => 'a_type',
        ]);

        $gate = mock(ThreadSubjectGateContract::class);

        $this->gateProvider->shouldReceive('createGate')->once()->with('a_type')->andReturn($gate);
        $gate->shouldReceive('authorizeThread')->once()->with($loggedInUser, $thread)->andReturnFalse();

        $this->assertFalse($this->policy->all($loggedInUser, $loggedInUser, $thread));
    }

    public function testAllPasses()
    {
        $loggedInUser = factory(User::class)->create();

        $thread = factory(Thread::class)->create([
            'subject_type' => 'a_type',
        ]);

        $gate = mock(ThreadSubjectGateContract::class);

        $this->gateProvider->shouldReceive('createGate')->once()->with('a_type')->andReturn($gate);
        $gate->shouldReceive('authorizeThread')->once()->with($loggedInUser, $thread)->andReturnTrue();

        $this->assertTrue($this->policy->all($loggedInUser, $loggedInUser, $thread));
    }

    public function testCreateBlocksWhenGateNotFound()
    {
        $loggedInUser = factory(User::class)->create();
        $requestedUser = factory(User::class)->create();

        $thread = factory(Thread::class)->create([
            'subject_type' => 'a_type',
        ]);

        $this->gateProvider->shouldReceive('createGate')->once()->with('a_type')->andReturnNull();

        $this->assertFalse($this->policy->create($loggedInUser, $requestedUser, $thread));
    }

    public function testCreateBlockWhenAccessingAnotherUser()
    {
        $loggedInUser = factory(User::class)->create();
        $requestedUser = factory(User::class)->create();

        $thread = factory(Thread::class)->create([
            'subject_type' => 'a_type',
        ]);

        $gate = mock(ThreadSubjectGateContract::class);

        $this->gateProvider->shouldReceive('createGate')->once()->with('a_type')->andReturn($gate);

        $this->assertFalse($this->policy->create($loggedInUser, $requestedUser, $thread));
    }

    public function testCreateBlockWhenGateFails()
    {
        $loggedInUser = factory(User::class)->create();

        $thread = factory(Thread::class)->create([
            'subject_type' => 'a_type',
        ]);

        $gate = mock(ThreadSubjectGateContract::class);

        $this->gateProvider->shouldReceive('createGate')->once()->with('a_type')->andReturn($gate);
        $gate->shouldReceive('authorizeThread')->once()->with($loggedInUser, $thread)->andReturnFalse();

        $this->assertFalse($this->policy->create($loggedInUser, $loggedInUser, $thread));
    }

    public function testCreatePasses()
    {
        $loggedInUser = factory(User::class)->create();

        $thread = factory(Thread::class)->create([
            'subject_type' => 'a_type',
        ]);

        $gate = mock(ThreadSubjectGateContract::class);

        $this->gateProvider->shouldReceive('createGate')->once()->with('a_type')->andReturn($gate);
        $gate->shouldReceive('authorizeThread')->once()->with($loggedInUser, $thread)->andReturnTrue();

        $this->assertTrue($this->policy->create($loggedInUser, $loggedInUser, $thread));
    }

    public function testUpdateBlocksWhenGateNotFound()
    {
        $loggedInUser = factory(User::class)->create();
        $requestedUser = factory(User::class)->create();

        $thread = factory(Thread::class)->create([
            'subject_type' => 'a_type',
        ]);
        $message = factory(Message::class)->create();

        $this->gateProvider->shouldReceive('createGate')->once()->with('a_type')->andReturnNull();

        $this->assertFalse($this->policy->update($loggedInUser, $requestedUser, $thread, $message));
    }

    public function testUpdateBlocksUserMismatch()
    {
        $loggedInUser = factory(User::class)->create();
        $requestedUser = factory(User::class)->create();
        $thread = factory(Thread::class)->create([
            'subject_type' => 'a_type',
        ]);
        $message = factory(Message::class)->create();

        $gate = mock(ThreadSubjectGateContract::class);

        $this->gateProvider->shouldReceive('createGate')->once()->with('a_type')->andReturn($gate);

        $this->assertFalse($this->policy->update($loggedInUser, $requestedUser, $thread, $message));
    }

    public function testUpdateBlockWhenGateFails()
    {
        $loggedInUser = factory(User::class)->create();

        $thread = factory(Thread::class)->create([
            'subject_type' => 'a_type',
        ]);
        $message = factory(Message::class)->create();

        $gate = mock(ThreadSubjectGateContract::class);

        $this->gateProvider->shouldReceive('createGate')->once()->with('a_type')->andReturn($gate);
        $gate->shouldReceive('authorizeThread')->once()->with($loggedInUser, $thread)->andReturnFalse();

        $this->assertFalse($this->policy->update($loggedInUser, $loggedInUser, $thread, $message));
    }

    public function testUpdateBlocksMessageNotInThread()
    {
        $thread = factory(Thread::class)->create([
            'subject_type' => 'a_type',
        ]);
        $user = factory(User::class)->create();
        $message = factory(Message::class)->create();

        $gate = mock(ThreadSubjectGateContract::class);

        $this->gateProvider->shouldReceive('createGate')->once()->with('a_type')->andReturn($gate);
        $gate->shouldReceive('authorizeThread')->once()->with($user, $thread)->andReturnTrue();

        $this->assertFalse($this->policy->update($user, $user, $thread, $message));
    }

    public function testUpdateBlocksUserNotSentMessage()
    {
        $thread = factory(Thread::class)->create([
            'subject_type' => 'a_type',
        ]);
        $user = factory(User::class)->create();
        $message = factory(Message::class)->create([
            'thread_id' => $thread->id,
            'to_id' => factory(User::class)->create()->id,
        ]);

        $gate = mock(ThreadSubjectGateContract::class);

        $this->gateProvider->shouldReceive('createGate')->once()->with('a_type')->andReturn($gate);
        $gate->shouldReceive('authorizeThread')->once()->with($user, $thread)->andReturnTrue();

        $this->assertFalse($this->policy->update($user, $user, $thread, $message));
    }

    public function testUpdatePasses()
    {
        $thread = factory(Thread::class)->create([
            'subject_type' => 'a_type',
        ]);
        $user = factory(User::class)->create();
        $message = factory(Message::class)->create([
            'thread_id' => $thread->id,
            'to_id' => $user->id,
        ]);

        $gate = mock(ThreadSubjectGateContract::class);

        $this->gateProvider->shouldReceive('createGate')->once()->with('a_type')->andReturn($gate);
        $gate->shouldReceive('authorizeThread')->once()->with($user, $thread)->andReturnTrue();

        $this->assertTrue($this->policy->update($user, $user, $thread, $message));
    }
}