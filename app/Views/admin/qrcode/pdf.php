<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>QR Code PDF</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h2 { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        td { border: 1px solid #000; padding: 10px; text-align: center; vertical-align: top; }
        img { width: 120px; height: auto; margin-bottom: 8px; }
        .info { font-size: 11px; }
    </style>
</head>
<body>
    <h2>Daftar QR Code Kelas</h2>
    <table>
        <tbody>
            <?php 
            $no = 1; 
            $cols = 3; // jumlah kolom per baris
            foreach ($qrCodes as $index => $qr): 
                if ($index % $cols === 0) echo "<tr>";
            ?>
                <td>
                    <img src="<?= base_url('uploads/qrcodes/' . basename($qr['file_path'])) ?>">
                    <div class="info">
                        <strong>No:</strong> <?= $no++ ?><br>
                        <strong>NIS:</strong> <?= esc($qr['nis']) ?><br>
                        <strong>Kelas:</strong> <?= esc($qr['kelas_id']) ?><br>
                        <strong>Tanggal:</strong> <?= esc($qr['created_at']) ?>
                    </div>
                </td>
            <?php 
                if (($index + 1) % $cols === 0) echo "</tr>";
            endforeach; 
            if (count($qrCodes) % $cols !== 0) echo "</tr>";
            ?>
        </tbody>
    </table>
</body>
</html>
