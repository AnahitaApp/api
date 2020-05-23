<?php
declare(strict_types=1);

namespace Tests\Unit\Models\Organization;

use App\Models\Organization\Organization;
use Tests\TestCase;

/**
 * Class OrganizationTest
 * @package Tests\Unit\Models\Organization
 */
class OrganizationTest extends TestCase
{
    public function testLocations()
    {
        $user = new Organization();
        $relation = $user->locations();

        $this->assertEquals('organizations.id', $relation->getQualifiedParentKeyName());
        $this->assertEquals('locations.organization_id', $relation->getQualifiedForeignKeyName());
    }

    public function testOrganizationManagers()
    {
        $user = new Organization();
        $relation = $user->organizationManagers();

        $this->assertEquals('organizations.id', $relation->getQualifiedParentKeyName());
        $this->assertEquals('organization_managers.organization_id', $relation->getQualifiedForeignKeyName());
    }
}