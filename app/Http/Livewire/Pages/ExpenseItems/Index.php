<?php

namespace App\Http\Livewire\Pages\ExpenseItems;

use App\Models\ExpenseItem;
use App\Models\Order;
use App\Models\Page;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use LivewireAlert;
    use WithPagination;

    public string $from_date = "";
    public string $to_date = "";

    public int $orders = 0;
    public int $orders_worth = 0;

    public int $order_items = 0;

    public int $expenses = 0;
    public int $expenses_worth = 0;

    public int $profitPerOrderItem = 6000;
    public int $profit = 0;

    public $pages;

    public int $page_id = 0;

    protected $paginationTheme = 'bootstrap';

    public $expenseItemTobeDeleted = null;

    protected $listeners = [
        'deleteExpenseItem',
        'refresh-expense-items' => '$refresh',
    ];

    public function triggerDeleteExpenseItem(ExpenseItem $item)
    {
        $this->confirm('Are you sure that you want to delete this item?', [
            'toast' => false,
            'position' => 'center',
            'showConfirmButton' => true,
            'cancelButtonText' => 'No',
            'onConfirmed' => 'deleteExpenseItem'
        ]);

        $this->expenseItemTobeDeleted = $item;
    }

    public function deleteExpenseItem()
    {
        $this->expenseItemTobeDeleted->delete();

        $this->alert('success', 'Item successfully deleted.', [
            'position' => 'top-end',
            'timer' => 5000,
            'toast' => true,
        ]);

        $this->mount();

    }

    public function updatedProfitPerOrderItem()
    {
        $this->processFilter();
    }

    public function processFilter()
    {

        $orders = Order::query();
        $expenses = ExpenseItem::query();

        if ($this->to_date && $this->from_date) {
            $orders->whereDate('created_at', '>=', $this->from_date)
                ->whereDate('created_at', '<=', $this->to_date);

            $expenses->whereDate('created_at', '>=', $this->from_date)
                ->whereDate('created_at', '<=', $this->to_date);
        }

        if ($this->page_id) {
            $orders->where('page_id', $this->page_id);
        }

        $orders = $orders->get();
        $expenses = $expenses->get();

        $this->orders = $orders->count();


        $this->orders_worth = $orders->sum(function ($order) {
            return $order->total();
        });

        $this->order_items = $orders->sum(function ($order) {
            return $order->items->count();
        });

        $this->expenses = $expenses->count();
        $this->expenses_worth = $expenses->sum('amount');
        $this->profit = $this->profitPerOrderItem * $this->order_items;

    }

//    public function filterExpensesByDate()
//    {
//        if (($this->from_date && $this->to_date) && $this->filter_on) {
//
//            $this->totalExpensesAmount = ExpenseItem::whereDate('created_at', '>=', $this->from_date)
//                ->whereDate('created_at', '<=', $this->to_date)->get()->sum(function ($item) {
//                    return $item->quantity * $item->amount;
//                });
//
//            $this->numberOfOrders = Order::whereDate('created_at', '>=', $this->from_date)
//                ->whereDate('created_at', '<=', $this->to_date)->count();
//        }
//    }

    public function mount()
    {

        $this->pages = Page::all();
        $this->from_date = Carbon::today()->startOfMonth()->format('Y-m-d');
        $this->to_date = Carbon::today()->format('Y-m-d');

        $this->processFilter();

    }

    public function render()
    {
        return view('livewire.pages.expense-items.index', [
            'expenseItems' => ExpenseItem::latest()->paginate(20),
        ])
            ->extends('layouts.app')
            ->section('content');
    }
}
