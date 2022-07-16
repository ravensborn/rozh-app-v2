<?php

namespace App\Http\Livewire\Pages\Orders;

use App\Models\Forwarder;
use App\Models\ForwarderStatus;
use App\Models\Order;
use App\Models\Page;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Livewire\Component;


class Statistic extends Component
{
    private $orders;

    public Collection $pages;
    public Collection $forwarderStatuses;
    public Collection $forwarders;

    public string $phone_number = "";

    public $from_date = null;
    public $to_date = null;

    public int $page_id = 1;
    public int $forwarder_id = 1;
    public int $forwarder_status_id = 0;
    public int $status = 0;

    public function mount()
    {

        $this->from_date = Carbon::today()->startOfMonth()->format('Y-m-d');
        $this->to_date = Carbon::today()->format('Y-m-d');

        $this->orders = Order::query();

        $this->pages = Page::all();
        $this->forwarders = Forwarder::all();

        $this->forwarder_id = Forwarder::FORWARDER_HYPERPOST;
        $this->forwarderStatuses = ForwarderStatus::where('forwarder_id', $this->forwarder_id)->get();
        $this->status = -1;
        $this->forwarder_status_id = -1;

        if ($this->ifForwarderSelected()) {
            if ($this->forwarderStatuses->count() > 0) {
                $this->filterOrdersByForwarder();
            }
        }
    }

    private function filterOrdersByForwarder()
    {
        if ($this->forwarder_id != 0) {
            $this->orders->where('forwarder_id', $this->forwarder_id);
        }

    }

    private function filterOrderByForwarderStatus()
    {
        if ($this->forwarder_status_id != -1) {
            $this->orders->where('forwarder_status_id', $this->forwarder_status_id);
        }
    }

    private function filterOrderByOrderStatus()
    {
        if ($this->status != -1) {
            $this->orders->where('status', $this->status);
        }
    }

    private function filterOrderByPhoneNumber()
    {
        if($this->phone_number) {
            $this->orders->where('customer_primary_phone', $this->phone_number);
        }
    }

    private function filterOrdersByDate()
    {
        if ($this->from_date && $this->to_date) {

            $this->orders
                ->whereDate('created_at', '>=', $this->from_date)
                ->whereDate('created_at', '<=', $this->to_date);
        }
    }

    public function filter()
    {
        $this->orders = Order::query();
        $this->filterOrdersByForwarder();
        $this->filterOrderByOrderStatus();
        $this->filterOrderByForwarderStatus();
        $this->filterOrderByPhoneNumber();
        $this->filterOrdersByDate();
    }

    public function filterByDate() {
        if ($this->from_date && $this->to_date) {
            $this->filter();
        }
    }
    public function updatedForwarderId()
    {
        $this->status = -1;
        $this->forwarder_status_id = -1;
        $this->filter();

        $this->forwarderStatuses = ForwarderStatus::where('forwarder_id', $this->forwarder_id)->get();
    }
    public function updatedPhoneNumber()
    {
        $this->filter();
    }

    public function updatedForwarderStatusId()
    {
        $this->filter();
    }

    public function updatedStatus()
    {
        $this->filter();
    }

    public function ifForwarderSelected(): bool
    {
        if ($this->forwarder_id == 0 || $this->forwarder_id == Forwarder::NO_FORWARDER) {
            return false;
        }
        return true;
    }

    public function render()
    {
        return view('livewire.pages.orders.statistic', [
            'orders' => !is_null($this->orders) ? $this->orders->get() : collect(),
        ])
            ->extends('layouts.app')
            ->section('content');
    }
}
