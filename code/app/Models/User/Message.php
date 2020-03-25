<?php
declare(strict_types=1);

namespace App\Models\User;

use App\Contracts\Models\HasPolicyContract;
use App\Contracts\Models\HasValidationRulesContract;
use App\Models\Traits\HasValidationRules;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Events\Message\MessageCreatedEvent;
use App\Models\BaseModelAbstract;

/**
 * Class Message
 *
 * @package App\Models\User
 * @property int $id
 * @property string $subject
 * @property string $template
 * @property mixed $data
 * @property string $email
 * @property int $to_id
 * @property int $from_id
 * @property int|null $thread_id
 * @property array $via
 * @property string|null $action
 * @property Carbon|null $scheduled_at
 * @property Carbon|null $sent_at
 * @property Carbon|null $seen_at
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $to
 * @property-read User $from
 * @property-read Thread|null $thread
 * @method static Builder|Message whereAction($value)
 * @method static Builder|Message whereCreatedAt($value)
 * @method static Builder|Message whereData($value)
 * @method static Builder|Message whereDeletedAt($value)
 * @method static Builder|Message whereEmail($value)
 * @method static Builder|Message whereFromId($value)
 * @method static Builder|Message whereId($value)
 * @method static Builder|Message whereScheduledAt($value)
 * @method static Builder|Message whereSeenAt($value)
 * @method static Builder|Message whereSentAt($value)
 * @method static Builder|Message whereSubject($value)
 * @method static Builder|Message whereTemplate($value)
 * @method static Builder|Message whereThreadId($value)
 * @method static Builder|Message whereToId($value)
 * @method static Builder|Message whereUpdatedAt($value)
 * @method static Builder|Message whereUserId($value)
 * @method static Builder|Message whereVia($value)
 * @mixin Eloquent
 * @method static Builder|Message newModelQuery()
 * @method static Builder|Message newQuery()
 * @method static Builder|Message query()
 */
class Message extends BaseModelAbstract implements HasPolicyContract, HasValidationRulesContract
{
    use HasValidationRules;

    const VIA_EMAIL = 'email';
    const VIA_PUSH_NOTIFICATION = 'push';

    /**
     * @var array
     */
    protected $dates = [
        'seen_at',
        'sent_at',
        'scheduled_at',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'data' => 'array',
        'via' => 'array',
    ];

    /**
     * Array of events that need to be dispatched
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => MessageCreatedEvent::class
    ];

    /**
     * Each message belongs to a user
     *
     * @return BelongsTo
     */
    public function from() : BelongsTo
    {
        return $this->belongsTo(User::class, 'from_id');
    }

    /**
     * The thread that this message is in
     *
     * @return BelongsTo
     */
    public function thread() : BelongsTo
    {
        return $this->belongsTo(Thread::class);
    }

    /**
     * Each message belongs to a user
     *
     * @return BelongsTo
     */
    public function to() : BelongsTo
    {
        return $this->belongsTo(User::class, 'to_id');
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
                'message' => [
                    'string',
                ],
                'seen' => [
                    'boolean',
                ]
            ],
            static::VALIDATION_RULES_CREATE => [
                static::VALIDATION_PREPEND_REQUIRED => [
                    'message',
                ],
            ],
            static::VALIDATION_RULES_UPDATE => [
                static::VALIDATION_PREPEND_NOT_PRESENT => [
                    'message',
                ],
            ],
        ];
    }
}