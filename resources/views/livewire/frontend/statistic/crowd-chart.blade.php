<div x-data="chartComponent()" x-init="initChart()">
    <!-- Chart -->
    <div class="max-w-6xl mx-auto bg-white shadow-2xl rounded-2xl p-6">
        <h3 id="chartTitle" class="text-xl font-semibold text-gray-700 mb-6">
            Besuchertrend {{ \Carbon\Carbon::create($year, $month)->translatedFormat('F Y') }}
        </h3>

        <div id="chartMessage" class="text-center text-gray-500 hidden">
            Keine Besucherandrang-Daten für diesen Monat verfügbar.
        </div>
        <div id="chartContainer">
            <canvas id="crowdChart"></canvas>
        </div>
    </div>

    <style>
        #chartContainer {
            position: relative;
            width: 100%;
            min-height: 320px;
        }

        canvas {
            width: 100% !important;
            height: auto !important;
            max-width: 100%;
        }

        @media (max-width: 640px) {
            canvas {
                height: 240px !important;
            }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function chartComponent() {
            return {
                chartInstance: null,
                labels: @json($chartLabels),
                data: @json($chartData),
                month: @json($month),
                year: @json($year),

                initChart() {
                    this.renderChart();

                    window.addEventListener('updateChartMonth', (event) => {
                        this.month = event.detail[0];
                        this.year = event.detail[1];

                        this.$wire.call('updateChartMonth', this.month, this.year).then(() => {
                            this.labels = JSON.parse(this.$el.querySelector('[data-labels]').dataset.labels);
                            this.data = JSON.parse(this.$el.querySelector('[data-data]').dataset.data);
                            this.renderChart();
                        });
                    });
                },

                renderChart() {
                    const chartMessage = document.getElementById('chartMessage');
                    const chartContainer = document.getElementById('chartContainer');
                    const hasData = this.data && this.data.length > 0 && this.data.some(val => val !== 0);

                    if (!hasData) {
                        chartMessage.classList.remove('hidden');
                        chartContainer.classList.add('hidden');
                        if (this.chartInstance) {
                            this.chartInstance.destroy();
                            this.chartInstance = null;
                        }
                        return;
                    }

                    chartMessage.classList.add('hidden');
                    chartContainer.classList.remove('hidden');

                    const canvas = document.getElementById('crowdChart');
                    const ctx = canvas.getContext('2d');

                    if (this.chartInstance) {
                        this.chartInstance.destroy();
                    }

                    this.chartInstance = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: this.labels,
                            datasets: [{
                                label: 'Besucherandrang (%)',
                                data: this.data,
                                backgroundColor: 'rgba(250, 204, 21, 0.7)',
                                borderColor: 'rgb(234, 179, 8)',
                                borderWidth: 2,
                                borderRadius: 5,
                                barThickness: 'flex',
                                maxBarThickness: 20
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    max: 100,
                                    ticks: {
                                        callback: value => value + '%'
                                    },
                                    grid: {
                                        color: 'rgba(0, 0, 0, 0.05)'
                                    }
                                },
                                x: {
                                    grid: { display: false }
                                }
                            },
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: 'rgba(17, 24, 39, 0.9)',
                                    padding: 10,
                                    cornerRadius: 8,
                                    callbacks: {
                                        label: ctx => ctx.parsed.y + '%'
                                    }
                                }
                            },
                            animation: {
                                duration: 1000,
                                easing: 'easeOutQuart'
                            }
                        }
                    });

                    const chartTitle = document.getElementById('chartTitle');
                    if (chartTitle) {
                        const monthYear = new Date(this.year, this.month - 1).toLocaleString('de-DE', {
                            month: 'long',
                            year: 'numeric'
                        });
                        chartTitle.textContent = `Besuchertrend ${monthYear}`;
                    }
                }
            }
        }
    </script>

    <!-- Versteckte Livewire-Daten -->
    <div hidden data-labels="@json($chartLabels)" data-data="@json($chartData)"></div>
</div>
