<?php
declare(strict_types=1);

namespace App\Http\V1\Controllers\User;

use App\Contracts\Repositories\AssetRepositoryContract;
use App\Http\V1\Controllers\BaseControllerAbstract;
use App\Http\V1\Controllers\Traits\HasIndexRequests;
use App\Http\V1\Requests;
use App\Models\Asset;
use App\Models\BaseModelAbstract;
use App\Models\User\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Mimey\MimeTypes;

/**
 * Class AssetController
 * @package App\Http\V1\Controllers\User
 */
class AssetController extends BaseControllerAbstract
{
    use HasIndexRequests;

    /**
     * @var AssetRepositoryContract
     */
    private $repository;

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
        return $this->repository->findAll($this->filter($request), $this->search($request), $this->expand($request), $this->order($request), $this->limit($request), [$user], (int)$request->input('page', 1));
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

        $model = $this->repository->create($data, $user);
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