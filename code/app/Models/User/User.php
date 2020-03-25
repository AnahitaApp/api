<?php
declare(strict_types=1);

namespace App\Models\User;

use App\Contracts\Models\CanBeIndexedContract;
use App\Contracts\Models\HasPaymentMethodsContract;
use App\Contracts\Models\HasValidationRulesContract;
use App\Models\Asset;
use App\Models\Organization\Organization;
use App\Models\Organization\OrganizationManager;
use App\Models\Payment\PaymentMethod;
use App\Models\Resource;
use App\Models\Role;
use App\Models\Subscription\Subscription;
use App\Models\Traits\CanBeIndexed;
use App\Models\Traits\HasPaymentMethods;
use App\Models\Traits\HasSubscriptions;
use App\Models\Traits\HasValidationRules;
use App\Models\Vote\BallotCompletion;
use App\Models\Wiki\Article;
use App\Models\Wiki\Iteration;
use Carbon\Carbon;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Contracts\Models\HasPolicyContract;
use App\Models\BaseModelAbstract;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Validation\Rule;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * Class User
 *
 * @package App\Models\User
 * @property int $id
 * @property int|null $merged_to_id
 * @property string $email the email address of the user
 * @property string $name the full name of the user
 * @property string $password the password of the user
 * @property bool $allow_users_to_add_me
 * @property int|null $profile_image_id
 * @property string|null $about_me
 * @property string|null $stripe_customer_key
 * @property bool $receive_push_notifications
 * @property string|null $push_notification_key
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read null|string $profile_image_url
 * @property-read ProfileImage|null $profileImage
 * @property-read Resource $resource
 * @property-read Collection|Asset[] $assets
 * @property-read Collection|BallotCompletion[] $ballotCompletions
 * @property-read Collection|Article[] $createdArticles
 * @property-read Collection|Iteration[] $createdIterations
 * @property-read Collection|Message[] $messages
 * @property-read Collection|OrganizationManager[] $organizationManagers
 * @property-read Collection|PaymentMethod[] $paymentMethods
 * @property-read Collection|Role[] $roles
 * @property-read Collection|Subscription[] $subscriptions
 * @property-read Collection|Thread[] $threads
 * @property-read int|null $assets_count
 * @property-read int|null $ballot_completions_count
 * @property-read int|null $created_articles_count
 * @property-read int|null $created_iterations_count
 * @property-read int|null $messages_count
 * @property-read int|null $organization_managers_count
 * @property-read int|null $payment_methods_count
 * @property-read int|null $roles_count
 * @property-read int|null $subscriptions_count
 * @property-read int|null $threads_count
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @method static Builder|User whereAboutMe($value)
 * @method static Builder|User whereAllowUsersToAddMe($value)
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereDeletedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereMergedToId($value)
 * @method static Builder|User whereName($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User whereProfileImageId($value)
 * @method static Builder|User wherePushNotificationKey($value)
 * @method static Builder|User whereReceivePushNotifications($value)
 * @method static Builder|User whereStripeCustomerKey($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @mixin Eloquent
 */
class User extends BaseModelAbstract
    implements AuthenticatableContract, JWTSubject,
            HasPolicyContract, HasValidationRulesContract, HasPaymentMethodsContract,
            CanBeIndexedContract
{
    use Authenticatable, HasValidationRules, HasPaymentMethods, HasSubscriptions, CanBeIndexed;

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'deleted_at',
        'password',
    ];

    /**
     * The url of the profile image
     *
     * @var array
     */
    protected $appends = [
        'profile_image_url',
    ];

    /**
     * All assets this user has created
     *
     * @return HasMany
     */
    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    /**
     * The ballot completions the user has done
     *
     * @return HasMany
     */
    public function ballotCompletions(): HasMany
    {
        return $this->hasMany(BallotCompletion::class);
    }

    /**
     * The articles that were created by this user
     *
     * @return HasMany
     */
    public function createdArticles(): HasMany
    {
        return $this->hasMany(Article::class, 'created_by_id');
    }

    /**
     * The iterations that were created by this user
     *
     * @return HasMany
     */
    public function createdIterations(): HasMany
    {
        return $this->hasMany(Iteration::class, 'created_by_id');
    }

    /**
     * The messages that were sent to a user
     *
     * @return HasMany
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'to_id');
    }

    /**
     * All organization manager relations this user has
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
     * The resource object for this user
     *
     * @return MorphOne
     */
    public function resource() : MorphOne
    {
        return $this->morphOne(Resource::class, 'resource');
    }

    /**
     * What roles this user has
     *
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Any threads this user is apart of
     *
     * @return BelongsToMany
     */
    public function threads(): BelongsToMany
    {
        return $this->belongsToMany(Thread::class);
    }

    /**
     * Add a Role to this user
     *
     * @param int $roleId
     * @return $this
     */
    public function addRole(int $roleId)
    {
        $this->roles()->attach($roleId);
        return $this;
    }

    /**
     * Does this have the role
     *
     * @param mixed $roles
     * @return bool
     */
    public function hasRole($roles)
    {
        $roles = (array)$roles;
        return $this->roles()->whereIn('id', $roles)->exists();
    }

    /**
     * Determines whether or not the user can manage the organization.
     *
     * @param Organization $organization
     * @param int|array $role
     *          If the manager role is passed in then this will return true for both the manager role and admin role.
     *          The admin role will only check for the admin role.
     * @return bool
     */
    public function canManageOrganization(Organization $organization, $role = Role::ORGANIZATION_MANAGER): bool
    {
        $roles = is_array($role) ? $role : [$role];
        if (!in_array(Role::ORGANIZATION_ADMIN, $roles)) {
            $roles[] = Role::ORGANIZATION_ADMIN;
        }
        return $this->organizationManagers->first(fn (OrganizationManager $organizationManager) =>
            in_array($organizationManager->role_id, $roles) && $organizationManager->organization_id === $organization->id
        ) != null;
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
     * The name of the morph relation
     *
     * @return string
     */
    public function morphRelationName(): string
    {
        return 'user';
    }

    /**
     * Gets the content string to index
     *
     * @return string
     */
    public function getContentString(): ?string
    {
        return $this->name;
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->id;
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Build the model validation rules
     * @param array $params
     * @return array
     */
    public function buildModelValidationRules(...$params): array
    {
        $emailUnique = Rule::unique('users', 'email');

        $userId = count($params) ? $params[0]->id : null;

        if ($userId) {
            $emailUnique->ignore($userId);
        }

        return [
            static::VALIDATION_RULES_BASE => [
                'email' => [
                    'string',
                    'max:120',
                    'email',
                    $emailUnique,
                ],
                'name' => [
                    'string',
                    'max:120',
                ],
                'password' => [
                    'string',
                    'min:6',
                ],
                'push_notification_key' => [
                    'string',
                    'max:512'
                ],
                'about_me' => [
                    'string',
                ],
                'allow_users_to_add_me' => [
                    'boolean',
                ],
                'receive_push_notifications' => [
                    'boolean',
                ],
            ],
        ];
    }

    /**
     * Swagger definition below...
     *
     * @SWG\Definition(
     *     type="object",
     *     definition="User",
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
     *         property="email",
     *         type="string",
     *         maxLength=120,
     *         description="The email address of this user"
     *     ),
     *     @SWG\Property(
     *         property="name",
     *         type="string",
     *         maxLength=120,
     *         description="The name of this user"
     *     ),
     *     @SWG\Property(
     *         property="password",
     *         type="string",
     *         minLength=6,
     *         description="The password for this user. This cannot be read, and it can only be set."
     *     ),
     *     @SWG\Property(
     *         property="roles",
     *         description="The roles that this user has.",
     *         type="array",
     *         @SWG\Items(ref="#/definitions/Role")
     *     ),
     *     @SWG\Property(
     *         property="created_articles",
     *         description="The articles that this user created.",
     *         type="array",
     *         @SWG\Items(ref="#/definitions/Articles")
     *     ),
     *     @SWG\Property(
     *         property="created_iterations",
     *         description="The iterations that this user created.",
     *         type="array",
     *         @SWG\Items(ref="#/definitions/Iterations")
     *     )
     * )
     */
}
