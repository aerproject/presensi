<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Login Pengguna - Absensi Digital</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    }
    .login-card {
      border-radius: 12px;
    }
    .logo {
      max-width: 100px;
    }
  </style>
</head>
<body>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card shadow login-card">
        <div class="card-body">
          <!-- Logo Sekolah -->
          <div class="text-center mb-3">
            <img src="<?= base_url('assets/img/logo.png') ?>" alt="Logo Sekolah" class="logo mb-2">
            <h5 class="fw-bold">SMK 2 MEI BANDAR LAMPUNG</h5>
          </div>

          <h4 class="text-center mb-4">🔐 Login Pengguna</h4>

          <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
          <?php endif; ?>

          <form action="<?= base_url('auth/login') ?>" method="post">
            <?= csrf_field() ?> <!-- CSRF protection -->

            <div class="mb-3">
              <label for="username" class="form-label">Username</label>
              <input type="text" name="username" id="username" class="form-control" required autofocus>
            </div>

            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <input type="password" name="password" id="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary w-100 mb-2">Masuk</button>
            <!-- Tombol kembali -->
            <a href="<?= base_url('/') ?>" class="btn btn-outline-secondary w-100">Kembali ke Halaman Utama</a>
          </form>

          <p class="mt-4 text-center text-muted small">
            Absensi Digital | AerProject © <?= date('Y') ?>
          </p>
        </div>
      </div>
    </div>
  </div>
</div>

</body>
</html>
