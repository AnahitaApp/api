<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\RoleRepositoryContract;
use App\Models\Role;
use App\Repositories\Traits\NotImplemented;
use Psr\Log\LoggerInterface as LogContract;

/**
 * Class RoleRepository
 * @package App\Repositories
 */
class RoleRepository extends BaseRepositoryAbstract implements RoleRepositoryContract
{
    use NotImplemented\Create,
        NotImplemented\Update,
        NotImplemented\FindOrFail,
        NotImplemented\Delete;

    /**
     * RoleRepository constructor.
     * @param Role $role
     * @param LogContract $log
     */
    public function __construct(Role $role, LogContract $log)
    {
        parent::__construct($role, $log);
    }
}