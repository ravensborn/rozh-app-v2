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

    public function mount()
    {

    }
    public function render()
    {

        $expenseItems = ExpenseItem::latest()->paginate(20);
        $total = $expenseItems->sum(function($item) {
            return $item->amount;
        });

        return view('livewire.pages.expense-items.index', [
            'expenseItems' => $expenseItems,
            'total' => $total,
        ])
            ->extends('layouts.app')
            ->section('content');
    }
}
