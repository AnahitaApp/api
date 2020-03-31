<?php
declare(strict_types=1);

namespace App\Models\Request;

use App\Models\BaseModelAbstract;
use App\Models\User\User;
use Eloquent;
use Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class SafetyReportRepository
 *
 * @package App\Models\Request
 * @property int $id
 * @property int $request_id
 * @property int $reporter_id
 * @property string $description
 * @property string|null $notes
 * @property Carbon|null $deleted_at
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property-read User $reporter
 * @property-read Request $request
 * @method static Builder|SafetyReport whereCreatedAt($value)
 * @method static Builder|SafetyReport whereDeletedAt($value)
 * @method static Builder|SafetyReport whereDescription($value)
 * @method static Builder|SafetyReport whereId($value)
 * @method static Builder|SafetyReport whereNotes($value)
 * @method static Builder|SafetyReport whereReporterId($value)
 * @method static Builder|SafetyReport whereRequestId($value)
 * @method static Builder|SafetyReport whereUpdatedAt($value)
 * @method static EloquentJoinBuilder|SafetyReport newModelQuery()
 * @method static EloquentJoinBuilder|SafetyReport newQuery()
 * @method static EloquentJoinBuilder|SafetyReport query()
 * @mixin Eloquent
 */
class SafetyReport extends BaseModelAbstract
{
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
}