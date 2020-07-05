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
 * App\Models\Vote\Ballot
 *
 * @property int $id
 * @property string|null $name
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Vote\BallotCompletion[] $ballotCompletions
 * @property-read int|null $ballot_completions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Vote\BallotSubject[] $ballotSubjects
 * @property-read int|null $ballot_subjects_count
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Vote\Ballot newModelQuery()
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Vote\Ballot newQuery()
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Vote\Ballot query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vote\Ballot whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vote\Ballot whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vote\Ballot whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vote\Ballot whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vote\Ballot whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Vote\Ballot whereUpdatedAt($value)
 * @mixin \Eloquent
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