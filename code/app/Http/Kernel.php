<?php
declare(strict_types=1);

namespace App\Http;

use App\Http\Middleware\LogMiddleware;
use App\Http\Middleware\TrimStrings;
use App\Http\V1\Middleware\ExpandParsingMiddleware;
use App\Http\V1\Middleware\Issue404IfPageAfterPaginationMiddleware;
use App\Http\V1\Middleware\SearchFilterParsingMiddleware;
use Barryvdh\Cors\HandleCors;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Foundation\Http\Middleware\ValidatePostSize;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Middleware\ThrottleRequests;
use App\Http\V1\Middleware\JWTGetUserFromTokenProtectedRouteMiddleware;
use App\Http\V1\Middleware\JWTGetUserFromTokenUnprotectedRouteMiddleware;

/**
 * Class Kernel
 * @package App\Http
 */
class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        CheckForMaintenanceMode::class,
        ValidatePostSize::class,
        TrimStrings::class,
        ConvertEmptyStringsToNull::class,
        LogMiddleware::class,
        HandleCors::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'api-v1' => [
            'throttle:60,1',
            'bindings',
            Issue404IfPageAfterPaginationMiddleware::class,
            SearchFilterParsingMiddleware::class,
            ExpandParsingMiddleware::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => Authenticate::class,
        'bindings' => SubstituteBindings::class,
        'throttle' => ThrottleRequests::class,
        'jwt.auth.unprotected' => JWTGetUserFromTokenUnprotectedRouteMiddleware::class,
        'jwt.auth.protected' => JWTGetUserFromTokenProtectedRouteMiddleware::class,
    ];
}
