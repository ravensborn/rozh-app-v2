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
            case 0:
                $result = 'Default status';
                break;
            case 1:
                $result = 'Waiting forwarder';
                break;
            case 2:
                $result = 'Status received';
                break;
            case 3:
                $result = 'Item returned';
                break;
            case 4:
                $result = 'Error while sending';
                break;
            case 5:
                $result = 'Order fulfilled';
                break;
            case 6:
                $result = 'Error while refreshing';
                break;
            default:
                $result = 'Status not found';
        }

        return $result;
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

    const INTERNAL_STATUS_PENDING = 0;
    const INTERNAL_STATUS_FULFILLED = 1;
    const INTERNAL_STATUS_PROCESS_LATER = 2;
    const INTERNAL_STATUS_CANCELLED = 3;

    public function getInternalStatus(): string
    {
        switch ($this->internal_status) {

            case self::INTERNAL_STATUS_PENDING:
                $result = 'Pending';
                break;
            case self::INTERNAL_STATUS_FULFILLED:
                $result = 'Done';
                break;
            case self::INTERNAL_STATUS_PROCESS_LATER:
                $result = 'Process Later';
                break;
            case self::INTERNAL_STATUS_CANCELLED:
                $result = 'Cancelled';
                break;
            default:
                $result = 'Internal status not found';
        }
        return $result;
    }

    public function getInternalStatusColor(): string
    {
        switch ($this->internal_status) {
            case self::INTERNAL_STATUS_PENDING:
                $result = '#909090';
                break;
            case self::INTERNAL_STATUS_FULFILLED:
                $result = '#1cc88a';
                break;
            case self::INTERNAL_STATUS_PROCESS_LATER:
                $result = '#f6c23e';
                break;
            case self::INTERNAL_STATUS_CANCELLED:
                $result = '#e74a3b';
                break;
            default:
                $result = 'Internal status not found';
        }
        return $result;
    }

    public static function getInternalStatusArray(): array
    {
        return [
            ['id' => self::INTERNAL_STATUS_FULFILLED, 'name' => 'Done'],
            ['id' => self::INTERNAL_STATUS_PENDING, 'name' => 'Pending'],
            ['id' => self::INTERNAL_STATUS_PROCESS_LATER, 'name' => 'Process Later'],
            ['id' => self::INTERNAL_STATUS_CANCELLED, 'name' => 'Cancelled'],
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
            $total = $total + $item->total();
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
        $properties = $this->properties;
        $properties[$key] = $data;
        $this->properties = $properties;
        $this->save();
    }

    public function getProperty($key)
    {
        if (array_key_exists($this->properties, $key)) {
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
