<?php
declare(strict_types=1);

namespace App\Repositories\Request;

use App\Contracts\Repositories\Request\RequestRepositoryContract;
use App\Models\Request\Request;
use App\Repositories\BaseRepositoryAbstract;
use Psr\Log\LoggerInterface as LogContract;

/**
 * Class RequestRepositoryTest
 * @package App\Contracts\Repositories\Request
 */
class RequestRepository extends BaseRepositoryAbstract implements RequestRepositoryContract
{
    /**
     * RequestRepositoryTest constructor.
     * @param Request $model
     * @param LogContract $log
     */
    public function __construct(Request $model, LogContract $log)
    {
        parent::__construct($model, $log);
    }
}