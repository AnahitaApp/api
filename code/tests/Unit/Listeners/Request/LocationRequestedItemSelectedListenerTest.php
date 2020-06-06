<?php
declare(strict_types=1);

namespace Tests\Unit\Listeners\Request;

use App\Contracts\Repositories\Request\RequestedItemRepositoryContract;
use App\Events\Request\LocationRequestedItemSelectedEvent;
use App\Listeners\Request\LocationRequestedItemSelectedListener;
use App\Models\Request\RequestedItem;
use Tests\TestCase;

/**
 * Class LocationRequestedItemSelectedListenerTest
 * @package Tests\Unit\Listeners\Request
 */
class LocationRequestedItemSelectedListenerTest extends TestCase
{
    public function testHandle()
    {
        $requestedItemRepository = mock(RequestedItemRepositoryContract::class);
        $listener = new LocationRequestedItemSelectedListener($requestedItemRepository);

        $parentRequestedItem = new RequestedItem([
            'quantity' => 3232,
        ]);
        $model = new RequestedItem([
            'quantity' => 3,
            'parentRequestedItem' => $parentRequestedItem,
        ]);

        $requestedItemRepository->shouldReceive('update')->once()->with($parentRequestedItem, [
            'quantity' => 3229,
        ]);

        $event = new LocationRequestedItemSelectedEvent($model);
        $listener->handle($event);
    }
}