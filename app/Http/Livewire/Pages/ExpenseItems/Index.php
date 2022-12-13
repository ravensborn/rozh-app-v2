<?php

namespace App\Http\Livewire\Pages\ExpenseItems;

use App\Models\ExpenseItem;
use App\Models\Order;
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

    public int $totalExpensesAmount = 0;
    public int $profitPerOrder = 6000;
    public int $numberOfOrders = 0;

    public bool $filter_on = false;

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

    public function updatedFilterOn()
    {

        if(!$this->filter_on) {
            $this->totalExpensesAmount = ExpenseItem::all()->sum(function ($item) {
                return $item->quantity * $item->amount;
            });

            $this->numberOfOrders = Order::count();
        } else {
            $this->filterExpensesByDate();
        }

    }

    public function filterExpensesByDate()
    {
        if (($this->from_date && $this->to_date) && $this->filter_on) {

            $this->totalExpensesAmount = ExpenseItem::whereDate('created_at', '>=', $this->from_date)
                ->whereDate('created_at', '<=', $this->to_date)->get()->sum(function ($item) {
                    return $item->quantity * $item->amount;
                });

            $this->numberOfOrders = Order::whereDate('created_at', '>=', $this->from_date)
                ->whereDate('created_at', '<=', $this->to_date)->count();
        }
    }

    public function mount()
    {

        $this->from_date = Carbon::today()->startOfMonth()->format('Y-m-d');
        $this->to_date = Carbon::today()->format('Y-m-d');
        $this->updatedFilterOn();
//        $this->filterExpensesByDate();

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
