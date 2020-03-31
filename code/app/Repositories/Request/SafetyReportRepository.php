<?php
declare(strict_types=1);

namespace App\Contracts\Repositories\Request;

use App\Models\BaseModelAbstract;
use App\Models\Request\SafetyReport;
use App\Models\User\Contact;
use Psr\Log\LoggerInterface as LogContract;

/**
 * Class SafetyReportRepository
 * @package App\Contracts\Repositories\Request
 */
class SafetyReportRepository extends BaseModelAbstract implements SafetyReportRepositoryContract
{
    public function __construct(SafetyReport $model, LogContract $log)
    {
        parent::__construct($model, $log);
    }
}