<?php
declare(strict_types=1);

namespace Tests\Integration\Repositories\Organization;

use App\Models\Organization\Organization;
use App\Models\Organization\Location;
use App\Repositories\Organization\LocationRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class LocationRepositoryTest
 * @package Tests\Integration\Repositories\Organization
 */
class LocationRepositoryTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var LocationRepository
     */
    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();

        $this->repository = new LocationRepository(
            new Location(),
            $this->getGenericLogMock()
        );
    }

    public function testFindAllSuccess()
    {
        factory(Location::class, 5)->create();
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
        $model = factory(Location::class)->create();

        $foundModel = $this->repository->findOrFail($model->id);
        $this->assertEquals($model->id, $foundModel->id);
    }

    public function testFindOrFailFails()
    {
        factory(Location::class)->create(['id' => 3452]);

        $this->expectException(ModelNotFoundException::class);
        $this->repository->findOrFail(546);
    }

    public function testCreateSuccess()
    {
        $organization = factory(Organization::class)->create();
        /** @var Location $model */
        $model = $this->repository->create([
            'name' => 'A Location',
            'address_line_1' => '123 Fake Street',
            'city' => 'A City',
            'country' => 'A Country',
        ], $organization);

        $this->assertEquals($organization->id, $model->organization_id);
        $this->assertEquals('A Location', $model->name);
        $this->assertEquals('123 Fake Street', $model->address_line_1);
        $this->assertEquals('A City', $model->city);
        $this->assertEquals('A Country', $model->country);
    }

    public function testUpdateSuccess()
    {
        $model = factory(Location::class)->create([
            'city' => 'An City',
        ]);
        $this->repository->update($model, [
            'city' => 'A City',
        ]);

        /** @var Location $updated */
        $updated = Location::find($model->id);
        $this->assertEquals('A City', $updated->city);
    }

    public function testDeleteSuccess()
    {
        $model = factory(Location::class)->create();

        $this->repository->delete($model);

        $this->assertNull(Location::find($model->id));
    }
}