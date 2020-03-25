<?php
declare(strict_types=1);

namespace App\Repositories\Subscription;

use App\Contracts\Repositories\Subscription\MembershipPlanRateRepositoryContract;
use App\Models\Subscription\MembershipPlanRate;
use App\Repositories\BaseRepositoryAbstract;
use Psr\Log\LoggerInterface as LogContract;

/**
 * Class MembershipPlanRateRepository
 * @package App\Repositories\Subscription
 */
class MembershipPlanRateRepository extends BaseRepositoryAbstract implements MembershipPlanRateRepositoryContract
{
    /**
     * MembershipPlanRateRepository constructor.
     * @param MembershipPlanRate $model
     * @param LogContract $log
     */
    public function __construct(MembershipPlanRate $model, LogContract $log)
    {
        parent::__construct($model, $log);
    }
}