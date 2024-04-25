<?php

namespace App\Livewire\Forms;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Validate;
use Livewire\Form;

class UserForm extends Form
{
    public ?User $user;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|email|max:255')]
    public string $email = '';

    #[Validate('required|string|max:255')]
    public string $password = '';

    public function setUser(User $user)
    {
        $this->user = $user;
        $this->fill($user->only([
            'name',
            'email',
        ]));
    }

    public function save()
    {
        $this->validate();

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        $this->reset();

        return true;
    }

    public function update()
    {
        $this->validate();

        $this->user->update([
            'name' => $this->name,
            'email' => $this->email,
        ]);

        // If the password is not empty, update it
        if (!empty($this->password)) {
            $this->user->update([
                'password' => Hash::make($this->password),
            ]);
        }

        $this->reset();

        return true;
    }
}
