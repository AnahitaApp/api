<?php
declare(strict_types=1);

namespace App\Models\Traits;

use App\Models\Subscription\Subscription;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Trait HasSubscriptions
 * @package App\Models\Traits
 */
trait HasSubscriptions
{
    /**
     * All Subscriptions this subscriber has signed up to
     *
     * @return MorphMany
     */
    public function subscriptions(): MorphMany
    {
        return $this->morphMany(Subscription::class, 'subscriber');
    }

    /**
     * Leads the users current active subscription if there is one
     *
     * @param Carbon|null $expiresAfter
     * @return Subscription|null
     */
    public function currentSubscription(?Carbon $expiresAfter = null) : ?Subscription
    {
        return $this->subscriptions->first(function (Subscription $subscription) use ($expiresAfter) {
            $expiresAfter = $expiresAfter ?? Carbon::now();
            return $subscription->isLifetime() ? true :
                ($subscription->expires_at ? $subscription->expires_at->greaterThan($expiresAfter) : false);
        });
    }
}