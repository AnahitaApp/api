<?php
declare(strict_types=1);

namespace App\Http\V1\Controllers\User;

use App\Contracts\Repositories\User\ContactRepositoryContract;
use App\Events\User\Contact\ContactCreatedEvent;
use App\Http\V1\Controllers\BaseControllerAbstract;
use App\Http\V1\Controllers\Traits\HasIndexRequests;
use App\Http\V1\Requests;
use App\Models\BaseModelAbstract;
use App\Models\User\Contact;
use App\Models\User\User;
use Carbon\Carbon;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

/**
 * Class ContactController
 * @package App\Http\V1\Controllers\Userzz
 */
class ContactController extends BaseControllerAbstract
{
    use HasIndexRequests;

    /**
     * @var ContactRepositoryContract
     */
    private $repository;

    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * ContactController constructor.
     * @param ContactRepositoryContract $repository
     * @param Dispatcher $dispatcher
     */
    public function __construct(ContactRepositoryContract $repository, Dispatcher $dispatcher)
    {
        $this->repository = $repository;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param Requests\User\Contact\IndexRequest $request
     * @param User $user
     * @return LengthAwarePaginator
     */
    public function index(Requests\User\Contact\IndexRequest $request, User $user)
    {
        return $this->repository->findAll($this->filter($request), $this->search($request), $this->order($request), $this->expand($request), $this->limit($request), [$user], (int)$request->input('page', 1));
    }

    /**
     * @param Requests\User\Contact\StoreRequest $request
     * @param User $user
     * @return JsonResponse
     */
    public function store(Requests\User\Contact\StoreRequest $request, User $user)
    {
        $data = $request->json()->all();

        $data['initiated_by_id'] = $user->id;

        /** @var Contact $model */
        $model = $this->repository->create($data);

        $this->dispatcher->dispatch(new ContactCreatedEvent($model));

        return new JsonResponse($model, 201);
    }

    /**
     * Updates an event participant, mostly used to link assets
     *
     * @param Requests\User\Contact\UpdateRequest $request
     * @param User $user
     * @param Contact $contact
     * @return BaseModelAbstract
     * @throws \Exception
     */
    public function update(Requests\User\Contact\UpdateRequest $request, User $user, Contact $contact)
    {
        $requestData = $request->json()->all();

        $data = [];

        if (isset($requestData['confirm'])) {
            $data['confirmed_at'] = new Carbon();
        }
        if (isset($requestData['deny'])) {
            $data['denied_at'] = new Carbon();
        }

        return $this->repository->update($contact, $data);
    }
}