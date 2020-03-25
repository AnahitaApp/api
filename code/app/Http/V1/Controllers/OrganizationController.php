<?php
declare(strict_types=1);

namespace App\Http\V1\Controllers;

use App\Contracts\Repositories\Organization\OrganizationRepositoryContract;
use App\Http\V1\Controllers\Traits\HasIndexRequests;
use App\Http\V1\Requests;
use App\Models\BaseModelAbstract;
use App\Models\Organization\Organization;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Class OrganizationController
 * @package App\Http\V1\Controllers
 */
class OrganizationController extends BaseControllerAbstract
{
    use HasIndexRequests;

    /**
     * @var OrganizationRepositoryContract
     */
    protected $repository;

    /**
     * OrganizationController constructor.
     * @param OrganizationRepositoryContract $repository
     */
    public function __construct(OrganizationRepositoryContract $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Requests\Organization\IndexRequest $request
     * @return LengthAwarePaginator
     */
    public function index(Requests\Organization\IndexRequest $request)
    {
        return $this->repository->findAll($this->filter($request), $this->search($request), $this->order($request), $this->expand($request), $this->limit($request), [], (int)$request->input('page', 1));
    }

    /**
     * Display the specified resource.
     *
     * @param Requests\Organization\RetrieveRequest $request
     * @param Organization $model
     * @return Organization
     */
    public function show(Requests\Organization\RetrieveRequest $request, Organization $model)
    {
        return $model->load($this->expand($request));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Requests\Organization\StoreRequest $request
     * @return Organization
     */
    public function store(Requests\Organization\StoreRequest $request)
    {
        $model = $this->repository->create($request->json()->all());
        return response($model, 201)->header('Location', route('v1.organizations.show', ['organization' => $model]));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Requests\Organization\UpdateRequest $request
     * @param Organization $model
     * @return BaseModelAbstract
     */
    public function update(Requests\Organization\UpdateRequest $request, Organization $model)
    {
        return $this->repository->update($model, $request->json()->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Requests\Organization\DeleteRequest $request
     * @param Organization $model
     * @return null
     */
    public function destroy(Requests\Organization\DeleteRequest $request, Organization $model)
    {
        $this->repository->delete($model);
        return response(null, 204);
    }
}