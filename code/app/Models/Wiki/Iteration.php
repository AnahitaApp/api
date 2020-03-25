<?php
declare(strict_types=1);

namespace App\Models\Wiki;

use App\Contracts\Models\HasPolicyContract;
use App\Models\BaseModelAbstract;
use App\Models\User\User;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Iteration
 *
 * @package App\Models\Wiki
 * @property int $id
 * @property string $content
 * @property int $created_by_id
 * @property int $article_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Article $article
 * @property-read User $createdBy
 * @property-read ArticleVersion $version
 * @method static Builder|Iteration newModelQuery()
 * @method static Builder|Iteration newQuery()
 * @method static Builder|Iteration query()
 * @method static Builder|Iteration whereArticleId($value)
 * @method static Builder|Iteration whereContent($value)
 * @method static Builder|Iteration whereCreatedAt($value)
 * @method static Builder|Iteration whereCreatedById($value)
 * @method static Builder|Iteration whereDeletedAt($value)
 * @method static Builder|Iteration whereId($value)
 * @method static Builder|Iteration whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Iteration extends BaseModelAbstract implements HasPolicyContract
{
    /**
     * The article that this iteration is for
     *
     * @return BelongsTo
     */
    public function article() : BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * The version of this article if there is one
     *
     * @return HasOne
     */
    public function version(): HasOne
    {
        return $this->hasOne(ArticleVersion::class);
    }

    /**
     * Makes sure everything is by default ordered by the created at date in reverse
     *
     * @return Builder
     */
    public function newQuery()
    {
        $query = parent::newQuery();

        $query->orderBy('created_at', 'desc');

        return $query;
    }

    /**
     * The user that originally created this article
     *
     * @return BelongsTo
     */
    public function createdBy() : BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    /**
     * Swagger definition below...
     *
     * @SWG\Definition(
     *     type="object",
     *     definition="Iteration",
     *     @SWG\Property(
     *         property="id",
     *         type="integer",
     *         format="int32",
     *         description="The primary id of the model",
     *         readOnly=true
     *     ),
     *     @SWG\Property(
     *         property="created_at",
     *         type="string",
     *         format="date-time",
     *         description="UTC date of the time this was created",
     *         readOnly=true
     *     ),
     *     @SWG\Property(
     *         property="updated_at",
     *         type="string",
     *         format="date-time",
     *         description="UTC date of the time this was last updated",
     *         readOnly=true
     *     ),
     *     @SWG\Property(
     *         property="content",
     *         type="string",
     *         description="The content of this iteration."
     *     ),
     *     @SWG\Property(
     *         property="article_id",
     *         type="integer",
     *         format="int32",
     *         description="The primary id of the article that created this iteration is for.",
     *         readOnly=true
     *     ),
     *     @SWG\Property(
     *         property="created_by_id",
     *         type="integer",
     *         format="int32",
     *         description="The primary id of the user that created this article.",
     *         readOnly=true
     *     ),
     *     @SWG\Property(
     *         property="createdBy",
     *         description="The users that created this article.",
     *         type="array",
     *         @SWG\Items(ref="#/definitions/User")
     *     ),
     *     @SWG\Property(
     *         property="article",
     *         description="The article that this iteration is for.",
     *         type="array",
     *         @SWG\Items(ref="#/definitions/Article")
     *     )
     * )
     */
}