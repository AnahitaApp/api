<?php
declare(strict_types=1);

namespace Tests\Unit\Models\Organization;

use App\Models\Organization\Location;
use Tests\TestCase;

/**
 * Class LocationTest
 * @package Tests\Unit\Models\Organization
 */
class LocationTest extends TestCase
{
    public function testOrganization()
    {
        $model = new Location();
        $relation = $model->organization();

        $this->assertEquals('locations.organization_id', $relation->getQualifiedForeignKeyName());
        $this->assertEquals('organizations.id', $relation->getQualifiedOwnerKeyName());
    }

    public function testRequests()
    {
        $model = new Location();
        $relation = $model->requests();

        $this->assertEquals('locations.id', $relation->getQualifiedParentKeyName());
        $this->assertEquals('requests.location_id', $relation->getQualifiedForeignKeyName());
    }

    public function testRequestedItems()
    {
        $model = new Location();
        $relation = $model->requestedItems();

        $this->assertEquals('locations.id', $relation->getQualifiedParentKeyName());
        $this->assertEquals('requested_items.location_id', $relation->getQualifiedForeignKeyName());
    }
}