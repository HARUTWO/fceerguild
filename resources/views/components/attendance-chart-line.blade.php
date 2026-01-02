@props([
    'chartId' => 'attendance-line-chart',
    'labels' => [],
    'data' => [],
    'datasets' => [], // Support datasets array directly
    'label' => 'Value',
    'borderColor' => 'rgb(99, 102, 241)',
    'backgroundColor' => 'rgba(99, 102, 241, 0.1)',
    'fill' => true,
    'tension' => 0.3,
    'title' => null,
    'height' => '300px',
])

@php
    // If datasets is provided, use it; otherwise build from data
    $chartDatasets = !empty($datasets) ? $datasets : [[
        'label' => $label,
        'data' => $data,
        'borderColor' => $borderColor,
        'backgroundColor' => $backgroundColor,
        'fill' => $fill,
        'tension' => $tension,
        'pointRadius' => 4,
        'pointHoverRadius' => 6,
        'pointBackgroundColor' => $borderColor,
    ]];
@endphp

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
                type: 'line',
                data: {
                    labels: {{ Js::from($labels) }},
                    datasets: {{ Js::from($chartDatasets) }}
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                color: textColor,
                                usePointStyle: true,
                                pointStyle: 'circle',
                                padding: 15,
                                font: { size: 11, weight: '500' }
                            }
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
                            boxPadding: 6
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { color: textColor, font: { size: 11 } }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: gridColor },
                            ticks: { color: textColor, font: { size: 11 } }
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
    wire:key="line-{{ $chartId }}"
>
    <canvas x-ref="canvas"></canvas>
</div>
