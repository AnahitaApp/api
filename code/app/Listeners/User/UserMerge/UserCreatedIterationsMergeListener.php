<?php
declare(strict_types=1);

namespace App\Listeners\User\UserMerge;

use App\Contracts\Repositories\Wiki\IterationRepositoryContract;
use App\Events\User\UserMergeEvent;

/**
 * Class UserCreatedIterationsMergeListener
 * @package App\Listeners\User\UserMerge
 */
class UserCreatedIterationsMergeListener
{
    /**
     * @var IterationRepositoryContract
     */
    private $iterationRepository;

    /**
     * UserCreatedIterationsMergeListener constructor.
     * @param IterationRepositoryContract $iterationRepository
     */
    public function __construct(IterationRepositoryContract $iterationRepository)
    {
        $this->iterationRepository = $iterationRepository;
    }

    /**
     * @param UserMergeEvent $event
     */
    public function handle(UserMergeEvent $event)
    {
        $mainUser = $event->getMainUser();
        $mergeUser = $event->getMergeUser();
        $mergeOptions = $event->getMergeOptions();

        if ($mergeOptions['created_iterations'] ?? false) {
            foreach ($mergeUser->createdIterations as $iteration) {
                $this->iterationRepository->update($iteration, [
                    'created_by_id' => $mainUser->id,
                ]);
            }
        }
    }
}