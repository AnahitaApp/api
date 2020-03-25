<?php
declare(strict_types=1);

namespace Tests\Unit\Models\Wiki;

use App\Models\Wiki\Iteration;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Tests\TestCase;

/**
 * Class IterationTest
 * @package Tests\Unit\Models\Wiki
 */
class IterationTest extends TestCase
{
    public function testArticle()
    {
        $article = new Iteration();
        $relation = $article->article();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertEquals('articles.id', $relation->getQualifiedOwnerKeyName());
        $this->assertEquals('iterations.article_id', $relation->getQualifiedForeignKeyName());
    }

    public function testCreatedBy()
    {
        $article = new Iteration();
        $relation = $article->createdBy();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertEquals('users.id', $relation->getQualifiedOwnerKeyName());
        $this->assertEquals('iterations.created_by_id', $relation->getQualifiedForeignKeyName());
    }

    public function testVersions()
    {
        $article = new Iteration();
        $relation = $article->version();

        $this->assertEquals('iterations.id', $relation->getQualifiedParentKeyName());
        $this->assertEquals('article_versions.iteration_id', $relation->getQualifiedForeignKeyName());
    }
}