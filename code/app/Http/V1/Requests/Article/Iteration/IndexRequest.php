<?php
declare(strict_types=1);

namespace App\Http\V1\Requests\Article\Iteration;

use App\Http\V1\Requests\BaseAuthenticatedRequestAbstract;
use App\Http\V1\Requests\Traits\HasNoPolicyParameters;
use App\Http\V1\Requests\Traits\HasNoRules;
use App\Models\Wiki\Iteration;
use App\Policies\Wiki\IterationPolicy;

/**
 * Class IndexRequest
 * @package App\Http\V1\Requests\Article\Iteration
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
        return IterationPolicy::ACTION_LIST;
    }

    /**
     * Get the class name of the policy that this request utilizes
     *
     * @return string
     */
    protected function getPolicyModel(): string
    {
        return Iteration::class;
    }

    /**
     * All expands that are allowed for this request
     *
     * @return array
     */
    public function allowedExpands(): array
    {
        return [
            'article',
            'createdBy',
        ];
    }
}