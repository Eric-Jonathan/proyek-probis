$(document).ready(function() {
    // Setup Chart.js
    const ctx = document.getElementById('dashboardChart').getContext('2d');
    
    // Gradient Fills for Chart
    const blueGradient = ctx.createLinearGradient(0, 0, 0, 300);
    blueGradient.addColorStop(0, 'rgba(0, 100, 210, 0.4)');
    blueGradient.addColorStop(1, 'rgba(0, 100, 210, 0.0)');

    const greenGradient = ctx.createLinearGradient(0, 0, 0, 300);
    greenGradient.addColorStop(0, 'rgba(22, 101, 52, 0.3)');
    greenGradient.addColorStop(1, 'rgba(22, 101, 52, 0.0)');

    // Initialize Chart instance
    let dashboardChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [
                {
                    label: 'Pengajuan Baru',
                    data: [],
                    borderColor: '#0064D2',
                    backgroundColor: blueGradient,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#0064D2',
                    pointHoverRadius: 7
                },
                {
                    label: 'Survei Selesai',
                    data: [],
                    borderColor: '#166534',
                    backgroundColor: greenGradient,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#166534',
                    pointHoverRadius: 7
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        boxWidth: 12,
                        font: {
                            family: 'Outfit, Inter',
                            size: 12
                        }
                    }
                },
                tooltip: {
                    padding: 10,
                    bodyFont: { family: 'Outfit, Inter' },
                    titleFont: { family: 'Outfit, Inter', weight: 'bold' }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.04)'
                    },
                    ticks: {
                        font: { family: 'Outfit, Inter', size: 10 }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: { family: 'Outfit, Inter', size: 10 }
                    }
                }
            }
        }
    });

    // Toggle visibility of pickers based on selected period
    $('#chartPeriod').on('change', function() {
        const period = $(this).val();
        
        // Hide all pickers first
        $('#pickerDate').addClass('d-none');
        $('#pickerWeek').addClass('d-none');
        $('#pickerMonthWrapper').addClass('d-none');
        $('#pickerYear').addClass('d-none');

        // Show selected picker
        if (period === 'day') {
            $('#pickerDate').removeClass('d-none');
        } else if (period === 'week') {
            $('#pickerWeek').removeClass('d-none');
        } else if (period === 'month') {
            $('#pickerMonthWrapper').removeClass('d-none');
        } else if (period === 'year') {
            $('#pickerYear').removeClass('d-none');
        }

        updateChartData();
    });

    // Setup listeners on all pickers
    $('#pickerDate, #pickerWeek, #pickerMonth, #pickerMonthYear, #pickerYear').on('change', function() {
        updateChartData();
    });

    // Function to generate and update chart datasets using AJAX
    function updateChartData() {
        const period = $('#chartPeriod').val();
        const dateVal = $('#pickerDate').val();
        const weekVal = $('#pickerWeek').val();
        const monthVal = $('#pickerMonth').val();
        
        // Use yearVal from pickerMonthYear if month period, else pickerYear
        let yearVal = '';
        if (period === 'month') {
            yearVal = $('#pickerMonthYear').val();
        } else if (period === 'year') {
            yearVal = $('#pickerYear').val();
        } else {
            yearVal = $('#pickerMonthYear').val() || $('#pickerYear').val();
        }

        $.ajax({
            url: '/admin/dashboard/chart-data',
            method: 'GET',
            data: {
                period: period,
                date: dateVal,
                week: weekVal,
                month: monthVal,
                year: yearVal
            },
            dataType: 'json',
            success: function(response) {
                // Update Chart JS instance
                dashboardChart.data.labels = response.labels;
                dashboardChart.data.datasets[0].data = response.newProposals;
                dashboardChart.data.datasets[1].data = response.completedSurveys;
                dashboardChart.update();
            },
            error: function(xhr, status, error) {
                console.error('Error fetching chart data:', error);
            }
        });
    }

    // Initial chart render
    updateChartData();
});
