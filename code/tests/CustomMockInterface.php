<?php
/**
 * A custom interface that makes it easier to provide hints for the usage of this function
 */
declare(strict_types=1);

namespace Tests;

/**
 * Interface CustomMockInterface
 * @package Tests
 */
interface CustomMockInterface extends \Mockery\MockInterface
{
    /**
     * @param array ...$function
     * @return mixed
     */
    public function shouldReceive(...$function);
}