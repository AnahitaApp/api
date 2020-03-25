<?php
declare(strict_types=1);

namespace App\Repositories\Traits\NotImplemented;

use App\Exceptions\NotImplementedException;
use App\Models\BaseModelAbstract;

/**
 * Class Update
 * @package App\Repositories\Traits\NotImplemented
 */
trait Update
{
    /**
     * @param BaseModelAbstract $model
     * @param array $data
     * @param array $forcedValues
     * @return BaseModelAbstract
     */
    public function update(BaseModelAbstract $model, array $data, array $forcedValues = []): BaseModelAbstract
    {
        throw new NotImplementedException();
    }
}