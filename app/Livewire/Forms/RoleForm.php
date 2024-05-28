<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use Spatie\Permission\Models\Role;

class RoleForm extends Form
{
    public ?Role $role;

    public $name = '';
    public $permissions = [];

    public function setRole(Role $role)
    {
        $this->role = $role;
        
        $this->fill($role->only([
            'name',
        ]));

        $this->permissions = $role->permissions->pluck('name')->toArray();
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'required|array|min:1',
        ];
    }

    public function editRules()
    {
        return [
            'name' => 'required|string|max:255|unique:roles,name,' . $this->role->id,
            'permissions' => 'required|array|min:1',
        ];
    }

    public function save()
    {
        $this->validate($this->rules());

        $role = Role::create([
            'name' => $this->name,
            'guard_name' => 'web',
        ]);

        $role->syncPermissions($this->permissions);

        $this->reset();

        return true;
    }

    public function update()
    {
        $this->validate($this->editRules());

        $this->role->update([
            'name' => $this->name,
        ]);

        $this->role->syncPermissions($this->permissions);

        $this->reset();

        return true;
    }
}
