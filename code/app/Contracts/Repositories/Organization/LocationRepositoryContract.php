<?php
declare(strict_types=1);

namespace App\Contracts\Repositories\Organization;

use App\Contracts\Repositories\BaseRepositoryContract;
use App\Contracts\Repositories\HasLocationRepositoryContract;

/**
 * Interface LocationRepositoryContract
 * @package App\Contracts\Repositories\Organization
 */
interface LocationRepositoryContract extends BaseRepositoryContract, HasLocationRepositoryContract
{}