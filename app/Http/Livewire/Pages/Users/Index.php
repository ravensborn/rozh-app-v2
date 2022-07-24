<?php

namespace App\Http\Livewire\Pages\Users;

use App\Models\User;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Index extends Component
{
    use LivewireAlert;

    public $users;
    public $userTobeDeleted = null;

    protected $listeners = [
        'deleteUser',
        'refresh-users' => '$refresh',
    ];

    public function triggerDeleteUser(User $user)
    {
        $this->confirm('Are you sure that you want to delete this user?', [
            'toast' => false,
            'position' => 'center',
            'showConfirmButton' => true,
            'cancelButtonText' => 'No',
            'onConfirmed' => 'deleteUser'
        ]);

        $this->userTobeDeleted = $user;
    }

    public function deleteUser()
    {
        if (auth()->user()->id == $this->userTobeDeleted->id) {

            $this->alert('error', 'You cannot delete yourself.', [
                'position' => 'top-end',
                'timer' => 5000,
                'toast' => true,
            ]);

            return false;
        }

        if ($this->userTobeDeleted->orders()->exists()) {

            $this->alert('error', 'You cannot delete a user that has orders.', [
                'position' => 'top-end',
                'timer' => 5000,
                'toast' => true,
            ]);

            return false;
        }

        $this->userTobeDeleted->delete();

        $this->alert('success', 'User successfully deleted.', [
            'position' => 'top-end',
            'timer' => 5000,
            'toast' => true,
        ]);

    }

    public function mount()
    {
        $this->users = User::where('email', '!=', 'default@default.com')
            ->get();
    }

    public function render()
    {
        return view('livewire.pages.users.index')
            ->extends('layouts.app')
            ->section('content');
    }
}
