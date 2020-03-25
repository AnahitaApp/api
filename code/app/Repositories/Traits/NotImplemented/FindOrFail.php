<?php
declare(strict_types=1);

namespace App\Repositories\Traits\NotImplemented;

use App\Exceptions\NotImplementedException;

/**
 * Class FindOrFail
 * @package App\Repositories\Traits\NotImplemented
 */
trait FindOrFail
{
    /**
     * Not Implemented
     * 
     * @param $id
     * @param array $with
     * @return \App\Models\BaseModelAbstract|void
     * @throws NotImplementedException
     */
    public function findOrFail($id, array $with = [])
    {
        throw new NotImplementedException();
    }
}