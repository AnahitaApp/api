<?php
declare(strict_types=1);

namespace App\Repositories\User;

use App\Models\BaseModelAbstract;
use Illuminate\Contracts\Hashing\Hasher;
use Psr\Log\LoggerInterface as LogContract;
use App\Contracts\Repositories\User\UserRepositoryContract;
use App\Models\User\User;
use App\Repositories\BaseRepositoryAbstract;
use App\Repositories\Traits\NotImplemented;

/**
 * Class UserRepository
 * @package App\Repositories\User
 */
class UserRepository extends BaseRepositoryAbstract implements UserRepositoryContract
{
    use NotImplemented\Delete;

    /**
     * @var Hasher
     */
    private $hasher;

    /**
     * UserRepository constructor.
     * @param User $model
     * @param LogContract $log
     * @param Hasher $hasher
     */
    public function __construct(User $model, LogContract $log, Hasher $hasher)
    {
        parent::__construct($model, $log);
        $this->hasher = $hasher;
    }

    /**
     * Overrides in order to hash the password properly
     *
     * @param array $data
     * @param BaseModelAbstract|null $relatedModel
     * @param array $forcedValues
     * @return BaseModelAbstract|User
     */
    public function create(array $data = [], BaseModelAbstract $relatedModel = null, array $forcedValues = [])
    {
        if (isset($data['password'])) {
            $data['password'] = $this->hasher->make($data['password']);
        }

        /** @var User $user */
        $user = parent::create($data, $relatedModel, $forcedValues);

        return $user;
    }

    /**
     * Overrides in order to allow the syncing of roles
     *
     * @param BaseModelAbstract|User $model
     * @param array $data
     * @param array $forcedValues
     * @return BaseModelAbstract
     */
    public function update(BaseModelAbstract $model, array $data, array $forcedValues = []): BaseModelAbstract
    {
        if (isset($data['password'])) {
            $data['password'] = $this->hasher->make($data['password']);
        }

        return parent::update($model, $data, $forcedValues);
    }

    /**
     * Attempts to look up a user by email address, and returns null if we cannot find one
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }

    /**
     * Swagger definitions below...
     *
     * @SWG\Definition(
     *     definition="Users",
     *     @SWG\Property(
     *         property="data",
     *         description="A list of user models",
     *         type="array",
     *         minItems=0,
     *         maxItems=100,
     *         uniqueItems=true,
     *         @SWG\Items(ref="#/definitions/User")
     *     )
     * )
     */
}