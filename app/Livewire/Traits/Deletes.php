<?php

namespace App\Livewire\Traits;

trait Deletes
{
    public $selectedIds = [];

    public $idsOnPage = [];

    public function deleteSelected()
    {
        $this->authorizeDeleting();

        $items = $this->model->whereIn('id', $this->selectedIds)->get();

        foreach ($items as $item) {
            $item->delete();
        }
    }

    public function destroy($id)
    {
        $this->dialog()->confirm([
            'icon' => 'error',
            'title' => '¿Estás seguro?',
            'description' => 'El registro se eliminara permanentemente',
            'acceptLabel' => 'ELIMINAR PERMANENTEMENTE',
            'rejectLabel' => 'No, cancelar',
            'method' => 'delete',
            'params' => $id,
        ]);
    }

    public function delete($id)
    {
        $this->authorizeDeleting();

        $item = $this->model->where('id', $id)->first();
        $item->delete();
    }

    /**
     * Deleting requires the module's delete permission, e.g. delete.roles.
     */
    protected function authorizeDeleting(): void
    {
        $this->authorize('delete.'.$this->model->getTable());
    }
}
