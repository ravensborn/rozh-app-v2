<?php

namespace App\Http\Livewire\Pages\Orders;


use App\Models\Order;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;


class QuickAccess extends Component
{

    use LivewireAlert;

    public $orders;
    public $miniOrders;

    public string $from_date = "";
    public string $to_date = "";
    public int $status = 0;

    public $lastOrderSentToPending;

    public function getOrders()
    {

        if ($this->from_date && $this->to_date) {

            $order = Order::query();

            $order->whereIn('status', [
                Order::STATUS_FORWARDER_STATUS,
                //Order::STATUS_FORWARDER_RETURNED,
                Order::STATUS_FORWARDER_NO_STATUS,
                Order::STATUS_FORWARDER_ORDER_FULFILLED,
            ]);

            $order->whereDate('created_at', '>=', $this->from_date)
                ->whereDate('created_at', '<=', $this->to_date);

            $miniOrder = $order;
            $miniOrder->orderBy('id');
            //$miniOrder->limit(100);
            $this->miniOrders = $miniOrder->get();

            $order->where('internal_status', $this->status);

            $order->orderBy('id');

            $order->limit(5);

            $this->orders = $order->get();


        }
    }

    public function setStatus(Order $order, $status)
    {

        $this->lastOrderSentToPending = $order;

        $order->update(['internal_status' => $status]);
        $this->alert('success', 'Successfully updated status to ' . strtolower($order->getInternalStatus()) . '.');
        $this->getOrders();
    }

    public function returnLastUpdatedOrderToPending()
    {

        if ($this->lastOrderSentToPending) {

            $this->lastOrderSentToPending->update(['internal_status' => Order::INTERNAL_STATUS_PENDING]);

            $this->alert('success', 'Successfully restored order to pending status.');

            $this->getOrders();
            $this->lastOrderSentToPending = null;

            return false;
        }

        $this->alert('error', 'No order selected.');
    }


    public function mount()
    {
        $this->orders = collect();
        $this->miniOrders = collect();

        $this->status = Order::INTERNAL_STATUS_PENDING;

        $this->from_date = Carbon::today()->startOfMonth()->subMonth(1)->format('Y-m-d');
        $this->to_date = Carbon::today()->format('Y-m-d');
        $this->getOrders();
    }

    public function render()
    {
        return view('livewire.pages.orders.quick-access')
            ->extends('layouts.app')
            ->section('content');
    }
}
