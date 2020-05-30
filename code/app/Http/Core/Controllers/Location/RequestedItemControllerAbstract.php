<?php
declare(strict_types=1);

namespace App\Http\Core\Controllers\Location;

use App\Contracts\Repositories\Request\RequestedItemRepositoryContract;
use App\Http\Core\Controllers\BaseControllerAbstract;
use App\Http\Core\Controllers\Traits\HasIndexRequests;
use App\Http\Core\Requests;
use App\Models\Organization\Location;
use App\Models\Request\RequestedItem;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Class RequestedItemController
 * @package App\Http\Core\Controllers\Location
 */
abstract class RequestedItemControllerAbstract extends BaseControllerAbstract
{
    use HasIndexRequests;

    /**
     * @var RequestedItemRepositoryContract
     */
    private RequestedItemRepositoryContract $repository;

    /**
     * RequestedItemController constructor.
     * @param RequestedItemRepositoryContract $repository
     */
    public function __construct(RequestedItemRepositoryContract $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Requests\Location\RequestedItem\IndexRequest $request
     * @param Location $location
     * @return LengthAwarePaginator
     */
    public function index(Requests\Location\RequestedItem\IndexRequest $request, Location $location)
    {
        return $this->repository->findAll($this->filter($request), $this->search($request), $this->order($request), $this->expand($request), $this->limit($request), [$location], (int)$request->input('page', 1));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Requests\Location\RequestedItem\StoreRequest $request
     * @param Location $location
     * @return RequestedItem
     */
    public function store(Requests\Location\RequestedItem\StoreRequest $request, Location $location)
    {
        $model = $this->repository->create($request->json()->all(), $location);
        return response($model, 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Requests\Location\RequestedItem\UpdateRequest $request
     * @param Location $location
     * @param RequestedItem $model
     * @return \App\Models\BaseModelAbstract
     */
    public function update(Requests\Location\RequestedItem\UpdateRequest $request, Location $location, RequestedItem $model)
    {
        return $this->repository->update($model, $request->json()->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Requests\Location\RequestedItem\DeleteRequest $request
     * @param Location $location
     * @param RequestedItem $model
     * @return null
     */
    public function destroy(Requests\Location\RequestedItem\DeleteRequest $request, Location $location, RequestedItem $model)
    {
        $this->repository->delete($model);
        return response(null, 204);
    }
}