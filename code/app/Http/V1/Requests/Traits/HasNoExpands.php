<?php
declare(strict_types=1);

namespace App\Http\V1\Requests\Traits;

/**
 * Trait HasNoExpands
 * @package App\Http\V1\Requests\Traits
 */
trait HasNoExpands
{
    /**
     * No expands allowed when using this trait
     *
     * @return array
     */
    public function allowedExpands(): array
    {
        return [];
    }
}