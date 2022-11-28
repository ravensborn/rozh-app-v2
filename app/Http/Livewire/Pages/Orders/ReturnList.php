<?php

namespace App\Http\Livewire\Pages\Orders;

use App\Models\Order;

use App\Models\ReturnedItem;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;


class ReturnList extends Component
{

    use LivewireAlert, WithFileUploads;

    public $code = '';
    public $quantity = '';
    public $image = '';
    public string $size = "Free Size";
    public string $color = "Same as picture";

    public function addNewCode()
    {
        $rules = [
            'code' => 'required|unique:returned_items,code',
            'quantity' => 'required|numeric',
            'image' => 'required|image|max:5242880', // 5MB Max
            'size' => 'required',
            'color' => 'required',
        ];

        $validated = $this->validate($rules);

        $item = new ReturnedItem;
        $item = $item->create($validated);

        if ($this->image) {
            $item
                ->addMedia($this->image)
                ->toMediaCollection('images');
        }

        $this->code = '';
        $this->quantity = 0;
        $this->size = "Free Size";
        $this->color = "Same as picture";
        $this->image = '';

        $this->emit('refreshed-items');

        $this->alert('success', 'Successfully added new code.');
    }

    public function mount()
    {

    }

    public function render()
    {
        return view('livewire.pages.orders.return-list')
            ->extends('layouts.app')
            ->section('content');
    }
}
