<?php
declare(strict_types=1);

namespace Tests\Traits;

use App\Models\Role;
use App\Models\User\User;

/**
 * Trait RolesTesting
 * @package Tests\Traits
 */
trait RolesTesting
{
    /**
     * Makes a user of this particular role - for unit testing
     *
     * @param $roleId
     * @return User
     */
    protected function getUserOfRole($roleId)
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        return $user->addRole($roleId);
    }

    /**
     * Get all roles without this particular set (also auto removes programmer and admin)
     * 
     * @param array $withoutRoles
     * @return array
     */
    protected function rolesWithoutAdmins(array $withoutRoles = [])
    {
        $withoutRoles[] = Role::SUPER_ADMIN;

        return array_filter(Role::ROLES, function($role) use ($withoutRoles) {
            return !in_array($role, $withoutRoles);
        });
    }
}