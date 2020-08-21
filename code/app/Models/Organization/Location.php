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
 * App\Models\Organization\Location
 *
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
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property bool $delivery_available
 * @property-read \App\Models\Organization\Organization $organization
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Request\RequestedItem[] $requestedItems
 * @property-read int|null $requested_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Request\Request[] $requests
 * @property-read int|null $requests_count
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Organization\Location newModelQuery()
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Organization\Location newQuery()
 * @method static \Fico7489\Laravel\EloquentJoin\EloquentJoinBuilder|\App\Models\Organization\Location query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organization\Location whereAddressLine1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organization\Location whereAddressLine2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organization\Location whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organization\Location whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organization\Location whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organization\Location whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organization\Location whereDeliveryAvailable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organization\Location whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organization\Location whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organization\Location whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organization\Location whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organization\Location whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organization\Location wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organization\Location whereRegion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Organization\Location whereUpdatedAt($value)
 * @mixin \Eloquent
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
                'delivery_available' => [
                    'boolean',
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
