<?php
declare(strict_types=1);

namespace App\Contracts\Repositories\Request;
use App\Models\BaseModelAbstract;

class SafetyReportRepository extends BaseModelAbstract implements SafetyReportRepositoryContract
{
    public function __construct(LineItem $model, LogContract $log)
    {
        parent::__construct($model, $log);
    }
}