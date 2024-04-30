<?php

namespace App\Http\Livewire\Pages\Calculations;

use App\Models\ExpenseItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Page;
use Carbon\Carbon;
use Livewire\Component;

class Index extends Component
{

    public $pages;

    //Most Profitable Items
    public string $mpi_filter_from_date = "";
    public string $mpi_filter_to_date = "";
    public int $mpi_filter_page_id = 0;
    public bool $mpi_most_sold_stats_enabled = false;
    public array $mpi_topSoldItemsByCodeArray = [];

    public function updatedMpiMostSoldStatsEnabled()
    {
        if ($this->mpi_most_sold_stats_enabled) {
            $this->MpiGetMostSoldItemList();
        } else {
            $this->mpi_filter_from_date = "";
            $this->mpi_filter_to_date = "";
            $this->mpi_filter_page_id = 0;
            $this->mpi_topSoldItemsByCodeArray = [];
        }
    }

    public function MpiGetMostSoldItemList()
    {
        $orderItems = OrderItem::query();

        if ($this->mpi_filter_page_id) {

            $orderItems->whereHas('order', function ($query) {
                $query->where('page_id', $this->mpi_filter_page_id);
            });

        }

        if ($this->mpi_filter_from_date && $this->mpi_filter_to_date) {

            $orderItems->whereHas('order', function ($query) {
                $query->whereDate('created_at', '>=', $this->mpi_filter_from_date)
                    ->whereDate('created_at', '<=', $this->mpi_filter_to_date);
            });
        }

        $data = $orderItems
            ->select('code')
            ->where('code', '!=', '')
            ->selectRaw('COUNT(*) AS count')
            ->groupBy('code')
            ->orderByDesc('count')
            ->limit(10)
            ->get()->toArray();

        $this->mpi_topSoldItemsByCodeArray = $data;
    }

    //Profit Calculator
    public bool $pc_enabled = false;
    public string $pc_from_date = "";
    public string $pc_to_date = "";
    public int $pc_orders = 0;
    public int $pc_orders_worth = 0;
    public int $pc_order_items = 0;
    public int $pc_expenses = 0;
    public int $pc_expenses_worth = 0;
    public int $pc_profitPerOrderItem = 6000;
    public int $pc_profit = 0;
    public int $pc_page_id = 0;

    public function processProfitCalculator()
    {

        $orders = Order::query();
        $expenses = ExpenseItem::query();

        if ($this->pc_to_date && $this->pc_from_date) {
            $orders->whereDate('created_at', '>=', $this->pc_from_date)
                ->whereDate('created_at', '<=', $this->pc_to_date);

            $expenses->whereDate('created_at', '>=', $this->pc_from_date)
                ->whereDate('created_at', '<=', $this->pc_to_date);
        }

        if ($this->pc_page_id) {
            $orders->where('page_id', $this->pc_page_id);
        }

        $orders = $orders->get();
        $expenses = $expenses->get();

        $this->pc_orders = $orders->count();


        $this->pc_orders_worth = $orders->sum(function ($order) {
            return $order->total();
        });

        $this->pc_order_items = $orders->sum(function ($order) {
            return $order->items->count();
        });

        $this->pc_expenses = $expenses->count();
        $this->pc_expenses_worth = $expenses->sum('amount');
        $this->pc_profit = $this->pc_profitPerOrderItem * $this->pc_order_items;

    }

    public function updatedPcProfitPerOrderItem()
    {
        $this->processProfitCalculator();
    }

    public function updatedPcEnabled()
    {

        if ($this->pc_enabled) {
            $this->pc_from_date = Carbon::today()->startOfMonth()->format('Y-m-d');
            $this->pc_to_date = Carbon::today()->format('Y-m-d');
            $this->processProfitCalculator();
        } else {
            $this->pc_from_date = "";
            $this->pc_to_date = "";
        }
    }


    //Per Website Statistics
    public bool $pws_enabled = false;
    public string $pws_from_date = "";
    public string $pws_to_date = "";

    public int $pws_number_of_orders = 0;
    public int $pws_number_of_order_items = 0;
    public int $pws_orders_worth = 0;

    public array $PWSwebsites = ['facebook', 'instagram', 'other'];

    public string $pws_selected_website = 'all';

    public function updatedPWSEnabled()
    {
        if ($this->pws_enabled) {
            $this->processPWS();
        } else {
            $this->pws_from_date = "";
            $this->pws_to_date = "";
            $this->pws_selected_website = 'all';
        }
    }

    public function processPWS(): void
    {

        $orders = Order::query();

        if(in_array($this->pws_selected_website, $this->PWSwebsites)) {
            $orders->where('customer_profile_type', $this->pws_selected_website);
        }

        if ($this->pws_to_date && $this->pws_from_date) {
            $orders->whereDate('created_at', '>=', $this->pws_from_date)
                ->whereDate('created_at', '<=', $this->pws_to_date);
        }

        $orders = $orders->get();

        $this->pws_number_of_orders = $orders->count();
        $this->pws_orders_worth = $orders->sum(function ($order) {
            return $order->total();
        });
        $this->pws_number_of_order_items = $orders->sum(function ($order) {
            return $order->items->count();
        });

    }

    public function mount()
    {
        $this->pages = Page::all();
    }

    public function render()
    {
        return view('livewire.pages.calculations.index')
            ->extends('layouts.app')
            ->section('content');
    }
}
