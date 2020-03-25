<?php
declare(strict_types=1);

namespace App\Http\V1\Controllers\Traits;

use App\Http\V1\Requests\BaseRequestAbstract;

/**
 * Trait HasViewRequests
 *
 * Adds some functionality to the controller for expand requests
 *
 * @package App\Http\V1\Controllers\Traits
 */
trait HasViewRequests
{
    /**
     * Get the expanded / width statement
     *
     * @param BaseRequestAbstract $request
     * @return array
     */
    protected function expand(BaseRequestAbstract $request): array
    {
        return $request->input('with', []);
    }
}