<?php
declare(strict_types=1);

namespace Tests;

/**
 * Class TestsHelper
 *
 * This class is for helping keep our state throughout the tests (this is rarely needed but.... laravel... artisan... etc.)
 */
class TestHelper
{
    /**
     * @var bool whether migrations have an in this instance
     */
    public static $migrationsRan = false;
}