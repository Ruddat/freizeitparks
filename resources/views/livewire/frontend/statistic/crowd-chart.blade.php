<div x-data="chartComponent()" x-init="initChart()">
    <!-- Chart -->
    <div class="max-w-6xl mx-auto bg-white shadow-2xl rounded-2xl p-6">
        <h3 id="chartTitle" class="text-xl font-semibold text-gray-700 mb-6">
            Besuchertrend {{ \Carbon\Carbon::create($year, $month)->translatedFormat('F Y') }}
        </h3>
        {{-- Debugging
        <div>
            <pre>chartLabels: <span x-text="JSON.stringify(labels)"></span></pre>
            <pre>chartData: <span x-text="JSON.stringify(data)"></span></pre>
            <pre>array_filter(chartData): <span x-text="JSON.stringify(Object.fromEntries(Object.entries(data).filter(([_, v]) => v != 0)))"></span></pre>
        </div>
        --}}

        <div id="chartMessage" class="text-center text-gray-500 hidden">
            Keine Besucherandrang-Daten für diesen Monat verfügbar.
        </div>
        <div id="chartContainer" class="overflow-x-auto">
            <canvas id="crowdChart" class="w-full" height="100"></canvas>
        </div>
    </div>

    <style>
        canvas {
           height: 320px !important;
            min-width: 100%;
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

                    // Lausche auf das updateChartMonth-Event
                    window.addEventListener('updateChartMonth', (event) => {
                        console.log('updateChartMonth Event empfangen in Alpine.js!', event.detail);
                        this.month = event.detail[0];
                        this.year = event.detail[1];

                        // Aktualisiere die Daten direkt von Livewire
                        this.$wire.call('updateChartMonth', this.month, this.year).then(() => {
                            // Hole die aktualisierten Daten aus den Livewire-Attributen
                            this.labels = JSON.parse(this.$el.querySelector('[data-labels]').dataset.labels);
                            this.data = JSON.parse(this.$el.querySelector('[data-data]').dataset.data);
                            this.renderChart();
                        });
                    });
                },

                renderChart() {
                    //console.log('renderChart wird ausgeführt!');

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
                    if (!canvas) {
                        console.error('Canvas nicht gefunden!');
                        return;
                    }

                    const ctx = canvas.getContext('2d');
                    if (!ctx) {
                        console.error('Canvas-Kontext nicht verfügbar!');
                        return;
                    }

                    //console.log('Canvas gefunden:', ctx);

                    if (this.chartInstance) {
                        this.chartInstance.destroy();
                        console.log('Vorheriger Chart zerstört.');
                    }

                    try {
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
                                    barThickness: 20,
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        max: 100,
                                        ticks: { callback: value => value + '%' },
                                        grid: { color: 'rgba(0, 0, 0, 0.05)' }
                                    },
                                    x: { grid: { display: false } }
                                },
                                plugins: {
                                    legend: { display: false },
                                    tooltip: {
                                        backgroundColor: 'rgba(17, 24, 39, 0.9)',
                                        padding: 10,
                                        cornerRadius: 8,
                                        callbacks: { label: ctx => ctx.parsed.y + '%' }
                                    }
                                },
                                animation: {
                                    duration: 1000,
                                    easing: 'easeOutQuart'
                                }
                            }
                        });
                        //console.log('Chart erfolgreich initialisiert:', this.chartInstance);
                    } catch (error) {
                        //console.error('Fehler beim Initialisieren des Charts:', error);
                    }

                    const chartTitle = document.getElementById('chartTitle');
                    if (chartTitle) {
                        const monthYear = new Date(this.year, this.month - 1).toLocaleString('de-DE', { month: 'long', year: 'numeric' });
                        chartTitle.textContent = `Besuchertrend ${monthYear}`;
                    }
                }
            }
        }
    </script>

    <!-- Versteckte Elemente für die Daten -->
    <div hidden data-labels="@json($chartLabels)" data-data="@json($chartData)"></div>
</div>
