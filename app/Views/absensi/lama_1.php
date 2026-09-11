<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Scan QR Code - Absensi Siswa</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Instascan JS -->
  <script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
  <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
</head>
<body class="bg-light">

<div class="container py-5">
  <h2 class="text-center mb-4">📷 Scan QR Code untuk Absensi</h2>

  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-body text-center">
          <video id="preview" class="w-100 border rounded" autoplay></video>
          <p class="mt-3 text-muted">Arahkan kamera ke QR Code kartu pelajar Anda</p>
          <div id="scan-result" class="alert alert-info d-none"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
  let scanner = new Instascan.Scanner({ video: document.getElementById('preview'), mirror: false });
  scanner.addListener('scan', function (content) {
    document.getElementById('scan-result').classList.remove('d-none');
    document.getElementById('scan-result').innerHTML = 'QR Terdeteksi: <strong>' + content + '</strong><br>Memproses absensi...';

    // Kirim ke server via AJAX
    fetch('/absensi/proses', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({ qr_code: content })
    })
    .then(res => res.json())
    .then(data => {
      if (data.status === 'success') {
        document.getElementById('scan-result').classList.replace('alert-info', 'alert-success');
        document.getElementById('scan-result').innerHTML = '✅ Absensi berhasil untuk <strong>' + data.nama + '</strong> pada ' + data.waktu;
      } else {
        document.getElementById('scan-result').classList.replace('alert-info', 'alert-danger');
        document.getElementById('scan-result').innerHTML = '❌ Gagal absensi: ' + data.message;
      }
    })
    .catch(err => {
      document.getElementById('scan-result').classList.replace('alert-info', 'alert-danger');
      document.getElementById('scan-result').innerHTML = '❌ Terjadi kesalahan saat memproses absensi.';
    });
  });

  Instascan.Camera.getCameras().then(function (cameras) {
    if (cameras.length > 0) {
      scanner.start(cameras[0]);
    } else {
      alert('Tidak ditemukan kamera pada perangkat.');
    }
  }).catch(function (e) {
    console.error(e);
    alert('Gagal mengakses kamera: ' + e);
  });
</script>

</body>
</html>
