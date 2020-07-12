<?php
declare(strict_types=1);

namespace App\Validators\Traits;

use App\Contracts\Models\IsAnEntity;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;

/**
 * Class IsEntityRequestTrait
 * @package App\Validators\Traits
 * @property  Request $request
 */
trait HasEntityInRequestTrait
{
    /**
     * Gets the entity out of the route. It will almost always be the first object.
     *
     * @return IsAnEntity|Route|object|string
     */
    public function getEntity(): IsAnEntity
    {
        $entityKey = $this->request->route()->parameterNames()[0];

        return $this->request->route($entityKey);
    }
}