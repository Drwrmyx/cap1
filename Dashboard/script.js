// script.js
document.addEventListener('DOMContentLoaded', function() {
    // 1. Earnings Bar Chart
    const ctxBar = document.getElementById('earningsChart');
    if (ctxBar) {
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: dashboardData.months,
                datasets: [{ 
                    label: 'Revenue', 
                    data: dashboardData.earnings, 
                    backgroundColor: '#fff' 
                }]
            },
            options: { 
                plugins: { legend: { display: false } }, 
                scales: { 
                    y: { 
                        beginAtZero: true, 
                        grid: { color: '#444' } 
                    } 
                } 
            }
        });
    }

    // 2. Payment Methods Pie Chart
    const ctxPie = document.getElementById('paymentPie');
    if (ctxPie) {
        new Chart(ctxPie, {
            type: 'pie',
            data: {
                labels: ['Cash', 'POS', 'Transfer'],
                datasets: [{ 
                    data: dashboardData.paymentData,
                    backgroundColor: ['#2c3e50', '#2980b9', '#1abc9c'] 
                }]
            }
        });
    }
});

// Navigation helper
function changeYear(year) {
    window.location.href = '?year=' + year;
}

async function loadCharts() {
    // Get the year from the URL or default to current
    const urlParams = new URLSearchParams(window.location.search);
    const year = urlParams.get('year') || new Date().getFullYear();

    try {
        // Fetch data from our new PHP endpoint
        const response = await fetch(`get_dashboard_data.php?year=${year}`);
        const data = await response.json();

        // Render Bar Chart
        new Chart(document.getElementById('earningsChart'), {
            type: 'bar',
            data: {
                labels: data.months,
                datasets: [{ label: 'Revenue', data: data.earnings, backgroundColor: '#fff' }]
            }
        });

        // Render Pie Chart
        new Chart(document.getElementById('paymentPie'), {
            type: 'pie',
            data: {
                labels: ['Cash', 'POS', 'Transfer'],
                datasets: [{ 
                    data: data.payments,
                    backgroundColor: ['#2c3e50', '#2980b9', '#1abc9c'] 
                }]
            }
        });
    } catch (error) {
        console.error("Error loading chart data:", error);
    }
}

// Run the function when the page loads
document.addEventListener('DOMContentLoaded', loadCharts);