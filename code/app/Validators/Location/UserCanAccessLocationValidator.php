<?php
declare(strict_types=1);

namespace App\Validators\Location;

use App\Contracts\Repositories\User\UserRepositoryContract;
use App\Models\Organization\Location;
use App\Models\User\User;
use App\Validators\BaseValidatorAbstract;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;

/**
 * Class UserCanAccessLocationValidator
 * @package App\Validators\Location
 */
class UserCanAccessLocationValidator extends BaseValidatorAbstract
{
    /**
     * @var string
     */
    const KEY = 'user_can_access_location';

    /**
     * @var UserRepositoryContract
     */
    private UserRepositoryContract $userRepository;

    /**
     * @var Request
     */
    private Request $request;

    /**
     * RequestNotAcceptedValidator constructor.
     * @param UserRepositoryContract $userRepository
     * @param Request $request
     */
    public function __construct(UserRepositoryContract $userRepository, Request $request)
    {
        $this->userRepository = $userRepository;
        $this->request = $request;
    }

    /**
     * This is invoked by the validator rule 'selected_iteration_belongs_to_article'
     *
     * @param $attribute string the attribute name that is validating
     * @param $value mixed the value that we're testing
     * @param $parameters array
     * @param $validator Validator The Validator instance
     * @return bool
     */
    public function validate($attribute, $value, $parameters = [], Validator $validator = null)
    {
        /** @var Location $location */
        $location = $this->request->route('location');

        if (!$location) {
            return false;
        }

        /** @var User $user */
        $user = $this->userRepository->findOrFail($value);

        if (!$user) {
            return false;
        }

        return $user->canManageOrganization($location->organization);
    }
}