<?php
declare(strict_types=1);

namespace App\Repositories\Subscription;

use App\Contracts\Repositories\Subscription\MembershipPlanRateRepositoryContract;
use App\Contracts\Repositories\Subscription\MembershipPlanRepositoryContract;
use App\Models\BaseModelAbstract;
use App\Models\Subscription\MembershipPlan;
use App\Repositories\BaseRepositoryAbstract;
use App\Traits\CanGetAndUnset;
use Psr\Log\LoggerInterface as LogContract;

/**
 * Class MembershipPlanRepository
 * @package App\Repositories\Subscription
 */
class MembershipPlanRepository extends BaseRepositoryAbstract implements MembershipPlanRepositoryContract
{
    use CanGetAndUnset;

    /**
     * @var MembershipPlanRateRepositoryContract
     */
    private $membershipPlanRateRepository;

    /**
     * MembershipPlanRepository constructor.
     * @param MembershipPlan $model
     * @param LogContract $log
     * @param MembershipPlanRateRepositoryContract $membershipPlanRateRepository
     */
    public function __construct(MembershipPlan $model, LogContract $log,
                                MembershipPlanRateRepositoryContract $membershipPlanRateRepository)
    {
        parent::__construct($model, $log);
        $this->membershipPlanRateRepository = $membershipPlanRateRepository;
    }

    /**
     * Overrides the create in order to create the current rate
     *
     * @param array $data
     * @param BaseModelAbstract|null $relatedModel
     * @param array $forcedValues
     * @return BaseModelAbstract
     */
    public function create(array $data = [], BaseModelAbstract $relatedModel = null, array $forcedValues = [])
    {
        $cost = $this->getAndUnset($data, 'current_cost');
        $model = parent::create($data, $relatedModel, $forcedValues);

        if ($cost) {
            $this->membershipPlanRateRepository->create([
                'cost' => $cost,
                'active' => true,
            ], $model);
        }

        return $model;
    }

    /**
     * @param MembershipPlan|BaseModelAbstract $model
     * @param array $data
     * @param array $forcedValues
     * @return BaseModelAbstract
     */
    public function update(BaseModelAbstract $model, array $data, array $forcedValues = []): BaseModelAbstract
    {
        $cost = $this->getAndUnset($data, 'current_cost');

        if ($cost && $cost != $model->current_cost) {

            foreach ($model->membershipPlanRates as $membershipPlanRate) {
                $this->membershipPlanRateRepository->update($membershipPlanRate, [
                    'active' => false,
                ]);
            }

            $this->membershipPlanRateRepository->create([
                'cost' => $cost,
                'active' => true,
            ], $model);
        }

        return parent::update($model, $data, $forcedValues);
    }
}