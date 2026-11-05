<?php

namespace App\Livewire\User\Index;

use App\Livewire\Forms\UserForm;
use App\Livewire\Traits\SoftDeletes;
use App\Livewire\Traits\Sortable;
use App\Models\Category;
use App\Models\User;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use WireUi\Traits\Actions;

#[Lazy]
class Table extends Component
{
    use Actions;
    use SoftDeletes;
    use Sortable;
    use WithPagination;

    public $model = null;

    public $modal = false;

    public $roles = [];

    public $categories = [];

    public UserForm $form;

    public function mount()
    {
        $this->model = new User; // This is used for the SoftDeletes trait
        $this->roles = Role::select('id', 'name')->get();
        $this->categories = Category::select('id', 'name')->get();
    }

    public function edit(User $user)
    {
        $this->authorize('edit.users');

        $this->form->setUser($user);
        $this->modal = true;
    }

    public function update()
    {
        $this->authorize('edit.users');

        $update = $this->form->update();

        if ($update) {
            $this->notification()->success(
                'Usuario actualizado',
                'El Usuario se ha actualizado correctamente'
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
        return ['id', 'name', 'email', 'updated_at'];
    }

    #[On('refresh-user-table')]
    public function render()
    {
        return view('livewire.user.index.table', [
            'users' => User::with('roles', 'currentCategory')
                ->search($this->search)->sort($this->sortColumn(), $this->sortAsc)->trash($this->trash)->paginate($this->perPage),
        ]);
    }
}
