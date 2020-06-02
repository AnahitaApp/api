<?php
declare(strict_types=1);

namespace App\Http\Core\Controllers\Location;

use App\Contracts\Repositories\Request\RequestRepositoryContract;
use App\Http\Core\Controllers\BaseControllerAbstract;
use App\Http\Core\Controllers\Traits\HasIndexRequests;
use App\Http\Core\Requests;
use App\Models\BaseModelAbstract;
use App\Models\Organization\Location;
use App\Models\Request\Request;
use App\Models\User\User;
use App\Traits\CanGetAndUnset;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Class RequestControllerAbstract
 * @package App\Http\Core\Controllers\Location
 */
abstract class RequestControllerAbstract extends BaseControllerAbstract
{
    use HasIndexRequests, CanGetAndUnset;

    /**
     * @var RequestRepositoryContract
     */
    protected RequestRepositoryContract $repository;

    /**
     * RequestControllerAbstract constructor.
     * @param RequestRepositoryContract $repository
     */
    public function __construct(RequestRepositoryContract $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Requests\Location\Request\IndexRequest $request
     * @param Location $location
     * @return LengthAwarePaginator|Collection
     */
    public function index(Requests\Location\Request\IndexRequest $request, Location $location)
    {
        return $this->repository->findAll($this->filter($request), $this->search($request), $this->order($request), $this->expand($request), $this->limit($request), [$location], (int)$request->input('page', 1));
    }

    /**
     * @param Requests\Location\Request\UpdateRequest $request
     * @param User $user
     * @param Request $requestModel
     * @return BaseModelAbstract
     */
    public function update(Requests\Location\Request\UpdateRequest $request, User $user, Request $requestModel)
    {
        return $this->repository->update($requestModel, $request->json()->all());
    }
}