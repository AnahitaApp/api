<?php
declare(strict_types=1);

namespace App\Contracts\Repositories\Request;

use App\Contracts\Repositories\BaseRepositoryContract;
use App\Contracts\Repositories\HasLocationRepositoryContract;

/**
 * Interface RequestRepositoryContract
 * @package App\Contracts\Repositories\Request
 */
interface RequestRepositoryContract extends BaseRepositoryContract, HasLocationRepositoryContract
{}