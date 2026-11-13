<?php

namespace App\Livewire\Forms;

use App\Enums\InterestRateType;
use App\Enums\PaymentFrequency;
use App\Enums\PromisePaymentMethod;
use App\Enums\PromiseStatus;
use App\Models\Parcel;
use App\Models\Promise;
use App\Services\Amortization\ScheduleCalculator;
use App\Services\Amortization\ScheduleException;
use App\Services\Amortization\ScheduleTerms;
use Carbon\CarbonImmutable;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Form;
use ValueError;

class PromiseForm extends Form
{
    public ?Promise $model;

    #[Validate('required|array|min:1|exists:buyers,id', 'compradores')]
    public $buyers = [];

    #[Validate('required|array|min:1|exists:parcels,id', 'lotes')]
    public $parcels = [];

    #[Validate('required', 'número o codigo de promesa')]
    public $number;

    #[Validate('required', 'fecha de firma')]
    public $signature_date;

    #[Validate('nullable', 'fecha de firma de escritura')]
    public $signature_deed_date;

    #[Validate('required|numeric|min:0', 'valor de la promesa')]
    public $value = 0;

    #[Validate('nullable|numeric|min:0', 'cuota inicial')]
    public $initial_fee = 0;

    #[Validate('nullable|numeric|min:0', 'valor de la cuota')]
    public $quota_amount = 0;

    #[Validate('required|numeric|min:0|max:100', 'tasa de interés')]
    public $interest_rate = 0;

    public $interest_rate_type = InterestRateType::MONTHLY;

    #[Validate('required', 'frecuencia de pago')]
    public $payment_frequency = PaymentFrequency::MONTHLY;

    #[Validate('nullable|date', 'fecha del primer pago')]
    public $cut_off_date;

    #[Validate('nullable', 'método de pago')]
    public $payment_method = PromisePaymentMethod::CASH;

    #[Validate('nullable', 'estado')]
    public $status = PromiseStatus::CONFIRMED;

    // How extra payments and edited quotas affect the following quotas: false reduces their number, true their value
    #[Validate('boolean', 'abonos')]
    public $switch_payment = false;

    // How the schedule is generated: true by number of quotas, false by quota value
    #[Validate('boolean', 'forma de proyección')]
    public $switch_quota = true;

    #[Validate('required|integer|min:1|max:600', 'número de cuotas')]
    public $number_of_fees = 1;

    public $has_extraordinary = false;

    /**
     * @var list<int>
     */
    public $extraordinary_months = [6, 12];

    public $extraordinary_amount = 0;

    #[Validate('nullable', 'observaciones')]
    public $observations;

    /**
     * A money amount as whole pesos, the way the currency inputs expect it.
     */
    public static function pesos(mixed $amount): int
    {
        return (int) round((float) $amount);
    }

    public function setModel(Promise $model): void
    {
        $this->model = $model;
        $this->fill($model->only([
            'number',
            'signature_date',
            'signature_deed_date',
            'value',
            'initial_fee',
            'quota_amount',
            'interest_rate',
            'interest_rate_type',
            'cut_off_date',
            'payment_frequency',
            'payment_method',
            'status',
            'switch_payment',
            'switch_quota',
            'observations',
            'number_of_fees',
        ]));

        // MariaDB returns decimal columns as text ("68000000.00"); the currency inputs, with a comma for decimals,
        // would read that dot as a thousands separator and show the amount a hundred times bigger
        $this->value = self::pesos($model->value);
        $this->initial_fee = self::pesos($model->initial_fee);
        $this->quota_amount = self::pesos($model->quota_amount);

        $this->has_extraordinary = (int) $model->extraordinary_amount > 0;
        $this->extraordinary_months = $model->extraordinary_months ?: [6, 12];
        $this->extraordinary_amount = (int) $model->extraordinary_amount;
        $this->buyers = $model->buyers->pluck('id')->toArray();
        $this->parcels = $model->parcels->pluck('id')->toArray();
    }

    /**
     * Terms of the schedule from what the form holds now (the promise does not need to exist yet).
     */
    public function scheduleTerms(): ScheduleTerms
    {
        return (new Promise)->forceFill($this->promiseAttributes())->scheduleTerms();
    }

    /**
     * What the current terms would give, for the summary shown while the agreement is filled in.
     * Null while the terms are incomplete or cannot make a schedule.
     *
     * @return list<array<string, mixed>>|null
     */
    public function previewSchedule(): ?array
    {
        if ((float) $this->value <= 0) {
            return null;
        }

        try {
            $calculator = app(ScheduleCalculator::class);
            $terms = $this->scheduleTerms();

            return $this->switch_quota
                ? $calculator->byNumberOfQuotas($terms, max(1, min(ScheduleCalculator::MAX_QUOTAS, (int) $this->number_of_fees)))
                : $calculator->byQuotaAmount($terms, (int) round((float) $this->quota_amount));
        } catch (ScheduleException|ValueError) {
            return null;
        }
    }

