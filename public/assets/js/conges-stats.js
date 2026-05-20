// Congés Statistics Chart
document.addEventListener('DOMContentLoaded', function() {
    // Use real data from backend (passed via PHP)
    const congesData = window.congesData || {
        monthly: {
            labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'],
            data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
        },
        daily: {
            labels: ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'],
            data: [0, 0, 0, 0, 0, 0, 0]
        }
    };

    let currentChart = null;
    let currentView = 'monthly';

    function buildDataset(view, data, ctx) {
        const gradient = ctx.createLinearGradient(0, 0, 0, 380);
        gradient.addColorStop(0, 'rgba(61, 122, 82, 0.85)');
        gradient.addColorStop(1, 'rgba(61, 122, 82, 0.25)');

        return {
            label: view === 'monthly' ? 'Congés approuvés par mois' : 'Congés approuvés par jour',
            data: data.data,
            backgroundColor: gradient,
            borderColor: '#2d5a3d',
            borderWidth: 1.4,
            borderRadius: 8,
            borderSkipped: false,
            hoverBackgroundColor: 'rgba(95, 168, 118, 0.85)',
            maxBarThickness: 44
        };
    }

    // Initialize chart
    function initChart(view) {
        const ctx = document.getElementById('congesChart').getContext('2d');
        const data = view === 'monthly' ? congesData.monthly : congesData.daily;
        
        if (currentChart) {
            currentChart.destroy();
        }

        currentChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [buildDataset(view, data, ctx)]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 650,
                    easing: 'easeOutQuart'
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            color: '#1c2b1e',
                            boxWidth: 12,
                            boxHeight: 12,
                            useBorderRadius: true,
                            borderRadius: 3,
                            font: {
                                family: 'DM Sans',
                                size: 12,
                                weight: '600'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(28, 43, 30, 0.92)',
                        borderColor: 'rgba(95, 168, 118, 0.6)',
                        borderWidth: 1,
                        padding: 10,
                        titleFont: {
                            family: 'DM Sans',
                            weight: '700'
                        },
                        bodyFont: {
                            family: 'DM Sans'
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            color: '#5f6f63',
                            font: {
                                family: 'DM Sans',
                                size: 11
                            }
                        },
                        grid: {
                            display: false
                        },
                        border: {
                            color: 'rgba(221, 232, 225, 0.9)'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            color: '#5f6f63',
                            font: {
                                family: 'DM Mono',
                                size: 11
                            }
                        },
                        grid: {
                            color: 'rgba(221, 232, 225, 0.7)',
                            drawBorder: false
                        },
                        border: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    // Toggle between monthly and daily views
    function toggleView(view) {
        currentView = view;
        
        // Update button states
        document.querySelectorAll('.chart-toggle-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        document.querySelector(`[data-view="${view}"]`).classList.add('active');
        
        // Reinitialize chart with new data
        initChart(view);
    }

    // Initialize on load
    if (document.getElementById('congesChart')) {
        initChart('monthly');
        
        // Add event listeners to toggle buttons
        document.querySelectorAll('.chart-toggle-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const view = this.getAttribute('data-view');
                toggleView(view);
            });
        });
    }
});
