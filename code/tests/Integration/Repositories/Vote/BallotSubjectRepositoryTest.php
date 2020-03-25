<?php
declare(strict_types=1);

namespace Tests\Integration\Repositories\Vote;

use App\Models\Vote\Ballot;
use App\Models\Vote\BallotSubject;
use App\Models\Wiki\Iteration;
use App\Repositories\Vote\BallotSubjectRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class BallotSubjectRepositoryTest
 * @package Tests\Integration\Repositories\Vote
 */
class BallotSubjectRepositoryTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var BallotSubjectRepository
     */
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();

        $this->repository = new BallotSubjectRepository(
            new BallotSubject(),
            $this->getGenericLogMock()
        );
    }

    public function testFindAllSuccess()
    {
        factory(BallotSubject::class, 5)->create();
        $items = $this->repository->findAll();
        $this->assertCount(5, $items);
    }

    public function testFindAllEmpty()
    {
        $items = $this->repository->findAll();
        $this->assertEmpty($items);
    }

    public function testFindOrFailSuccess()
    {
        $model = factory(BallotSubject::class)->create();

        $foundModel = $this->repository->findOrFail($model->id);
        $this->assertEquals($model->id, $foundModel->id);
    }

    public function testFindOrFailFails()
    {
        factory(BallotSubject::class)->create(['id' => 19]);

        $this->expectException(ModelNotFoundException::class);
        $this->repository->findOrFail(20);
    }

    public function testCreateSuccess()
    {
        /** @var Ballot $ballot */
        $ballot = factory(Ballot::class)->create();

        /** @var Iteration $iteration */
        $iteration = factory(Iteration::class)->create();

        /** @var BallotSubject $ballotSubject */
        $ballotSubject = $this->repository->create([
            'subject_id' => $iteration->id,
            'subject_type' => 'iteration',
        ], $ballot);

        $this->assertEquals($ballotSubject->subject_id, $iteration->id);
        $this->assertEquals($ballotSubject->ballot_id, $ballot->id);
    }

    public function testUpdateSuccess()
    {
        $model = factory(BallotSubject::class)->create([
            'votes_cast' => 1,
        ]);
        $this->repository->update($model, [
            'votes_cast' => 5,
        ]);

        /** @var BallotSubject $updated */
        $updated = BallotSubject::find($model->id);
        $this->assertEquals(5, $updated->votes_cast);
    }

    public function testDeleteSuccess()
    {
        $model = factory(BallotSubject::class)->create();

        $this->repository->delete($model);

        $this->assertNull(BallotSubject::find($model->id));
    }
}