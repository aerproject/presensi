'use strict';

document.addEventListener('DOMContentLoaded', async function () {
    const dashboard = document.getElementById('adminDashboard');

    if (!dashboard) {
        return;
    }

    const endpoints = {
        stats: dashboard.dataset.statsUrl,
        jurusan: dashboard.dataset.jurusanUrl,
        kelas: dashboard.dataset.kelasUrl,
        izin: dashboard.dataset.izinUrl,
    };

    async function fetchData(url) {
        const response = await fetch(url);

        if (!response.ok) {
            throw new Error('Server error: ' + response.status);
        }

        return await response.json();
    }

    /*
     * 1. Chart Kehadiran
     */
    try {
        const stats = await fetchData(endpoints.stats);

        new Chart(document.getElementById('attendanceChart'), {
            type: 'doughnut',
            data: {
                labels: ['Hadir', 'Izin', 'Sakit', 'Alpha'],
                datasets: [{
                    data: [
                        stats.hadir || 0,
                        stats.izin || 0,
                        stats.sakit || 0,
                        stats.alpha || 0
                    ],
                    backgroundColor: [
                        '#198754',
                        '#ffc107',
                        '#0dcaf0',
                        '#dc3545'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

    } catch (error) {
        console.error('Attendance Error:', error);
    }

    /*
     * 2. Statistik Jurusan
     */
    try {
        const jurusan = await fetchData(endpoints.jurusan);

        const container = document.getElementById('jurusanStats');

        if (!container) {
            return;
        }

        if (jurusan.length === 0) {
            container.innerHTML =
                '<div class="text-muted">Tidak ada data</div>';
        } else {
            container.innerHTML =
                jurusan.map(item => `
                    <div class="col-md-4">
                        <div class="p-3 border rounded text-center">
                            <div class="small text-muted">
                                ${item.nama_jurusan ?? '-'}
                            </div>
                            <div class="fw-bold text-primary">
                                ${item.jumlah}
                            </div>
                        </div>
                    </div>
                `).join('');
        }

    } catch (error) {
        console.error('Jurusan Error:', error);
    }

    /*
     * 3. Statistik Tingkat
     */
    try {
        const kelas = await fetchData(endpoints.kelas);

        const kelasX = kelas
            .filter(k => k.tingkat === 'X')
            .reduce((sum, k) => sum + parseInt(k.jumlah), 0);

        const kelasXI = kelas
            .filter(k => k.tingkat === 'XI')
            .reduce((sum, k) => sum + parseInt(k.jumlah), 0);

        const kelasXII = kelas
            .filter(k => k.tingkat === 'XII')
            .reduce((sum, k) => sum + parseInt(k.jumlah), 0);

        new Chart(document.getElementById('tingkatChart'), {
            type: 'bar',
            data: {
                labels: ['Kelas X', 'Kelas XI', 'Kelas XII'],
                datasets: [{
                    label: 'Jumlah Siswa',
                    data: [kelasX, kelasXI, kelasXII],
                    backgroundColor: '#014d40',
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

    } catch (error) {
        console.error('Kelas Error:', error);
    }

    /*
     * 4. Izin Terbaru
     */
    try {
        const izin = await fetchData(endpoints.izin);

        const container = document.getElementById('izinList');

        if (!container) {
            return;
        }

        if (izin.length === 0) {
            container.innerHTML =
                '<li class="list-group-item">Tidak ada izin terbaru</li>';
        } else {
            container.innerHTML =
                izin.map(item => `
                    <li class="list-group-item py-3">
                        <div class="d-flex justify-content-between">
                            <strong>${item.nama_siswa}</strong>
                            <small class="text-muted">
                                ${new Date(item.created_at).toLocaleDateString()}
                            </small>
                        </div>

                        <div class="small text-secondary mt-1">
                            ${item.isi_pesan}
                        </div>

                        <span class="badge bg-warning text-dark mt-2">
                            ${item.jenis_izin}
                        </span>
                    </li>
                `).join('');
        }

    } catch (error) {
        console.error('Izin Error:', error);
    }
});