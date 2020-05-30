<?php
declare(strict_types=1);

namespace App\Policies\Request;

use App\Models\Organization\Location;
use App\Models\Request\RequestedItem;
use App\Models\User\User;
use App\Policies\BasePolicyAbstract;

/**
 * Class RequestedItemPolicy
 * @package App\Policies\Request
 */
class RequestedItemPolicy extends BasePolicyAbstract
{
    /**
     * Anyone who can manage the related organization can view all requested items at a location
     *
     * @param User $loggedInUser
     * @param Location $location
     * @return bool
     */
    public function all(User $loggedInUser, Location $location)
    {
        return true;
    }

    /**
     * Anyone who can manage the related organization can created requested items for the location
     *
     * @param User $loggedInUser
     * @param Location $location
     * @return bool
     */
    public function create(User $loggedInUser, Location $location)
    {
        return $loggedInUser->canManageOrganization($location->organization);
    }

    /**
     * A user who can manage the location of a related requested item can update it
     *
     * @param User $loggedInUser
     * @param Location $location
     * @param RequestedItem $requestedItem
     * @return bool
     */
    public function update(User $loggedInUser, Location $location, RequestedItem $requestedItem)
    {
        return $requestedItem->location_id == $location->id && $loggedInUser->canManageOrganization($location->organization);
    }

    /**
     * A user who can manage the location of a related requested item can delete it
     *
     * @param User $loggedInUser
     * @param Location $location
     * @param RequestedItem $requestedItem
     * @return bool
     */
    public function delete(User $loggedInUser, Location $location, RequestedItem $requestedItem)
    {
        return $requestedItem->location_id == $location->id && $loggedInUser->canManageOrganization($location->organization);
    }
}