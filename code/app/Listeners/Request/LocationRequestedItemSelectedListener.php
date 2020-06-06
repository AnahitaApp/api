<?php
declare(strict_types=1);

namespace App\Listeners\Request;

use App\Contracts\Repositories\Request\RequestedItemRepositoryContract;
use App\Events\Request\LocationRequestedItemSelectedEvent;

/**
 * Class LocationRequestedItemSelectedListener
 * @package App\Listeners\Request
 */
class LocationRequestedItemSelectedListener
{
    /**
     * @var RequestedItemRepositoryContract
     */
    private RequestedItemRepositoryContract $requestedItemRepository;

    /**
     * LocationRequestedItemSelectedListener constructor.
     * @param RequestedItemRepositoryContract $requestedItemRepository
     */
    public function __construct(RequestedItemRepositoryContract $requestedItemRepository)
    {
        $this->requestedItemRepository = $requestedItemRepository;
    }

    /**
     * @param LocationRequestedItemSelectedEvent $event
     */
    public function handle(LocationRequestedItemSelectedEvent $event)
    {
        $requestedItem = $event->getRequestedItem();
        $this->requestedItemRepository->update($requestedItem->parentRequestedItem, [
            'quantity' => $requestedItem->parentRequestedItem->quantity - $requestedItem->quantity
        ]);
    }
}