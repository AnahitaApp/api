<?php
declare(strict_types=1);

namespace App\Models\User;

use App\Models\Asset;
use App\Models\Organization\Organization;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * App\Models\User\IdentificationCard
 *
 * @property int $id
 * @property int|null $owner_id
 * @property string|null $name
 * @property string|null $caption
 * @property string $url
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property string $owner_type
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $owner
 * @property-read \App\Models\User\User $user
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\User\IdentificationCard newModelQuery()
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\User\IdentificationCard newQuery()
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\User\IdentificationCard query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\IdentificationCard whereCaption($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\IdentificationCard whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\IdentificationCard whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\IdentificationCard whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\IdentificationCard whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\IdentificationCard whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\IdentificationCard whereOwnerType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\IdentificationCard whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\User\IdentificationCard whereUrl($value)
 * @mixin \Eloquent
 */
class IdentificationCard extends Asset
{
    /**
     * @return HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }
}
