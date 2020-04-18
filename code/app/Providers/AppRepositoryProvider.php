<?php
declare(strict_types=1);

namespace App\Providers;

use App\Contracts\Repositories\Request\RequestedItemRepositoryContract;
use App\Contracts\Repositories\Request\RequestRepositoryContract;
use App\Contracts\Repositories\Request\SafetyReportRepositoryContract;
use App\Contracts\Repositories\User\IdentificationCardRepositoryContract;
use App\Models\Request\Request;
use App\Models\Request\RequestedItem;
use App\Models\Request\SafetyReport;
use App\Models\User\IdentificationCard;
use App\Repositories\Request\RequestedItemRepository;
use App\Repositories\Request\RequestRepository;
use App\Repositories\Request\SafetyReportRepository;
use App\Repositories\User\IdentificationCardRepository;

/**
 * Class AppRepositoryProvider
 * @package App\Providers
 */
class AppRepositoryProvider extends AtheniaRepositoryProvider
{
    /**
     * All app specific repositories that are provided here
     *
     * @return array
     */
    public function appProviders(): array
    {
        return [
            IdentificationCardRepositoryContract::class,
            RequestRepositoryContract::class,
            RequestedItemRepositoryContract::class,
            SafetyReportRepositoryContract::class,
        ];
    }

    /**
     * Gets all morph maps application specific
     *
     * @return array
     */
    public function appMorphMaps(): array
    {
        return [];
    }

    /**
     * Runs any app specific registrations
     *
     * @return mixed
     */
    public function registerApp()
    {
        $this->app->bind(IdentificationCardRepositoryContract::class, function() {
            return new IdentificationCardRepository(
                new IdentificationCard(),
                $this->app->make('log'),
                $this->app->make('filesystem')->disk('local'),
                storage_path(),
                "identification"
            );
        });
        $this->app->bind(RequestRepositoryContract::class, function() {
            return new RequestRepository(
                new Request(),
                $this->app->make('log'),
                $this->app->make(RequestedItemRepositoryContract::class),
            );
        });
        $this->app->bind(RequestedItemRepositoryContract::class, function() {
            return new RequestedItemRepository(
                new RequestedItem(),
                $this->app->make('log'),
            );
        });
        $this->app->bind(SafetyReportRepositoryContract::class, function() {
            return new SafetyReportRepository(
                new SafetyReport(),
                $this->app->make('log'),
            );
        });
    }
}