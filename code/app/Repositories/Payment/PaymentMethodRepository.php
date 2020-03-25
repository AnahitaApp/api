<?php
declare(strict_types=1);

namespace App\Repositories\Payment;

use App\Contracts\Repositories\Payment\PaymentMethodRepositoryContract;
use App\Models\Payment\PaymentMethod;
use App\Repositories\BaseRepositoryAbstract;
use Psr\Log\LoggerInterface as LogContract;

/**
 * Class PaymentMethodRepository
 * @package App\Repositories\Payment
 */
class PaymentMethodRepository extends BaseRepositoryAbstract implements PaymentMethodRepositoryContract
{
    /**
     * PaymentMethodRepository constructor.
     * @param PaymentMethod $model
     * @param LogContract $log
     */
    public function __construct(PaymentMethod $model, LogContract $log)
    {
        parent::__construct($model, $log);
    }
}