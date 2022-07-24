<?php

namespace App\Http\Livewire\Pages\Users;

use App\Models\User;
use Livewire\Component;

class Create extends Component
{
    public string $name = "";
    public string $email = "";
    public string $role_name = "";
    public string $password = "";

    public function submitUser() {

        $rules = [
            'name' => 'required|max:255|min:2',
            'email' => 'required|email|max:255',
            'role_name' => 'required|in:admin,data-entry',
            'password' => 'required|max:50|min:6',
        ];

        $validated = $this->validate($rules);
        $validated['password'] = bcrypt($validated['password']);

        $user = new User;
        $user = $user->create($validated);

        $user->assignRole($validated['role_name']);

        return redirect()->route('users.index');
    }

    public function render()
    {
        return view('livewire.pages.users.create')
            ->extends('layouts.app')
            ->section('content');
    }
}
