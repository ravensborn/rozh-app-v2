<?php

namespace App\Http\Livewire\Pages\Orders\Items;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ReturnedItem;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

use Livewire\WithFileUploads;

class Index extends Component
{

    use LivewireAlert, withFileUploads;

    protected $listeners = [
        'deleteOrderItem',
    ];

    public $order;

    //Creating item variables
    public string $name = "";
    public $image;
    public string $size = "Free Size";
    public string $color = "Same as picture";
    public int $price = 0;
    public int $quantity = 0;
    public string $code = "";

    public int $foundInReturnedList = 0;

    public function submitOrderItem()
    {

        $rules = [
            'name' => 'required|max:255',
            'image' => 'required|image|max:5242880', // 5MB Max
            'size' => 'required',
            'color' => 'required',
            'price' => 'required',
            'quantity' => 'required',
            'code' => 'nullable',
        ];

        $validated = $this->validate($rules);
        $validated['order_id'] = $this->order->id;

        $orderItem = OrderItem::create($validated);

        if ($this->image) {
            $orderItem
                ->addMedia($this->image)
                ->toMediaCollection('images');
        }

        $this->mount($this->order->id);

        $this->name = "";
        $this->image = "";
        $this->size = "Free Size";
        $this->color = "Same as picture";
        $this->price = 0;
        $this->quantity = 0;
        $this->code = "";

        $this->alert('success', 'Item added successfully.');
    }

    public function updatingCode($value)
    {

        $returnedItems = ReturnedItem::where('code', $value)->get();

        $total = 0;

        foreach ($returnedItems as $item) {
            $total = $total + $item->quantity;
        }

        $this->foundInReturnedList = $total;
    }

    public function triggerDeleteOrderItem(OrderItem $orderItem)
    {
        $this->confirm('Are you sure that you want to delete this item?', [
            'toast' => false,
            'position' => 'center',
            'showConfirmButton' => true,
            'cancelButtonText' => 'No',
            'onConfirmed' => 'deleteOrderItem'
        ]);

        $this->orderItemToBeDeleted = $orderItem;
    }

    public function deleteOrderItem()
    {

        $this->orderItemToBeDeleted->delete();

        $this->alert('success', 'Order item successfully deleted.', [
            'position' => 'top-end',
            'timer' => 5000,
            'toast' => true,
        ]);

        $this->mount($this->order->id);
    }

    public function mount($order)
    {
        $this->order = Order::findOrFail($order);

        //Todo: Prevent while order is sent.
        if (in_array($this->order->status, [
            Order::STATUS_DEFAULT,
            Order::STATUS_FORWARDER_NO_STATUS,
            Order::STATUS_FORWARDER_ERROR_SENDING
        ])) {
            //Not Sent
        } else {
            //Sent
            abort(401, "You cannot do this action.");
        }
    }

    public function render()
    {
        return view('livewire.pages.orders.items.index')
            ->extends('layouts.app')
            ->section('content');
    }
}
