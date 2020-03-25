<?php
declare(strict_types=1);

namespace App\Repositories\Organization;

use App\Contracts\Repositories\Organization\OrganizationManagerRepositoryContract;
use App\Models\Organization\OrganizationManager;
use App\Repositories\BaseRepositoryAbstract;
use Psr\Log\LoggerInterface as LogContract;

/**
 * Class OrganizationManagerRepository
 * @package App\Repositories\Organization
 */
class OrganizationManagerRepository extends BaseRepositoryAbstract implements OrganizationManagerRepositoryContract
{
    /**
     * OrganizationManagerRepository constructor.
     * @param OrganizationManager $model
     * @param LogContract $log
     */
    public function __construct(OrganizationManager $model, LogContract $log)
    {
        parent::__construct($model, $log);
    }
}