<?php
declare(strict_types=1);

namespace App\Models;

use App\Contracts\Models\HasValidationRulesContract;
use App\Models\Traits\HasValidationRules;
use App\Models\User\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

/**
 * Class Asset
 *
 * @package App\Models
 * @property int $id
 * @property int|null $user_id
 * @property string $url
 * @property string|null $name
 * @property string|null $caption
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 * @method static Builder|Asset newModelQuery()
 * @method static Builder|Asset newQuery()
 * @method static Builder|Asset query()
 * @method static Builder|Asset whereCaption($value)
 * @method static Builder|Asset whereCreatedAt($value)
 * @method static Builder|Asset whereDeletedAt($value)
 * @method static Builder|Asset whereId($value)
 * @method static Builder|Asset whereName($value)
 * @method static Builder|Asset whereUpdatedAt($value)
 * @method static Builder|Asset whereUrl($value)
 * @method static Builder|Asset whereUserId($value)
 * @mixin Eloquent
 */
class Asset extends BaseModelAbstract implements HasValidationRulesContract
{
    use HasValidationRules;

    /**
     * @var string Makes sure to override all children
     */
    protected $table = 'assets';

    /**
     * The user that created this asset
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Build the model validation rules
     * @param array $params
     * @return array
     */
    public function buildModelValidationRules(...$params): array
    {
        return [
            self::VALIDATION_RULES_BASE => [
                'file_contents' => [
                    'string',
                ],

                'name' => [
                    'nullable',
                    'string',
                ],

                'caption' => [
                    'nullable',
                    'string',
                ],

                // Set in the request object, and not set from the user request
                'mime_type' => [
                    Rule::in([
                        'image/jpeg',
                        'image/png',
                        'image/gif',
                    ]),
                ],
            ],
            self::VALIDATION_RULES_CREATE => [
                self::VALIDATION_PREPEND_REQUIRED => [
                    'file_contents',
                ],
            ],
            self::VALIDATION_RULES_UPDATE => [
                self::VALIDATION_PREPEND_NOT_PRESENT => [
                    'file_contents',
                ],
            ]
        ];
    }
}
