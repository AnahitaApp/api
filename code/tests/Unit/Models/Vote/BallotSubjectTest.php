<?php
declare(strict_types=1);

namespace Tests\Unit\Models\Vote;

use App\Models\Vote\BallotSubject;
use Tests\TestCase;

/**
 * Class BallotCompletionTest
 * @package Tests\Unit\Models\Vote
 */
class BallotSubjectTest extends TestCase
{
    public function testBallot()
    {
        $model = new BallotSubject();
        $relation = $model->ballot();

        $this->assertEquals('ballots.id', $relation->getQualifiedOwnerKeyName());
        $this->assertEquals('ballot_subjects.ballot_id', $relation->getQualifiedForeignKeyName());
    }

    public function testUser()
    {
        $model = new BallotSubject();
        $relation = $model->subject();

        $this->assertEquals('subject_type', $relation->getMorphType());
        $this->assertEquals('ballot_subjects.subject_id', $relation->getQualifiedForeignKeyName());
    }

    public function testVotes()
    {
        $model = new BallotSubject();
        $relation = $model->votes();

        $this->assertEquals('ballot_subjects.id', $relation->getQualifiedParentKeyName());
        $this->assertEquals('votes.ballot_subject_id', $relation->getQualifiedForeignKeyName());
    }
}