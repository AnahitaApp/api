<?php
declare(strict_types=1);

namespace App\Models;

use App\Contracts\Models\HasPolicyContract;
use App\Models\User\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * Class Role
 *
 * @property int $id
 * @property string $name
 * @property Carbon|null $deleted_at
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property-read Collection|User[] $users
 * @property-read int|null $users_count
 * @method static Builder|Role newModelQuery()
 * @method static Builder|Role newQuery()
 * @method static Builder|Role query()
 * @method static Builder|Role whereCreatedAt($value)
 * @method static Builder|Role whereDeletedAt($value)
 * @method static Builder|Role whereId($value)
 * @method static Builder|Role whereName($value)
 * @method static Builder|Role whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Role extends BaseModelAbstract implements HasPolicyContract
{
    const APP_USER = 1;
    const SUPER_ADMIN = 2;
    const ARTICLE_VIEWER = 3;
    const ARTICLE_EDITOR = 4;
    const ORGANIZATION_ADMIN = 10;
    const ORGANIZATION_MANAGER = 11;
    // Add more roles here start with 100 in order to avoid application collision

    /**
     * @var array[string] the roles (usefully mainly for testing I suppose)
     */
    const ROLES = [
        self::APP_USER,
        self::SUPER_ADMIN,
        self::ARTICLE_VIEWER,
        self::ARTICLE_EDITOR,
        // Add application specific roles here too
    ];

    /**
     * All roles that are related to an organization
     */
    const ORGANIZATION_ROLES = [
        self::ORGANIZATION_ADMIN,
        self::ORGANIZATION_MANAGER,
        // Add more organization roles here
    ];

    /**
     * Has many users
     *
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    /**
     * Swagger definition below...
     *
     * @SWG\Definition(
     *     type="object",
     *     definition="Role",
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
     *         property="name",
     *         type="string",
     *         maxLength=32,
     *         description="The name of the role"
     *     ),
     * )
     */
}
