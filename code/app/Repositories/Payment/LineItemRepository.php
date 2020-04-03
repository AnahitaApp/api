<?php
declare(strict_types=1);

namespace App\Repositories\Payment;

use App\Contracts\Repositories\Payment\LineItemRepositoryContract;
use App\Models\Payment\LineItem;
use App\Repositories\BaseRepositoryAbstract;
use Psr\Log\LoggerInterface as LogContract;

/**
 * Class LineItemRepository
 * @package App\Repositories\Payment
 */
class LineItemRepository extends BaseRepositoryAbstract implements LineItemRepositoryContract
{
    /**
     * LineItemRepository constructor.
     * @param LineItem $model
     * @param LogContract $log
     */
    public function __construct(LineItem $model, LogContract $log)
    {
        parent::__construct($model, $log);
    }
}