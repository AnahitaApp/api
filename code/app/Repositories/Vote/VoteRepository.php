<?php
declare(strict_types=1);

namespace App\Repositories\Vote;

use App\Contracts\Repositories\Vote\VoteRepositoryContract;
use App\Models\Vote\Vote;
use App\Repositories\BaseRepositoryAbstract;
use Psr\Log\LoggerInterface as LogContract;

/**
 * Class VoteRepository
 * @package App\Repositories\Vote
 */
class VoteRepository extends BaseRepositoryAbstract implements VoteRepositoryContract
{
    /**
     * VoteRepository constructor.
     * @param Vote $model
     * @param LogContract $log
     */
    public function __construct(Vote $model, LogContract $log)
    {
        parent::__construct($model, $log);
    }
}