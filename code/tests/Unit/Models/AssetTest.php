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
    public function testUser()
    {
        $model = new Asset();
        $relation = $model->user();

        $this->assertEquals('users.id', $relation->getQualifiedOwnerKeyName());
        $this->assertEquals('assets.user_id', $relation->getQualifiedForeignKeyName());
    }
}