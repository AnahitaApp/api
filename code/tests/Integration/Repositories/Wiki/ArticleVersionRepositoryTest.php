<?php
declare(strict_types=1);

namespace Tests\Integration\Repositories\Wiki;

use App\Events\Article\ArticleVersionCreatedEvent;
use App\Models\Wiki\Article;
use App\Models\Wiki\ArticleVersion;
use App\Models\Wiki\Iteration;
use App\Repositories\Wiki\ArticleVersionRepository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\CustomMockInterface;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\MocksApplicationLog;

/**
 * Class ArticleVersionRepositoryTest
 * @package Tests\Integration\Repositories\Wiki
 */
class ArticleVersionRepositoryTest extends TestCase
{
    use DatabaseSetupTrait, MocksApplicationLog;

    /**
     * @var ArticleVersionRepository
     */
    protected $repository;

    /**
     * @var Dispatcher|CustomMockInterface
     */
    private $dispatcher;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();

        $this->dispatcher = mock(Dispatcher::class);

        $this->repository = new ArticleVersionRepository(
            new ArticleVersion(),
            $this->getGenericLogMock(),
            $this->dispatcher,
        );
    }

    public function testFindAllSuccess()
    {
        factory(ArticleVersion::class, 5)->create();
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
        $model = factory(ArticleVersion::class)->create();

        $foundModel = $this->repository->findOrFail($model->id);
        $this->assertEquals($model->id, $foundModel->id);
    }

    public function testFindOrFailFails()
    {
        factory(ArticleVersion::class)->create(['id' => 19]);

        $this->expectException(ModelNotFoundException::class);
        $this->repository->findOrFail(20);
    }

    public function testCreateSuccess()
    {
        $article = factory(Article::class)->create();
        $iteration = factory(Iteration::class)->create();

        $this->dispatcher->shouldReceive('dispatch')->once()->with(\Mockery::on(function (ArticleVersionCreatedEvent $event) {
            return true;
        }));

        /** @var ArticleVersion $articleVersion */
        $articleVersion = $this->repository->create([
            'iteration_id' => $iteration->id,
        ], $article);

        $this->assertEquals($articleVersion->article_id, $article->id);
        $this->assertEquals($articleVersion->iteration_id, $iteration->id);
    }

    public function testUpdateSuccess()
    {
        $model = factory(ArticleVersion::class)->create([
            'name' => null,
        ]);
        $this->repository->update($model, [
            'name' => '1.0.0',
        ]);

        $updated = ArticleVersion::find($model->id);
        $this->assertEquals('1.0.0', $updated->name);
    }

    public function testDeleteSuccess()
    {
        $model = factory(ArticleVersion::class)->create();

        $this->repository->delete($model);

        $this->assertNull(ArticleVersion::find($model->id));
    }
}