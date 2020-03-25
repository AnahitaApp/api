<?php
declare(strict_types=1);

namespace App\Models\Vote;

use App\Models\BaseModelAbstract;
use Eloquent;
use Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Class Ballot
 *
 * @package App\Models\Vote
 * @property int $id
 * @property string|null $name
 * @property string $type
 * @property Carbon|null $deleted_at
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property-read int|null $ballot_completions_count
 * @property-read int|null $ballot_subjects_count
 * @property-read Collection|BallotCompletion[] $ballotCompletions
 * @property-read Collection|BallotSubject[] $ballotSubjects
 * @method static EloquentJoinBuilder|Ballot newModelQuery()
 * @method static EloquentJoinBuilder|Ballot newQuery()
 * @method static EloquentJoinBuilder|Ballot query()
 * @method static Builder|Ballot whereCreatedAt($value)
 * @method static Builder|Ballot whereDeletedAt($value)
 * @method static Builder|Ballot whereId($value)
 * @method static Builder|Ballot whereName($value)
 * @method static Builder|Ballot whereType($value)
 * @method static Builder|Ballot whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Ballot extends BaseModelAbstract
{
    /**
     * This ballot type when there is a single subject and the user chooses yes or no
     */
    const TYPE_SINGLE_OPTION = 'single_option';

    /**
     * All times someone has completed this ballot
     *
     * @return HasMany
     */
    public function ballotCompletions(): HasMany
    {
        return $this->hasMany(BallotCompletion::class);
    }

    /**
     * All subjects contained in this ballot
     *
     * @return HasMany
     */
    public function ballotSubjects(): HasMany
    {
        return $this->hasMany(BallotSubject::class);
    }
}