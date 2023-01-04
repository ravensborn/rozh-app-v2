<?php

namespace App\Http\Livewire\Pages\Orders;

use App\Models\Order;

use App\Models\OrderItem;
use App\Models\Page;
use App\Models\ReturnedItem;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;


class ReturnList extends Component
{

    use LivewireAlert, WithFileUploads;

    public $code = '';
    public int $price = 0;
    public int $quantity = 0;
    public $image = '';
    public string $size = "Free Size";
    public string $color = "Same as picture";

    public $pages;
    public int $page_id = 1;

    public $returnListItems;
    public $returnListItemsAmount;

    public function addNewCode()
    {
        $rules = [
            'code' => 'required|unique:returned_items,code,' . $this->code . ',id,page_id,'  . $this->page_id,
            'price' => 'required|numeric',
            'quantity' => 'required|numeric',
            'image' => 'required|image|max:5242880', // 5MB Max
            'size' => 'required',
            'color' => 'required',
            'page_id' => 'required'
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
        $this->price = 0;
        $this->quantity = 0;
        $this->size = "Free Size";
        $this->color = "Same as picture";
        $this->image = '';

        $this->emit('refreshed-items');

        $this->calculateStatistics();

        $this->alert('success', 'Successfully added new code.');
    }

    public function calculateStatistics() {

        $items = ReturnedItem::all();

        $this->returnListItems = $items->count();

        $this->returnListItemsAmount = $items->sum(function($item) {
            return $item->quantity * $item->price;
        });

        $this->alert('success', 'Refreshed statistics.');

    }

    public function mount()
    {
        $this->pages = Page::all();
        $this->calculateStatistics();
    }

    public function render()
    {
        return view('livewire.pages.orders.return-list')
            ->extends('layouts.app')
            ->section('content');
    }
}
