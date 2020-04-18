<?php
declare(strict_types=1);

namespace App\Policies\Request;

use App\Models\Request\Request;
use App\Models\User\User;
use App\Policies\BasePolicyAbstract;

/**
 * Class SafetyReportPolicy
 * @package App\Policies\Request
 */
class SafetyReportPolicy extends BasePolicyAbstract
{
    /**
     * Only people that have created the request or completed the request can report a safety concern for the request
     *
     * @param User $loggedInUser
     * @param Request $request
     * @return bool
     */
    public function create(User $loggedInUser, Request $request)
    {
        return $loggedInUser->id === $request->requested_by_id || $loggedInUser->id === $request->completed_by_id;
    }
}