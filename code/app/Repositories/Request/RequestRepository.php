<?php
declare(strict_types=1);

namespace App\Contracts\Repositories\Request;

use App\Models\Request\Request;
use App\Repositories\BaseRepositoryAbstract;
use Psr\Log\LoggerInterface as LogContract;

/**
 * Class RequestRepository
 * @package App\Contracts\Repositories\Request
 */

class RequestRepository extends BaseRepositoryAbstract implements RequestRepositoryContract
{
    public function __construct(Request $model, LogContract $log)
    {
        parent::__construct($model, $log);
    }
}