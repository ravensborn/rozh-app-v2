<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ForwarderLocation
 *
 * @method static \Illuminate\Database\Eloquent\Builder|ForwarderLocation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ForwarderLocation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ForwarderLocation query()
 * @mixin \Eloquent
 */
class ForwarderLocation extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function forwarder(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Forwarder::class);
    }
}
