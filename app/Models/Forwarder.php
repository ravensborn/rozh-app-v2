<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Forwarder
 *
 * @method static \Database\Factories\ForwarderFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Forwarder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Forwarder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Forwarder query()
 * @mixin \Eloquent
 */
class Forwarder extends Model
{
    use HasFactory;

    const NO_FORWARDER = 1;
    const FORWARDER_HYPERPOST = 2;

}