    /**
     * Generates the schedule. With $paidRows (the quotas already paid) only the rest is generated again,
     * from the balance they leave and from $firstDueDate on.
     *
     * @param  list<array<string, mixed>>  $paidRows
     * @return list<array<string, mixed>>
     */
    public function generateSchedule(array $paidRows = [], ?string $firstDueDate = null): array
    {
        $this->validate($this->scheduleRules(), [], $this->scheduleAttributes());

        $terms = $this->scheduleTerms();

        if ($paidRows !== []) {
            // The paid quotas keep what they paid to principal; the balance comes from the current value
            $balance = $terms->principal;
            $paidRows = array_map(function (array $row) use (&$balance) {
                $balance -= $row['principal'];

                return [...$row, 'balance' => $balance];
            }, array_values($paidRows));

            $lastPaidDate = CarbonImmutable::parse(end($paidRows)['due_date']);
            $terms = new ScheduleTerms(
                principal: $balance,
                firstDueDate: $firstDueDate ? CarbonImmutable::parse($firstDueDate) : $terms->dueDateAfter($lastPaidDate, 1),
                frequency: $terms->frequency,
                rate: $terms->rate,
                startDate: $lastPaidDate,
                extraordinaryMonths: $terms->extraordinaryMonths,
                extraordinaryAmount: $terms->extraordinaryAmount,
            );
        }

        $calculator = app(ScheduleCalculator::class);
        $generated = $this->switch_quota
            ? $calculator->byNumberOfQuotas($terms, max(1, (int) $this->number_of_fees - count($paidRows)))
            : $calculator->byQuotaAmount($terms, (int) round((float) $this->quota_amount));

        $rows = array_map(
            fn (array $row, int $index) => [...$row, 'number' => $index + 1],
            [...$paidRows, ...$generated],
            array_keys([...$paidRows, ...$generated]),
        );

        $this->number_of_fees = count($rows);
        $ordinary = collect($rows)->slice(count($paidRows))->firstWhere('is_extraordinary', false);
        $this->quota_amount = $ordinary['amount'] ?? $this->quota_amount;

        return $rows;
    }

    /**
     * @param  list<array<string, mixed>>  $schedule
     */
    public function save(array $schedule): Promise
    {
        $this->validate();
        $this->validate($this->scheduleRules(), [], $this->scheduleAttributes());

        $promise = Promise::create($this->promiseAttributes());
        $promise->buyers()->attach($this->buyers);
        $promise->saveSchedule($schedule ?: $this->generateSchedule());

        Parcel::whereIn('id', $this->parcels)->whereNull('promise_id')->update(['promise_id' => $promise->id]);

        $this->reset();

        return $promise;
    }

    /**
     * @param  list<array<string, mixed>>  $schedule
     */
    public function update(array $schedule): bool
    {
        $this->validate();
        $this->validate($this->scheduleRules(), [], $this->scheduleAttributes());

        $this->model->update($this->promiseAttributes());
        $this->model->buyers()->sync($this->buyers);
        $this->model->saveSchedule($schedule ?: $this->generateSchedule());

        Parcel::where('promise_id', $this->model->id)->update(['promise_id' => null]);
        Parcel::whereIn('id', $this->parcels)->whereNull('promise_id')->update(['promise_id' => $this->model->id]);

        return true;
    }

    /**
     * @return array<string, mixed>
     */
    private function promiseAttributes(): array
    {
        return [
            ...$this->only([
                'number',
                'signature_date',
                'signature_deed_date',
                'value',
                'initial_fee',
                'quota_amount',
                'interest_rate',
                'interest_rate_type',
                'cut_off_date',
                'payment_frequency',
                'payment_method',
                'status',
                'switch_payment',
                'switch_quota',
                'number_of_fees',
                'observations',
            ]),
            'interest_rate' => (float) $this->interest_rate,
            'extraordinary_months' => $this->has_extraordinary ? array_values(array_map('intval', $this->extraordinary_months)) : null,
            'extraordinary_amount' => $this->has_extraordinary ? (int) round((float) $this->extraordinary_amount) : null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function scheduleRules(): array
    {
        return [
            'cut_off_date' => ['required', 'date'],
            'payment_frequency' => ['required', Rule::enum(PaymentFrequency::class)],
            'interest_rate_type' => ['required', Rule::enum(InterestRateType::class)],
            'number_of_fees' => [$this->switch_quota ? 'required' : 'nullable', 'integer', 'min:1', 'max:600'],
            'quota_amount' => [$this->switch_quota ? 'nullable' : 'required', 'numeric', $this->switch_quota ? 'min:0' : 'gt:0'],
            'extraordinary_months' => [$this->has_extraordinary ? 'required' : 'nullable', 'array'],
            'extraordinary_months.*' => ['integer', 'between:1,12'],
            'extraordinary_amount' => [$this->has_extraordinary ? 'required' : 'nullable', 'numeric', $this->has_extraordinary ? 'gt:0' : 'min:0'],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function scheduleAttributes(): array
    {
        return [
            'cut_off_date' => 'fecha del primer pago',
            'payment_frequency' => 'periodicidad',
            'interest_rate_type' => 'tipo de tasa',
            'number_of_fees' => 'número de cuotas',
            'quota_amount' => 'valor de la cuota',
            'extraordinary_months' => 'meses de las cuotas extraordinarias',
            'extraordinary_amount' => 'valor de las cuotas extraordinarias',
        ];
    }
}
