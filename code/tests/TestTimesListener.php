<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestListener;
use PHPUnit\Framework\TestListenerDefaultImplementation;

/**
 * Class TestTimesListener
 * @package Tests
 */
class TestTimesListener implements TestListener
{
    use TestListenerDefaultImplementation;

    /**
     * @var integer the number of milliseconds that mean this was a long test
     */
    const TEST_LIMIT_MILLISECONDS = 3000;

    /**
     * @var bool whether this is the first test or not
     */
    protected $firstTest = true;

    /**
     * A test ended - print out if it was too long (and if it wasn't the first - cuz that's for handling db potentially - could potentially be a false negative)
     *
     * @param Test $test
     * @param float $time seconds
     */
    public function endTest(Test $test, float $time): void
    {
        if (!$this->firstTest && $time * 1000 > self::TEST_LIMIT_MILLISECONDS) {
            $error = sprintf('%s::%s ran for %s seconds', get_class($test), $test->getName(), $time);
            print "\n\033[41m" . $error . "\033[0m\n";
        }
        $this->firstTest = false;
    }
}