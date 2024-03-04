<?php

namespace App\Livewire\Payment\Index;

use App\Models\Payment;
use Livewire\Attributes\On;
use Livewire\Attributes\Renderless;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Table extends Component
{
    use WithPagination;

    #[Url]
    public $search = '';

    #[Url]
    public $sortCol = 'payment_date';

    #[Url]
    public $sortAsc = false;

    public $selectedPaymentIds = [];

    public $paymentIdsOnPage = [];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function sortBy($column)
    {
        if ($this->sortCol === $column) {
            $this->sortAsc = ! $this->sortAsc;
        } else {
            $this->sortCol = $column;
            $this->sortAsc = false;
        }
    }

    #[Renderless]
    public function export()
    {
        return Payment::toCsv();
    }

    public function deleteSelected()
    {
        $payments = Payment::whereIn('id', $this->selectedPaymentIds)->get();

        foreach ($payments as $payment) {
            $this->archive($payment);
        }
    }

    public function archive(Payment $payment)
    {
        $payment->delete();
    }

    public function placeholder()
    {
        return view('components.table.placeholder');
    }

    #[On('refresh-payment-table')] 
    public function render()
    {
        return view('livewire.payment.index.table', [
            'payments' => Payment::search($this->search)->sort($this->sortCol, $this->sortAsc)->with('promise.buyers')->paginate(10),
        ]);
    }
}
