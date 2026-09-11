// Konfigurasi Dasar Chart
const chartOptions = { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } };

const chartPersentase = new Chart(document.getElementById('chartPersentase'), {
    type: 'doughnut',
    data: { labels: ['Hadir', 'Tidak Hadir'], datasets: [{ data: [0, 0], backgroundColor: ['#28a745', '#dc3545'] }] },
    options: chartOptions
});

const chartTidakHadir = new Chart(document.getElementById('chartTidakHadir'), {
    type: 'bar',
    data: { labels: ['Hari Ini'], datasets: [] },
    options: chartOptions
});

function updateDashboard() {
    // Update Stats
    fetch('/beranda/stats')
        .then(res => res.json())
        .then(data => {
            document.querySelectorAll('[data-stat]').forEach(el => {
                const key = el.getAttribute('data-stat');
                if(data[key] !== undefined) el.textContent = data[key];
            });
        });

    // Update Charts
    fetch('/beranda/chart-data')
        .then(res => res.json())
        .then(data => {
            chartPersentase.data.datasets[0].data = [data.totalHadir || 0, data.totalTidakHadir || 0];
            chartPersentase.update();
            chartTidakHadir.data.labels = data.labels || ['Hari Ini'];
            chartTidakHadir.data.datasets = data.datasets || [];
            chartTidakHadir.update();
        });
}

// Jalankan
updateDashboard();
setInterval(updateDashboard, 10000);
