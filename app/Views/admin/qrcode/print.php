<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Cetak QR Code</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header {
            border: 1px solid #000;
            background-color: #f2f2f2; /* warna latar tipis */
            padding: 10px;
            text-align: center;
            margin-bottom: 30px;
        }
        .grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }
        .item {
            width: 25%; /* 4 kolom */
            text-align: center;
            margin-bottom: 20px;
        }
        .item img {
            width: 120px;
            height: auto;
            margin-bottom: 8px;
        }
        .nis {
            font-size: 11px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>DATA QRCODE SISWA</h2>
        <h3>KELAS <?= esc($qrCodes[0]['nama_kelas'] ?? '') ?></h3>
    </div>

    <div class="grid">
        <?php foreach ($qrCodes as $qr): ?>
            <div class="item">
                <img src="<?= base_url('uploads/qrcodes/' . basename($qr['file_path'])) ?>">
                <div class="nis">NIS: <?= esc($qr['nis']) ?></div>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
