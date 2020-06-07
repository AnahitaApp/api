<?php
declare(strict_types=1);

namespace Tests\Unit\Validators\Request;

use App\Contracts\Repositories\Request\RequestedItemRepositoryContract;
use App\Models\Request\RequestedItem;
use App\Validators\Request\LocationRequestedItemHasQuantityBelowMaxValidator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Tests\TestCase;

/**
 * Class LocationRequestedItemHasQuantityBelowMaxTest
 * @package Tests\Unit\Validators\Request
 */
class LocationRequestedItemHasQuantityBelowMaxValidatorTest extends TestCase
{
    public function testValidateReturnsTrueWithoutParentRequestId()
    {
        $requestedItemRepository = mock(RequestedItemRepositoryContract::class);
        $request = mock(Request::class);

        $request->shouldReceive('input')->once()->with('requested_items.3.parent_requested_item_id')->andReturnNull();

        $validator = new LocationRequestedItemHasQuantityBelowMaxValidator($requestedItemRepository, $request);

        $this->assertTrue($validator->validate('requested_items.3.quantity', 1));
    }

    public function testValidateReturnsFalseWhenRequestedItemNotFound()
    {
        $requestedItemRepository = mock(RequestedItemRepositoryContract::class);
        $request = mock(Request::class);

        $request->shouldReceive('input')->once()->with('requested_items.3.parent_requested_item_id')->andReturn(23);
        $requestedItemRepository->shouldReceive('findOrFail')->andThrow(new ModelNotFoundException());

        $validator = new LocationRequestedItemHasQuantityBelowMaxValidator($requestedItemRepository, $request);

        $this->assertFalse($validator->validate('requested_items.3.quantity', 3));
    }

    public function testValidateReturnsFalseWhenRequestedItemQuantityIsBelowPassedInQuantity()
    {
        $requestedItemRepository = mock(RequestedItemRepositoryContract::class);
        $request = mock(Request::class);

        $requestedItem = new RequestedItem([
            'max_quantity_per_request' => 2,
        ]);

        $request->shouldReceive('input')->once()->with('requested_items.3.parent_requested_item_id')->andReturn(23);
        $requestedItemRepository->shouldReceive('findOrFail')->andReturn($requestedItem);

        $validator = new LocationRequestedItemHasQuantityBelowMaxValidator($requestedItemRepository, $request);

        $this->assertFalse($validator->validate('requested_items.3.quantity', 3));
    }

    public function testValidateReturnsTrueWhenMaxQuantityNull()
    {
        $requestedItemRepository = mock(RequestedItemRepositoryContract::class);
        $request = mock(Request::class);

        $requestedItem = new RequestedItem([
            'max_quantity_per_request' => null,
        ]);

        $request->shouldReceive('input')->once()->with('requested_items.3.parent_requested_item_id')->andReturn(23);
        $requestedItemRepository->shouldReceive('findOrFail')->andReturn($requestedItem);

        $validator = new LocationRequestedItemHasQuantityBelowMaxValidator($requestedItemRepository, $request);

        $this->assertTrue($validator->validate('requested_items.3.quantity', 2));
    }

    public function testValidateReturnsTrueWhenBelowMaxQuantity()
    {
        $requestedItemRepository = mock(RequestedItemRepositoryContract::class);
        $request = mock(Request::class);

        $requestedItem = new RequestedItem([
            'max_quantity_per_request' => 2,
        ]);

        $request->shouldReceive('input')->once()->with('requested_items.3.parent_requested_item_id')->andReturn(23);
        $requestedItemRepository->shouldReceive('findOrFail')->andReturn($requestedItem);

        $validator = new LocationRequestedItemHasQuantityBelowMaxValidator($requestedItemRepository, $request);

        $this->assertTrue($validator->validate('requested_items.3.quantity', 2));
    }
}