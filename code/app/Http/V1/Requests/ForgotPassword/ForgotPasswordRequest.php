<?php
declare(strict_types=1);

namespace App\Http\V1\Requests\ForgotPassword;

use App\Http\V1\Requests\BaseUnauthenticatedRequest;
use App\Http\V1\Requests\Traits\HasNoExpands;
use Illuminate\Validation\Rule;

/**
 * Class ForgotPasswordRequest
 * @package App\Http\V1\Requests\ForgotPassword
 */
class ForgotPasswordRequest extends BaseUnauthenticatedRequest
{
    use HasNoExpands;

    /**
     * get the validation rules for when someone has forgotten their password, and needs a token sent to them
     *
     * @return array
     */
    public function rules() : array
    {
        return [
            'email' => 'required|max:120|email|' . Rule::exists('users', 'email')
        ];
    }
}