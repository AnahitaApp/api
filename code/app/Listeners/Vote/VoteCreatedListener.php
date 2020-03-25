<?php
declare(strict_types=1);

namespace App\Listeners\Vote;

use App\Contracts\Repositories\Vote\BallotSubjectRepositoryContract;
use App\Events\Vote\VoteCreatedEvent;

/**
 * Class VoteCreatedListener
 * @package App\Listeners\Vote
 */
class VoteCreatedListener
{
    /**
     * @var BallotSubjectRepositoryContract
     */
    private $ballotSubjectRepository;

    /**
     * VoteCreatedListener constructor.
     * @param BallotSubjectRepositoryContract $ballotSubjectRepository
     */
    public function __construct(BallotSubjectRepositoryContract $ballotSubjectRepository)
    {
        $this->ballotSubjectRepository = $ballotSubjectRepository;
    }

    /**
     * Makes sure to recount the vote subject vote counts
     *
     * @param VoteCreatedEvent $event
     */
    public function handle(VoteCreatedEvent $event)
    {
        $vote = $event->getVote();

        $this->ballotSubjectRepository->update($vote->ballotSubject, [
            'vote_count' => $vote->ballotSubject->vote_count + $vote->result,
            'votes_cast' => $vote->ballotSubject->votes_cast + 1,
        ]);
    }
}