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

	<input type="text" id="qr-input" autofocus style="opacity:0; position:absolute;" />
	<div id="scan-result" class="alert d-none mt-3"></div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
  const input = document.getElementById('qr-input');
  const resultBox = document.getElementById('scan-result');

  input.addEventListener('input', function () {
    const qrCode = input.value.trim();
    if (qrCode.length > 0) {
      resultBox.classList.remove('d-none', 'alert-success', 'alert-danger');
      resultBox.classList.add('alert-info');
      resultBox.innerHTML = 'QR Terdeteksi: <strong>' + qrCode + '</strong><br>Memproses absensi...';

      fetch('/absensi/proses', {
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
          resultBox.innerHTML = '❌ ' + data.message;
        }
      })
      .catch(err => {
        resultBox.classList.remove('alert-info');
        resultBox.classList.add('alert-danger');
        resultBox.innerHTML = '❌ Terjadi kesalahan saat memproses absensi.';
      });

      input.value = '';
    }
  });

  window.addEventListener('load', () => input.focus());
</script>



</body>
</html>
