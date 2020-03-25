<?php
declare(strict_types=1);

namespace App\Http\V1\Controllers\User;

use App\Contracts\Repositories\User\ThreadRepositoryContract;
use App\Http\V1\Controllers\BaseControllerAbstract;
use App\Http\V1\Controllers\Traits\HasIndexRequests;
use App\Http\V1\Requests;
use App\Models\User\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

/**
 * Class ThreadController
 * @package App\Http\V1\Controllers\User
 */
class ThreadController extends BaseControllerAbstract
{
    use HasIndexRequests;

    /**
     * @var ThreadRepositoryContract
     */
    private $repository;

    /**
     * ThreadController constructor.
     * @param ThreadRepositoryContract $repository
     */
    public function __construct(ThreadRepositoryContract $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Requests\User\Thread\IndexRequest $request
     * @param User $user
     * @return LengthAwarePaginator
     */
    public function index(Requests\User\Thread\IndexRequest $request, User $user)
    {
        return $this->repository->findAll($this->filter($request), $this->search($request), $this->order($request), $this->expand($request), $this->limit($request), [$user], (int)$request->input('page', 1));
    }

    /**
     * @param Requests\User\Thread\StoreRequest $request
     * @param User $user
     * @return JsonResponse
     */
    public function store(Requests\User\Thread\StoreRequest $request, User $user) : JsonResponse
    {
        $data = $request->json()->all();
        $data['users'][] = $user->id;

        return new JsonResponse($this->repository->create($data), 201);
    }
}