<?php

namespace App\Enums;

/**
 * What happens to the following quotas when one of them changes (an edited value or an extra payment).
 * Each promise chooses it with its `switch_payment` option.
 */
enum RebalanceMode: string
{
    // The following quotas keep their value; quotas are added or removed at the end
    case REDUCE_COUNT = 'reduce_count';

    // The number of quotas stays; the remaining balance is spread over the ordinary quotas
    case REDUCE_AMOUNT = 'reduce_amount';

    public static function fromSwitchPayment(bool $reducesAmount): self
    {
        return $reducesAmount ? self::REDUCE_AMOUNT : self::REDUCE_COUNT;
    }

    public function label(): string
    {
        return match ($this) {
            self::REDUCE_COUNT => 'Reducción del número de cuotas',
            self::REDUCE_AMOUNT => 'Disminución del valor de las cuotas',
        };
    }
}
