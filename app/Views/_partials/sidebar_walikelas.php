<?php
$uri = service('uri');
$segment2 = $uri->getSegment(2); // misalnya 'dashboard', 'kehadiran', dll
?>

<div class="sidebar bg-dark text-white p-3 rounded shadow-sm min-vh-100">
  <h5 class="mb-4">Menu Wali Kelas</h5>

  <ul class="nav flex-column">
    <!-- Dashboard -->
    <li class="nav-item">
      <a class="nav-link text-white <?= ($segment2 === 'dashboard') ? 'active' : '' ?>" href="<?= base_url('/walikelas/dashboard') ?>">
        <i class="bi bi-speedometer2 me-2"></i>Dashboard
      </a>
    </li>

    <!-- Presensi -->
    <li class="nav-item">
      <a class="nav-link text-white" data-bs-toggle="collapse" href="#submenuPresensi" role="button">
        <i class="bi bi-calendar-plus me-2"></i> Presensi <i class="bi bi-chevron-down float-end"></i>
      </a>
      <div class="collapse <?= in_array($segment2, ['siswa','kehadiran','laporan']) ? 'show' : '' ?>" id="submenuPresensi">
        <ul class="nav flex-column submenu">
          <li><a class="nav-link text-white <?= ($segment2==='siswa')?'active':'' ?>" href="<?= base_url('/walikelas/siswa') ?>"><i class="bi bi-people me-2"></i>Daftar Siswa</a></li>
          <li><a class="nav-link text-white <?= ($segment2==='kehadiran')?'active':'' ?>" href="<?= base_url('/walikelas/kehadiran') ?>"><i class="bi bi-calendar-check me-2"></i>Kehadiran</a></li>
        </ul>
      </div>
    </li>

    <!-- Notifikasi -->
    <li class="nav-item">
      <a class="nav-link text-white" data-bs-toggle="collapse" href="#submenuNotifikasi" role="button">
        <i class="bi bi-bell me-2"></i> Notifikasi <i class="bi bi-chevron-down float-end"></i>
      </a>
      <div class="collapse <?= in_array($segment2, ['izin','pesan']) ? 'show' : '' ?>" id="submenuNotifikasi">
        <ul class="nav flex-column submenu">
          <li><a class="nav-link text-white <?= ($segment2==='izin')?'active':'' ?>" href="<?= base_url('/walikelas/izin') ?>"><i class="bi bi-calendar-x me-2"></i>Surat Izin</a></li>
          <li><a class="nav-link text-white <?= ($segment2==='pesan')?'active':'' ?>" href="<?= base_url('/walikelas/pesan') ?>"><i class="bi bi-info-circle me-2"></i>Notif Absensi</a></li>
        </ul>
      </div>
    </li>

    <!-- Profil -->
    <li class="nav-item">
      <a class="nav-link text-white <?= ($segment2 === 'profil') ? 'active' : '' ?>"
         href="<?= base_url('/walikelas/profil') ?>">
        <i class="bi bi-person-circle me-2"></i> Profil
      </a>
    </li>

    <!-- Logout -->
    <li class="nav-item">
      <a class="nav-link text-white" href="/auth/logout">
        <i class="bi bi-box-arrow-right me-2"></i> Keluar
      </a>
    </li>
  </ul>
</div>
