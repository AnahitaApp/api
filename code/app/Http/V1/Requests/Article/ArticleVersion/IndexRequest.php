<?php
declare(strict_types=1);

namespace App\Http\V1\Requests\Article\ArticleVersion;

use App\Http\V1\Requests\BaseAuthenticatedRequestAbstract;
use App\Http\V1\Requests\Traits\HasNoPolicyParameters;
use App\Http\V1\Requests\Traits\HasNoRules;
use App\Models\Wiki\ArticleVersion;
use App\Policies\Wiki\ArticleVersionPolicy;

/**
 * Class IndexRequest
 * @package App\Http\V1\Requests\Article\ArticleVersion
 */
class IndexRequest extends BaseAuthenticatedRequestAbstract
{
    use HasNoRules, HasNoPolicyParameters;

    /**
     * Get the policy action for the guard
     *
     * @return string
     */
    protected function getPolicyAction(): string
    {
        return ArticleVersionPolicy::ACTION_LIST;
    }

    /**
     * Get the class name of the policy that this request utilizes
     *
     * @return string
     */
    protected function getPolicyModel(): string
    {
        return ArticleVersion::class;
    }

    /**
     * All expands that are allowed for this request
     *
     * @return array
     */
    public function allowedExpands(): array
    {
        return [
            'iteration',
            'iteration.createdBy',
        ];
    }
}