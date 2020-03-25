<?php
declare(strict_types=1);

namespace App\Models\User;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\BaseModelAbstract;

/**
 * Class PasswordToken
 *
 * @package App\Models\User
 * @property int $id
 * @property int $user_id
 * @property string $token
 * @property \Carbon\Carbon|null $deleted_at
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\User\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordToken query()
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordToken whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordToken whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordToken whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordToken whereUserId($value)
 * @mixin \Eloquent
 */
class PasswordToken extends BaseModelAbstract
{
    /**
     * The user relation to the user that generated this token
     *
     * @return BelongsTo
     */
    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Swagger definition below for a password token...
     *
     * @SWG\Definition(
     *     type="object",
     *     definition="PasswordToken",
     *     @SWG\Property(
     *         property="token",
     *         type="string",
     *         maxLength=120,
     *         description="The token that was generated."
     *     ),
     *     @SWG\Property(
     *         property="email",
     *         type="string",
     *         maxLength=120,
     *         description="The email address of the user the token is associated with."
     *     ),
     * )
     */
}