<?php
declare(strict_types=1);

namespace App\Repositories\Request;

use App\Contracts\Repositories\Request\LineItemRepositoryContract;
use App\Models\Request\LineItem;
use Psr\Log\LoggerInterface as LogContract;

/**
 * Class LineItemRepository
 * @package App\Repositories\Request
 */
class LineItemRepository extends \App\Repositories\BaseRepositoryAbstract implements LineItemRepositoryContract
{
    public function __construct(LineItem $model, LogContract $log)
    {
        parent::__construct($model, $log);
    }
}