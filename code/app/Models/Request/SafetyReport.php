<?php
declare(strict_types=1);

namespace App\Models\Request;

use App\Models\BaseModelAbstract;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class SafetyReport
 * @package App\Models\Request
 */
class SafetyReport extends BaseModelAbstract
{
    /**
     * The user that created this safety report
     *
     * @return BelongsTo
     */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    /**
     * The request this was made for
     *
     * @return BelongsTo
     */
    public function request(): BelongsTo
    {
        return $this->belongsTo(Request::class);
    }
}