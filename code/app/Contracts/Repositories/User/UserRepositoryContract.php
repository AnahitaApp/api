<?php
declare(strict_types=1);

namespace App\Contracts\Repositories\User;

use App\Contracts\Repositories\BaseRepositoryContract;
use App\Models\User\User;

/**
 * Interface UserRepositoryContract
 * @package App\Contracts\Repositories\User
 */
interface UserRepositoryContract extends BaseRepositoryContract
{
    /**
     * Attempts to look up a user by email address, and returns null if we cannot find one
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email) : ?User;
}