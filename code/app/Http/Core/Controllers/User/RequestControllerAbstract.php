<?php
declare(strict_types=1);

namespace App\Http\Core\Controllers\User;

use App\Contracts\Repositories\Request\RequestRepositoryContract;
use App\Http\Core\Controllers\BaseControllerAbstract;
use App\Http\Core\Controllers\Traits\HasIndexRequests;
use App\Http\Core\Requests;
use App\Models\BaseModelAbstract;
use App\Models\Request\Request;
use App\Models\User\User;
use App\Traits\CanGetAndUnset;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Class RequestControllerAbstract
 * @package App\Http\Core\Controllers\User
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

    /**
     * @param Requests\User\Request\UpdateRequest $request
     * @param User $user
     * @param Request $requestModel
     * @return BaseModelAbstract
     */
    public function update(Requests\User\Request\UpdateRequest $request, User $user, Request $requestModel)
    {
        $data = $request->json()->all();

        $cancel = (bool) $this->getAndUnset($data, 'cancel', false);
        if ($cancel) {
            $data['canceled_at'] = Carbon::now();
        }

        return $this->repository->update($requestModel, $data);
    }
}