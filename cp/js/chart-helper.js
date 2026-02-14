/**
 * Chart.js Helper Functions for TradeX
 * Replaces Silverlight Visifire Charts with HTML5 Chart.js
 */

class TradeXChart {
    constructor(containerId, width, height) {
        this.containerId = containerId;
        this.width = width;
        this.height = height;
        this.dataUri = null;
        this.chart = null;
        this.canvas = null;
    }

    setDataUri(uri) {
        this.dataUri = uri;
    }

    async render(targetId) {
        const container = document.getElementById(targetId);
        if (!container) {
            console.error('Container not found:', targetId);
            return;
        }

        // Clear container
        container.innerHTML = '';

        // Create canvas element
        this.canvas = document.createElement('canvas');
        this.canvas.id = this.containerId;
        this.canvas.width = this.width;
        this.canvas.height = this.height;
        this.canvas.style.visibility = 'hidden';
        container.appendChild(this.canvas);

        // Fetch data
        try {
            const response = await fetch(this.dataUri);
            const data = await response.json();

            // Destroy existing chart if any
            if (this.chart) {
                this.chart.destroy();
            }

            // Determine chart type from datasets
            const chartType = data.datasets[0].borderWidth > 2 ? 'line' : 'bar';

            // Create chart
            const ctx = this.canvas.getContext('2d');
            this.chart = new Chart(ctx, {
                type: chartType,
                data: {
                    labels: data.labels,
                    datasets: data.datasets
                },
                options: {
                    responsive: false,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: data.title,
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        },
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: '#666',
                            borderWidth: 1,
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        // Format as percentage for line charts
                                        if (chartType === 'line') {
                                            label += context.parsed.y.toFixed(2) + '%';
                                        } else {
                                            label += context.parsed.y;
                                        }
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                maxRotation: chartType === 'line' ? 45 : 90,
                                minRotation: chartType === 'line' ? 45 : 90
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#ececec',
                                drawBorder: false
                            },
                            ticks: {
                                callback: function(value) {
                                    if (chartType === 'line') {
                                        return value + '%';
                                    }
                                    return value;
                                }
                            }
                        }
                    }
                }
            });

            // Show chart after rendering
            this.canvas.style.visibility = 'visible';

        } catch (error) {
            console.error('Error loading chart data:', error);
            container.innerHTML = '<div style="color: red; padding: 20px;">Error loading chart data</div>';
        }
    }
}
