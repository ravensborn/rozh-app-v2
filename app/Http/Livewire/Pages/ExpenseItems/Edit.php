<?php

namespace App\Http\Livewire\Pages\ExpenseItems;

use App\Models\ExpenseItem;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Edit extends Component
{
    use LivewireAlert;

    public string $title = "";
    public string $quantity = "";
    public string $amount = "";
    public string $note = "";

    public $expenseItem;

    public function mount(ExpenseItem $expenseItem) {

        $this->expenseItem = $expenseItem;

        $this->title = $expenseItem->title;
        $this->quantity = $expenseItem->quantity;
        $this->amount = $expenseItem->amount;
        $this->note = $expenseItem->note;
    }

    public function updateExpenseItem() {

        $rules = [
            'title' => 'required',
            'quantity' => 'required',
            'amount' => 'required',
            'note' => 'required',
        ];

        $validated = $this->validate($rules);

        $this->expenseItem->update($validated);

        $this->alert('success', 'Successfully updated item.', [
            'position' => 'top-end',
            'timer' => 5000,
            'toast' => true,
        ]);

    }

    public function render()
    {
        return view('livewire.pages.expense-items.edit')
            ->extends('layouts.app')
            ->section('content');
    }
}
