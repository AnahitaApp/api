<?php
declare(strict_types=1);

namespace App\Models\Wiki;

use App\Contracts\Models\HasValidationRulesContract;
use App\Events\Article\ArticleVersionCreatedEvent;
use App\Models\BaseModelAbstract;
use App\Models\Traits\HasValidationRules;
use App\Validators\ArticleVersion\SelectedIterationBelongsToArticleValidator;
use Eloquent;
use Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

/**
 * Class ArticleVersion
 *
 * @package App\Models\Wiki
 * @property int $id
 * @property int $article_id
 * @property int $iteration_id
 * @property string|null $name
 * @property Carbon|null $deleted_at
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property-read Article $article
 * @property-read Iteration $iteration
 * @method static EloquentJoinBuilder|ArticleVersion newModelQuery()
 * @method static EloquentJoinBuilder|ArticleVersion newQuery()
 * @method static EloquentJoinBuilder|ArticleVersion query()
 * @method static Builder|ArticleVersion whereArticleId($value)
 * @method static Builder|ArticleVersion whereCreatedAt($value)
 * @method static Builder|ArticleVersion whereDeletedAt($value)
 * @method static Builder|ArticleVersion whereId($value)
 * @method static Builder|ArticleVersion whereIterationId($value)
 * @method static Builder|ArticleVersion whereName($value)
 * @method static Builder|ArticleVersion whereUpdatedAt($value)
 * @mixin Eloquent
 */
class ArticleVersion extends BaseModelAbstract implements HasValidationRulesContract
{
    use HasValidationRules;

    /**
     * The article this version is for
     *
     * @return BelongsTo
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * The iteration that this version is for
     *
     * @return BelongsTo
     */
    public function iteration(): BelongsTo
    {
        return $this->belongsTo(Iteration::class);
    }

    /**
     * Build the model validation rules
     * @param array $params
     * @return array
     */
    public function buildModelValidationRules(...$params): array
    {
        return [
            static::VALIDATION_RULES_BASE => [
                'iteration_id' => [
                    'bail',
                    'required',
                    'int',
                    Rule::exists('iterations', 'id'),
                    SelectedIterationBelongsToArticleValidator::KEY,
                ],
            ],
        ];
    }
}