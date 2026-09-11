<?php
$uri = service('uri');
$segment2 = $uri->getSegment(2);
$role = session()->get('role');
?>

<!-- ================= DESKTOP SIDEBAR ================= -->
<div class="sidebar d-none d-lg-block">

    <div class="text-center mb-3">
        <img src="<?= base_url('images/LogoAPP.png') ?>"
             alt="Logo Aplikasi"
             class="img-fluid sidebar-logo">
    </div>

    <h5 class="sidebar-title text-center fw-bold text-uppercase">
        Menu Admin
    </h5>

    <ul class="nav flex-column">

        <!-- Dashboard -->
        <li class="nav-item mb-1 mt-3">
            <a class="nav-link d-flex align-items-center <?= ($segment2==='dashboard') ? 'active bg-primary bg-opacity-25 rounded' : 'text-white' ?>"
               href="<?= base_url('/admin/dashboard') ?>">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
        </li>

        <!-- Master -->
        <li class="nav-item">
            <a class="nav-link text-white d-flex align-items-center"
               data-bs-toggle="collapse"
               href="#masterDesktop">
                <i class="bi bi-pencil-square me-2"></i> Master Data
                <i class="bi bi-chevron-down ms-auto"></i>
            </a>

            <div class="collapse <?= in_array($segment2,['tapel','jurusan','kelas','guru','siswa','libursekolah']) ? 'show':'' ?>"
                 id="masterDesktop">

                <ul class="nav flex-column ms-3 border-start ps-2">
                  <li>
                      <a class="nav-link <?= ($segment2==='tapel') ? 'active bg-primary bg-opacity-25 rounded text-white' : 'text-white' ?>"
                        href="<?= base_url('/admin/tapel') ?>">
                          Tahun Pelajaran
                      </a>
                  </li>

                  <li>
                      <a class="nav-link <?= ($segment2==='jurusan') ? 'active bg-primary bg-opacity-25 rounded text-white' : 'text-white' ?>"
                        href="<?= base_url('/admin/jurusan') ?>">
                          Data Jurusan
                      </a>
                  </li>

                  <li>
                      <a class="nav-link <?= ($segment2==='kelas') ? 'active bg-primary bg-opacity-25 rounded text-white' : 'text-white' ?>"
                        href="<?= base_url('/admin/kelas') ?>">
                          Kelas
                      </a>
                  </li>

                  <li>
                      <a class="nav-link <?= ($segment2==='guru') ? 'active bg-primary bg-opacity-25 rounded text-white' : 'text-white' ?>"
                        href="<?= base_url('/admin/guru') ?>">
                          Guru
                      </a>
                  </li>

                  <li>
                      <a class="nav-link <?= ($segment2==='siswa') ? 'active bg-primary bg-opacity-25 rounded text-white' : 'text-white' ?>"
                        href="<?= base_url('/admin/siswa') ?>">
                          Calon Siswa
                      </a>
                  </li>

                  <li>
                      <a class="nav-link <?= ($segment2==='libursekolah') ? 'active bg-primary bg-opacity-25 rounded text-white' : 'text-white' ?>"
                        href="<?= base_url('/admin/libursekolah') ?>">
                          Libur Sekolah
                      </a>
                  </li>

              </ul>
            </div>
        </li>

        <!-- Mapping -->
        <li class="nav-item">

            <a class="nav-link d-flex align-items-center 
                <?= ($segment2 === 'mapping') 
                    ? 'active bg-primary bg-opacity-25 rounded text-white' 
                    : 'text-white' ?>"
              data-bs-toggle="collapse"
              href="#mappingDesktop">

                <i class="bi bi-diagram-3-fill me-2"></i>
                Mapping Kelas
                <i class="bi bi-chevron-down ms-auto"></i>
            </a>

            <div class="collapse <?= ($segment2 === 'mapping') ? 'show' : '' ?>"
                id="mappingDesktop">

                <ul class="nav flex-column ms-3 border-start ps-2">

                    <!-- Daftar Mapping -->
                    <li>
                        <a class="nav-link 
                            <?= ($segment2 === 'mapping' && $uri->getSegment(3) == null) 
                                ? 'active bg-primary bg-opacity-25 rounded text-white' 
                                : 'text-white' ?>"
                          href="<?= base_url('admin/mapping') ?>">

                            Daftar Mapping
                        </a>
                    </li>

                    <!-- Tambah Mapping -->
                    <li>
                        <a class="nav-link 
                            <?= ($segment2 === 'mapping' && $uri->getSegment(3) === 'create') 
                                ? 'active bg-primary bg-opacity-25 rounded text-white' 
                                : 'text-white' ?>"
                          href="<?= base_url('admin/mapping/create') ?>">

                            Tambah Mapping
                        </a>
                    </li>

                </ul>
            </div>

        </li>

        <!-- Plotting -->
        <li class="nav-item">

            <a class="nav-link d-flex align-items-center
                <?= ($segment2 === 'siswa-akademik' || $segment2 === 'promote')
                    ? 'active bg-primary bg-opacity-25 rounded text-white'
                    : 'text-white' ?>"
              data-bs-toggle="collapse"
              href="#plottingDesktop">

                <i class="bi bi-bar-chart-line me-2"></i>
                Plotting Data
                <i class="bi bi-chevron-down ms-auto"></i>
            </a>

            <div class="collapse <?= ($segment2 === 'siswa-akademik' || $segment2 === 'promote') ? 'show' : '' ?>"
                id="plottingDesktop">

                <ul class="nav flex-column ms-3 border-start ps-2">

                    <!-- Siswa Akademik -->
                    <li>
                        <a class="nav-link
                            <?= ($segment2 === 'siswa-akademik')
                                ? 'active bg-primary bg-opacity-25 rounded text-white'
                                : 'text-white' ?>"
                          href="<?= base_url('/admin/siswa-akademik') ?>">

                            Data Siswa
                        </a>
                    </li>

                    <!-- Kenaikan Kelas -->
                    <li>
                        <a class="nav-link
                            <?= ($segment2 === 'promote')
                                ? 'active bg-primary bg-opacity-25 rounded text-white'
                                : 'text-white' ?>"
                          href="<?= base_url('/admin/promote') ?>">

                            Kenaikan Kelas
                        </a>
                    </li>

                </ul>
            </div>

        </li>

        <!-- Presensi -->
        <li class="nav-item">

            <a class="nav-link d-flex align-items-center
                <?= in_array($segment2, ['presensi', 'kehadiran', 'laporan'])
                    ? 'active bg-primary bg-opacity-25 rounded text-white'
                    : 'text-white' ?>"
              data-bs-toggle="collapse"
              href="#presensiDesktop">

                <i class="bi bi-calendar-plus me-2"></i>
                Presensi
                <i class="bi bi-chevron-down ms-auto"></i>
            </a>

            <div class="collapse <?= in_array($segment2,['presensi','kehadiran','laporan']) ? 'show' : '' ?>"
                id="presensiDesktop">

                <ul class="nav flex-column ms-3 border-start ps-2">

                    <!-- Presensi Siswa -->
                    <li>
                        <a class="nav-link
                            <?= ($segment2 === 'presensi')
                                ? 'active bg-primary bg-opacity-25 rounded text-white'
                                : 'text-white' ?>"
                          href="<?= base_url('/admin/presensi') ?>">

                            Siswa
                        </a>
                    </li>

                    <!-- Kehadiran -->
                    <li>
                        <a class="nav-link
                            <?= ($segment2 === 'kehadiran')
                                ? 'active bg-primary bg-opacity-25 rounded text-white'
                                : 'text-white' ?>"
                          href="<?= base_url('/admin/kehadiran') ?>">

                            Kehadiran
                        </a>
                    </li>

                    <!-- Laporan -->
                    <li>
                        <a class="nav-link
                            <?= ($segment2 === 'laporan')
                                ? 'active bg-primary bg-opacity-25 rounded text-white'
                                : 'text-white' ?>"
                          href="<?= base_url('/admin/laporan') ?>">

                            Laporan
                        </a>
                    </li>

                </ul>
            </div>

        </li>

        <!-- Notifikasi -->
        <li class="nav-item">

            <a class="nav-link d-flex align-items-center
                <?= in_array($segment2, ['izin', 'pesan'])
                    ? 'active bg-primary bg-opacity-25 rounded text-white'
                    : 'text-white' ?>"
              data-bs-toggle="collapse"
              href="#notifDesktop">

                <i class="bi bi-bell me-2"></i>
                Notifikasi
                <i class="bi bi-chevron-down ms-auto"></i>
            </a>

            <div class="collapse <?= in_array($segment2, ['izin', 'pesan']) ? 'show' : '' ?>"
                id="notifDesktop">

                <ul class="nav flex-column ms-3 border-start ps-2">

                    <!-- Surat Izin -->
                    <li>
                        <a class="nav-link
                            <?= ($segment2 === 'izin')
                                ? 'active bg-primary bg-opacity-25 rounded text-white'
                                : 'text-white' ?>"
                          href="<?= base_url('/admin/izin') ?>">

                            Surat Izin
                        </a>
                    </li>

                    <!-- Notif Absensi -->
                    <li>
                        <a class="nav-link
                            <?= ($segment2 === 'pesan')
                                ? 'active bg-primary bg-opacity-25 rounded text-white'
                                : 'text-white' ?>"
                          href="<?= base_url('/admin/pesan') ?>">

                            Notif Absensi
                        </a>
                    </li>

                </ul>
            </div>

        </li>

        <!-- Whatsapp -->
        <li class="nav-item">

            <a class="nav-link d-flex align-items-center
                <?= in_array($segment2, ['broadcast-view', 'broadcast-create'])
                    ? 'active bg-primary bg-opacity-25 rounded text-white'
                    : 'text-white' ?>"
              data-bs-toggle="collapse"
              href="#waDesktop">

                <i class="bi bi-whatsapp me-2"></i>
                Pesan Whatsapp
                <i class="bi bi-chevron-down ms-auto"></i>
            </a>

            <div class="collapse <?= in_array($segment2, ['broadcast-view', 'broadcast-create']) ? 'show' : '' ?>"
                id="waDesktop">

                <ul class="nav flex-column ms-3 border-start ps-2">

                    <!-- Tampilkan Pesan -->
                    <li>
                        <a class="nav-link
                            <?= ($segment2 === 'broadcast-view')
                                ? 'active bg-primary bg-opacity-25 rounded text-white'
                                : 'text-white' ?>"
                          href="<?= base_url('/admin/broadcast-view') ?>">

                            Tampilkan Pesan
                        </a>
                    </li>

                    <!-- Buat Pesan -->
                    <li>
                        <a class="nav-link
                            <?= ($segment2 === 'broadcast-create')
                                ? 'active bg-primary bg-opacity-25 rounded text-white'
                                : 'text-white' ?>"
                          href="<?= base_url('/admin/broadcast-create') ?>">

                            Buat Pesan
                        </a>
                    </li>

                </ul>
            </div>

        </li>

        <!-- Pengaturan -->
        <?php if ($role === 'admin'): ?>
        <li class="nav-item">

            <a class="nav-link d-flex align-items-center
                <?= in_array($segment2, ['aplikasi', 'upload', 'wapikey', 'qrcode'])
                    ? 'active bg-primary bg-opacity-25 rounded text-white'
                    : 'text-white' ?>"
              data-bs-toggle="collapse"
              href="#settingDesktop">

                <i class="bi bi-gear me-2"></i>
                Pengaturan
                <i class="bi bi-chevron-down ms-auto"></i>
            </a>

            <div class="collapse <?= in_array($segment2, ['aplikasi', 'upload', 'wapikey', 'qrcode']) ? 'show' : '' ?>"
                id="settingDesktop">

                <ul class="nav flex-column ms-3 border-start ps-2">

                    <!-- Aplikasi -->
                    <li>
                        <a class="nav-link
                            <?= ($segment2 === 'aplikasi')
                                ? 'active bg-primary bg-opacity-25 rounded text-white'
                                : 'text-white' ?>"
                          href="<?= base_url('/admin/aplikasi') ?>">

                            Aplikasi
                        </a>
                    </li>

                    <!-- Upload Data -->
                    <li>
                        <a class="nav-link
                            <?= ($segment2 === 'upload')
                                ? 'active bg-primary bg-opacity-25 rounded text-white'
                                : 'text-white' ?>"
                          href="<?= base_url('/admin/upload') ?>">

                            Upload Data
                        </a>
                    </li>

                    <!-- Whatsapp API -->
                    <li>
                        <a class="nav-link
                            <?= ($segment2 === 'wapikey')
                                ? 'active bg-primary bg-opacity-25 rounded text-white'
                                : 'text-white' ?>"
                          href="<?= base_url('/admin/wapikey') ?>">

                            Whatsapp API
                        </a>
                    </li>

                    <!-- QR Code -->
                    <li>
                        <a class="nav-link
                            <?= ($segment2 === 'qrcode')
                                ? 'active bg-primary bg-opacity-25 rounded text-white'
                                : 'text-white' ?>"
                          href="<?= base_url('/admin/qrcode') ?>">

                            Generate QR Code
                        </a>
                    </li>

                    <!-- Lisensi -->
                    <li>
                        <a class="nav-link
                            <?= ($segment2 === 'license')
                                ? 'active bg-primary bg-opacity-25 rounded text-white'
                                : 'text-white' ?>"
                          href="<?= base_url('/admin/license') ?>">

                            <i class="bi bi-shield-check me-2"></i>
                            Lisensi
                        </a>
                    </li>

                </ul>
            </div>

        </li>
        <?php endif; ?>

        <li class="nav-item mt-3">
            <a class="nav-link text-white" href="/auth/logout">
                <i class="bi bi-box-arrow-right me-2"></i> Keluar
            </a>
        </li>

    </ul>
