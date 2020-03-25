<?php
declare(strict_types=1);

namespace App\Repositories\Payment;

use App\Contracts\Repositories\Payment\PaymentRepositoryContract;
use App\Models\Payment\Payment;
use App\Repositories\BaseRepositoryAbstract;
use Psr\Log\LoggerInterface as LogContract;

/**
 * Class PaymentRepository
 * @package App\Repositories\Payment
 */
class PaymentRepository extends BaseRepositoryAbstract implements PaymentRepositoryContract
{
    /**
     * PaymentRepository constructor.
     * @param Payment $model
     * @param LogContract $log
     */
    public function __construct(Payment $model, LogContract $log)
    {
        parent::__construct($model, $log);
    }
}