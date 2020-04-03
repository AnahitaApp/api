<?php
declare(strict_types=1);

namespace App\Http\Core\Controllers\User;

use App\Contracts\Repositories\Subscription\SubscriptionRepositoryContract;
use App\Contracts\Services\StripePaymentServiceContract;
use App\Http\Core\Controllers\BaseControllerAbstract;
use App\Http\Core\Requests;
use App\Models\BaseModelAbstract;
use App\Models\Subscription\Subscription;
use App\Models\User\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

/**
 * Class SubscriptionControllerAbstract
 * @package App\Http\Core\Controllers\User
 */
abstract class SubscriptionControllerAbstract extends BaseControllerAbstract
{
    /**
     * @var SubscriptionRepositoryContract
     */
    private $repository;

    /**
     * @var StripePaymentServiceContract
     */
    private $stripeChargeService;

    /**
     * SubscriptionController constructor.
     * @param SubscriptionRepositoryContract $repository
     * @param StripePaymentServiceContract $stripePaymentService
     */
    public function __construct(SubscriptionRepositoryContract $repository,
                                StripePaymentServiceContract $stripePaymentService)
    {
        $this->repository = $repository;
        $this->stripeChargeService = $stripePaymentService;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @SWG\Post(
     *     path="/users/{user_id}/subscriptions",
     *     summary="Create a new Subscription model",
     *     tags={"Users","Subscriptions"},
     *     @SWG\Parameter(ref="#/parameters/AuthorizationHeader"),
     *     @SWG\Parameter(
     *          name="model",
     *          in="body",
     *          required=true,
     *          @SWG\Schema(ref="#/definitions/Subscription"),
     *          description="The model to create"
     *     ),
     *     @SWG\Parameter(
     *          name="user_id",
     *          in="path",
     *          required=true,
     *          type="integer",
     *          format="int32",
     *          description="The ID of the user model"
     *     ),
     *     @SWG\Response(
     *          response=201,
     *          description="Model created successfully",
     *          @SWG\Schema(ref="#/definitions/Subscription"),
     *          @SWG\Header(
     *              header="X-RateLimit-Limit",
     *              description="The number of allowed requests in the period",
     *              type="integer"
     *          ),
     *          @SWG\Header(
     *              header="X-RateLimit-Remaining",
     *              description="The number of remaining requests in the period",
     *              type="integer"
     *          )
     *      ),
     *     @SWG\Response(
     *          response=400,
     *          ref="#/responses/Standard400BadRequestResponse"
     *      ),
     *     @SWG\Response(
     *          response=401,
     *          ref="#/responses/Standard401UnauthorizedResponse"
     *      ),
     *     @SWG\Response(
     *          response="default",
     *          ref="#/responses/Standard500ErrorResponse"
     *      ),
     * )
     *
     * @param Requests\User\Subscription\StoreRequest $request
     * @param User $user
     * @return JsonResponse
     */
    public function store(Requests\User\Subscription\StoreRequest $request, User $user)
    {
        $data = $request->json()->all();

        $data['subscriber_id'] = $user->id;
        $data['subscriber_type'] = 'user';
        /** @var Subscription $model */
        $model = $this->repository->create($data);

        try {
            $this->stripeChargeService->createPayment($user, $model->paymentMethod,
                'Subscription Payment for ' . $model->membershipPlanRate->membershipPlan->name, [
                [
                    'item_id' => $model->id,
                    'item_type' => 'subscription',
                    'amount' => (float)$model->membershipPlanRate->cost,
                ]
            ]);

        } catch (\Exception $e) {
            $this->repository->delete($model);
            throw new ServiceUnavailableHttpException(5, 'Unable to accept payments right now');
        }
        return new JsonResponse($model, 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @SWG\Patch(
     *     path="/users/{user_id}/subscriptions/{id}",
     *     summary="Updates a single user",
     *     tags={"Users", "Subscriptions"},
     *     @SWG\Parameter(ref="#/parameters/AuthorizationHeader"),
     *     @SWG\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          type="integer",
     *          format="int32",
     *          description="The ID of the model"
     *     ),
     *     @SWG\Parameter(
     *          name="user_id",
     *          in="path",
     *          required=true,
     *          type="integer",
     *          format="int32",
     *          description="The ID of the user model"
     *     ),
     *     @SWG\Parameter(
     *          name="model",
     *          in="body",
     *          required=true,
     *          @SWG\Schema(ref="#/definitions/Subscription"),
     *          description="The model updates to make"
     *     ),
     *     @SWG\Response(
     *          response=200,
     *          description="Successful update",
     *          @SWG\Schema(ref="#/definitions/Subscription"),
     *          @SWG\Header(
     *              header="X-RateLimit-Limit",
     *              description="The number of allowed requests in the period",
     *              type="integer"
     *          ),
     *          @SWG\Header(
     *              header="X-RateLimit-Remaining",
     *              description="The number of remaining requests in the period",
     *              type="integer"
     *          )
     *      ),
     *     @SWG\Response(
     *          response=400,
     *          ref="#/responses/Standard400BadRequestResponse"
     *      ),
     *     @SWG\Response(
     *          response=401,
     *          ref="#/responses/Standard401UnauthorizedResponse"
     *      ),
     *     @SWG\Response(
     *          response=404,
     *          ref="#/responses/Standard404ItemNotFoundResponse"
     *      ),
     *     @SWG\Response(
     *          response="default",
     *          ref="#/responses/Standard500ErrorResponse"
     *      ),
     * )
     *
     * @param Requests\User\Subscription\UpdateRequest $request
     * @param User $user
     * @param Subscription $subscription
     * @return Subscription|BaseModelAbstract
     */
    public function update(Requests\User\Subscription\UpdateRequest $request, User $user, Subscription $subscription)
    {
        $data = $request->json()->all();

        if (isset ($data['cancel']) && $data['cancel']) {
            $data['canceled_at'] = Carbon::now();
            unset($data['cancel']);
        }

        return $this->repository->update($subscription, $data);
    }
}