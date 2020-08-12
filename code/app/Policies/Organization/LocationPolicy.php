<?php
declare(strict_types=1);

namespace App\Policies\Organization;

use App\Contracts\Models\BelongsToOrganizationContract;
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
    public function all(User $user, ?Organization $organization = null)
    {
        return true;
    }

    /**
     * All users can view a location
     * @param User $user
     * @param Organization $organization
     * @param BelongsToOrganizationContract $model
     * @return bool
     */
    public function view(User $user, Organization $organization, BelongsToOrganizationContract $model)
    {
        return true;
    }
}
