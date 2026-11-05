<?php

namespace App\Livewire\Traits;

trait SoftDeletes
{
    public $selectedIds = [];

    public $idsOnPage = [];

    public $trash = false;

    public function deleteSelected()
    {
        $this->authorizeDeleting();

        $items = $this->model->whereIn('id', $this->selectedIds)->get();

        foreach ($items as $item) {
            $item->delete();
        }
    }

    public function archive($id)
    {
        $this->dialog()->confirm([
            'icon' => 'warning',
            'title' => '¿Estás seguro?',
            'description' => 'El registro quedara en la papelera',
            'acceptLabel' => 'Si, archivar',
            'rejectLabel' => 'No, cancelar',
            'method' => 'delete',
            'params' => $id,
        ]);
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

    public function revert($id)
    {
        $this->dialog()->confirm([
            'icon' => 'info',
            'title' => '¿Estás seguro?',
            'description' => 'El registro se restaurara',
            'acceptLabel' => 'Si, restaurar',
            'rejectLabel' => 'No, cancelar',
            'method' => 'restore',
            'params' => $id,
        ]);
    }

    public function delete($id)
    {
        $this->authorizeDeleting();

        $item = $this->model->withTrashed()->find($id);

        $item->trashed()
            ? $item->forceDelete()
            : $item->delete();
    }

    public function restore($id)
    {
        $this->authorizeDeleting();

        $item = $this->model->withTrashed()->find($id);

        $item->restore();
    }

    /**
     * Archiving, restoring and deleting require the module's delete permission, e.g. delete.buyers.
     */
    protected function authorizeDeleting(): void
    {
        $this->authorize('delete.'.$this->model->getTable());
    }

    public function onlyTrash()
    {
        $this->trash = ! $this->trash;
        $this->resetPage();
    }
}
