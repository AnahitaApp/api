<?php
declare(strict_types=1);

namespace App\Models\Vote;

use App\Models\BaseModelAbstract;
use App\Models\User\User;
use Eloquent;
use Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Class BallotCompletion
 *
 * @package App\Models\Vote
 * @property int $id
 * @property int $ballot_id
 * @property int $user_id
 * @property Carbon|null $deleted_at
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property-read int|null $votes_count
 * @property-read Ballot $ballot
 * @property-read User $user
 * @property-read Collection|Vote[] $votes
 * @method static EloquentJoinBuilder|BallotCompletion newModelQuery()
 * @method static EloquentJoinBuilder|BallotCompletion newQuery()
 * @method static EloquentJoinBuilder|BallotCompletion query()
 * @method static Builder|BallotCompletion whereBallotId($value)
 * @method static Builder|BallotCompletion whereCreatedAt($value)
 * @method static Builder|BallotCompletion whereDeletedAt($value)
 * @method static Builder|BallotCompletion whereId($value)
 * @method static Builder|BallotCompletion whereUpdatedAt($value)
 * @method static Builder|BallotCompletion whereUserId($value)
 * @mixin Eloquent
 */
class BallotCompletion extends BaseModelAbstract
{
    /**
     * The ballot that was completed
     *
     * @return BelongsTo
     */
    public function ballot(): BelongsTo
    {
        return $this->belongsTo(Ballot::class);
    }

    /**
     * THe user that completed this ballot
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * All votes that were cat in this ballot
     *
     * @return HasMany
     */
    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }
}