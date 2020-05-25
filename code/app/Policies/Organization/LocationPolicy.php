<?php
declare(strict_types=1);

namespace App\Policies\Organization;

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
}