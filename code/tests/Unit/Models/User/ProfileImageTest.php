<?php
declare(strict_types=1);

namespace Tests\Unit\Models\User;

use App\Models\User\ProfileImage;
use Tests\TestCase;

/**
 * Class ProfileImageTest
 * @package Tests\Unit\Models\User
 */
class ProfileImageTest extends TestCase
{
    public function testUser()
    {
        $model = new ProfileImage();
        $relation = $model->user();

        $this->assertEquals('assets.id', $relation->getQualifiedParentKeyName());
        $this->assertEquals('users.profile_image_id', $relation->getQualifiedForeignKeyName());
    }
}