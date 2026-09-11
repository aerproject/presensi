

<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="col-md-4 mb-8">
    <h3 class="mb-4 text-primary">
        <i class="bi bi-qr-code-scan"></i> Scan QR Code untuk Absensi
    </h3>

    <!-- ✅ Tampilan kamera webcam -->
    <div class="mb-4">
        <video id="webcam-preview" autoplay playsinline></video>
        <div class="text-muted fs-4 mt-2">🎥 Proses Absensi Dalam Pantauan</div>
    </div>

    <input type="text" id="username" name="username"
           class="form-control"
           style="opacity:0; position:absolute; z-index:-1;"
           autocomplete="off" autofocus>

    <div id="scan-result" class="alert mt-3 d-none fw-semibold"></div>

    <div id="loading-spinner" class="mt-3 d-none">
        <div class="spinner-border text-info" role="status">
            <span class="visually-hidden">Memproses...</span>
        </div>
        <div class="text-info mt-2">⏳ Memproses absensi...</div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const video = document.getElementById('webcam-preview');
    navigator.mediaDevices.getUserMedia({ video: true })
        .then(stream => {
            video.srcObject = stream;
        })
        .catch(err => {
            console.error('Gagal mengakses webcam:', err);
            video.parentElement.innerHTML = '<div class="text-danger">❌ Kamera tidak tersedia atau ditolak.</div>';
        });

    const input = document.getElementById('username');
    const resultBox = document.getElementById('scan-result');

    window.addEventListener('DOMContentLoaded', () => input.focus());

    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();

            const qrCode = input.value.trim();
            if (qrCode.length >= 3) {
                resultBox.classList.remove('alert-success', 'alert-danger', 'd-none');
                resultBox.classList.add('alert-info');
                resultBox.innerHTML = '⏳ Memproses absensi...';

                fetch('/scan/process', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ qr_code: qrCode })
                })
                .then(res => res.json())
                .then(data => {
                    resultBox.classList.remove('alert-info');

                    if (data.status === 'success') {
                        resultBox.classList.add('alert-success');
                        resultBox.innerHTML = '✅ ' + data.message;
                    } else {
                        resultBox.classList.add('alert-danger');
                        resultBox.innerHTML = '❌ ' + (data.message || 'Terjadi kesalahan.');
                    }

                    input.value = '';
                    input.focus();

                    setTimeout(() => {
                        resultBox.classList.add('d-none');
                        resultBox.classList.remove('alert-success', 'alert-danger');
                        resultBox.innerHTML = '';
                    }, 20000);
                })
                .catch(err => {
                    resultBox.classList.remove('alert-info');
                    resultBox.classList.add('alert-danger');
                    resultBox.innerHTML = '❌ Gagal menghubungi server.';
                    input.value = '';
                    input.focus();

                    setTimeout(() => {
                        resultBox.classList.add('d-none');
                        resultBox.classList.remove('alert-danger');
                        resultBox.innerHTML = '';
                    }, 20000);
                });
            }
        }
    });
</script>
<?= $this->endSection() ?>
