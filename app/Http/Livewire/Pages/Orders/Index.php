<?php

namespace App\Http\Livewire\Pages\Orders;

use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        return view('livewire.pages.orders.index')
            ->extends('layouts.app')
            ->section('content');
    }
}
