<?php
// Include this file in any group where the model in the route implements the contract IsAnEntity
// All routes here will then automatically be functional.

Route::resource('assets', 'Entity\AssetController', [
    'only' => [
        'index', 'store', 'update', 'destroy',
    ],
]);

Route::resource('payment-methods', 'Entity\PaymentMethodController', [
    'only' => [
        'store', 'destroy',
    ],
]);
Route::resource('profile-images', 'Entity\ProfileImageController', [
    'only' => [
        'store',
    ],
]);
Route::resource('subscriptions', 'Entity\SubscriptionController', [
    'only' => [
        'store', 'update',
    ],
]);