<?php

namespace App\Http\Livewire\Pages;

use App\Models\Forwarder;
use App\Models\ForwarderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Page;
use Carbon\Carbon;
use DB;
use Livewire\Component;

class Home extends Component
{

    //Hyperpost
    public array $hyperpostOrderData = [];
    public string $hyperpost_from_date = "";
    public string $hyperpost_to_date = "";
    public int $hyperpost_page_id = 0;

    public $pages;


    public function filterHyperpostStats()
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

            $orders = Order::query();

            $orders->where('forwarder_id', Forwarder::FORWARDER_HYPERPOST)
                ->where('forwarder_status_id', $status->status_id)
                ->whereDate('created_at', '>=', $this->hyperpost_from_date)
                ->whereDate('created_at', '<=', $this->hyperpost_to_date);

            if($this->hyperpost_page_id != 0) {
                $orders->where('page_id', $this->hyperpost_page_id);
            }

            $orders = $orders->get();


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

        if (auth()->user()->hasRole('admin')) {

            $this->pages = Page::all();

            //        $this->hyperpost_from_date = Carbon::today()->startOfMonth()->format('Y-m-d');
            $this->hyperpost_from_date = Carbon::today()->format('Y-m-d');
            $this->hyperpost_to_date = Carbon::today()->format('Y-m-d');
            $this->generateHyperpostStatistics();

        }

    }

    public function render()
    {
        return view('livewire.pages.home')
            ->extends('layouts.app')
            ->section('content');
    }
}
