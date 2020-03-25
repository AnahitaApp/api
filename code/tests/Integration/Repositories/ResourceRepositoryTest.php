<?php
declare(strict_types=1);

namespace Tests\Integration\Repositories;

use App\Models\Resource;
use App\Models\User\User;
use App\Repositories\ResourceRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class ResourceRepositoryTest
 * @package Tests\Integration\Repositories
 */
class ResourceRepositoryTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var ResourceRepository
     */
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();

        User::unsetEventDispatcher();

        $this->repository = new ResourceRepository(
            new Resource(),
            $this->getGenericLogMock()
        );
    }

    public function testFindAllSuccess()
    {
        foreach (Resource::all() as $resource) {
            $resource->delete();
        }

        factory(Resource::class, 5)->create();
        $items = $this->repository->findAll();
        $this->assertCount(5, $items);
    }

    public function testFindAllEmpty()
    {
        foreach (Resource::all() as $resource) {
            $resource->delete();
        }

        $items = $this->repository->findAll();
        $this->assertEmpty($items);
    }

    public function testFindOrFailSuccess()
    {
        $model = factory(Resource::class)->create();

        $foundModel = $this->repository->findOrFail($model->id);
        $this->assertEquals($model->id, $foundModel->id);
    }

    public function testFindOrFailFails()
    {
        factory(Resource::class)->create(['id' => 19]);

        $this->expectException(ModelNotFoundException::class);
        $this->repository->findOrFail(20);
    }

    public function testCreateSuccess()
    {
        /** @var User $user */
        $user = factory(User::class)->create();

        /** @var Resource $resource */
        $resource = $this->repository->create([
            'content' => 'Some Content',
            'resource_id' => $user->id,
            'resource_type' => 'user'
        ]);

        $this->assertEquals('user', $resource->resource_type);
        $this->assertEquals($user->id, $resource->resource_id);
        $this->assertEquals('Some Content', $resource->content);
    }

    public function testUpdateSuccess()
    {
        $model = factory(Resource::class)->create([
            'content' => 'a code'
        ]);
        $this->repository->update($model, [
            'content' => 'the same',
        ]);

        $updated = Resource::find($model->id);
        $this->assertEquals('the same', $updated->content);
    }

    public function testDeleteSuccess()
    {
        $model = factory(Resource::class)->create();

        $this->repository->delete($model);

        $this->assertNull(Resource::find($model->id));
    }
}