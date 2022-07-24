<?php

namespace App\Http\Livewire\Pages\Users;

use App\Models\User;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Edit extends Component
{
    use LivewireAlert;

    public string $name = "";
    public string $email = "";
    public string $role_name = "data-entry";

    public $user;

    public function mount(User $user) {

        $this->user = $user;

        $this->name = $user->name;
        $this->email = $user->email;

        $userRoles = $user->getRoleNames();

        if($userRoles->count() > 0) {
            $this->role_name = $userRoles->first();
        } else {
            $this->role_name = "";
        }

    }

    public function updateUser() {

        $rules = [
            'name' => 'required|max:255|min:2',
            'email' => 'required|email|max:255|unique:users,email,' . $this->user->id,
            'role_name' => 'required|in:admin,data-entry',
        ];

        $validated = $this->validate($rules);

        $this->user->update($validated);

        $this->user->syncRoles($validated['role_name']);

        $this->alert('success', 'Successfully updated user information.', [
            'position' => 'top-end',
            'timer' => 5000,
            'toast' => true,
        ]);

    }

    public function render()
    {
        return view('livewire.pages.users.edit')
            ->extends('layouts.app')
            ->section('content');
    }
}
