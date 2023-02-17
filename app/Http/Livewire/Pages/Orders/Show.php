<?php

namespace App\Http\Livewire\Pages\Orders;

use App\Http\Controllers\ForwarderController;
use App\Models\Order;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Show extends Component
{
    use LivewireAlert;

    public Order $order;

    public function mount(Order $order)
    {

        $this->order = $order;
    }

    public function refreshWithForwarder()
    {
        if (!$this->order->forwarder_order_id) {
            $this->alert('error', 'Order does not have a forwarder order id.');
            return false;
        }

        $forwarderClient = new ForwarderController;
        $forwarderClient->refreshHyperpostOrders([$this->order->forwarder_order_id]);

        $this->alert('info', "Successfully initiated order refresh.");

        $this->order = Order::find($this->order->id); //Weird livewire behaviour, without this line, the relationships break.
    }

    public function sendToForwarder()
    {

        if($this->order->total() <= 0) {
            $this->alert('error', "Order total is zero, please add items and try again.");

            return 0;
        }
        $forwarderClient = new ForwarderController;
        $forwarderClient->sendOrders([$this->order]);
        $forwarderClient->sendLogToTelegram();

        $this->alert('success', "Successfully initiated order send.");

        $this->order = Order::find($this->order->id); //Weird livewire behaviour, without this line, the relationships break.

    }

    public function render()
    {
        return view('livewire.pages.orders.show')
            ->extends('layouts.app')
            ->section('content');
    }
}
