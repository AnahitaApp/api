<?php
declare(strict_types=1);

namespace App\Contracts\Models;

use App\Models\User\User;

interface CanBeManagedByEntity
{
    /**
     * @param User $loggedInUser
     * @param IsAnEntity $entity
     * @param string $action
     * @return bool
     */
    public function canUserManage(User $loggedInUser, IsAnEntity $entity, string $action): bool;
}