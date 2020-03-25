<?php
declare(strict_types=1);

namespace App\Models;

use App\Contracts\Models\HasPolicyContract;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Resource
 *
 * @property int $id
 * @property string $content
 * @property int $resource_id
 * @property string $resource_type
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Model|BaseModelAbstract $resource
 * @method static Builder|Resource newModelQuery()
 * @method static Builder|Resource newQuery()
 * @method static Builder|Resource query()
 * @method static Builder|Resource whereContent($value)
 * @method static Builder|Resource whereCreatedAt($value)
 * @method static Builder|Resource whereDeletedAt($value)
 * @method static Builder|Resource whereId($value)
 * @method static Builder|Resource whereResourceId($value)
 * @method static Builder|Resource whereResourceType($value)
 * @method static Builder|Resource whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Resource extends BaseModelAbstract implements HasPolicyContract
{
    /**
     * The database resource this is related to
     *
     * @return MorphTo
     */
    public function resource() : MorphTo
    {
        return $this->morphTo();
    }
}
