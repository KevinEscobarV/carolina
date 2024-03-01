<?php

namespace App\Livewire\Forms;

use App\Models\Block;
use Livewire\Attributes\Validate;
use Livewire\Form;

class BlockForm extends Form
{
    public ?Block $block;

    #[Validate('required|string|max:255')]
    public $code;

    #[Validate('required|numeric')]
    public $area;

    #[Validate('required|numeric')]
    public $area_m2;

    #[Validate('required|exists:categories,id')]
    public $category_id;

    public function setBlock(Block $block): void
    {
        $this->block = $block;
        $this->fill($block->only([
            'code',
            'area',
            'area_m2',
            'category_id',
        ]));
    }

    public function save(): void
    {
        $this->validate();

        if (Block::where('code', $this->code)->exists()) {
            $this->addError('code', 'Ya existe un bloque con este código');
            return;
        }

        Block::create([
            'code' => $this->code,
            'area' => $this->area,
            'area_m2' => $this->area_m2,
            'category_id' => $this->category_id,
        ]);

        $this->reset();
    }

    public function update(): void
    {
        $this->validate();

        if (Block::where('code', $this->code)->where('id', '!=', $this->block->id)->exists()) {
            $this->addError('code', 'Ya existe un bloque con este código');
            return;
        }

        $this->block->update([
            'code' => $this->code,
            'area' => $this->area,
            'area_m2' => $this->area_m2,
            'category_id' => $this->category_id,
        ]);

        $this->reset();
    }
}
