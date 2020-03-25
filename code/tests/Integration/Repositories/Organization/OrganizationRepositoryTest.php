<?php
declare(strict_types=1);

namespace Tests\Integration\Repositories\Organization;

use App\Models\Organization\Organization;
use App\Repositories\Organization\OrganizationRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class OrganizationRepositoryTest
 * @package Tests\Integration\Repositories\Organization
 */
class OrganizationRepositoryTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var OrganizationRepository
     */
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();

        $this->repository = new OrganizationRepository(
            new Organization(),
            $this->getGenericLogMock()
        );
    }

    public function testFindAllSuccess()
    {
        factory(Organization::class, 5)->create();
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
        $model = factory(Organization::class)->create();

        $foundModel = $this->repository->findOrFail($model->id);
        $this->assertEquals($model->id, $foundModel->id);
    }

    public function testFindOrFailFails()
    {
        factory(Organization::class)->create(['id' => 19]);

        $this->expectException(ModelNotFoundException::class);
        $this->repository->findOrFail(20);
    }

    public function testCreateSuccess()
    {
        /** @var Organization $model */
        $model = $this->repository->create([
            'name' => 'An Organization',
        ]);

        $this->assertEquals('An Organization', $model->name);
    }

    public function testUpdateSuccess()
    {
        $model = factory(Organization::class)->create([
            'name' => 'A Organization',
        ]);
        $this->repository->update($model, [
            'name' => 'An Organization',
        ]);

        /** @var Organization $updated */
        $updated = Organization::find($model->id);
        $this->assertEquals('An Organization', $updated->name);
    }

    public function testDeleteSuccess()
    {
        $model = factory(Organization::class)->create();

        $this->repository->delete($model);

        $this->assertNull(Organization::find($model->id));
    }
}