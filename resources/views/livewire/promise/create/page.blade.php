<x-slot:header>
    <h2 class="text-3xl text-gray-800 dark:text-gray-200 leading-tight">
        Crear Promesa
    </h2>
</x-slot>

<div class="mx-auto sm:px-6 lg:px-8 flex flex-col gap-8" x-data="quota">
    <x-card>
        <form wire:submit.prevent="save">
            <x-promise.form :$block :projection="$form->projection" :$isCreate :interest="$form->interest_rate" />
            <div class="flex items-center justify-end gap-2 mt-6">
                <x-wireui-button lg rose label="Volver" href="{{ route('promises') }}" icon="rewind" />
                <x-wireui-button lg type="submit" spinner="save" lime label="Crear Promesa" icon="save-as" />
            </div>
        </form>
    </x-card>
</div>

@script
    <script>
        Alpine.data('quota', () => {
            return {
                init() { 
                    this.$wire.$watch('form.number_of_fees', () => {
                        if (this.$wire.switch_quota == 1) {
                            this.$wire.form.quota_amount = this.calculateQuota(this.$wire.form.value, this.$wire.form.number_of_fees, this.$wire.form.interest_rate, this.$wire.form.initial_fee, this.$wire.form.payment_frequency)
                        }
                    })
                    
                    this.$wire.$watch('form.quota_amount', () => {
                        if (this.$wire.switch_quota == 0) {
                            this.$wire.form.number_of_fees = this.calculateNumberOfFees(this.$wire.form.value, this.$wire.form.quota_amount, this.$wire.form.interest_rate, this.$wire.form.initial_fee, this.$wire.form.payment_frequency)
                        }
                    })

                    this.$wire.$watch('form.initial_fee', () => {
                        this.$wire.form.quota_amount = this.calculateQuota(this.$wire.form.value, this.$wire.form.number_of_fees, this.$wire.form.interest_rate, this.$wire.form.initial_fee, this.$wire.form.payment_frequency)
                    })

                    this.$wire.$watch('form.interest_rate', () => {
                        this.$wire.form.quota_amount = this.calculateQuota(this.$wire.form.value, this.$wire.form.number_of_fees, this.$wire.form.interest_rate, this.$wire.form.initial_fee, this.$wire.form.payment_frequency)
                    })

                    this.$wire.$watch('form.payment_frequency', () => {
                        this.$wire.form.quota_amount = this.calculateQuota(this.$wire.form.value, this.$wire.form.number_of_fees, this.$wire.form.interest_rate, this.$wire.form.initial_fee, this.$wire.form.payment_frequency)
                    })
                },

                calculateNumberOfFees(promise, quota, interest, initial, frequency) {
                    if (promise && quota) {
                        promise = promise - initial;
                        if (interest > 0) {
                            tasaInteres = interest / 100 / this.multiplier(frequency);
                            n = log(quota / (quota - promise * tasaInteres)) / log(1 + tasaInteres);
                            n = Math.ceil(n);
                            return n;
                        } else {
                            return Math.ceil(promise / quota);
                        }
                    } else {
                        return 1;
                    }
                },

                calculateQuota(promise, fees, interest, initial, frequency) {
                    if (promise && fees) {
                        promise = promise - initial;
                        if (interest > 0) {
                            tasaInteres = interest / 100 / this.multiplier(frequency);
                            quota = promise * tasaInteres / (1 - Math.pow(1 + tasaInteres, -fees));
                            return quota;
                        } else {
                            quota = promise / fees;
                            return quota;
                        }
                    } else {
                        return 0;
                    }
                },

                multiplier(frequency) {
                    switch (frequency) {
                        case 'weekly':
                            return 52;
                        case 'biweekly':
                            return 26;
                        case 'monthly':
                            return 12;
                        case 'bimonthly':
                            return 6;
                        case 'quarterly':
                            return 4;
                        case 'semi_annual':
                            return 2;
                        case 'annual':
                            return 1;
                        case 'irregular':
                            return 1;
                        default:
                            return 1;
                    }
                },
            }
        })
    </script>
@endscript
