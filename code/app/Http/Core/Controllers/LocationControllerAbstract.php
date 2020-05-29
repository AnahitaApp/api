<?php
declare(strict_types=1);

namespace App\Http\Core\Controllers;

use App\Contracts\Repositories\Organization\LocationRepositoryContract;
use App\Http\Core\Controllers\Traits\HasIndexRequests;
use App\Http\Core\Requests;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Class LocationControllerAbstract
 * @package App\Http\Core\Controllers
 */
abstract class LocationControllerAbstract extends BaseControllerAbstract
{
    use HasIndexRequests;

    /**
     * @var LocationRepositoryContract
     */
    protected $repository;

    /**
     * LocationControllerAbstract constructor.
     * @param LocationRepositoryContract $repository
     */
    public function __construct(LocationRepositoryContract $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @SWG\Get(
     *     path="/locations",
     *     summary="Get all locations",
     *     tags={"Locations"},
     *     @SWG\Parameter(ref="#/parameters/AuthorizationHeader"),
     *     @SWG\Parameter(ref="#/parameters/PaginationPage"),
     *     @SWG\Parameter(ref="#/parameters/PaginationLimit"),
     *     @SWG\Parameter(ref="#/parameters/SearchParameter"),
     *     @SWG\Parameter(ref="#/parameters/FilterParameter"),
     *     @SWG\Parameter(ref="#/parameters/ExpandParameter"),
     *     @SWG\Response(
     *          response=200,
     *          description="Returns a collection of the model",
     *          @SWG\Schema(ref="#/definitions/PagedMembershipPlans"),
     *          @SWG\Header(
     *              header="X-RateLimit-Limit",
     *              description="The number of allowed requests in the period",
     *              type="integer"
     *          ),
     *          @SWG\Header(
     *              header="X-RateLimit-Remaining",
     *              description="The number of remaining requests in the period",
     *              type="integer"
     *          )
     *      ),
     *     @SWG\Response(
     *          response=400,
     *          ref="#/responses/Standard400BadRequestResponse"
     *      ),
     *     @SWG\Response(
     *          response=401,
     *          ref="#/responses/Standard401UnauthorizedResponse"
     *      ),
     *     @SWG\Response(
     *          response=404,
     *          ref="#/responses/Standard404PagingRequestTooLarge"
     *      ),
     *     @SWG\Response(
     *          response="default",
     *          ref="#/responses/Standard500ErrorResponse"
     *      ),
     * )
     * @SWG\Definition(
     *     definition="PagedBundles",
     *     allOf={
     *          @SWG\Schema(ref="#/definitions/GiftPackBundles"),
     *          @SWG\Schema(ref="#/definitions/Paging")
     *     }
     * )
     * @SWG\Definition(
     *     definition="MembershipPlans",
     *     @SWG\Property(
     *          property="data",
     *          type="array",
     *          minItems=0,
     *          maxItems=100,
     *          uniqueItems=true,
     *          @SWG\Items(ref="#/definitions/MembershipPlan")
     *     )
     * )
     *
     * @param Requests\Location\IndexRequest $request
     * @return LengthAwarePaginator
     */
    public function index(Requests\Location\IndexRequest $request)
    {
        $radius = $request->input('radius');
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');

        if ($radius && $latitude && $longitude) {
            $filter = $this->filter($request);
            $filter [] = [
                'completed_by_id',
                null,
                null,
            ];
            $filter [] = [
                'canceled_at',
                null,
                null,
            ];
            return $this->repository->findAllAroundLocation((float) $latitude, (float) $longitude, (float) $radius, $filter, $this->search($request), $this->order($request), $this->expand($request), $this->limit($request), [], (int)$request->input('page', 1));
        }

        return $this->repository->findAll($this->filter($request), $this->search($request), $this->order($request), $this->expand($request), $this->limit($request), [], (int)$request->input('page', 1));
    }
}