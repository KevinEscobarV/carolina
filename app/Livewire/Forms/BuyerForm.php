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

    #[Validate('required|string|max:255')]
    public $names;

    #[Validate('required|string|max:255')]
    public $surnames;

    #[Validate('required|email|max:255|unique:buyers,email')]
    public $email;

    #[Validate('required')]
    public DocumentType $document_type = DocumentType::CC;

    #[Validate('required|string|max:255|unique:buyers,document_number')]
    public $document_number;

    #[Validate('required')]
    public CivilStatus $civil_status = CivilStatus::SINGLE;

    #[Validate('required|string|max:255')]
    public $phone_one;

    #[Validate('nullable|string|max:255')]
    public $phone_two;

    #[Validate('required|string|max:255')]
    public $address;

    public function setBuyer(Buyer $buyer): void
    {
        $this->buyer = $buyer;
        $this->names = $buyer->names;
        $this->surnames = $buyer->surnames;
        $this->email = $buyer->email;
        $this->document_type = $buyer->document_type;
        $this->document_number = $buyer->document_number;
        $this->civil_status = $buyer->civil_status;
        $this->phone_one = $buyer->phone_one;
        $this->phone_two = $buyer->phone_two;
        $this->address = $buyer->address;
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
        ]);

        $this->buyer->update($this->all());

        $this->reset();
    }
}
