<?php

namespace App\Livewire\Category\Index;

use App\Livewire\Forms\CategoryForm;
use App\Livewire\Traits\SoftDeletes;
use App\Livewire\Traits\Sortable;
use App\Models\Category;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Attributes\Renderless;
use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\Actions;

#[Lazy]
class Table extends Component
{
    use Actions;
    use WithPagination;
    use SoftDeletes;
    use Sortable;

    public $model = null;
    public $modal = false;
    public CategoryForm $form;

    public function mount()
    {
        $this->model = new Category(); // This is used for the SoftDeletes trait
    }

    public function edit(Category $category)
    {
        $this->form->setCategory($category);
        $this->modal = true;
    }

    public function update()
    {
        $update = $this->form->update();

        if ($update) {
            $this->notification()->success(
                'Campaña actualizada',
                'La campaña se ha actualizado correctamente'
            );

            $this->modal = false;
        }
    }

    #[Renderless]
    public function export()
    {
        return Category::toCsv();
    }

    public function placeholder()
    {
        return view('components.table.placeholder');
    }

    #[On('refresh-category-table')] 
    public function render()
    {
        return view('livewire.category.index.table', [
            'categories' => Category::search($this->search)->sort($this->sortCol, $this->sortAsc)->trash($this->trash)->paginate($this->perPage),
        ]);
    }
}
