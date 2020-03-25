<?php
declare(strict_types=1);

namespace Tests\Unit\Models\Vote;

use App\Models\Vote\Ballot;
use Tests\TestCase;

/**
 * Class BallotTest
 * @package Tests\Unit\Models\Vote
 */
class BallotTest extends TestCase
{
    public function testBallotCompletions()
    {
        $model = new Ballot();
        $relation = $model->ballotCompletions();

        $this->assertEquals('ballots.id', $relation->getQualifiedParentKeyName());
        $this->assertEquals('ballot_completions.ballot_id', $relation->getQualifiedForeignKeyName());
    }

    public function testBallotSubjects()
    {
        $model = new Ballot();
        $relation = $model->ballotSubjects();

        $this->assertEquals('ballots.id', $relation->getQualifiedParentKeyName());
        $this->assertEquals('ballot_subjects.ballot_id', $relation->getQualifiedForeignKeyName());
    }
}