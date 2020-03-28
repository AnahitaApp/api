<?php
declare(strict_types=1);

namespace App\Http\Core\Requests\Authentication;

use App\Http\Core\Requests\BaseUnauthenticatedRequest;
use App\Http\Core\Requests\Traits\HasNoExpands;
use App\Models\User\User;

/**
 * Class SignUpRequest
 * @package App\Http\Core\Requests\Authentication
 */
class SignUpRequest extends BaseUnauthenticatedRequest
{
    use HasNoExpands;

    /**
     * Gets the rules for the verification
     *
     * @param User $user
     * @return array
     */
    public function rules(User $user)
    {
        return [
            'email' => 'required|string|max:120|email|unique:users,email',
            'name' => 'required|string|max:120',
            'password' => 'required|string|min:6|max:256',
        ];
    }
}