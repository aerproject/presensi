<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= isset($title) ? esc($title) : 'Dashboard Wali Kelas' ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="<?= base_url('assets/css/admin.css') ?>" rel="stylesheet">

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <!-- Select2 -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <!-- TinyMCE -->
  <script src="<?= base_url('tinymce/tinymce.min.js') ?>"></script>
<style>
@media (max-width: 767.98px) {
  .content {
    padding-left: 4px;
    padding-right: 4px;
  }
}
</style>
</head>
<body>

  <!-- Header bar -->
  <nav class="navbar navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
      <button class="btn btn-primary d-lg-none ms-2" id="toggleSidebar">
        <i class="bi bi-list"></i>
      </button>
      <span class="navbar-brand ms-2">Absensi Digital | Walikelas Dashboard</span>
    </div>
  </nav>

  <!-- Layout -->
  <div class="container-fluid mt-5">
    <div class="row">
      <!-- Sidebar -->
      <div class="col-auto">
        <?= $this->include('_partials/sidebar_walikelas') ?>
        <div id="sidebarOverlay" class="overlay d-lg-none"></div>
      </div>

      <!-- Content -->
      <div class="col ps-lg-4">
        <main class="content">
          <?= $this->renderSection('content') ?>
        </main>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="footer mt-auto">
    <div class="container">
      <span class="text-muted">© <?= date('Y') ?> AerProject. All rights reserved.</span>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <script src="<?= base_url('js/walas.js') ?>"></script>

</body>
</html>
