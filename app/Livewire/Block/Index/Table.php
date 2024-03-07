<?php

namespace App\Livewire\Block\Index;

use App\Livewire\Forms\BlockForm;
use App\Livewire\Traits\SoftDeletes;
use App\Livewire\Traits\Sortable;
use App\Models\Block;
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
    public BlockForm $form;

    public function mount()
    {
        $this->model = new Block(); // This is used for the SoftDeletes trait
    }

    public function edit(Block $block)
    {
        $this->form->setBlock($block);
        $this->modal = true;
    }

    public function update()
    {
        $update = $this->form->update();

        if ($update) {
            $this->notification()->success(
                'Bloque actualizado',
                'El bloque se ha actualizado correctamente'
            );

            $this->modal = false;
        }
    }

    #[Renderless]
    public function export()
    {
        return Block::toCsv();
    }

    public function placeholder()
    {
        return view('components.table.placeholder');
    }

    #[On('refresh-block-table')] 
    public function render()
    {
        return view('livewire.block.index.table', [
            'blocks' => Block::search($this->search)->sort($this->sortCol, $this->sortAsc)->with('category')->trash($this->trash)->paginate($this->perPage),
        ]);
    }
}
