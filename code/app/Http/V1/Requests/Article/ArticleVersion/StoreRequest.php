<?php
declare(strict_types=1);

namespace App\Http\V1\Requests\Article\ArticleVersion;

use App\Http\V1\Requests\BaseAuthenticatedRequestAbstract;
use App\Http\V1\Requests\Traits\HasNoExpands;
use App\Models\Wiki\ArticleVersion;
use App\Policies\Wiki\ArticleVersionPolicy;

/**
 * Class StoreRequest
 * @package App\Http\V1\Requests\Article\ArticleVersion
 */
class StoreRequest extends BaseAuthenticatedRequestAbstract
{
    use HasNoExpands;

    /**
     * Get the policy action for the guard
     *
     * @return string
     */
    protected function getPolicyAction(): string
    {
        return ArticleVersionPolicy::ACTION_CREATE;
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
     * Get validation rules for the create request
     *
     * @param ArticleVersion $articleVersion
     * @return array
     */
    public function rules(ArticleVersion $articleVersion) : array
    {
        return $articleVersion->getValidationRules(ArticleVersion::VALIDATION_RULES_CREATE);
    }

    /**
     * Gets any additional parameters needed for the policy function
     *
     * @return array
     */
    protected function getPolicyParameters(): array
    {
        return [
            $this->route('article'),
        ];
    }
}