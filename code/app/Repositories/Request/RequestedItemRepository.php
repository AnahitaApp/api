<?php
declare(strict_types=1);

namespace App\Repositories\Request;

use App\Contracts\Repositories\Request\RequestedItemRepositoryContract;
use App\Models\Request\RequestedItem;
use App\Repositories\BaseRepositoryAbstract;
use Psr\Log\LoggerInterface as LogContract;

/**
 * Class RequestedItemRepository
 * @package App\Repositories\Request
 */
class RequestedItemRepository extends BaseRepositoryAbstract implements RequestedItemRepositoryContract
{
    /**
     * RequestedItemRepository constructor.
     * @param RequestedItem $model
     * @param LogContract $log
     */
    public function __construct(RequestedItem $model, LogContract $log)
    {
        parent::__construct($model, $log);
    }
}