<?php
declare(strict_types=1);

namespace App\Contracts\Services;

/**
 * Interface TokenGenerationServiceContract
 * @package App\Contracts\Services
 */
interface TokenGenerationServiceContract
{
    /**
     * Generates a token
     *
     * @param int $length
     * @return string
     */
    public function generateToken($length = 40) : string;
}