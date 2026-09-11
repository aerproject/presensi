<?php
// C:\wamp\www\absensiku\app\Views\absensi\scan.php
?>

<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container text-center">
  <h2 class="mb-4 mt-5 text-primary">
    <i class="bi bi-qr-code-scan"></i> Scan QR Code untuk Absensi
  </h2>

  <!-- ✅ Tampilan kamera webcam -->
  <div class="mb-4">
    <video id="webcam-preview" autoplay playsinline class="rounded border w-100" style="max-width:460px; height:auto;"></video>
    <div class="text-muted fs-6 mt-2">🎥 Proses Absensi Dalam Pantauan</div>
  </div>

  <!-- Input tersembunyi untuk scanner -->
  <input type="text" id="username" name="username"
         class="form-control"
         style="opacity:0; position:absolute; z-index:-1;"
         autocomplete="off" autofocus>

  <!-- Kotak hasil scan -->
  <div id="scan-result" class="alert mt-3 d-none text-center fw-semibold"></div>

  <!-- Loading spinner -->
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

      const username = input.value.trim();
      if (username.length >= 3) {
        resultBox.classList.remove('alert-success', 'alert-danger', 'd-none');
        resultBox.classList.add('alert-info');
        resultBox.innerHTML = '⏳ Memproses absensi...';

        fetch('/absensi/proses', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: JSON.stringify({ scan_user: username })
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
          }, 2000);
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
          }, 2000);
        });
      }
    }
  });
</script>

<style>
  /* Tambahan CSS untuk mobile */
  @media (max-width: 576px) {
    #webcam-preview {
      width: 100% !important;
      height: auto !important;
    }
    h2 { font-size: 1.25rem; }
    .fs-6 { font-size: 0.9rem !important; }
  }
</style>
<?= $this->endSection() ?>
