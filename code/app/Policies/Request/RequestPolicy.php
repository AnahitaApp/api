<?php
declare(strict_types=1);

namespace App\Policies\Request;

use App\Models\Request\Request;
use App\Models\User\User;
use App\Policies\BasePolicyAbstract;

/**
 * Class RequestPolicy
 * @package App\Policies\Request
 */
class RequestPolicy extends BasePolicyAbstract
{
    /**
     * Everyone can index requests
     *
     * @param User $user
     * @return bool
     */
    public function all(User $user)
    {
        return true;
    }

    /**
     * Everyone can create requests
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Only people who are completing a request can complete requests they are already assigned to be completing.
     * Other users can update a request only when someone else has not decided to complete it
     *
     * @param User $user
     * @param Request $request
     * @return bool
     */
    public function update(User $user, Request $request)
    {
        return $request->completed_by_id == null || $user->id == $request->completed_by_id;
    }
}