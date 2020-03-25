<?php
declare(strict_types=1);

namespace Tests;

use App\Models\User\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Psr\Log\LoggerInterface as LogContract;

/**
 * Class TestCase
 * @package Tests
 */
abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * @var User
     */
    protected $actingAs;

    /**
     * Overriding this - because i am handling my own database stuff
     *
     * Boot the testing helper traits.
     *
     * @return void
     */
    protected function setUpTraits()
    {
        $uses = array_flip(class_uses_recursive(static::class));

        if (isset($uses[WithoutMiddleware::class])) {
            $this->disableMiddlewareForAllTests();
        }

        if (isset($uses[WithoutEvents::class])) {
            $this->disableEventsForAllTests();
        }
    }

    /**
     * This is used just temporarily often to enable debugging on the integration tests.
     */
    protected function enableDebug()
    {
        config(['app.debug' => true]);
    }

    /**
     * Get a logging instance that ignores anything going on
     *
     * @return LogContract
     */
    protected function getGenericLogMock()
    {
        $logMock = mock(LogContract::class);
        $logMock->shouldReceive('info');
        return $logMock;
    }

    /**
     * Call this to make the user an authenticated user
     * @param array $data
     */
    protected function actAsUser($data = [])
    {
        $this->actingAs = factory(User::class)->create($data);
        $this->actingAs($this->actingAs);
    }

    /**
     * Act as a role type
     *
     * @param int $roleId
     */
    protected function actAs(int $roleId)
    {
        $this->actAsUser();
        $this->actingAs->addRole($roleId);
    }
}
