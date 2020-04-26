<?php
/**
 * V1 Routes!!
 *
 * loaded by RouteServiceProvider within a group, which
 * is assigned the "api-v1" middleware group
 */
declare(strict_types=1);

/******************************************************
 * API v1 routes
 ******************************************************/
Route::group(['prefix' => 'v1', 'as' => 'v1.'], function() {

    include 'core.php';

    /**
     * Routes that are available to the public
     */
    Route::group(['middleware' => 'jwt.auth.unprotected'], function() {
        // add open routes below
    });

    /**
     * Routes that a user needs to be authenticated for in order to access
     */
    Route::group(['middleware' => 'jwt.auth.protected'], function() {
        // Add protected routes below
        /**
         * Request Context
         */
        Route::resource('requests', 'RequestController', [
            'except' => [
                'create', 'edit', 'destroy'
            ]
        ]);
        Route::group(['prefix' => 'requests/{request}', 'as' => 'request.'], function () {

            Route::resource('safety-reports', 'Request\SafetyReportController', [
                'only' => [
                    'store'
                ]
            ]);
        });

        /**
         * User Context
         */
        Route::group(['prefix' => 'users/{user}', 'as' => 'user.'], function () {
            Route::resource('requests', 'User\RequestController', [
                'only' => [
                    'index', 'update',
                ],
            ]);
        });
    });
});
