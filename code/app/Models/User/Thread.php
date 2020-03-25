<?php
declare(strict_types=1);

namespace App\Models\User;

use App\Contracts\Models\HasPolicyContract;
use App\Contracts\Models\HasValidationRulesContract;
use App\Models\BaseModelAbstract;
use App\Models\Traits\HasValidationRules;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

/**
 * Class Thread
 *
 * @package App\Models\User
 * @property int $id
 * @property string|null $topic
 * @property int|null $subject_id
 * @property string|null $subject_type
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read int|null $messages_count
 * @property-read int|null $users_count
 * @property-read Message|null $last_message
 * @property-read Collection|Message[] $messages
 * @property-read Collection|User[] $users
 * @method static Builder|Thread newModelQuery()
 * @method static Builder|Thread newQuery()
 * @method static Builder|Thread query()
 * @method static Builder|Thread whereCreatedAt($value)
 * @method static Builder|Thread whereDeletedAt($value)
 * @method static Builder|Thread whereId($value)
 * @method static Builder|Thread whereSubjectId($value)
 * @method static Builder|Thread whereSubjectType($value)
 * @method static Builder|Thread whereTopic($value)
 * @method static Builder|Thread whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Thread extends BaseModelAbstract implements HasPolicyContract, HasValidationRulesContract
{
    use HasValidationRules;

    /**
     * The url of the profile image
     *
     * @var array
     */
    protected $appends = [
        'last_message',
    ];

    /**
     * All messages in this thread
     *
     * @return HasMany
     */
    public function messages() : HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'desc');
    }

    /**
     * All users that are in this thread
     *
     * @return BelongsToMany
     */
    public function users() : BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * Get the URL for the profile image
     *
     * @return null|string
     */
    public function getLastMessageAttribute()
    {
        return $this->messages ? $this->messages->first() : null;
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
                'subject_type' => [
                    'bail',
                    'string',
                ],
                'subject_id' => [
                    'int',
                ],
                'users' => [
                    'array',
                ],
                'users.*' => [
                    'integer',
                    Rule::exists('users', 'id'),
                ],
            ],
            static::VALIDATION_RULES_CREATE => [
                static::VALIDATION_PREPEND_REQUIRED => [
                    'subject_type',
                ],
            ],
            static::VALIDATION_RULES_UPDATE => [
                static::VALIDATION_PREPEND_REQUIRED => [
                    'users',
                ],
                static::VALIDATION_PREPEND_NOT_PRESENT => [
                    'subject_type',
                    'subject_id',
                ],
            ],
        ];
    }
}
