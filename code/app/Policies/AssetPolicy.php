<?php
declare(strict_types=1);

namespace App\Policies;

use App\Contracts\Models\IsAnEntity;
use App\Models\Asset;
use App\Models\User\User;

/**
 * Class AssetPolicy
 * @package App\Policies
 */
class AssetPolicy extends BasePolicyAbstract
{
    /**
     * @param User $loggedInUser
     * @param IsAnEntity $entity
     * @return bool
     */
    public function all(User $loggedInUser, IsAnEntity $entity)
    {
        return $entity->canUserManageEntity($loggedInUser);
    }

    /**
     * @param User $loggedInUser
     * @param IsAnEntity $entity
     * @return bool
     */
    public function create(User $loggedInUser, IsAnEntity $entity)
    {
        return $entity->canUserManageEntity($loggedInUser);
    }

    /**
     * @param User $loggedInUser
     * @param IsAnEntity $entity
     * @param Asset $asset
     * @return bool
     */
    public function update(User $loggedInUser, IsAnEntity $entity, Asset $asset)
    {
        return $asset->owner_type == $entity->morphRelationName() && $asset->owner_id == $entity->id
            && $entity->canUserManageEntity($loggedInUser);
    }

    /**
     * @param User $loggedInUser
     * @param IsAnEntity $entity
     * @param Asset $asset
     * @return bool
     */
    public function delete(User $loggedInUser, IsAnEntity $entity, Asset $asset)
    {
        return $asset->owner_type == $entity->morphRelationName() && $asset->owner_id == $entity->id
            && $entity->canUserManageEntity($loggedInUser);
    }
}