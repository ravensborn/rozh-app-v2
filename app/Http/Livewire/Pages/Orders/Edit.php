<?php

namespace App\Http\Livewire\Pages\Orders;

use App\Models\Order;
use Jantinnerezo\LivewireAlert\LivewireAlert;

use App\Models\Page;
use Illuminate\Support\Collection;
use Livewire\Component;

class Edit extends Component
{
    use LivewireAlert;

    public Collection $pages;
    public $order;

    public string $customer_name = "";
    public string $customer_profile_link = "";
    public string $customer_primary_phone = "";
    public string $customer_secondary_phone = "";
    public int $page_id = 1;
    public string $delivery_address = "";
    public int $delivery_price = 0;

    public function updateOrder()
    {

        $rules = [
            'customer_name' => 'required|max:255',
            'customer_profile_link' => 'required|max:255',
            'customer_primary_phone' => 'required|max:255',
            'customer_secondary_phone' => 'nullable|max:255',
            'page_id' => 'required|exists:pages,id',
            'delivery_price' => 'required|numeric',
        ];

        if($this->order->status == \App\Models\Order::STATUS_FORWARDER_NO_STATUS) {
            $rules['delivery_address'] = 'required|max:255';
        }

        $validated = $this->validate($rules);


        $order = new Order();
        $order = $order->update($validated);

        return redirect()->route('orders.show', [
            'order' => $this->order->id,
        ]);

    }

    public function mount($order) {

        $this->pages = Page::all();
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

        $this->customer_name = $this->order->customer_name;
        $this->customer_profile_link = $this->order->customer_profile_link;
        $this->customer_primary_phone = $this->order->customer_primary_phone;
        $this->customer_secondary_phone = $this->order->customer_secondary_phone;
        $this->page_id = $this->order->page_id;
        $this->delivery_address = $this->order->delivery_address;
        $this->delivery_price = $this->order->delivery_price;


    }

    public function render()
    {
        return view('livewire.pages.orders.edit')
            ->extends('layouts.app')
            ->section('content');
    }

}
