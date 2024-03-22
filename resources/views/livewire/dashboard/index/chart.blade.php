<div>
    <div x-data="chart" wire:ignore wire:loading.class="opacity-50"
        class="relative h-[5rem] sm:h-[13rem] w-full">
        <canvas id="area" class="w-full"></canvas>
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
                    let el = this.$wire.$el.querySelector('#area')

                    let {
                        labels,
                        values
                    } = dataset

                    return new Chart(el, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                tension: 0.3,
                                label: 'Total',
                                data: values,
                                fill: {
                                    target: 'origin',
                                    above: 'rgba(255, 124, 0, 0.1)',
                                },
                                borderColor: '#F59E0B',
                                pointStyle: 'circle',
                                pointRadius: 0,
                                pointBackgroundColor: '#5ba5e1',
                                pointBorderColor: '#5ba5e1',
                                pointHoverRadius: 4,
                                borderWidth: 2,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    mode: 'index',
                                    intersect: false,
                                    displayColors: false,
                                },
                            },
                            hover: {
                                mode: 'index',
                                intersect: false
                            },
                            layout: {
                                padding: {
                                    left: 0,
                                    right: 0,
                                },
                            },
                            scales: {
                                x: {
                                    display: false,
                                    border: {
                                        dash: [5, 5]
                                    },
                                    ticks: {
                                        callback: function(val, index, values) {
                                            let label = this.getLabelForValue(val)

                                            return index === 0 || index === values.length - 1 ? '' :
                                                label;
                                        }
                                    },
                                    grid: {
                                        border: {
                                            display: false
                                        },
                                    },
                                },
                                y: {
                                    display: false,
                                    border: {
                                        display: false
                                    },
                                    beginAtZero: true,
                                    grid: {
                                        display: false
                                    },
                                    ticks: {
                                        display: false
                                    },
                                },
                            },
                        },
                    })
                },
            }
        }))
    </script>
@endscript
