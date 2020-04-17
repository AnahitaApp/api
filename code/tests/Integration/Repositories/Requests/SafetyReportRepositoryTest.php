<?php
declare(strict_types=1);


use App\Models\Request\SafetyReport;
use App\Models\User\User;
use App\Repositories\Request\SafetyReportRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

class SafetyReportRepositoryTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var SafetyReportRepositoryTest
     */
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();

        $this->repository = new SafetyReportRepository(
            new SafetyReport(),
            $this->getGenericLogMock()
        );
    }

    public function testFindAllSuccess()
    {
        factory(SafetyReport::class, 5)->create();
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
        $model = factory(SafetyReport::class)->create();

        $foundModel = $this->repository->findOrFail($model->id);
        $this->assertEquals($model->id, $foundModel->id);
    }

    public function testFindOrFailFails()
    {
        factory(SafetyReport::class)->create(['id' => 19]);

        $this->expectException(ModelNotFoundException::class);
        $this->repository->findOrFail(20);
    }

    public function testCreateSuccess()
    {
        /** @var SafetyReport $model */
        $user = factory(User::class)->create();
        $model = $this->repository->create([

            'requested_by_id' => $user->id,
            'reporter_id' => $user->id,
            'description' => 'Violated terms of app'
        ]);

        $this->assertEquals($model->requested_by_id, $user->id);
        $this->assertEquals($model->reporter_id, $user->id);
        $this->assertEquals('Violated terms of app', $model->description);
    }

    public function testUpdateSuccess()
    {
        $user = factory(User::class)->create();
        $model = factory(SafetyReport::class)->create([
            'completed' => true
        ]);
        $this->repository->update($model, [
            'completed' => false
        ]);

        /** @var SafetyReport $updated */
        $updated = SafetyReport::find($model->id);
        $this->assertEquals(false, $model->completed);
    }

    public function testDeleteSuccess()
    {
        $model = factory(SafetyReport::class)->create();

        $this->repository->delete($model);

        $this->assertNull(SafetyReport::find($model->id));
    }
}