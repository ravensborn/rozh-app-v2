<?php

namespace App\Http\Livewire\Pages\Orders;

use App\Models\Forwarder;
use App\Models\ForwarderStatus;
use App\Models\Order;
use App\Models\Page;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Livewire\Component;


class QuickFind extends Component
{

    public $orders;

    public string $phone_number = "";


    public function mount()
    {
        $this->orders = collect();

    }

    public function updatedPhoneNumber()
    {
        if ($this->phone_number && strlen($this->phone_number) > 5) {
            $this->orders = Order::where('customer_primary_phone', $this->phone_number)
                ->orderBy('created_at', 'desc')
                ->get();
        }
    }

    public function render()
    {
        return view('livewire.pages.orders.quick-find')
            ->extends('layouts.app')
            ->section('content');
    }
}
