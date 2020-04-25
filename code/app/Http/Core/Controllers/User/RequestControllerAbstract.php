<?php
declare(strict_types=1);

namespace App\Http\Core\Controllers\User;

use App\Contracts\Repositories\Request\RequestRepositoryContract;
use App\Http\Core\Controllers\BaseControllerAbstract;
use App\Http\Core\Controllers\Traits\HasIndexRequests;
use App\Http\Core\Requests;
use App\Models\User\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Class RequestControllerAbstract
 * @package App\Http\Core\Controllers\User
 */
abstract class RequestControllerAbstract extends BaseControllerAbstract
{
    use HasIndexRequests;

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
     * @param Requests\User\Request\IndexRequest $request
     * @param User $user
     * @return LengthAwarePaginator|Collection
     */
    public function index(Requests\User\Request\IndexRequest $request, User $user)
    {
        $filters = $this->filter($request);
        $filters[] = [
            'requested_by_id',
            '=',
            $user->id,
        ];
        return $this->repository->findAll($filters, $this->search($request), $this->order($request), $this->expand($request), $this->limit($request), [], (int)$request->input('page', 1));
    }
}