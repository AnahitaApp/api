<?php
declare(strict_types=1);

namespace Tests\Integration\Repositories\Wiki;

use App\Exceptions\NotImplementedException;
use App\Models\User\User;
use App\Models\Wiki\Article;
use App\Models\Wiki\Iteration;
use App\Repositories\Wiki\IterationRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class IterationRepositoryTest
 * @package Tests\Integration\Repositories\Wiki
 */
class IterationRepositoryTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var IterationRepository
     */
    private $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();

        $this->repository = new IterationRepository(new Iteration(), $this->getGenericLogMock());
    }

    public function testDeleteThrowsException()
    {
        $this->expectException(NotImplementedException::class);

        $this->repository->delete(new Iteration());
    }

    public function testUpdateThrowsException()
    {
        $this->expectException(NotImplementedException::class);

        $this->repository->update(new Iteration(), []);
    }

    public function testFindOrFailSuccess()
    {
        $model = factory(Iteration::class)->create();

        $foundModel = $this->repository->findOrFail($model->id);
        $this->assertEquals($model->id, $foundModel->id);
    }

    public function testFindOrFailFails()
    {
        factory(Iteration::class)->create(['id' => 2]);

        $this->expectException(ModelNotFoundException::class);
        $this->repository->findOrFail(1);
    }

    public function testFindAllSuccess()
    {
        factory(Iteration::class, 5)->create();
        $items = $this->repository->findAll();
        $this->assertCount(5, $items);
    }

    public function testFindAllEmpty()
    {
        $items = $this->repository->findAll();
        $this->assertEmpty($items);
    }

    public function testCreateSuccess()
    {
        $article = factory(Article::class)->create();
        $user = factory(User::class)->create();

        /** @var Iteration $model */
        $model = $this->repository->create([
            'content' => 'hello',
            'created_by_id' => $user->id,
        ], $article);

        $this->assertEquals('hello', $model->content);
        $this->assertEquals($article->id, $model->article_id);
        $this->assertEquals($user->id, $model->created_by_id);
    }
}