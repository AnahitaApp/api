<?php
declare(strict_types=1);

namespace Tests\Integration\Repositories\Request;

use App\Models\Request\Request;
use App\Models\Request\RequestedItem;
use App\Repositories\Request\RequestedItemRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class RequestedItemRepositoryTest
 * @package Tests\Integration\Repositories\Request
 */
class RequestedItemRepositoryTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var RequestedItemRepository
     */
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();

        $this->repository = new RequestedItemRepository(
            new RequestedItem(),
            $this->getGenericLogMock()
        );
    }

    public function testFindAllSuccess()
    {
        factory(RequestedItem::class, 5)->create();
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
        $model = factory(RequestedItem::class)->create();

        $foundModel = $this->repository->findOrFail($model->id);
        $this->assertEquals($model->id, $foundModel->id);
    }

    public function testFindOrFailFails()
    {
        factory(RequestedItem::class)->create(['id' => 19]);

        $this->expectException(ModelNotFoundException::class);
        $this->repository->findOrFail(20);
    }

    public function testCreateSuccess()
    {
        /** @var Request $request */
        $request = factory(Request::class)->create();
        /** @var RequestedItem $model */
        $model = $this->repository->create([
            'name' => 'An Item',
        ], $request);

        $this->assertEquals($model->request_id, $request->id);
        $this->assertEquals('An Item', $model->name);
    }

    public function testUpdateSuccess()
    {
        $model = factory(RequestedItem::class)->create([
            'name' => 'A Item',
        ]);
        $this->repository->update($model, [
            'name' => 'An Item',
        ]);

        /** @var RequestedItem $updated */
        $updated = RequestedItem::find($model->id);
        $this->assertEquals('An Item', $updated->name);
    }

    public function testDeleteSuccess()
    {
        $model = factory(RequestedItem::class)->create();

        $this->repository->delete($model);

        $this->assertNull(RequestedItem::find($model->id));
    }
}