</div>



<!-- ================= MOBILE SIDEBAR ================= -->
<div class="offcanvas offcanvas-start bg-dark text-white"
     tabindex="-1"
     id="mobileSidebar">

    <div class="offcanvas-header">
        <h5 class="fw-bold">Menu Admin</h5>
        <button class="btn-close btn-close-white"
                data-bs-dismiss="offcanvas"></button>
    </div>

    <div class="offcanvas-body">

        <div class="text-center mb-3">
            <img src="<?= base_url('images/LogoAPP.png') ?>"
                 class="img-fluid sidebar-logo-mobile">
        </div>

        <ul class="nav flex-column">

            <!-- Dashboard -->
            <li class="nav-item mb-2">
                <a class="nav-link text-white"
                   href="<?= base_url('/admin/dashboard') ?>">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
            </li>

            <!-- Master -->
            <li class="nav-item">
                <a class="nav-link text-white"
                   data-bs-toggle="collapse"
                   href="#masterMobile">

                    <i class="bi bi-pencil-square me-2"></i>
                    Master Data
                </a>

                <div class="collapse" id="masterMobile">
                    <ul class="nav flex-column ms-3">
                        <li><a class="nav-link text-white" href="<?= base_url('/admin/tapel') ?>">Tahun Pelajaran</a></li>
                        <li><a class="nav-link text-white" href="<?= base_url('/admin/jurusan') ?>">Data Jurusan</a></li>
                        <li><a class="nav-link text-white" href="<?= base_url('/admin/kelas') ?>">Kelas</a></li>
                        <li><a class="nav-link text-white" href="<?= base_url('/admin/guru') ?>">Guru</a></li>
                        <li><a class="nav-link text-white" href="<?= base_url('/admin/siswa') ?>">Daftar Siswa</a></li>
                        <li><a class="nav-link text-white" href="<?= base_url('/admin/libursekolah') ?>">Libur Sekolah</a></li>
                    </ul>
                </div>
            </li>

            <!-- Mapping -->
            <li class="nav-item">
                <a class="nav-link text-white"
                   data-bs-toggle="collapse"
                   href="#mappingMobile">
                    <i class="bi bi-diagram-3-fill me-2"></i> Mapping Kelas
                </a>

                <div class="collapse" id="mappingMobile">
                    <ul class="nav flex-column ms-3">
                        <li><a class="nav-link text-white" href="<?= base_url('admin/mapping') ?>">Daftar Mapping</a></li>
                        <li><a class="nav-link text-white" href="<?= base_url('admin/mapping/create') ?>">Tambah Mapping</a></li>
                    </ul>
                </div>
            </li>

            <!-- Plotting -->
            <li class="nav-item">
                <a class="nav-link text-white"
                   data-bs-toggle="collapse"
                   href="#plottingMobile">
                    <i class="bi bi-bar-chart-line me-2"></i> Plotting Data
                </a>

                <div class="collapse" id="plottingMobile">
                    <ul class="nav flex-column ms-3">
                        <li><a class="nav-link text-white" href="<?= base_url('/admin/plotkelas') ?>">Data Siswa</a></li>
                        <li><a class="nav-link text-white" href="<?= base_url('/admin/promote') ?>">Kenaikan Kelas</a></li>
                    </ul>
                </div>
            </li>

            <!-- Presensi -->
            <li class="nav-item">
                <a class="nav-link text-white"
                   data-bs-toggle="collapse"
                   href="#presensiMobile">
                    <i class="bi bi-calendar-plus me-2"></i> Presensi
                </a>

                <div class="collapse" id="presensiMobile">
                    <ul class="nav flex-column ms-3">
                        <li><a class="nav-link text-white" href="<?= base_url('/admin/presensi') ?>">Siswa</a></li>
                        <li><a class="nav-link text-white" href="<?= base_url('/admin/kehadiran') ?>">Kehadiran</a></li>
                        <li><a class="nav-link text-white" href="<?= base_url('/admin/laporan') ?>">Laporan</a></li>
                    </ul>
                </div>
            </li>

            <!-- Notifikasi -->
            <li class="nav-item">
                <a class="nav-link text-white"
                   data-bs-toggle="collapse"
                   href="#notifMobile">
                    <i class="bi bi-bell me-2"></i> Notifikasi
                </a>

                <div class="collapse" id="notifMobile">
                    <ul class="nav flex-column ms-3">
                        <li><a class="nav-link text-white" href="<?= base_url('/admin/izin') ?>">Surat Izin</a></li>
                        <li><a class="nav-link text-white" href="<?= base_url('/admin/pesan') ?>">Notif Absensi</a></li>
                    </ul>
                </div>
            </li>

            <!-- Whatsapp -->
            <li class="nav-item">
                <a class="nav-link text-white"
                   data-bs-toggle="collapse"
                   href="#waMobile">
                    <i class="bi bi-whatsapp me-2"></i> Pesan Whatsapp
                </a>

                <div class="collapse" id="waMobile">
                    <ul class="nav flex-column ms-3">
                        <li><a class="nav-link text-white" href="<?= base_url('/admin/broadcast-view') ?>">Tampilkan Pesan</a></li>
                        <li><a class="nav-link text-white" href="<?= base_url('/admin/broadcast-create') ?>">Buat Pesan</a></li>
                    </ul>
                </div>
            </li>

            <!-- Pengaturan -->
            <?php if($role==='admin'): ?>
            <li class="nav-item">
                <a class="nav-link text-white"
                   data-bs-toggle="collapse"
                   href="#settingMobile">

                    <i class="bi bi-gear me-2"></i>
                    Pengaturan
                </a>

                <div class="collapse" id="settingMobile">
                    <ul class="nav flex-column ms-3">
                        <li><a class="nav-link text-white" href="<?= base_url('/admin/aplikasi') ?>">Aplikasi</a></li>
                        <li><a class="nav-link text-white" href="<?= base_url('/admin/upload') ?>">Upload Data</a></li>
                        <li><a class="nav-link text-white" href="<?= base_url('/admin/wapikey') ?>">Whatsapp API</a></li>
                        <li><a class="nav-link text-white" href="<?= base_url('/admin/qrcode') ?>">Generate QR Code</a></li>
                        <li>
                            <a class="nav-link text-white <?= ($segment2 === 'license') ? 'active bg-primary bg-opacity-25 rounded text-white' : '' ?>"
                               href="<?= base_url('/admin/license') ?>">
                                <i class="bi bi-shield-check me-2"></i>
                                Lisensi
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <?php endif; ?>

            <!-- Logout -->
            <li class="nav-item mt-3">
                <a class="nav-link text-white" href="/auth/logout">
                    <i class="bi bi-box-arrow-right me-2"></i> Keluar
                </a>
            </li>

        </ul>

    </div>
</div>