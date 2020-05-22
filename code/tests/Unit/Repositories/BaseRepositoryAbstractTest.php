<?php
declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\User\User;
use App\Repositories\BaseRepositoryAbstract;
use Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder;
use Tests\TestCase;

/**
 * Class BaseRepositoryAbstractTest
 * @package Tests\Unit\Repositories
 */
class BaseRepositoryAbstractTest extends TestCase
{
    public function testFindOrFailPassesProperParameters()
    {
        $withArgs = ['with' => 'args'];
        $id = 123;

        $mockModel = mock(User::class)->shouldAllowMockingMethod('findOrFail')->shouldAllowMockingMethod('with');
        $mockModel->shouldReceive('with')->once()->with($withArgs)->andReturn(\Mockery::self());
        $mockModel->shouldReceive('findOrFail')->once()->with($id);

        $repository = new class($mockModel, $this->getGenericLogMock()) extends BaseRepositoryAbstract {};
        $repository->findOrFail($id, $withArgs);
    }

    public function testFindOrFailDefaultParameters()
    {
        $id = 123;

        $mockModel = mock(User::class)
            ->shouldAllowMockingMethod('findOrFail')
            ->shouldAllowMockingMethod('with');
        $mockModel->shouldReceive('with')->once()->with([])->andReturn(\Mockery::self());
        $mockModel->shouldReceive('findOrFail')->once()->with($id);

        $repository = new class($mockModel, $this->getGenericLogMock()) extends BaseRepositoryAbstract {};
        $repository->findOrFail($id);
    }

    public function testFindAllPassesProperParameters()
    {
        $whereArgs = ['where' => 'args'];
        $withArgs = ['with' => 'args'];
        $limitArg = 22;

        $mockModel = mock(EloquentJoinBuilder::class)
            ->shouldAllowMockingMethod('with')
            ->shouldAllowMockingMethod('where')
            ->shouldAllowMockingMethod('whereJoin')
            ->shouldAllowMockingMethod('appends')
            ->shouldAllowMockingMethod('paginate');
        $mockModel->shouldReceive('with')->once()->with($withArgs)->andReturn(\Mockery::self());
        $mockModel->shouldReceive('whereJoin')->once()->with('where', '=', 'args')->andReturn(\Mockery::self());
        $mockModel->shouldReceive('paginate')->once()->with($limitArg, ['*'], 'page', 1)->andReturn(\Mockery::self());

        $repository = new class($mockModel, $this->getGenericLogMock()) extends BaseRepositoryAbstract {};
        $repository->findAll($whereArgs, [], [], $withArgs, $limitArg);
    }

    public function testFindAllDefaultParameters()
    {
        $mockModel = mock(EloquentJoinBuilder::class)
            ->shouldAllowMockingMethod('with')
            ->shouldAllowMockingMethod('appends')
            ->shouldAllowMockingMethod('paginate');
        $mockModel->shouldReceive('with')->once()->with([])->andReturn(\Mockery::self());
        $mockModel->shouldReceive('paginate')->once()->with(10, ['*'], 'page', 1)->andReturn(\Mockery::self());

        $repository = new class($mockModel, $this->getGenericLogMock()) extends BaseRepositoryAbstract {};
        $repository->findAll();
    }

    public function testCreatePassesProperParameters()
    {
        $args = ['some' => 'args'];

        $mockModel = mock(User::class)->shouldAllowMockingMethod('create');
        $mockModel->shouldReceive('newInstance')->once()->with($args)->andReturn(\Mockery::self());
        $mockModel->shouldReceive('save')->once();
        $mockModel->shouldReceive('getAttribute')->once();

        $repository = new class($mockModel, $this->getGenericLogMock()) extends BaseRepositoryAbstract {};
        $repository->create($args);
    }

    public function testCreateDefaultParameters()
    {
        $mockModel = mock(User::class)->shouldAllowMockingMethod('create');
        $mockModel->shouldReceive('newInstance')->once()->with([])->andReturn(\Mockery::self());
        $mockModel->shouldReceive('getAttribute')->once();
        $mockModel->shouldReceive('save')->once();

        $repository = new class($mockModel, $this->getGenericLogMock()) extends BaseRepositoryAbstract {};
        $repository->create();
    }

    public function testCreatePassesForcedValues()
    {
        $mockModel = mock(User::class)->shouldAllowMockingMethod('create');
        $mockModel->shouldReceive('newInstance')->once()->with([])->andReturn(\Mockery::self());
        $mockModel->shouldReceive('setAttribute')->once()->with('test', 'chicken');
        $mockModel->shouldReceive('getAttribute')->once();
        $mockModel->shouldReceive('save')->once();

        $repository = new class($mockModel, $this->getGenericLogMock()) extends BaseRepositoryAbstract {};
        $repository->create([], null, ['test' => 'chicken']);
    }

    public function testUpdatePassesProperParameters()
    {
        $args = ['some' => 'args'];

        $mockModel = mock(User::class);
        $mockModel->shouldReceive('update')->once()->with($args)->andReturn(true);
        $mockModel->shouldReceive('getAttribute')->once();

        $repository = new class($mockModel, $this->getGenericLogMock()) extends BaseRepositoryAbstract {};
        $repository->update($mockModel, $args);
    }

    public function testUpdatePassesForcedValues()
    {
        $args = ['some' => 'args'];
        $forcedArgs = ['other' => 'forced'];

        $mockModel = mock(User::class);
        $mockModel->shouldReceive('forceFill')->once()->with(['other' => 'forced']);
        $mockModel->shouldReceive('update')->once()->with($args)->andReturn(true);
        $mockModel->shouldReceive('getAttribute')->once();

        $repository = new class($mockModel, $this->getGenericLogMock()) extends BaseRepositoryAbstract {};
        $repository->update($mockModel, $args, $forcedArgs);
    }

    public function testUpdateThrowsExceptionWhenFails()
    {
        $this->expectException(\DomainException::class);
        $args = ['some' => 'args'];

        $mockModel = mock(User::class);
        $mockModel->shouldReceive('update')->once()->with($args)->andReturn(false);
        $mockModel->shouldReceive('getAttribute')->andReturn('something');

        $repository = new class($mockModel, $this->getGenericLogMock()) extends BaseRepositoryAbstract {};
        $repository->update($mockModel, $args);
    }

    public function testDeleteIsCalled()
    {
        $mockModel = mock(User::class);
        $mockModel->shouldReceive('delete')->once()->andReturn(true);
        $mockModel->shouldReceive('getAttribute')->once();

        $repository = new class($mockModel, $this->getGenericLogMock()) extends BaseRepositoryAbstract {};
        $repository->delete($mockModel);
    }
}