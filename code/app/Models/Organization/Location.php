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
use Eloquent;
use Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Class Location
 *
 * @package App\Models\Organization
 * @property-read Organization $organization
 * @property-read Collection|RequestedItem[] $requestedItems
 * @property-read int|null $requested_items_count
 * @property-read Collection|Request[] $requests
 * @property-read int|null $requests_count
 * @method static EloquentJoinBuilder|Location newModelQuery()
 * @method static EloquentJoinBuilder|Location newQuery()
 * @method static EloquentJoinBuilder|Location query()
 * @mixin Eloquent
 * @property int $id
 * @property int $organization_id
 * @property string $name
 * @property string $address_line_1
 * @property string|null $address_line_2
 * @property string $city
 * @property string|null $postal_code
 * @property string|null $region
 * @property string $country
 * @property float|null $latitude
 * @property float|null $longitude
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property Carbon|null $deleted_at
 * @method static Builder|Location whereAddressLine1($value)
 * @method static Builder|Location whereAddressLine2($value)
 * @method static Builder|Location whereCity($value)
 * @method static Builder|Location whereCountry($value)
 * @method static Builder|Location whereCreatedAt($value)
 * @method static Builder|Location whereDeletedAt($value)
 * @method static Builder|Location whereId($value)
 * @method static Builder|Location whereLatitude($value)
 * @method static Builder|Location whereLongitude($value)
 * @method static Builder|Location whereName($value)
 * @method static Builder|Location whereOrganizationId($value)
 * @method static Builder|Location wherePostalCode($value)
 * @method static Builder|Location whereRegion($value)
 * @method static Builder|Location whereUpdatedAt($value)
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