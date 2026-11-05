<?php

namespace App\Livewire\Dashboard\Index;

use App\Enums\PaymentMethod;

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
            self::All => 'Todos',
            self::Cash => PaymentMethod::CASH->label(),
            self::Card => PaymentMethod::CREDIT_CARD->label(),
            self::Check => PaymentMethod::CHECK->label(),
            self::Transfer => PaymentMethod::BANK_TRANSFER->label(),
            self::Deposit => PaymentMethod::DEPOSIT->label(),
            self::Other => PaymentMethod::OTHER->label(),
        };
    }
}
