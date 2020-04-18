<?php
declare(strict_types=1);

namespace Tests\Integration\Repositories\Request;

use App\Models\Request\Request;
use App\Models\Request\SafetyReport;
use App\Repositories\Request\SafetyReportRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class SafetyReportRepositoryTest
 * @package Tests\Integration\Repositories\Request
 */
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
        /** @var Request $request */
        $request = factory(Request::class)->create();
        /** @var SafetyReport $model */
        $model = $this->repository->create([
            'reporter_id' => $request->requested_by_id,
            'description' => 'Violated terms of app'
        ], $request);

        $this->assertEquals($model->request_id, $request->id);
        $this->assertEquals($model->reporter_id, $request->requested_by_id);
        $this->assertEquals('Violated terms of app', $model->description);
    }

    public function testUpdateSuccess()
    {
        $model = factory(SafetyReport::class)->create([
            'description' => 'A Description',
        ]);
        $this->repository->update($model, [
            'description' => 'A New Description',
        ]);

        /** @var SafetyReport $updated */
        $updated = SafetyReport::find($model->id);
        $this->assertEquals('A New Description', $updated->description);
    }

    public function testDeleteSuccess()
    {
        $model = factory(SafetyReport::class)->create();

        $this->repository->delete($model);

        $this->assertNull(SafetyReport::find($model->id));
    }
}