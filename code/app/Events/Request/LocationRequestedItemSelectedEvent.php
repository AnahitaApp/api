<?php
declare(strict_types=1);

namespace App\Events\Request;

use App\Models\Request\RequestedItem;

/**
 * Class LocationRequestedItemSelectedEvent
 * @package App\Events\Request
 */
class LocationRequestedItemSelectedEvent
{
    /**
     * @var RequestedItem
     */
    private RequestedItem $requestedItem;

    /**
     * LocationRequestedItemSelectedEvent constructor.
     * @param RequestedItem $requestedItem
     */
    public function __construct(RequestedItem $requestedItem)
    {
        $this->requestedItem = $requestedItem;
    }

    /**
     * @return RequestedItem
     */
    public function getRequestedItem(): RequestedItem
    {
        return $this->requestedItem;
    }
}