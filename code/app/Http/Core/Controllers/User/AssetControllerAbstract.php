<?php
declare(strict_types=1);

namespace App\Http\Core\Controllers\User;

use App\Contracts\Repositories\AssetRepositoryContract;
use App\Http\Core\Controllers\BaseControllerAbstract;
use App\Http\Core\Controllers\Traits\HasIndexRequests;
use App\Http\Core\Requests;
use App\Models\Asset;
use App\Models\BaseModelAbstract;
use App\Models\User\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Mimey\MimeTypes;

/**
 * Class AssetControllerAbstract
 * @package App\Http\Core\Controllers\User
 */
abstract class AssetControllerAbstract extends BaseControllerAbstract
{
    use HasIndexRequests;

    /**
     * @var AssetRepositoryContract
     */
    private $repository;

    /**
     * @var MimeTypes
     */
    private $mimeTypes;

    /**
     * AssetController constructor.
     * @param AssetRepositoryContract $repository
     * @param MimeTypes $mimeTypes
     */
    public function __construct(AssetRepositoryContract $repository, MimeTypes $mimeTypes)
    {
        $this->repository = $repository;
        $this->mimeTypes = $mimeTypes;
    }

    /**
     * Gets all assets for a user
     *
     * @param Requests\User\Asset\IndexRequest $request
     * @param User $user
     * @return LengthAwarePaginator
     */
    public function index(Requests\User\Asset\IndexRequest $request, User $user)
    {
        $filter = $this->filter($request);

        $filter[] = [
            'owner_id',
            '=',
            $user->id,
        ];
        $filter[] = [
            'owner_type',
            '=',
            'user',
        ];

        return $this->repository->findAll($filter, $this->search($request), $this->expand($request), $this->order($request), $this->limit($request), [], (int)$request->input('page', 1));
    }

    /**
     * Creates the new asset for us
     *
     * @param Requests\User\Asset\StoreRequest $request
     * @param User $user
     * @return JsonResponse
     */
    public function store(Requests\User\Asset\StoreRequest $request, User $user)
    {
        $data = $request->json()->all();

        $data['file_contents'] = $request->getDecodedContents();
        $data['file_extension'] = $this->mimeTypes->getExtension($request->getFileMimeType());

        $data['owner_id'] = $user->id;
        $data['owner_type'] = 'user';

        $model = $this->repository->create($data);
        return new JsonResponse($model, 201);
    }

    /**
     * Updates an asset properly
     *
     * @param Requests\User\Asset\UpdateRequest $request
     * @param User $user
     * @param Asset $asset
     * @return BaseModelAbstract
     */
    public function update(Requests\User\Asset\UpdateRequest $request, User $user, Asset $asset)
    {
        return $this->repository->update($asset, $request->json()->all());
    }

    /**
     * Deletes an asset from the server
     *
     * @param Requests\User\Asset\DeleteRequest $request
     * @param User $user
     * @param Asset $asset
     * @return ResponseFactory|Response
     */
    public function destroy(Requests\User\Asset\DeleteRequest $request, User $user, Asset $asset)
    {
        $this->repository->delete($asset);
        return response(null, 204);
    }
}