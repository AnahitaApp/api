<?php
declare(strict_types=1);

namespace App\Repositories\Vote;

use App\Contracts\Repositories\Vote\BallotRepositoryContract;
use App\Contracts\Repositories\Vote\BallotSubjectRepositoryContract;
use App\Models\BaseModelAbstract;
use App\Models\Vote\Ballot;
use App\Repositories\BaseRepositoryAbstract;
use App\Traits\CanGetAndUnset;
use Psr\Log\LoggerInterface as LogContract;

/**
 * Class BallotRepository
 * @package App\Repositories\Vote
 */
class BallotRepository extends BaseRepositoryAbstract implements BallotRepositoryContract
{
    use CanGetAndUnset;

    /**
     * @var BallotSubjectRepositoryContract
     */
    private $ballotSubjectRepository;

    /**
     * BallotRepository constructor.
     * @param Ballot $model
     * @param LogContract $log
     * @param BallotSubjectRepositoryContract $ballotSubjectRepository
     */
    public function __construct(Ballot $model, LogContract $log,
                                BallotSubjectRepositoryContract $ballotSubjectRepository)
    {
        parent::__construct($model, $log);
        $this->ballotSubjectRepository = $ballotSubjectRepository;
    }

    /**
     * overrides the parent in order to create all related ballot subjects
     *
     * @param array $data
     * @param BaseModelAbstract|null $relatedModel
     * @param array $forcedValues
     * @return BaseModelAbstract
     */
    public function create(array $data = [], BaseModelAbstract $relatedModel = null, array $forcedValues = [])
    {
        $ballotSubjects = $this->getAndUnset($data, 'ballot_subjects', []);
        $model = parent::create($data, $relatedModel, $forcedValues);

        $this->syncChildModels($this->ballotSubjectRepository, $model, $ballotSubjects);

        return $model;
    }

    /**
     * Makes sure to sync child models properly
     *
     * @param Ballot|BaseModelAbstract $model
     * @param array $data
     * @param array $forcedValues
     * @return BaseModelAbstract
     */
    public function update(BaseModelAbstract $model, array $data, array $forcedValues = []): BaseModelAbstract
    {
        $ballotSubjects = $this->getAndUnset($data, 'ballot_subjects', null);

        if ($ballotSubjects) {
            $this->syncChildModels($this->ballotSubjectRepository, $model, $ballotSubjects, $model->ballotSubjects);
        }

        return parent::update($model, $data, $forcedValues);
    }
}