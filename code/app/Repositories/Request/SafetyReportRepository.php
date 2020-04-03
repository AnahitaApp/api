<?php
declare(strict_types=1);

namespace App\Contracts\Repositories\Request;

use App\Models\Request\SafetyReport;
use App\Repositories\BaseRepositoryAbstract;
use Psr\Log\LoggerInterface as LogContract;

/**
 * Class SafetyReportRepository
 * @package App\Contracts\Repositories\Request
 */
class SafetyReportRepository extends BaseRepositoryAbstract implements SafetyReportRepositoryContract
{
    /**
     * SafetyReportRepository constructor.
     * @param SafetyReport $model
     * @param LogContract $log
     */
    public function __construct(SafetyReport $model, LogContract $log)
    {
        parent::__construct($model, $log);
    }
}