<?php
declare(strict_types=1);

namespace App\Http\V1\Controllers;

use App\Contracts\Repositories\ResourceRepositoryContract;
use App\Http\V1\Controllers\Traits\HasIndexRequests;
use App\Http\V1\Requests;

/**
 * Class ResourceController
 * @package App\Http\V1\Controllers
 */
class ResourceController extends BaseControllerAbstract
{
    use HasIndexRequests;

    /**
     * @var ResourceRepositoryContract
     */
    private $repository;

    /**
     * ResourcesController constructor.
     * @param ResourceRepositoryContract $repository
     */
    public function __construct(ResourceRepositoryContract $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Requests\Resource\IndexRequest $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Requests\Resource\IndexRequest $request)
    {
        return $this->repository->findAll($this->filter($request), $this->search($request), $this->order($request), $this->expand($request), $this->limit($request), [], (int)$request->input('page', 1));
    }
}