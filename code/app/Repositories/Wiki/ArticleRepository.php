<?php
declare(strict_types=1);

namespace App\Repositories\Wiki;

use App\Contracts\Repositories\Wiki\ArticleRepositoryContract;
use App\Models\Wiki\Article;
use App\Repositories\BaseRepositoryAbstract;
use App\Repositories\Traits\NotImplemented;
use Psr\Log\LoggerInterface as LogContract;

/**
 * Class ArticleRepository
 * @package App\Repositories\Wiki
 */
class ArticleRepository extends BaseRepositoryAbstract implements ArticleRepositoryContract
{
    use NotImplemented\Delete;

    /**
     * ArticleRepository constructor.
     * @param Article $model
     * @param LogContract $log
     */
    public function __construct(Article $model, LogContract $log)
    {
        parent::__construct($model, $log);
    }
}