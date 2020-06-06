<?php
declare(strict_types=1);

namespace Tests\Unit\Events\Request;

use App\Events\Request\LocationRequestedItemSelectedEvent;
use App\Models\Request\RequestedItem;
use Tests\TestCase;

/**
 * Class LocationRequestedItemSelectedEventTest
 * @package Tests\Unit\Events\Request
 */
class LocationRequestedItemSelectedEventTest extends TestCase
{
    public function testGetRequestedItem()
    {
        $model = new RequestedItem();
        $event = new LocationRequestedItemSelectedEvent($model);

        $this->assertEquals($model, $event->getRequestedItem());
    }
}