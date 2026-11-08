<?php

namespace App\Exports\Promise;

use App\Services\Reports\PortfolioStatement;
use Carbon\CarbonImmutable;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/**
 * Portfolio of the current campaign on a cut-off date: the detail per promise and a summary per status.
 */
class WalletStatus implements WithMultipleSheets
{
    use Exportable;

    public function __construct(
        public CarbonImmutable $cutOff,
        public bool $onlyLate = false,
        public string $campaign = '',
    ) {}

    /**
     * @return list<object>
     */
    public function sheets(): array
    {
        $statement = new PortfolioStatement($this->cutOff, $this->onlyLate);

        return [
            new WalletStatusDetail($statement, $this->campaign),
            new WalletStatusSummary($statement, $this->campaign),
        ];
    }
}
