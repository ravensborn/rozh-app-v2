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

    public function mount(Order $order) {

        $this->order = $order;
    }

    public function refreshWithForwarder()
    {
        $array = [];

        if(!$this->order->forwarder_order_id) {
            $this->alert('error', 'Order does not have a forwarder order id.');
            return false;
        }

        array_push($array, ['sender_track' => $this->order->forwarder_order_id]);

        $forwarderClient = new ForwarderController;
        $result = $forwarderClient->refreshHyperpostOrders($array);


        $number = $result['refresh_count'];

        $message = "Orders refreshed: $number";
        $message .= "\n";
        if(count($result['error_array'])) {
            $message .= "Errors:\n";
            foreach ($result['error_array'] as $errorMessage) {
                $message .= " - $errorMessage\n";
            }
        }

        $this->alert('info', $message);

        $this->order = Order::find($this->order->id); //Weird livewire behaviour, without this line, the relationships break.
    }

    public function sendToForwarder()
    {
        $array = [];
        array_push($array, $this->order);

        $forwarderClient = new ForwarderController;
        $result = $forwarderClient->sendOrders($array);

        $number = $result['sent_count'];

        $message = "Orders sent: $number";
        $message .= "\n";
        if(count($result['error_array'])) {
            $message .= "Errors:\n";
            foreach ($result['error_array'] as $errorMessage) {
                $message .= " - $errorMessage\n";
            }
        }

        $this->alert('info', $message);

        $this->order = Order::find($this->order->id); //Weird livewire behaviour, without this line, the relationships break.

    }

    public function render()
    {
        return view('livewire.pages.orders.show')
            ->extends('layouts.app')
            ->section('content');
    }
}
