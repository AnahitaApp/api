<?php
declare(strict_types=1);

namespace Tests\Unit\Models\Request;

use App\Models\Request\Request;
use Tests\TestCase;

/**
 * Class RequestTest
 * @package Tests\Unit\Models\Request
 */
class RequestTest extends TestCase
{
    public function testAssets()
    {
        $model = new Request();

        $relation = $model->assets();

        $this->assertEquals('asset_request', $relation->getTable());
        $this->assertEquals('asset_request.request_id', $relation->getQualifiedForeignPivotKeyName());
        $this->assertEquals('asset_request.asset_id', $relation->getQualifiedRelatedPivotKeyName());
        $this->assertEquals('requests.id', $relation->getQualifiedParentKeyName());
    }

    public function testCompletedBy()
    {
        $model = new Request();
        $relation = $model->completedBy();

        $this->assertEquals('users.id', $relation->getQualifiedOwnerKeyName());
        $this->assertEquals('requests.completed_by_id', $relation->getQualifiedForeignKeyName());
    }

    public function testCreatedBy()
    {
        $model = new Request();
        $relation = $model->createdBy();

        $this->assertEquals('users.id', $relation->getQualifiedOwnerKeyName());
        $this->assertEquals('requests.created_by_id', $relation->getQualifiedForeignKeyName());
    }

    public function testRequestedItems()
    {
        $model = new Request();
        $relation = $model->requestedItems();

        $this->assertEquals('requests.id', $relation->getQualifiedParentKeyName());
        $this->assertEquals('requested_items.request_id', $relation->getQualifiedForeignKeyName());
    }

    public function testSafetyReport()
    {
        $model = new Request();
        $relation = $model->safetyReport();

        $this->assertEquals('requests.id', $relation->getQualifiedParentKeyName());
        $this->assertEquals('safety_reports.request_id', $relation->getQualifiedForeignKeyName());
    }
}