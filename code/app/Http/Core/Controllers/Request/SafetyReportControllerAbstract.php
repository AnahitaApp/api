<?php
declare(strict_types=1);

namespace App\Http\Core\Controllers\Request;

use App\Contracts\Repositories\Request\SafetyReportRepositoryContract;
use App\Http\Core\Controllers\BaseControllerAbstract;
use App\Http\Core\Requests;
use App\Models\Request\Request;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * Class SafetyReportControllerAbstract
 * @package App\Http\Core\Controllers\Request
 */
abstract class SafetyReportControllerAbstract extends BaseControllerAbstract
{
    /**
     * @var SafetyReportRepositoryContract
     */
    private SafetyReportRepositoryContract $repository;

    /**
     * SafetyReportControllerAbstract constructor.
     * @param SafetyReportRepositoryContract $repository
     */
    public function __construct(SafetyReportRepositoryContract $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Requests\Request\SafetyReport\StoreRequest $request
     * @param Request $requestModel
     * @return ResponseFactory|Response
     */
    public function store(Requests\Request\SafetyReport\StoreRequest $request, Request $requestModel)
    {
        $data = $request->json()->all();
        $data['reporter_id'] = Auth::user()->id;
        $model = $this->repository->create($data, $requestModel);
        return response($model, 201);
    }
}