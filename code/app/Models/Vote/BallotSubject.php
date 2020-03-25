<?php
declare(strict_types=1);

namespace App\Models\Vote;

use App\Models\BaseModelAbstract;
use Eloquent;
use Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * Class BallotSubject
 *
 * @package App\Models\Vote
 * @property int $id
 * @property int $ballot_id
 * @property int $subject_id
 * @property string $subject_type
 * @property int $votes_cast
 * @property int $vote_count
 * @property Carbon|null $deleted_at
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property-read int|null $votes_count
 * @property-read Ballot $ballot
 * @property-read Collection|BallotSubject[] $subject
 * @property-read Collection|Vote[] $votes
 * @method static EloquentJoinBuilder|BallotSubject newModelQuery()
 * @method static EloquentJoinBuilder|BallotSubject newQuery()
 * @method static EloquentJoinBuilder|BallotSubject query()
 * @method static Builder|BallotSubject whereBallotId($value)
 * @method static Builder|BallotSubject whereCreatedAt($value)
 * @method static Builder|BallotSubject whereDeletedAt($value)
 * @method static Builder|BallotSubject whereId($value)
 * @method static Builder|BallotSubject whereSubjectId($value)
 * @method static Builder|BallotSubject whereSubjectType($value)
 * @method static Builder|BallotSubject whereUpdatedAt($value)
 * @method static Builder|BallotSubject whereVoteCount($value)
 * @method static Builder|BallotSubject whereVotesCast($value)
 * @mixin Eloquent
 */
class BallotSubject extends BaseModelAbstract
{
    /**
     * The ballot this is apart of
     *
     * @return BelongsTo
     */
    public function ballot(): BelongsTo
    {
        return $this->belongsTo(Ballot::class);
    }

    /**
     * The subject that this represents
     *
     * @return MorphTo
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * The votes that have been submitted for this ballot subject
     *
     * @return HasMany
     */
    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }
}