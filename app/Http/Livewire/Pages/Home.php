<?php

namespace App\Http\Livewire\Pages;

use App\Models\Forwarder;
use App\Models\ForwarderStatus;
use App\Models\Order;
use Carbon\Carbon;
use Livewire\Component;

class Home extends Component
{

    public array $hyperpostOrderData = [];
    public string $hyperpost_from_date = "";
    public string $hyperpost_to_date = "";

    public function filterHyperpostByDate()
    {
        if ($this->hyperpost_from_date && $this->hyperpost_to_date) {
            $this->generateHyperpostStatistics();
        }
    }

    public function generateHyperpostStatistics()
    {

        $this->hyperpostOrderData = [];

        $hyperpostStatuses = ForwarderStatus::where('forwarder_id', Forwarder::FORWARDER_HYPERPOST)->get();

        foreach ($hyperpostStatuses as $status) {

            $orders = Order::where('forwarder_id', Forwarder::FORWARDER_HYPERPOST)
                ->whereDate('created_at', '>=', $this->hyperpost_from_date)
                ->whereDate('created_at', '<=', $this->hyperpost_to_date)
                ->where('forwarder_status_id', $status->id)
                ->get();


            array_push($this->hyperpostOrderData, [
                'status_id' => $status->id,
                'status_name' => $status->name,
                'orders_count' => count($orders),
                'orders_total' => $orders->sum(function ($order) {
                    return $order->total();
                })
            ]);

        }

    }

    public function mount()
    {
//        $this->hyperpost_from_date = Carbon::today()->startOfMonth()->format('Y-m-d');
        $this->hyperpost_from_date = Carbon::today()->format('Y-m-d');
        $this->hyperpost_to_date = Carbon::today()->format('Y-m-d');
        $this->generateHyperpostStatistics();
    }

    public function render()
    {
        return view('livewire.pages.home')
            ->extends('layouts.app')
            ->section('content');
    }
}
