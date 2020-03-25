<?php
declare(strict_types=1);

namespace App\Repositories\Vote;

use App\Contracts\Repositories\Vote\BallotCompletionRepositoryContract;
use App\Models\Vote\BallotCompletion;
use App\Repositories\BaseRepositoryAbstract;
use App\Repositories\Traits\NotImplemented;
use Psr\Log\LoggerInterface as LogContract;

/**
 * Class BallotCompletionRepository
 * @package App\Repositories\Vote
 */
class BallotCompletionRepository extends BaseRepositoryAbstract implements BallotCompletionRepositoryContract
{
    use NotImplemented\Update;

    /**
     * BallotCompletionRepository constructor.
     * @param BallotCompletion $model
     * @param LogContract $log
     */
    public function __construct(BallotCompletion $model, LogContract $log)
    {
        parent::__construct($model, $log);
    }
}