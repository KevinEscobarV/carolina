<?php

namespace App\Livewire\Forms;

use App\Enums\CivilStatus;
use App\Enums\DocumentType;
use App\Models\Buyer;
use Illuminate\Validation\Rule;
use Livewire\Form;

class BuyerForm extends Form
{
    public ?Buyer $buyer;

    public $names;
    public $surnames;
    public $email;
    public $document_type = DocumentType::CC;
    public $document_number;
    public $civil_status = CivilStatus::SINGLE;
    public $phone_one;
    public $phone_two;
    public $address;

    public function rules()
    {
        return [
            'names' => 'required|string|max:255',
            'surnames' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:buyers,email',
            'document_type' => [
                'required',
                Rule::enum(DocumentType::class)
            ],
            'document_number' => 'required|string|max:255|unique:buyers,document_number',
            'civil_status' => [
                'required',
                Rule::enum(CivilStatus::class)
            ],
            'phone_one' => 'required|string|max:255',
            'phone_two' => 'nullable|string|max:255',
            'address' => 'required|string|max:255',
        ];
    }

    public function editRules()
    {
        return [
            'names' => 'required|string|max:255',
            'surnames' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:buyers,email,' . $this->buyer->id,
            'document_type' => [
                'required',
                Rule::enum(DocumentType::class)
            ],
            'document_number' => 'required|string|max:255|unique:buyers,document_number,' . $this->buyer->id,
            'civil_status' => [
                'required',
                Rule::enum(CivilStatus::class)
            ],
            'phone_one' => 'required|string|max:255',
            'phone_two' => 'nullable|string|max:255',
            'address' => 'required|string|max:255',
        ];
    }

    public function validationAttributes() 
    {
        return [
            'names' => 'nombres',
            'surnames' => 'apellidos',
            'email' => 'correo electrónico',
            'document_type' => 'tipo de documento',
            'document_number' => 'número de documento',
            'civil_status' => 'estado civil',
            'phone_one' => 'teléfono principal',
            'phone_two' => 'teléfono alternativo',
        ];
    }

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

    public function save()
    {
        $this->validate($this->rules());

        Buyer::create($this->all());

        $this->reset();

        return true;
    }

    public function update()
    {
        $this->validate($this->editRules());

        $this->buyer->update($this->all());

        return true;
    }
}
