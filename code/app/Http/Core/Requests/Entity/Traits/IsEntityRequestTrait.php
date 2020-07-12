<?php
declare(strict_types=1);

namespace App\Http\Core\Requests\Entity\Traits;

use App\Contracts\Models\IsAnEntity;
use Illuminate\Routing\Route;

/**
 * Class IsEntityRequestTrait
 * @package App\Http\Core\Requests\Entity\Traits
 * @method Route|object|string|null route($name = null)
 */
trait IsEntityRequestTrait
{
    /**
     * Gets the entity out of the route. It will almost always be the first object.
     *
     * @return IsAnEntity|Route|object|string
     */
    public function getEntity(): IsAnEntity
    {
        $entityKey = $this->route()->parameterNames[0];

        return $this->route($entityKey);
    }
}