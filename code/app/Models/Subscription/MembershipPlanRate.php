<?php
declare(strict_types=1);

namespace App\Models\Subscription;

use App\Models\BaseModelAbstract;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Class MembershipPlanRate
 *
 * @property int $id
 * @property int $membership_plan_id
 * @property float $cost
 * @property bool $active
 * @property Carbon|null $deleted_at
 * @property mixed|null $created_at
 * @property mixed|null $updated_at
 * @property-read MembershipPlan $membershipPlan
 * @property-read Collection|Subscription[] $subscriptions
 * @property-read int|null $subscriptions_count
 * @method static Builder|MembershipPlanRate newModelQuery()
 * @method static Builder|MembershipPlanRate newQuery()
 * @method static Builder|MembershipPlanRate query()
 * @method static Builder|MembershipPlanRate whereActive($value)
 * @method static Builder|MembershipPlanRate whereCost($value)
 * @method static Builder|MembershipPlanRate whereCreatedAt($value)
 * @method static Builder|MembershipPlanRate whereDeletedAt($value)
 * @method static Builder|MembershipPlanRate whereId($value)
 * @method static Builder|MembershipPlanRate whereMembershipPlanId($value)
 * @method static Builder|MembershipPlanRate whereUpdatedAt($value)
 * @mixin Eloquent
 */
class MembershipPlanRate extends BaseModelAbstract
{
    /**
     * The membership plan this is related to
     *
     * @return BelongsTo
     */
    public function membershipPlan(): BelongsTo
    {
        return $this->belongsTo(MembershipPlan::class);
    }

    /**
     * All subscriptions that have been signed up for this membership plan rate
     *
     * @return HasMany
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}