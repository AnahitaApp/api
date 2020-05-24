<?php
declare(strict_types=1);

namespace App\Http\Core\Controllers\Organization;

use App\Contracts\Repositories\Organization\LocationRepositoryContract;
use App\Contracts\Repositories\User\UserRepositoryContract;
use App\Http\Core\Controllers\BaseControllerAbstract;
use App\Http\Core\Controllers\Traits\HasIndexRequests;
use App\Http\Core\Requests;
use App\Models\BaseModelAbstract;
use App\Models\Organization\Organization;
use App\Models\Organization\Location;
use App\Traits\CanGetAndUnset;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Class LocationControllerAbstract
 * @package App\Http\Core\Controllers\Organization
 */
abstract class LocationControllerAbstract extends BaseControllerAbstract
{
    use HasIndexRequests, CanGetAndUnset;

    /**
     * @var LocationRepositoryContract
     */
    private $repository;

    /**
     * @var UserRepositoryContract
     */
    private $userRepository;

    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * OrganizationController constructor.
     * @param LocationRepositoryContract $repository
     * @param UserRepositoryContract $userRepository
     * @param Dispatcher $dispatcher
     */
    public function __construct(LocationRepositoryContract $repository,
                                UserRepositoryContract $userRepository,
                                Dispatcher $dispatcher)
    {
        $this->repository = $repository;
        $this->userRepository = $userRepository;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Requests\Organization\Location\IndexRequest $request
     * @param Organization $organization
     * @return LengthAwarePaginator
     */
    public function index(Requests\Organization\Location\IndexRequest $request, Organization $organization)
    {
        return $this->repository->findAll($this->filter($request), $this->search($request), $this->order($request), $this->expand($request), $this->limit($request), [$organization], (int)$request->input('page', 1));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Requests\Organization\Location\StoreRequest $request
     * @param Organization $organization
     * @return Location
     */
    public function store(Requests\Organization\Location\StoreRequest $request, Organization $organization)
    {
        return response($this->repository->create($request->json()->all(), $organization), 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Requests\Organization\Location\UpdateRequest $request
     * @param Organization $organization
     * @param Location $model
     * @return BaseModelAbstract
     */
    public function update(Requests\Organization\Location\UpdateRequest $request, Organization $organization, Location $model)
    {
        return $this->repository->update($model, $request->json()->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Requests\Organization\Location\DeleteRequest $request
     * @param Organization $organization
     * @param Location $model
     * @return null
     */
    public function destroy(Requests\Organization\Location\DeleteRequest $request, Organization $organization, Location $model)
    {
        $this->repository->delete($model);
        return response(null, 204);
    }
}