<?php

namespace App\Http\Livewire\Pages;

use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class BlockList extends Component
{

    use LivewireAlert;
    public $phone = "";

    public function addNewItem() {
        $rules = [
            'phone' => 'required|unique:block_lists,phone'
        ];

       $validated =  $this->validate($rules);

       $item = new \App\Models\BlockList();
       $item = $item->create($validated);

       $this->phone = "";

       $this->emit('refresh-items');

       $this->alert('success', 'New phone number successfully blocked.');


    }
    public function mount()
    {


    }

    public function render()
    {
        return view('livewire.pages.block-list')
            ->extends('layouts.app')
            ->section('content');
    }
}
