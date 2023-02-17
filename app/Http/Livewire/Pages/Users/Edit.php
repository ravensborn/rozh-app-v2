<?php

namespace App\Http\Livewire\Pages\Users;

use App\Models\User;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class Edit extends Component
{
    use LivewireAlert;

    public string $name = "";
    public string $email = "";
    public string $role_name = "data-entry";
    public string $limited_to_page_id = "";

    public $user;

    public function mount(User $user) {

        $this->user = $user;

        $this->name = $user->name;
        $this->email = $user->email;

        //Should not be hardcoded I know.
        if($user->hasRole('data-entry')) {
            $this->role_name = 'data-entry';
        } else if($user->hasRole('admin')) {
            $this->role_name = 'admin';
        }

        if($user->hasRole('limited_to_page')) {
            $this->limited_to_page_id = $this->user->getLimitedByPageId();
        }

    }


    public function updateUser() {

        $rules = [
            'name' => 'required|max:255|min:2',
            'email' => 'required|email|max:255|unique:users,email,' . $this->user->id,
            'role_name' => 'required|in:admin,data-entry',
            'limited_to_page_id' => 'sometimes|exists:pages,id',
        ];

        $validated = $this->validate($rules);

        $this->user->update($validated);

        $syncRoleArray = [];
        array_push($syncRoleArray, $validated['role_name']);

        if($this->role_name == 'data-entry') {

            if ($validated['limited_to_page_id']) {

                Role::findOrCreate('limited_to_page');
                Role::findOrCreate('limited_to_page_' . $validated['limited_to_page_id']);

                array_push($syncRoleArray, 'limited_to_page');
                array_push($syncRoleArray, 'limited_to_page_' . $validated['limited_to_page_id']);
            }
        }

        $this->user->syncRoles($syncRoleArray);

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
