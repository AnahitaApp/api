<?php
declare(strict_types=1);

namespace Tests\Integration\V1\Middleware;

use App\Models\Role;
use App\Models\Wiki\Article;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;

/**
 * Class SearchFilteringMiddlewareTest
 * @package Tests\Integration\V1\Middleware
 */
class SearchFilteringMiddlewareTest extends TestCase
{
    use DatabaseSetupTrait;

    public function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
        $this->actAs(Role::ARTICLE_VIEWER);
    }

    public function testSearchWithLike()
    {
        factory(Article::class, 1)->create(['title' => 'h']);
        factory(Article::class, 1)->create(['title' => 'cart']);
        factory(Article::class, 1)->create(['title' => 'can']);
        factory(Article::class, 1)->create(['title' => 'the']);
        factory(Article::class, 1)->create(['title' => 'butts']);

        // first page
        $response = $this->json('GET', '/v1/articles?search[title]=like,*h*');
        $response->assertJson([
                'total' => 2,
                'current_page' => 1,
                'per_page' => 10,
                'from' => 1,
                'to' => 2,
                'last_page' => 1
            ]);

        $response->assertStatus(200);
    }

    public function testFilter()
    {
        factory(Article::class, 1)->create(['title' => 'h']);
        factory(Article::class, 1)->create(['title' => 'cart']);
        factory(Article::class, 1)->create(['title' => 'can']);
        factory(Article::class, 1)->create(['title' => 'the']);
        factory(Article::class, 1)->create(['title' => 'butts']);

        // first page
        $response = $this->json('GET', '/v1/articles?filter[title]=butts');
        $response->assertJson([
                'total' => 1,
                'current_page' => 1,
                'per_page' => 10,
                'from' => 1,
                'to' => 1,
                'last_page' => 1
            ]);

        $response->assertStatus(200);
    }
}