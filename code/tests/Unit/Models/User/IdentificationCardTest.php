<?php
declare(strict_types=1);

namespace Tests\Unit\Models\User;

use App\Models\User\IdentificationCard;
use Tests\TestCase;

/**
 * Class IdentificationCardTest
 * @package Tests\Unit\Models\User
 */
class IdentificationCardTest extends TestCase
{
    public function testUser()
    {
        $model = new IdentificationCard();
        $relation = $model->user();

        $this->assertEquals('assets.id', $relation->getQualifiedParentKeyName());
        $this->assertEquals('users.identification_card_id', $relation->getQualifiedForeignKeyName());
    }
}