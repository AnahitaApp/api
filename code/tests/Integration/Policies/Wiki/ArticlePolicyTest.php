<?php
declare(strict_types=1);

namespace Tests\Integration\Policies\Wiki;

use App\Models\Role;
use App\Models\Wiki\Article;
use App\Policies\Wiki\ArticlePolicy;
use Tests\DatabaseSetupTrait;
use Tests\TestCase;
use Tests\Traits\RolesTesting;

/**
 * Class ArticlePolicyTest
 * @package Tests\Integration\Policies\Wiki
 */
class ArticlePolicyTest extends TestCase
{
    use DatabaseSetupTrait, RolesTesting;

    public function testAllSuccess()
    {
        $policy = new ArticlePolicy();

        foreach ([Role::ARTICLE_EDITOR, Role::ARTICLE_VIEWER] as $role) {
            $user = $this->getUserOfRole($role);

            $this->assertTrue($policy->all($user));
        }
    }

    public function testAllBlocks()
    {
        $policy = new ArticlePolicy();

        foreach ($this->rolesWithoutAdmins([Role::ARTICLE_EDITOR, Role::ARTICLE_VIEWER]) as $role) {
            $user = $this->getUserOfRole($role);

            $this->assertFalse($policy->all($user));
        }
    }

    public function testViewSuccess()
    {
        $policy = new ArticlePolicy();

        foreach ([Role::ARTICLE_EDITOR, Role::ARTICLE_VIEWER] as $role) {
            $user = $this->getUserOfRole($role);

            $this->assertTrue($policy->view($user, new Article()));
        }
    }

    public function testViewBlocks()
    {
        $policy = new ArticlePolicy();

        foreach ($this->rolesWithoutAdmins([Role::ARTICLE_EDITOR, Role::ARTICLE_VIEWER]) as $role) {
            $user = $this->getUserOfRole($role);

            $this->assertFalse($policy->view($user, new Article()));
        }
    }

    public function testCreateSuccess()
    {
        $policy = new ArticlePolicy();

        $user = $this->getUserOfRole(Role::ARTICLE_EDITOR);

        $this->assertTrue($policy->create($user));
    }

    public function testCreateBlocks()
    {
        $policy = new ArticlePolicy();

        foreach ($this->rolesWithoutAdmins([Role::ARTICLE_EDITOR]) as $role) {
            $user = $this->getUserOfRole($role);

            $this->assertFalse($policy->create($user));
        }
    }

    public function testUpdateSuccess()
    {
        $policy = new ArticlePolicy();

        $user = $this->getUserOfRole(Role::ARTICLE_EDITOR);

        $article = new Article([
            'created_by_id' => $user->id,
        ]);

        $this->assertTrue($policy->update($user, $article));
    }

    public function testUpdateBlocks()
    {
        $policy = new ArticlePolicy();

        foreach ($this->rolesWithoutAdmins([Role::ARTICLE_EDITOR]) as $role) {
            $user = $this->getUserOfRole($role);

            $article = new Article([
                'created_by_id' => $user->id,
            ]);

            $this->assertFalse($policy->update($user, $article));
        }

        $user = $this->getUserOfRole(Role::ARTICLE_EDITOR);

        $article = new Article();
        $this->assertFalse($policy->update($user, $article));
    }
}