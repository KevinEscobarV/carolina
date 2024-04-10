<?php

namespace App\Livewire\Dashboard\Index;

use App\Enums\PaymentMethod;
use App\Enums\Status;

enum FilterStatus: string
{
    case All = 'all';
    case Cash = PaymentMethod::CASH->value;
    case Card = PaymentMethod::CREDIT_CARD->value;
    case Check = PaymentMethod::CHECK->value;
    case Transfer = PaymentMethod::BANK_TRANSFER->value;
    case Deposit = PaymentMethod::DEPOSIT->value;
    case Other = PaymentMethod::OTHER->value;

    public function label()
    {
        return match ($this) {
            static::All => 'Todos',
            static::Cash => PaymentMethod::CASH->label(),
            static::Card => PaymentMethod::CREDIT_CARD->label(),
            static::Check => PaymentMethod::CHECK->label(),
            static::Transfer => PaymentMethod::BANK_TRANSFER->label(),
            static::Deposit => PaymentMethod::DEPOSIT->label(),
            static::Other => PaymentMethod::OTHER->label(),
        };
    }
}
