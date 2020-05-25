<?php
declare(strict_types=1);

namespace App\Models\Organization;

use App\Contracts\Models\BelongsToOrganizationContract;
use App\Contracts\Models\HasValidationRulesContract;
use App\Models\BaseModelAbstract;
use App\Models\Request\Request;
use App\Models\Request\RequestedItem;
use App\Models\Traits\BelongsToOrganization;
use App\Models\Traits\HasValidationRules;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Location
 * @package App\Models\Organization
 */
class Location extends BaseModelAbstract implements BelongsToOrganizationContract, HasValidationRulesContract
{
    use BelongsToOrganization, HasValidationRules;

    /**
     * The request that have been made to this location
     *
     * @return HasMany
     */
    public function requests(): HasMany
    {
        return $this->hasMany(Request::class);
    }

    /**
     * All requested items available at this location
     *
     * @return HasMany
     */
    public function requestedItems(): HasMany
    {
        return $this->hasMany(RequestedItem::class);
    }

    /**
     * @inheritDoc
     */
    public function buildModelValidationRules(...$params): array
    {
        return [
            static::VALIDATION_RULES_BASE => [
                'name' => [
                    'string',
                ],
                'address_line_1' => [
                    'string',
                ],
                'address_line_2' => [
                    'string',
                    'nullable',
                ],
                'city' => [
                    'string',
                ],
                'postal_code' => [
                    'string',
                    'nullable',
                ],
                'region' => [
                    'string',
                    'nullable',
                ],
                'country' => [
                    'string',
                ],
                'latitude' => [
                    'not_present',
                ],
                'longitude' => [
                    'not_present',
                ],
            ],
            static::VALIDATION_RULES_CREATE => [
                static::VALIDATION_PREPEND_REQUIRED => [
                    'name',
                    'address_line_1',
                    'city',
                    'country',
                ],
            ],
        ];
    }
}