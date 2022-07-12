<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Order
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Order query()
 * @mixin \Eloquent
 * @property int $id
 * @property string $number
 * @property int $status
 * @property int $user_id
 * @property int $page_id
 * @property string $address
 * @property int $total
 * @property string|null $logs
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereLogs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order wherePageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Order whereUserId($value)
 */
class Order extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = ['logs' => 'array', 'properties' => 'array', 'forwarder_refresh_timestamp' => 'datetime'];

    const STATUS_DEFAULT = 0;
    const STATUS_FORWARDER_NO_STATUS = 1;
    const STATUS_FORWARDER_STATUS = 2;
    const STATUS_FORWARDER_RETURNED = 3;
    const STATUS_FORWARDER_ERROR_SENDING = 4;
    const STATUS_FORWARDER_ERROR_REFRESHING = 6;
    const STATUS_FORWARDER_ORDER_FULFILLED = 5;

    public function getStatus(): string
    {
        switch ($this->status) {
            case 0: $result = 'Default status'; break;
            case 1: $result = 'Waiting forwarder'; break;
            case 2: $result = 'Status received'; break;
            case 3: $result = 'Item returned'; break;
            case 4: $result = 'Error while sending'; break;
            case 5: $result = 'Order fulfilled'; break;
            case 6: $result = 'Error while refreshing'; break;
            default: $result = 'Status not found';
        }

        return $result;

//        return match ($this->status) {
//            0 => 'Default status',
//            1 => 'Waiting forwarder',
//            2 => 'Status received',
//            3 => 'Item returned',
//            4 => 'Error while sending',
//            6 => 'Error while refreshing',
//            5 => 'Order fulfilled',
//            default => 'Status not found',
//        };
    }

    public static function getStatusArray(): array
    {
        return [
            ['id' => 0, 'name' => 'Default status'],
            ['id' => 1, 'name' => 'Waiting forwarder'],
            ['id' => 2, 'name' => 'Status received'],
            ['id' => 3, 'name' => 'Item returned'],
            ['id' => 4, 'name' => 'Error while sending'],
            ['id' => 6, 'name' => 'Error while refreshing'],
            ['id' => 5, 'name' => 'Order Fulfilled'],

        ];
    }

    public function items(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function page(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function forwarder(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Forwarder::class);
    }

    public function forwarderLocation(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ForwarderLocation::class, 'forwarder_location_id', 'location_id')
            ->where('forwarder_id', $this->forwarder_id)
            ->withDefault();
    }

    public function forwarderStatus(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ForwarderStatus::class, 'forwarder_status_id', 'status_id')
            ->where('forwarder_id', $this->forwarder_id)
            ->withDefault();
    }

    public function total(): int
    {

        $total = 0;
        $items = $this->items;

        foreach ($items as $item) {
            $total = $total = $item->total();
        }

        return $total;
    }

    public function hasForwarder(): bool
    {
        if ($this->forwarder_id == Forwarder::NO_FORWARDER || is_null($this->forwarder_id)) {
            return false;
        }
        return true;
    }

    public function setProperty($key, $data)
    {
        $peroperties = $this->properties;
        $peroperties[$key] = $data;
        $this->properties = $peroperties;
        $this->save();
    }

    public function getProperty($key)
    {
        if(array_key_exists($this->properties, $key)) {
            return $this->properties[$key];
        }

        return null;
    }

    public function setStatus($status_id)
    {
        $this->status = $status_id;
        $this->save();
    }


}
