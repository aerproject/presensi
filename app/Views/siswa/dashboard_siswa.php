<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Siswa</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background: #f4f7f9;
      padding-bottom: 80px;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .card { border: none; border-radius: 15px; overflow: hidden; }
    .card-header { font-weight: 600; padding: 15px; }
    .navbar { background: #fff !important; border-bottom: 1px solid #e0e0e0; }
    .footer {
      position: fixed; bottom: 0; left: 0; right: 0;
      background: #ffffff; text-align: center;
      padding: 10px; font-size: 12px; border-top: 1px solid #ddd;
      z-index: 1000;
    }
    .badge-status { font-size: 0.8rem; padding: 0.5em 0.8em; }
    .table-sm { font-size: 0.85rem; }
  </style>
</head>
<body>

<nav class="navbar navbar-light shadow-sm mb-3">
  <div class="container-fluid">
    <span class="navbar-brand fw-bold text-primary"><i class="bi bi-person-circle me-1"></i> Hai, <?= esc($student['nama_siswa']) ?></span>
    <a href="<?= base_url('auth/logout') ?>" class="btn btn-outline-danger btn-sm"><i class="bi bi-box-arrow-right"></i></a>
  </div>
</nav>

<div class="container">
  <div class="row g-2 mb-3">
    <div class="col-6">
      <div class="card shadow-sm h-100">
        <div class="card-header bg-primary text-white p-2 text-center small">Profil</div>
        <div class="card-body p-3">
          <small class="d-block text-muted">Kelas</small>
          <p class="fw-bold mb-1"><?= esc($student['nama_kelas'] ?? '-') ?></p>
          <small class="d-block text-muted">Jurusan</small>
          <p class="fw-bold mb-0"><?= esc($student['nama_jurusan'] ?? '-') ?></p>
        </div>
      </div>
    </div>
    <div class="col-6">
      <div class="card shadow-sm h-100">
        <div class="card-header bg-success text-white p-2 text-center small">Status Hari Ini</div>
        <div class="card-body p-3 text-center">
          <?php if ($absenHariIni): ?>
            <span class="badge bg-success badge-status mb-2"><?= strtoupper($absenHariIni['status']) ?></span>
            <div class="small fw-bold"><?= esc($absenHariIni['jam_masuk']) ?></div>
          <?php else: ?>
            <div class="text-danger small">Belum Absen</div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <div class="card shadow-sm mb-3">
    <div class="card-header bg-white border-bottom"><i class="bi bi-bar-chart-line me-2"></i>Statistik</div>
    <div class="card-body p-2">
      <div class="row g-1 text-center">
        <?php foreach ($statistik as $stat): ?>
          <div class="col-3">
            <div class="p-1 rounded bg-light">
              <div class="small fw-bold"><?= $stat['jumlah'] ?></div>
              <div class="x-small text-muted" style="font-size: 10px;"><?= strtoupper(substr($stat['status'], 0, 4)) ?></div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <h6 class="mb-2 text-secondary"><i class="bi bi-clock-history me-2"></i>Riwayat Terakhir</h6>
  <div class="table-responsive shadow-sm bg-white rounded">
    <table class="table table-sm table-striped align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>Tgl</th>
          <th>Sts</th>
          <th>Masuk</th>
          <th>Plg</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($riwayat as $r): ?>
          <tr>
            <td><?= date('d/m', strtotime($r['tanggal'])) ?></td>
            <td>
              <span class="badge bg-<?= $r['status'] == 'hadir' ? 'success' : 'warning' ?>">
                <?= strtoupper(substr($r['status'], 0, 1)) ?>
              </span>
            </td>
            <td><?= esc($r['jam_masuk'] ?? '-') ?></td>
            <td><?= esc($r['jam_pulang'] ?? '-') ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="footer">
  Absensi Digital | AerProject | © <?= date('Y') ?>
</div>

</body>
</html>