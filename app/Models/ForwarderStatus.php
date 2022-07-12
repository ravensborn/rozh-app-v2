<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ForwarderStatus
 *
 * @method static \Illuminate\Database\Eloquent\Builder|ForwarderStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ForwarderStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ForwarderStatus query()
 * @mixin \Eloquent
 */
class ForwarderStatus extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function forwarder(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Forwarder::class);
    }
}
