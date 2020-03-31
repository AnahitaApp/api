<?php
declare(strict_types=1);

namespace App\Contracts\Repositories\Request;
use App\Repositories\BaseRepositoryAbstract;

class RequestRepository extends BaseRepositoryAbstract implements RequestRepositoryContract
{
    public function __construct(LineItem $model, LogContract $log)
    {
        parent::__construct($model, $log);
    }
}