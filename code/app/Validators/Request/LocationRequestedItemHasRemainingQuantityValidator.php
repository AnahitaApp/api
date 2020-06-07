<?php
declare(strict_types=1);

namespace App\Validators\Request;

use App\Models\Request\RequestedItem;

/**
 * Class LocationRequestedItemHasRemainingQuantityValidator
 * @package App\Validators\Request
 */
class LocationRequestedItemHasRemainingQuantityValidator extends BaseLocationRequestedItemValidator
{
    /**
     * @var string
     */
    const KEY = 'location_requested_item_has_remaining_quantity';

    /**
     * @inheritDoc
     */
    public function checkParentRequestedItem(string $attribute, $value, RequestedItem $requestedItem): bool
    {
        $this->ensureValidatorAttribute('quantity', $attribute);
        return $requestedItem->quantity == null || $requestedItem->quantity >= $value;
    }
}