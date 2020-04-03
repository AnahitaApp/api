<?php
declare(strict_types=1);

namespace App\Providers;

use App\Contracts\Repositories\Payment\LineItemRepositoryContract;
use App\Contracts\Repositories\Payment\PaymentMethodRepositoryContract;
use App\Contracts\Repositories\Payment\PaymentRepositoryContract;
use App\Contracts\Repositories\User\UserRepositoryContract;
use App\Contracts\Services\ArticleVersionCalculationServiceContract;
use App\Contracts\Services\StringHelperServiceContract;
use App\Contracts\Services\StripeCustomerServiceContract;
use App\Contracts\Services\StripePaymentServiceContract;
use App\Contracts\Services\TokenGenerationServiceContract;
use App\Services\ArticleVersionCalculationService;
use App\Services\StringHelperService;
use App\Services\StripeCustomerService;
use App\Services\StripePaymentService;
use App\Services\TokenGenerationService;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use Laracasts\Generators\GeneratorsServiceProvider;

/**
 * Class AppServiceProvider
 * @package App\Providers
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * @return array
     */
    public function provides()
    {
        return [
            ArticleVersionCalculationServiceContract::class,
            StringHelperServiceContract::class,
            StripeCustomerServiceContract::class,
            StripePaymentServiceContract::class,
            TokenGenerationServiceContract::class,
        ];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerEnvironmentSpecificProviders();

        $this->app->bind(ArticleVersionCalculationServiceContract::class, function () {
            return new ArticleVersionCalculationService();
        });
        $this->app->bind(StringHelperServiceContract::class, function () {
            return new StringHelperService();
        });
        $this->app->bind(StripeCustomerServiceContract::class, function () {
            return new StripeCustomerService(
                $this->app->make(UserRepositoryContract::class),
                $this->app->make(PaymentMethodRepositoryContract::class),
                $this->app->make('stripe')->customers(),
                $this->app->make('stripe')->cards(),
            );
        });
        $this->app->bind(StripePaymentServiceContract::class, function () {
            $stripe = $this->app->make('stripe');
            return new StripePaymentService(
                $this->app->make(PaymentRepositoryContract::class),
                $this->app->make(LineItemRepositoryContract::class),
                $this->app->make(Dispatcher::class),
                $stripe->charges(),
                $stripe->refunds(),
            );
        });
        $this->app->bind(TokenGenerationServiceContract::class, function() {
            return new TokenGenerationService();
        });
    }

    /**
     * Registers any environment specific rpviders
     */
    public function registerEnvironmentSpecificProviders()
    {
        if ($this->app->environment() == 'local') {
            $this->app->register(GeneratorsServiceProvider::class);
            $this->app->register(IdeHelperServiceProvider::class);
        }
    }
}
