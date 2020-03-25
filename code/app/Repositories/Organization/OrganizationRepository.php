<?php
declare(strict_types=1);

namespace App\Repositories\Organization;

use App\Contracts\Repositories\Organization\OrganizationRepositoryContract;
use App\Models\Organization\Organization;
use App\Repositories\BaseRepositoryAbstract;
use Psr\Log\LoggerInterface as LogContract;

/**
 * Class OrganizationRepository
 * @package App\Repositories\Organization
 */
class OrganizationRepository extends BaseRepositoryAbstract implements OrganizationRepositoryContract
{
    /**
     * OrganizationRepository constructor.
     * @param Organization $model
     * @param LogContract $log
     */
    public function __construct(Organization $model, LogContract $log)
    {
        parent::__construct($model, $log);
    }
}