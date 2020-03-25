<?php
declare(strict_types=1);

namespace Tests\Unit\Models\Vote;

use App\Models\Vote\BallotCompletion;
use Tests\TestCase;

/**
 * Class BallotCompletionTest
 * @package Tests\Unit\Models\Vote
 */
class BallotCompletionTest extends TestCase
{
    public function testBallot()
    {
        $model = new BallotCompletion();
        $relation = $model->ballot();

        $this->assertEquals('ballots.id', $relation->getQualifiedOwnerKeyName());
        $this->assertEquals('ballot_completions.ballot_id', $relation->getQualifiedForeignKeyName());
    }

    public function testUser()
    {
        $model = new BallotCompletion();
        $relation = $model->user();

        $this->assertEquals('users.id', $relation->getQualifiedOwnerKeyName());
        $this->assertEquals('ballot_completions.user_id', $relation->getQualifiedForeignKeyName());
    }

    public function testVotes()
    {
        $model = new BallotCompletion();
        $relation = $model->votes();

        $this->assertEquals('ballot_completions.id', $relation->getQualifiedParentKeyName());
        $this->assertEquals('votes.ballot_completion_id', $relation->getQualifiedForeignKeyName());
    }
}