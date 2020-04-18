<?php
declare(strict_types=1);

namespace Tests\Unit\Models\Request;

use App\Models\Request\RequestedItem;
use Tests\TestCase;

/**
 * Class RequestedItemTest
 * @package Tests\Unit\Models\Request
 */
class RequestedItemTest extends TestCase
{
    public function testAsset()
    {
        $model = new RequestedItem();
        $relation = $model->asset();

        $this->assertEquals('assets.id', $relation->getQualifiedOwnerKeyName());
        $this->assertEquals('requested_items.asset_id', $relation->getQualifiedForeignKeyName());
    }

    public function testRequest()
    {
        $model = new RequestedItem();
        $relation = $model->request();

        $this->assertEquals('requests.id', $relation->getQualifiedOwnerKeyName());
        $this->assertEquals('requested_items.request_id', $relation->getQualifiedForeignKeyName());
    }
}