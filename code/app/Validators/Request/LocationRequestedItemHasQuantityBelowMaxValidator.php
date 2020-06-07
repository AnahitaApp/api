<?php
declare(strict_types=1);

namespace App\Validators\Request;

use App\Models\Request\RequestedItem;

/**
 * Class LocationRequestedItemHasQuantityBelowMaxValidator
 * @package App\Validators\Request
 */
class LocationRequestedItemHasQuantityBelowMaxValidator extends BaseLocationRequestedItemValidator
{
    /**
     * @var string
     */
    const KEY = 'location_requested_item_has_quantity_below_max';

    /**
     * @inheritDoc
     */
    public function checkParentRequestedItem(string $attribute, $value, RequestedItem $requestedItem): bool
    {
        $this->ensureValidatorAttribute('quantity', $attribute);
        return $requestedItem->max_quantity_per_request >= $value;
    }
}