<?php

namespace App\Http\Livewire\Pages\ExpenseItems;

use App\Models\ExpenseItem;
use Livewire\Component;

class Create extends Component
{
    public string $title = "";
    public string $quantity = "";
    public string $amount = "";
    public string $note = "";

    public function submitExpenseItem()
    {

        $rules = [
            'title' => 'required',
            'quantity' => 'required',
            'amount' => 'required',
            'note' => 'required',
        ];

        $validated = $this->validate($rules);

        $expenseItem = new ExpenseItem();
        $expenseItem = $expenseItem->create($validated);

        return redirect()->route('expense-items.index');
    }

    public function render()
    {
        return view('livewire.pages.expense-items.create')
            ->extends('layouts.app')
            ->section('content');
    }
}
