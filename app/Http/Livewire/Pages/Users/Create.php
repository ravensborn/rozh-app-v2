<?php

namespace App\Http\Livewire\Pages\Users;

use App\Models\User;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class Create extends Component
{
    public string $name = "";
    public string $email = "";
    public string $role_name = "";
    public string $limited_to_page_id = "";
    public string $password = "";

    public function submitUser()
    {

        $rules = [
            'name' => 'required|max:255|min:2',
            'email' => 'required|email|max:255',
            'role_name' => 'required|in:admin,data-entry',
            'limited_to_page_id' => 'sometimes|exists:pages,id',
            'password' => 'required|max:50|min:6',
        ];

        $validated = $this->validate($rules);

        $validated['password'] = bcrypt($validated['password']);

        $user = new User;
        $user = $user->create($validated);

        $user->assignRole($validated['role_name']);

        if($this->role_name = 'data-entry') {

            if ($validated['limited_to_page_id']) {

                Role::findOrCreate('limited_to_page');
                Role::findOrCreate('limited_to_page_' . $validated['limited_to_page_id']);

                $user->assignRole('limited_to_page');
                $user->assignRole('limited_to_page_' . $validated['limited_to_page_id']);
            }

        }



        return redirect()->route('users.index');
    }

    public function render()
    {
        return view('livewire.pages.users.create')
            ->extends('layouts.app')
            ->section('content');
    }
}
