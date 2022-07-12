<?php

namespace App\Http\Livewire\Pages\Orders;

use App\Models\Forwarder;
use App\Models\ForwarderStatus;
use App\Models\Order;
use App\Models\Page;
use Illuminate\Support\Collection;
use Livewire\Component;


class Statistic extends Component
{
    private $orders;

    public Collection $pages;
    public Collection $forwarderStatuses;
    public Collection $forwarders;

    public string $phone_number = "";

    public int $page_id = 1;
    public int $forwarder_id = 1;
    public int $forwarder_status_id = 0;
    public int $status = 0;

    public function mount() {

        $this->orders = Order::query();

        $this->pages = Page::all();
        $this->forwarders = Forwarder::all();

        $this->forwarder_id = Forwarder::FORWARDER_HYPERPOST;
        $this->forwarderStatuses = ForwarderStatus::where('forwarder_id', $this->forwarder_id)->get();
        $this->status = -1;
        $this->forwarder_status_id = -1;

        if($this->ifForwarderSelected()) {
            if($this->forwarderStatuses->count() > 0) {
                $this->filterOrdersByForwarder();
            }
        }
    }

    private function filterOrdersByForwarder() {
        $this->orders->where('forwarder_id', $this->forwarder_id);
    }

    private function filterOrderByForwarderStatus() {
        if($this->forwarder_status_id != -1) {
            $this->orders->where('forwarder_status_id', $this->forwarder_status_id);
        }
    }

    private function filterOrderByOrderStatus() {
        if($this->status != -1) {
            $this->orders->where('status', $this->status);
        }
    }

    private function filterOrderByPhoneNumber() {
        $this->orders->where('customer_primary_phone', $this->phone_number);
    }

    public function resetFilter() {
        $this->orders = Order::query();
    }

    public function updatedForwarderId() {

        $this->phone_number = "";

        if($this->forwarder_id == 0) {
            $this->orders = Order::query();
        } else {
            $this->resetFilter();
            $this->filterOrdersByForwarder();
        }

        $this->forwarderStatuses = ForwarderStatus::where('forwarder_id', $this->forwarder_id)->get();
    }

    public function updatedPhoneNumber() {
        $this->forwarder_id = 0;
        $this->forwarder_status_id = -1;
        $this->status = -1;
        $this->resetFilter();
        $this->filterOrderByPhoneNumber();
    }

    public function updatedForwarderStatusId() {
        $this->phone_number = "";
        $this->resetFilter();
        $this->filterOrdersByForwarder();
        $this->filterOrderByOrderStatus();
        $this->filterOrderByForwarderStatus();
    }

    public function updatedStatus() {
        $this->phone_number = "";
        $this->resetFilter();
        $this->filterOrdersByForwarder();
        $this->filterOrderByForwarderStatus();
        $this->filterOrderByOrderStatus();
    }

    public function ifForwarderSelected(): bool
    {
        if($this->forwarder_id == 0 || $this->forwarder_id == Forwarder::NO_FORWARDER) {
            return false;
        }
        return true;
    }

    public function render()
    {
        return view('livewire.pages.orders.statistic',[
            'orders' => !is_null($this->orders) ? $this->orders->get() : collect(),
        ])
            ->extends('layouts.app')
            ->section('content');
    }
}
