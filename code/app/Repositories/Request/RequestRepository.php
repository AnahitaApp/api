<?php
declare(strict_types=1);

namespace App\Repositories\Request;

use App\Contracts\Repositories\Request\RequestedItemRepositoryContract;
use App\Contracts\Repositories\Request\RequestRepositoryContract;
use App\Models\BaseModelAbstract;
use App\Models\Request\Request;
use App\Repositories\BaseRepositoryAbstract;
use App\Traits\CanGetAndUnset;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Psr\Log\LoggerInterface as LogContract;

/**
 * Class RequestRepositoryTest
 * @package App\Contracts\Repositories\Request
 */
class RequestRepository extends BaseRepositoryAbstract implements RequestRepositoryContract
{
    use CanGetAndUnset;

    /**
     * @var RequestedItemRepositoryContract
     */
    private RequestedItemRepositoryContract $requestedItemRepository;

    /**
     * RequestRepositoryTest constructor.
     * @param Request $model
     * @param LogContract $log
     * @param RequestedItemRepositoryContract $requestedItemRepository
     */
    public function __construct(Request $model, LogContract $log,
                                RequestedItemRepositoryContract $requestedItemRepository)
    {
        parent::__construct($model, $log);
        $this->requestedItemRepository = $requestedItemRepository;
    }

    /**
     * Overrides to sync the requested items properly
     *
     * @param array $data
     * @param BaseModelAbstract|null $relatedModel
     * @param array $forcedValues
     * @return BaseModelAbstract|void
     */
    public function create(array $data = [], BaseModelAbstract $relatedModel = null, array $forcedValues = [])
    {
        $requestedItems = $this->getAndUnset($data, 'requested_items', []);

        $model = parent::create($data, $relatedModel, $forcedValues);

        $this->syncChildModels($this->requestedItemRepository, $model, $requestedItems);

        return $model;
    }

    /**
     * Makes sure to sync requested items properly
     *
     * @param BaseModelAbstract|Request $model
     * @param array $data
     * @param array $forcedValues
     * @return BaseModelAbstract
     */
    public function update(BaseModelAbstract $model, array $data, array $forcedValues = []): BaseModelAbstract
    {
        $requestedItems = $this->getAndUnset($data, 'requested_items', null);

        if ($requestedItems) {
            $this->syncChildModels($this->requestedItemRepository, $model, $requestedItems, $model->requestedItems);
        }

        return parent::update($model, $data, $forcedValues);
    }
    /**
     * Finds all requests around a specific location
     *
     * @param float $latitude
     * @param float $longitude
     * @param float $radius in KM
     * @param array $filters
     * @param array $searches
     * @param array $orderBy
     * @param array $with
     * @param int $limit
     * @param array $belongsToArray
     * @param int $page
     * @return LengthAwarePaginator
     */
    public function findAllAroundLocation(float $latitude, float $longitude, float $radius, array $filters = [], array $searches = [], array $orderBy = [], array $with = [], $limit = 10, array $belongsToArray = [], int $page = 1): LengthAwarePaginator
    {
        $query = parent::buildFindAllQuery($filters, $searches, $orderBy, $with, $belongsToArray);

        $distanceFormula = "( 6371 * acos( cos( radians($latitude) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians($longitude) ) + sin( radians($latitude) ) * sin(radians(latitude)) ) )";
        $query->whereRaw("$distanceFormula < $radius");
        $query->orderByRaw($distanceFormula);

        $query->whereNull('completed_by_id');

        $query->groupBy('requests.id');

        return $query->paginate($limit, $columns = ['*'], $pageName = 'page', $page);
    }
}