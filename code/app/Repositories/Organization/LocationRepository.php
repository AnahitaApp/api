<?php
declare(strict_types=1);

namespace App\Repositories\Organization;

use App\Contracts\Repositories\Organization\LocationRepositoryContract;
use App\Models\Organization\Location;
use App\Repositories\BaseRepositoryAbstract;
use App\Repositories\Traits\HasLocationTrait;
use Psr\Log\LoggerInterface as LogContract;

/**
 * Class LocationRepository
 * @package App\Repositories\Organization
 */
class LocationRepository extends BaseRepositoryAbstract implements LocationRepositoryContract
{
    use HasLocationTrait;

    /**
     * LocationRepository constructor.
     * @param Location $model
     * @param LogContract $log
     */
    public function __construct(Location $model, LogContract $log)
    {
        parent::__construct($model, $log);
    }
}