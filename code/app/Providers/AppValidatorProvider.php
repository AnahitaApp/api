<?php
declare(strict_types=1);

namespace App\Providers;

use App\Validators\ArticleVersion\SelectedIterationBelongsToArticleValidator;
use App\Validators\ForgotPassword\TokenIsNotExpiredValidator;
use App\Validators\ForgotPassword\UserOwnsTokenValidator;
use App\Validators\Location\UserCanAccessLocationValidator;
use App\Validators\NotPresentValidator;
use App\Validators\Request\LocationRequestedItemHasQuantityBelowMaxValidator;
use App\Validators\Request\LocationRequestedItemHasRemainingQuantityValidator;
use App\Validators\Request\RequestNotAcceptedValidator;
use App\Validators\Request\RequestNotCanceledValidator;
use App\Validators\Subscription\MembershipPlanRateIsActiveValidator;
use App\Validators\Subscription\PaymentMethodIsOwnedByUserValidator;
use Illuminate\Contracts\Validation\Factory;
use Illuminate\Support\ServiceProvider;

/**
 * Class AppValidatorProvider
 * @package App\Providers
 */
class AppValidatorProvider extends ServiceProvider
{
    /**
     * Registers all application validators
     */
    public function boot()
    {
        /** @var Factory $validator */
        $validator = $this->app->make(Factory::class);

        $validator->extend('token_is_not_expired', TokenIsNotExpiredValidator::class);
        $validator->extend('user_owns_token', UserOwnsTokenValidator::class);
        $validator->extend('not_present', NotPresentValidator::class);
        $validator->extend(MembershipPlanRateIsActiveValidator::KEY, MembershipPlanRateIsActiveValidator::class);
        $validator->extend(PaymentMethodIsOwnedByUserValidator::KEY, PaymentMethodIsOwnedByUserValidator::class);
        $validator->extend(SelectedIterationBelongsToArticleValidator::KEY, SelectedIterationBelongsToArticleValidator::class);
        $validator->extend(RequestNotAcceptedValidator::KEY, RequestNotAcceptedValidator::class);
        $validator->extend(RequestNotCanceledValidator::KEY, RequestNotCanceledValidator::class);
        $validator->extend(UserCanAccessLocationValidator::KEY, UserCanAccessLocationValidator::class);
        $validator->extend(LocationRequestedItemHasRemainingQuantityValidator::KEY, LocationRequestedItemHasRemainingQuantityValidator::class);
        $validator->extend(LocationRequestedItemHasQuantityBelowMaxValidator::KEY, LocationRequestedItemHasQuantityBelowMaxValidator::class);
    }
}