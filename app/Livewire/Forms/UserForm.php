<?php

namespace App\Livewire\Forms;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Validate;
use Livewire\Form;

class UserForm extends Form
{
    public ?User $user;

    public $name;
    public $email;
    public $password;
    public $password_confirmation;
    public $current_category_id;
    public $roles = [];

    public function setUser(User $user)
    {
        $this->user = $user;
        $this->fill($user->only([
            'name',
            'email',
            'current_category_id',
        ]));

        $this->roles = $user->roles->pluck('id')->toArray();
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'roles' => 'required|array|min:1',
            'password' => 'required|string|min:8|confirmed',
            'current_category_id' => 'required|integer',
        ];
    }

    public function editRules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $this->user->id,
            'roles' => 'required|array|min:1',
            'password' => 'nullable|string|min:8|confirmed',
            'current_category_id' => 'required|integer',
        ];
    }

    public function save()
    {
        $this->validate($this->rules());

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'current_category_id' => $this->current_category_id,
        ])->assignRole($this->roles);

        $this->reset();

        return true;
    }

    public function update()
    {
        $this->validate($this->editRules());

        $this->user->update([
            'name' => $this->name,
            'email' => $this->email,
            'current_category_id' => $this->current_category_id,
        ]);

        // If the password is not empty, update it
        if (!empty($this->password)) {
            $this->user->update([
                'password' => Hash::make($this->password),
            ]);
        }

        $this->reset('password', 'password_confirmation');

        return true;
    }
}
