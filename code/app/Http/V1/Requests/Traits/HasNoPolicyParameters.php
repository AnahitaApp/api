<?php
declare(strict_types=1);

namespace App\Http\V1\Requests\Traits;

/**
 * Trait HasNoPolicyParameters
 * @package App\Http\V1\Requests\Traits
 */
trait HasNoPolicyParameters
{
    /**
     * Gets any additional parameters needed for the policy function
     *
     * @return array
     */
    protected function getPolicyParameters(): array
    {
        return [];
    }
}