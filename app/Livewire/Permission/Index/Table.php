<?php

namespace App\Livewire\Permission\Index;

use App\Livewire\Forms\RoleForm;
use App\Livewire\Traits\Deletes;
use App\Livewire\Traits\Sortable;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use WireUi\Traits\Actions;

#[Lazy]
class Table extends Component
{
    use Actions;
    use Deletes;
    use Sortable;
    use WithPagination;

    public $model = null;

    public $modal = false;

    public $permissions = [];

    public RoleForm $form;

    public function mount()
    {
        $this->model = new Role; // This is used for the SoftDeletes trait
        $this->permissions = Permission::select('id', 'name', 'description')->get();
    }

    public function edit(Role $role)
    {
        $this->authorize('edit.roles');

        $this->form->setRole($role);
        $this->modal = true;
    }

    public function update()
    {
        $this->authorize('edit.roles');

        $update = $this->form->update();

        if ($update) {
            $this->notification()->success(
                'Rol actualizado',
                'El Rol se ha actualizado correctamente'
            );

            $this->modal = false;
        }
    }

    public function placeholder()
    {
        return view('components.table.placeholder');
    }

    /**
     * @return array<int, string>
     */
    protected function sortableColumns(): array
    {
        return ['id', 'name', 'updated_at'];
    }

    #[On('refresh-role-table')]
    public function render()
    {
        $roles = Role::query();

        if ($this->search) {
            $roles->where('name', config('database.operator'), "%{$this->search}%");
        }

        if ($this->sortColumn()) {
            $roles->orderBy($this->sortColumn(), $this->sortAsc ? 'asc' : 'desc');
        }

        return view('livewire.permission.index.table', [
            'roles' => $roles->paginate($this->perPage),
        ]);
    }
}
