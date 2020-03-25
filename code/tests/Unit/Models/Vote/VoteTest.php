<?php
declare(strict_types=1);

namespace Tests\Unit\Models\Vote;

use App\Models\Vote\Vote;
use Tests\TestCase;

/**
 * Class VoteTest
 * @package Tests\Unit\Models\Vote
 */
class VoteTest extends TestCase
{
    public function testBallotCompletion()
    {
        $model = new Vote();
        $relation = $model->ballotCompletion();

        $this->assertEquals('ballot_completions.id', $relation->getQualifiedOwnerKeyName());
        $this->assertEquals('votes.ballot_completion_id', $relation->getQualifiedForeignKeyName());
    }

    public function testBallotSubject()
    {
        $model = new Vote();
        $relation = $model->ballotSubject();

        $this->assertEquals('ballot_subjects.id', $relation->getQualifiedOwnerKeyName());
        $this->assertEquals('votes.ballot_subject_id', $relation->getQualifiedForeignKeyName());
    }
}