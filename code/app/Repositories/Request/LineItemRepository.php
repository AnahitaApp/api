<?php
declare(strict_types=1);

namespace App\Repositories\Request;
class LineItemRepository extends \App\Repositories\BaseRepositoryAbstract implements LineItemRepositoryContract
{
    public function __construct(LineItem $model, LogContract $log)
    {
        parent::__construct($model, $log);
    }
}