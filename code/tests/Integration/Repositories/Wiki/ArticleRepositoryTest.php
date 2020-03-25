<?php
declare(strict_types=1);

namespace Tests\Integration\Repositories\Wiki;

use App\Exceptions\NotImplementedException;
use App\Models\User\User;
use App\Models\Wiki\Article;
use App\Repositories\Wiki\ArticleRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class ArticleRepositoryTest
 * @package Tests\Integration\Repositories\Wiki
 */
class ArticleRepositoryTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var ArticleRepository
     */
    private $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();

        $this->repository = new ArticleRepository(new Article(), $this->getGenericLogMock());
    }

    public function testDeleteThrowsException()
    {
        $this->expectException(NotImplementedException::class);

        $this->repository->delete(new Article());
    }

    public function testFindAllSuccess()
    {
        factory(Article::class, 5)->create();
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
        $model = factory(Article::class)->create();

        $foundModel = $this->repository->findOrFail($model->id);
        $this->assertEquals($model->id, $foundModel->id);
    }

    public function testFindOrFailFails()
    {
        factory(Article::class)->create(['id' => 2]);

        $this->expectException(ModelNotFoundException::class);
        $this->repository->findOrFail(1);
    }

    public function testCreateSuccess()
    {
        $user = factory(User::class)->create();

        /** @var Article $article */
        $article = $this->repository->create([
            'title' => 'An Article',
            'created_by_id' => $user->id,
        ]);

        $this->assertEquals('An Article', $article->title);
        $this->assertEquals($user->id, $article->created_by_id);
    }

    public function testUpdateSuccess()
    {
        $model = factory(Article::class)->create([
            'title' => 'Ann Article'
        ]);
        $this->repository->update($model, [
            'title' => 'An Article',
        ]);

        $updated = Article::find($model->id);
        $this->assertEquals('An Article', $updated->title);
    }
}