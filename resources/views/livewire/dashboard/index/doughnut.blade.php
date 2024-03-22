<div>
    <div x-data="chart" wire:ignore wire:loading.class="opacity-50" class="relative w-full p-6">
        <canvas id="doughnut" class="w-full"></canvas>
    </div>
</div>

@script
    <script>
        Alpine.data('chart', Alpine.skipDuringClone(() => {
            let chart

            return {
                init() {
                    chart = this.initChart(this.$wire.dataset)

                    this.$wire.$watch('dataset', () => {
                        this.updateChart(chart, this.$wire.dataset)
                    })
                },

                destroy() {
                    chart.destroy()
                },

                updateChart(chart, dataset) {
                    let {
                        labels,
                        values
                    } = dataset

                    chart.data.labels = labels
                    chart.data.datasets[0].data = values
                    chart.update()
                },

                initChart(dataset) {
                    let el = this.$wire.$el.querySelector('#doughnut')

                    let {
                        labels,
                        values
                    } = dataset

                    return new Chart(el, {
                        type: 'doughnut',
                        data: {
                        labels: labels,
                        datasets: [{
                            label: 'Medios de pago',
                            data: values,
                            hoverOffset: 4
                        }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                        }
                    })
                },
            }
        }))
    </script>
@endscript
