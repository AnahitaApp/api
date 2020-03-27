<?php
declare(strict_types=1);

namespace App\Models\Request;

use App\Models\Asset;
use App\Models\BaseModelAbstract;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class LineItem
 * @package App\Models\Request
 */
class LineItem extends BaseModelAbstract
{
    /**
     * An image uploaded for this line item
     *
     * @return BelongsTo
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * The request this line item is apart of
     *
     * @return BelongsTo
     */
    public function request(): BelongsTo
    {
        return $this->belongsTo(Request::class);
    }
}