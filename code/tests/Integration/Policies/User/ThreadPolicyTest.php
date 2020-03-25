<?php
declare(strict_types=1);

namespace Tests\Integration\Policies\User;

use App\Contracts\ThreadSecurity\ThreadSubjectGateContract;
use App\Contracts\ThreadSecurity\ThreadSubjectGateProviderContract;
use App\Models\User\User;
use App\Policies\User\ThreadPolicy;
use Tests\CustomMockInterface;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;

/**
 * Class ThreadPolicyTest
 * @package Tests\Integration\Policies\User
 */
class ThreadPolicyTest extends TestCase
{
    use DatabaseSetupTrait;

    /**
     * @var ThreadSubjectGateProviderContract|CustomMockInterface
     */
    private $gateProvider;

    /**
     * @var ThreadPolicy
     */
    private $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->gateProvider = mock(ThreadSubjectGateProviderContract::class);
        $this->policy = new ThreadPolicy($this->gateProvider);
    }

    public function testAllBlocksWhenGateNotFound()
    {
        $loggedInUser = factory(User::class)->create();
        $requestedUser = factory(User::class)->create();

        $this->gateProvider->shouldReceive('createGate')->once()->with('a_type')->andReturnNull();

        $this->assertFalse($this->policy->all($loggedInUser, $requestedUser, 'a_type'));
    }

    public function testAllBlockWhenAccessingAnotherUser()
    {
        $loggedInUser = factory(User::class)->create();
        $requestedUser = factory(User::class)->create();

        $gate = mock(ThreadSubjectGateContract::class);

        $this->gateProvider->shouldReceive('createGate')->once()->with('a_type')->andReturn($gate);

        $this->assertFalse($this->policy->all($loggedInUser, $requestedUser, 'a_type'));
    }

    public function testAllBlockWhenGateFails()
    {
        $loggedInUser = factory(User::class)->create();

        $gate = mock(ThreadSubjectGateContract::class);

        $this->gateProvider->shouldReceive('createGate')->once()->with('a_type')->andReturn($gate);
        $gate->shouldReceive('authorizeSubject')->once()->with($loggedInUser, 43)->andReturnFalse();

        $this->assertFalse($this->policy->all($loggedInUser, $loggedInUser, 'a_type', 43));
    }

    public function testAllPasses()
    {
        $loggedInUser = factory(User::class)->create();

        $gate = mock(ThreadSubjectGateContract::class);

        $this->gateProvider->shouldReceive('createGate')->once()->with('a_type')->andReturn($gate);
        $gate->shouldReceive('authorizeSubject')->once()->with($loggedInUser, 43)->andReturnTrue();

        $this->assertTrue($this->policy->all($loggedInUser, $loggedInUser, 'a_type', 43));
    }

    public function testCreateBlocksWhenGateNotFound()
    {
        $loggedInUser = factory(User::class)->create();
        $requestedUser = factory(User::class)->create();

        $this->gateProvider->shouldReceive('createGate')->once()->with('a_type')->andReturnNull();

        $this->assertFalse($this->policy->create($loggedInUser, $requestedUser, 'a_type'));
    }

    public function testCreateBlockWhenAccessingAnotherUser()
    {
        $loggedInUser = factory(User::class)->create();
        $requestedUser = factory(User::class)->create();

        $gate = mock(ThreadSubjectGateContract::class);

        $this->gateProvider->shouldReceive('createGate')->once()->with('a_type')->andReturn($gate);

        $this->assertFalse($this->policy->create($loggedInUser, $requestedUser, 'a_type'));
    }

    public function testCreateBlockWhenGateFails()
    {
        $loggedInUser = factory(User::class)->create();

        $gate = mock(ThreadSubjectGateContract::class);

        $this->gateProvider->shouldReceive('createGate')->once()->with('a_type')->andReturn($gate);
        $gate->shouldReceive('authorizeSubject')->once()->with($loggedInUser, 43)->andReturnFalse();

        $this->assertFalse($this->policy->create($loggedInUser, $loggedInUser, 'a_type', 43));
    }

    public function testCreatePasses()
    {
        $loggedInUser = factory(User::class)->create();

        $gate = mock(ThreadSubjectGateContract::class);

        $this->gateProvider->shouldReceive('createGate')->once()->with('a_type')->andReturn($gate);
        $gate->shouldReceive('authorizeSubject')->once()->with($loggedInUser, 43)->andReturnTrue();

        $this->assertTrue($this->policy->create($loggedInUser, $loggedInUser, 'a_type', 43));
    }
}