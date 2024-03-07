<?php

namespace App\Livewire\Forms;

use App\Models\Category;
use Livewire\Attributes\Validate;
use Livewire\Form;

class CategoryForm extends Form
{
    public ?Category $category;

    #[Validate('required|string|max:255')]
    public string $name = '';

    public function setCategory(Category $category)
    {
        $this->category = $category;
        $this->name = $category->name;
    }

    public function save()
    {
        $this->validate();

        if (Category::where('name', $this->name)->exists()) {
            $this->addError('name', 'Ya existe una categoría con este nombre');
            return;
        }

        Category::create([
            'name' => $this->name,
        ]);

        $this->reset();

        return true;
    }

    public function update()
    {
        $this->validate();

        if (Category::where('name', $this->name)->where('id', '!=', $this->category->id)->exists()) {
            $this->addError('name', 'Ya existe una categoría con este nombre');
            return;
        }

        $this->category->update([
            'name' => $this->name,
        ]);

        $this->reset();

        return true;
    }
}
