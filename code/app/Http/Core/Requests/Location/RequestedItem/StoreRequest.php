<?php
declare(strict_types=1);

namespace App\Http\Core\Requests\Location\RequestedItem;

use App\Http\Core\Requests\BaseAssetUploadRequestAbstract;
use App\Http\Core\Requests\Traits\HasNoExpands;
use App\Models\Request\RequestedItem;
use App\Policies\Request\RequestedItemPolicy;

/**
 * Class StoreRequest
 * @package App\Http\Core\Requests\Location\RequestedItem
 */
class StoreRequest extends BaseAssetUploadRequestAbstract
{
    use HasNoExpands;

    /**
     * Get the policy action for the guard
     *
     * @return string
     */
    protected function getPolicyAction(): string
    {
        return RequestedItemPolicy::ACTION_CREATE;
    }

    /**
     * Get the class name of the policy that this request utilizes
     *
     * @return string
     */
    protected function getPolicyModel(): string
    {
        return RequestedItem::class;
    }

    /**
     * Gets any additional parameters needed for the policy function
     *
     * @return array
     */
    protected function getPolicyParameters(): array
    {
        return [
            $this->route('location'),
        ];
    }

    /**
     * @param RequestedItem $model
     * @return array
     */
    public function rules(RequestedItem $model)
    {
        return $model->getValidationRules(RequestedItem::VALIDATION_RULES_CREATE);
    }
}