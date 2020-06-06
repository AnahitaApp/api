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
 * Class ProfileImage
 *
 * @package App\Models\User
 * @property int $id
 * @property string $url
 * @property string|null $caption
 * @property string|null $name
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read int|null $conferences_count
 * @property-read int|null $events_count
 * @property-read Model|User|Organization $owner
 * @property int|null $owner_id
 * @property string $owner_type
 * @method static Builder|IdentificationCard newModelQuery()
 * @method static Builder|IdentificationCard newQuery()
 * @method static Builder|IdentificationCard query()
 * @method static Builder|IdentificationCard whereCaption($value)
 * @method static Builder|IdentificationCard whereCreatedAt($value)
 * @method static Builder|IdentificationCard whereDeletedAt($value)
 * @method static Builder|IdentificationCard whereId($value)
 * @method static Builder|IdentificationCard whereName($value)
 * @method static Builder|IdentificationCard whereOwnerId($value)
 * @method static Builder|IdentificationCard whereOwnerType($value)
 * @method static Builder|IdentificationCard whereUpdatedAt($value)
 * @method static Builder|IdentificationCard whereUrl($value)
 * @mixin Eloquent
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
