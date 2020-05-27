<?php
declare(strict_types=1);

namespace App\Policies\Organization;

use App\Models\Organization\Organization;
use App\Models\User\User;
use App\Policies\BaseBelongsToOrganizationPolicyAbstract;

/**
 * Class OrganizationManagerPolicy
 * @package App\Policies\Organization
 */
class LocationPolicy extends BaseBelongsToOrganizationPolicyAbstract
{
    /**
     * @var bool
     */
    protected bool $requiresAdminForManagement = true;

    /**
     * Locations are publicly available
     *
     * @param User $user
     * @param Organization $organization
     * @return bool
     */
    public function all(User $user, Organization $organization)
    {
        return true;
    }
}