<?php

namespace App\Livewire\Forms;

use App\Enums\CivilStatus;
use App\Enums\DocumentType;
use App\Models\Buyer;
use Livewire\Attributes\Validate;
use Livewire\Form;

class BuyerForm extends Form
{
    public ?Buyer $buyer;

    #[Validate('required|string|max:255', 'nombres')]
    public $names;

    #[Validate('required|string|max:255', 'apellidos')]
    public $surnames;

    #[Validate('required|email|max:255|unique:buyers,email', 'correo electrónico')]
    public $email;

    #[Validate('required', 'tipo de documento')]
    public DocumentType $document_type = DocumentType::CC;

    #[Validate('required|string|max:255|unique:buyers,document_number', 'número de documento')]
    public $document_number;

    #[Validate('required', 'estado civil')]
    public CivilStatus $civil_status = CivilStatus::SINGLE;

    #[Validate('required|string|max:255', 'teléfono principal')]
    public $phone_one;

    #[Validate('nullable|string|max:255', 'teléfono alternativo')]
    public $phone_two;

    #[Validate('required|string|max:255', 'direccion')]
    public $address;

    public function setBuyer(Buyer $buyer): void
    {
        $this->buyer = $buyer;
        $this->fill($buyer->only([
            'names',
            'surnames',
            'email',
            'document_type',
            'document_number',
            'civil_status',
            'phone_one',
            'phone_two',
            'address',
        ]));
    }

    public function save(): void
    {
        $this->validate();

        Buyer::create($this->all());

        $this->reset();
    }

    public function update(): void
    {
        $this->validate([
            'names' => 'required|string|max:255',
            'surnames' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:buyers,email,' . $this->buyer->id,
            'document_type' => 'required',
            'document_number' => 'required|string|max:255|unique:buyers,document_number,' . $this->buyer->id,
            'civil_status' => 'required',
            'phone_one' => 'required|string|max:255',
            'phone_two' => 'nullable|string|max:255',
        ],
        $attributes = [
            'names' => 'nombres',
            'surnames' => 'apellidos',
            'email' => 'correo electrónico',
            'document_type' => 'tipo de documento',
            'document_number' => 'número de documento',
            'civil_status' => 'estado civil',
            'phone_one' => 'teléfono principal',
            'phone_two' => 'teléfono alternativo',
        ]);

        $this->buyer->update($this->all());

        $this->reset();
    }
}
