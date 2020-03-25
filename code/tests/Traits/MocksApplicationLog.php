<?php
declare(strict_types=1);

namespace Tests\Traits;

use Psr\Log\LoggerInterface;

/**
 * Class MocksApplicationLog
 * @package Tests\Traits
 */
trait MocksApplicationLog
{
    /**
     * Replaces the instance of the log with a mock that mocks out calls
     */
    protected function mockApplicationLog()
    {
        $logMock = mock(LoggerInterface::class);
        $logMock->shouldReceive('info');
        $logMock->shouldReceive('debug');
        $logMock->shouldReceive('warning');
        $logMock->shouldReceive('error');
        $this->app->instance('log', $logMock);
    }
}