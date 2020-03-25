<?php
declare(strict_types=1);

namespace Tests\Integration\Repositories\Vote;

use App\Exceptions\NotImplementedException;
use App\Models\User\User;
use App\Models\Vote\Ballot;
use App\Models\Vote\BallotCompletion;
use App\Repositories\Vote\BallotCompletionRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class BallotCompletionRepositoryTest
 * @package Tests\Integration\Repositories\Vote
 */
class BallotCompletionRepositoryTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var BallotCompletionRepository
     */
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();

        $this->repository = new BallotCompletionRepository(
            new BallotCompletion(),
            $this->getGenericLogMock()
        );
    }

    public function testFindAllSuccess()
    {
        factory(BallotCompletion::class, 5)->create();
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
        $model = factory(BallotCompletion::class)->create();

        $foundModel = $this->repository->findOrFail($model->id);
        $this->assertEquals($model->id, $foundModel->id);
    }

    public function testFindOrFailFails()
    {
        factory(BallotCompletion::class)->create(['id' => 19]);

        $this->expectException(ModelNotFoundException::class);
        $this->repository->findOrFail(20);
    }

    public function testCreateSuccess()
    {
        /** @var Ballot $ballot */
        $ballot = factory(Ballot::class)->create();

        /** @var User $user */
        $user = factory(User::class)->create();

        /** @var BallotCompletion $ballotCompletion */
        $ballotCompletion = $this->repository->create([
            'user_id' => $user->id,
        ], $ballot);

        $this->assertEquals($ballotCompletion->user_id, $user->id);
        $this->assertEquals($ballotCompletion->ballot_id, $ballot->id);
    }

    public function testUpdateThrowsException()
    {
        $this->expectException(NotImplementedException::class);
        $this->repository->update(new BallotCompletion(), []);
    }

    public function testDeleteSuccess()
    {
        $model = factory(BallotCompletion::class)->create();

        $this->repository->delete($model);

        $this->assertNull(BallotCompletion::find($model->id));
    }
}