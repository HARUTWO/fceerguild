@props([
    'chartId' => 'attendance-bar-chart',
    'labels' => [],
    'datasets' => [],
    'stacked' => true,
    'title' => null,
    'height' => '300px',
])

<div
    id="{{ $chartId }}-container"
    x-data="{ 
        chart: null,
        initChart() {
            const canvas = this.$refs.canvas;
            if (!canvas || typeof Chart === 'undefined') {
                setTimeout(() => this.initChart(), 50);
                return;
            }
            
            // Destroy any existing chart on this canvas
            const existing = Chart.getChart(canvas);
            if (existing) existing.destroy();
            
            const isDark = document.documentElement.classList.contains('dark');
            const textColor = isDark ? 'rgb(148, 163, 184)' : 'rgb(71, 85, 105)';
            const gridColor = isDark ? 'rgba(148, 163, 184, 0.1)' : 'rgba(0, 0, 0, 0.05)';
            
            this.chart = new Chart(canvas.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: {{ Js::from($labels) }},
                    datasets: {{ Js::from($datasets) }}
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
                            backgroundColor: isDark ? 'rgb(30, 41, 59)' : 'rgb(255, 255, 255)',
                            titleColor: isDark ? 'rgb(226, 232, 240)' : 'rgb(15, 23, 42)',
                            bodyColor: isDark ? 'rgb(148, 163, 184)' : 'rgb(71, 85, 105)',
                            borderColor: isDark ? 'rgb(51, 65, 85)' : 'rgb(226, 232, 240)',
                            borderWidth: 1,
                            padding: 12,
                            boxPadding: 6,
                            callbacks: {
                                label: function(context) {
                                    const label = context.dataset.label || '';
                                    const value = context.parsed.y || 0;
                                    const total = context.chart.data.datasets.reduce((sum, ds) => {
                                        return sum + (ds.data[context.dataIndex] || 0);
                                    }, 0);
                                    const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                    return `${label}: ${value.toFixed(2)} (${percentage}%)`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            stacked: {{ $stacked ? 'true' : 'false' }},
                            grid: { display: false },
                            ticks: { color: textColor, font: { size: 11 } }
                        },
                        y: {
                            stacked: {{ $stacked ? 'true' : 'false' }},
                            beginAtZero: true,
                            grid: { color: gridColor },
                            ticks: { 
                                color: textColor, 
                                font: { size: 11 },
                                precision: 0,
                                stepSize: 1
                            }
                        }
                    },
                    interaction: { mode: 'index', intersect: false }
                }
            });
        }
    }"
    x-init="$nextTick(() => initChart())"
    x-on:livewire:navigating.window="if(chart) chart.destroy()"
    class="w-full"
    style="height: {{ $height }};"
    wire:key="bar-{{ $chartId }}"
>
    <canvas x-ref="canvas"></canvas>
</div>
