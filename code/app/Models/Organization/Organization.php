<?php
declare(strict_types=1);

namespace App\Models\Organization;

use App\Contracts\Models\HasPaymentMethodsContract;
use App\Contracts\Models\HasValidationRulesContract;
use App\Contracts\Models\IsAnEntity;
use App\Models\Asset;
use App\Models\BaseModelAbstract;
use App\Models\Role;
use App\Models\Traits\HasPaymentMethods;
use App\Models\Traits\HasSubscriptions;
use App\Models\Traits\HasValidationRules;
use App\Models\User\ProfileImage;
use App\Models\User\User;
use Eloquent;
use Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;

/**
 * Class Organization
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property int|null $profile_image_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Asset[] $assets
 * @property-read int|null $assets_count
 * @property-read null|string $profile_image_url
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Organization\Location[] $locations
 * @property-read int|null $locations_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Organization\OrganizationManager[] $organizationManagers
 * @property-read int|null $organization_managers_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Payment\PaymentMethod[] $paymentMethods
 * @property-read int|null $payment_methods_count
 * @property-read \App\Models\User\ProfileImage|null $profileImage
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Subscription\Subscription[] $subscriptions
 * @property-read int|null $subscriptions_count
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Organization\Organization newModelQuery()
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Organization\Organization newQuery()
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Organization\Organization query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organization\Organization whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organization\Organization whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organization\Organization whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organization\Organization whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organization\Organization whereProfileImageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organization\Organization whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Organization extends BaseModelAbstract
    implements HasValidationRulesContract, IsAnEntity, HasPaymentMethodsContract
{
    use HasValidationRules, HasSubscriptions, HasPaymentMethods;

    /**
     * All assets this user has created
     *
     * @return MorphMany
     */
    public function assets(): MorphMany
    {
        return $this->morphMany(Asset::class, 'owner');
    }

    /**
     * All locations in this organization
     *
     * @return HasMany
     */
    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    /**
     * All organization managers in this organization
     *
     * @return HasMany
     */
    public function organizationManagers(): HasMany
    {
        return $this->hasMany(OrganizationManager::class);
    }

    /**
     * The asset that contains the profile image for this user
     *
     * @return BelongsTo
     */
    public function profileImage() : BelongsTo
    {
        return $this->belongsTo(ProfileImage::class);
    }

    /**
     * Get the URL for the profile image
     *
     * @return null|string
     */
    public function getProfileImageUrlAttribute()
    {
        return $this->profileImage ? $this->profileImage->url : null;
    }

    /**
     * @inheritDoc
     */
    public function morphRelationName(): string
    {
        return 'organization';
    }

    /**
     * @inheritDoc
     */
    public function canUserManageEntity(User $user, int $role = Role::MANAGER): bool
    {
        return $user->canManageOrganization($this, $role);
    }

    /**
     * @param mixed ...$params
     * @return array
     */
    public function buildModelValidationRules(...$params): array
    {
        return [
            self::VALIDATION_RULES_BASE => [
                'name' => [
                    'string',
                    'max:120',
                ],
            ],
            self::VALIDATION_RULES_CREATE => [
                self::VALIDATION_PREPEND_REQUIRED => [
                    'name',
                ],
            ],
        ];
    }
}