<?php
declare(strict_types=1);

namespace Tests\Integration\V4\Middleware;

use App\Models\Role;
use App\Models\Wiki\Article;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;

/**
 * Class Issue404IfPageAfterPaginationTest
 * @package Tests\Integration\ApiCalls
 */
class Issue404IfPageAfterPaginationTest extends TestCase
{
    use DatabaseSetupTrait;

    public function setUp(): void
    {
        parent::setUp();
        $this->setupDatabase();
    }

    public function testGetPaginationResult()
    {
        $this->actAs(Role::ARTICLE_VIEWER);

        factory(Article::class, 3)->create();

        // first page test 200
        $response = $this->json('GET', '/v1/articles?page=1&limit=2');
        $response->assertStatus(200);

        // test second page has 404
        $response = $this->json('GET', '/v1/articles?page=3&limit=2');
        $response->assertStatus(404);
    }
}