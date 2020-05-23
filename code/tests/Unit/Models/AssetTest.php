<?php
declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Asset;
use Tests\TestCase;

/**
 * Class AssetTest
 * @package Tests\Unit\Models
 */
class AssetTest extends TestCase
{
    public function testOwner()
    {
        $model = new Asset();
        $relation = $model->owner();

        $this->assertEquals('assets.owner_id', $relation->getQualifiedForeignKeyName());
    }
}