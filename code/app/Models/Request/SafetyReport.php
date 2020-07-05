<?php
declare(strict_types=1);

namespace App\Models\Request;

use App\Contracts\Models\HasValidationRulesContract;
use App\Models\BaseModelAbstract;
use App\Models\Traits\HasValidationRules;
use App\Models\User\User;
use Eloquent;
use Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Request\SafetyReport
 *
 * @property int $id
 * @property int $request_id
 * @property int $reporter_id
 * @property string $description
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property-read \App\Models\User\User $reporter
 * @property-read \App\Models\Request\Request $request
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Request\SafetyReport newModelQuery()
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Request\SafetyReport newQuery()
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Request\SafetyReport query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Request\SafetyReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Request\SafetyReport whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Request\SafetyReport whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Request\SafetyReport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Request\SafetyReport whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Request\SafetyReport whereReporterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Request\SafetyReport whereRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Request\SafetyReport whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SafetyReport extends BaseModelAbstract implements HasValidationRulesContract
{
    use HasValidationRules;

    /**
     * The user that created this safety report
     *
     * @return BelongsTo
     */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    /**
     * The request this was made for
     *
     * @return BelongsTo
     */
    public function request(): BelongsTo
    {
        return $this->belongsTo(Request::class);
    }

    /**
     * @inheritDoc
     */
    public function buildModelValidationRules(...$params): array
    {
        return [
            self::VALIDATION_RULES_BASE => [
                'description' => [
                    'required',
                    'string',
                ],
            ],
        ];
    }
}