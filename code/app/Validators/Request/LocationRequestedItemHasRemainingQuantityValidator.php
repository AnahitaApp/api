<?php
declare(strict_types=1);

namespace App\Validators\Request;

use App\Contracts\Repositories\Request\RequestedItemRepositoryContract;
use App\Models\Request\RequestedItem;
use App\Validators\BaseValidatorAbstract;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

/**
 * Class LocationRequestedItemHasRemainingQuantityValidator
 * @package App\Validators\Request
 */
class LocationRequestedItemHasRemainingQuantityValidator extends BaseValidatorAbstract
{
    /**
     * @var string
     */
    const KEY = 'location_requested_item_has_remaining_quantity';

    /**
     * @var RequestedItemRepositoryContract
     */
    private RequestedItemRepositoryContract $requestedItemRepository;

    /**
     * @var Request
     */
    private Request $request;

    /**
     * LocationRequestedItemHasRemainingQuantityValidator constructor.
     * @param RequestedItemRepositoryContract $requestedItemRepository
     * @param Request $request
     */
    public function __construct(RequestedItemRepositoryContract $requestedItemRepository, Request $request)
    {
        $this->requestedItemRepository = $requestedItemRepository;
        $this->request = $request;
    }

    /**
     * @param $attribute
     * @param $value
     * @param array $parameters
     * @param Validator|null $validator
     * @return bool
     */
    public function validate($attribute, $value, $parameters = [], Validator $validator = null)
    {
        $parts = explode('.', $attribute);
        $this->ensureValidatorAttribute('requested_item', $parts[0]);
        $this->ensureValidatorAttribute('quantity', $parts[2]);

        $parentRequestedItemId = $this->request->input($parts[0] . '.' . $parts[1] . '.parent_requested_item_id');

        if ($parentRequestedItemId) {
            /** @var RequestedItem $parentRequestedItem */
            try {
                $parentRequestedItem = $this->requestedItemRepository->findOrFail($parentRequestedItemId);
            } catch (ModelNotFoundException $e) {
                return false;
            }

            return $parentRequestedItem->quantity >= $value;
        }

        return true;
    }
}