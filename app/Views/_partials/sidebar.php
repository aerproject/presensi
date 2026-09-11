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
        <?php if ($role === 'admin'): ?>
        <li class="nav-item">
            <a class="nav-link text-white d-flex align-items-center"
                    <?= in_array($segment2, ['tapel','jurusan','kelas','guru','siswa','libursekolah'])
                    ? 'active bg-primary bg-opacity-25 rounded text-white'
                    : 'text-white' ?>"
               data-bs-toggle="collapse"
               href="#masterDesktop">
                <i class="bi bi-pencil-square me-2"></i> Master Data
                <i class="bi bi-chevron-down ms-auto"></i>
            </a>

            <div class="collapse <?= in_array($segment2,['tapel','jurusan','kelas','guru','siswa','libursekolah']) ? 'show' : '' ?>"
                id="masterDesktop">

                <ul class="nav flex-column sidebar-mini-submenu">
                  <li>
                      <a class="nav-link <?= ($segment2==='tapel') ? 'active bg-primary bg-opacity-25 rounded text-white' : 'text-white' ?>"
                        href="<?= base_url('/admin/tapel') ?>">
                            <i class="bi bi-calendar3 sidebar-submenu-icon" aria-hidden="true"></i>
                          Tahun Pelajaran
                      </a>
                  </li>

                  <li>
                      <a class="nav-link <?= ($segment2==='jurusan') ? 'active bg-primary bg-opacity-25 rounded text-white' : 'text-white' ?>"
                        href="<?= base_url('/admin/jurusan') ?>">
                            <i class="bi bi-diagram-3 sidebar-submenu-icon" aria-hidden="true"></i>
                          Data Jurusan
                      </a>
                  </li>

                  <li>
                      <a class="nav-link <?= ($segment2==='kelas') ? 'active bg-primary bg-opacity-25 rounded text-white' : 'text-white' ?>"
                        href="<?= base_url('/admin/kelas') ?>">
                            <i class="bi bi-collection sidebar-submenu-icon" aria-hidden="true"></i>
                          Kelas
                      </a>
                  </li>

                  <li>
                      <a class="nav-link <?= ($segment2==='guru') ? 'active bg-primary bg-opacity-25 rounded text-white' : 'text-white' ?>"
                        href="<?= base_url('/admin/guru') ?>">
                            <i class="bi bi-person-badge sidebar-submenu-icon" aria-hidden="true"></i>
                          Guru
                      </a>
                  </li>

                  <li>
                      <a class="nav-link <?= ($segment2==='siswa') ? 'active bg-primary bg-opacity-25 rounded text-white' : 'text-white' ?>"
                        href="<?= base_url('/admin/siswa') ?>">
                            <i class="bi bi-people sidebar-submenu-icon" aria-hidden="true"></i>
                          Calon Siswa
                      </a>
                  </li>

                  <li>
                      <a class="nav-link <?= ($segment2==='libursekolah') ? 'active bg-primary bg-opacity-25 rounded text-white' : 'text-white' ?>"
                        href="<?= base_url('/admin/libursekolah') ?>">
                            <i class="bi bi-calendar-x sidebar-submenu-icon" aria-hidden="true"></i>
                          Libur Sekolah
                      </a>
                  </li>

              </ul>
            </div>
        </li>
        <?php endif; ?>

        <!-- Mapping -->
        <?php if ($role === 'admin'): ?>
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
        
                <ul class="nav flex-column sidebar-mini-submenu">
        
                    <!-- Daftar Mapping -->
                    <li>
                        <a class="nav-link 
                            <?= ($segment2 === 'mapping' && $uri->getSegment(3) == null) 
                                ? 'active bg-primary bg-opacity-25 rounded text-white' 
                                : 'text-white' ?>"
                          href="<?= base_url('admin/mapping') ?>">
        
                            <i class="bi bi-list-check sidebar-submenu-icon" aria-hidden="true"></i>
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
        
                            <i class="bi bi-plus-square sidebar-submenu-icon" aria-hidden="true"></i>
                            Tambah Mapping
                        </a>
                    </li>
        
                </ul>
            </div>
        
        </li>
        <?php endif; ?>

        <!-- Plotting -->
        <?php if ($role === 'admin'): ?>
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
        
                <ul class="nav flex-column sidebar-mini-submenu">
        
                    <!-- Siswa Akademik -->
                    <li>
                        <a class="nav-link
                            <?= ($segment2 === 'siswa-akademik')
                                ? 'active bg-primary bg-opacity-25 rounded text-white'
                                : 'text-white' ?>"
                          href="<?= base_url('/admin/siswa-akademik') ?>">
        
                            <i class="bi bi-people sidebar-submenu-icon" aria-hidden="true"></i>
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
        
                            <i class="bi bi-arrow-up-circle sidebar-submenu-icon" aria-hidden="true"></i>
                            Kenaikan Kelas
                        </a>
                    </li>
        
                </ul>
            </div>
        
        </li>
        <?php endif; ?>

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
        
                <ul class="nav flex-column sidebar-mini-submenu">
        
                    <!-- Presensi Siswa -->
                    <li>
                        <a class="nav-link
                            <?= ($segment2 === 'presensi')
                                ? 'active bg-primary bg-opacity-25 rounded text-white'
                                : 'text-white' ?>"
                          href="<?= base_url('/admin/presensi') ?>">
        
                            <i class="bi bi-person-check sidebar-submenu-icon" aria-hidden="true"></i>
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
        
                            <i class="bi bi-clock-history sidebar-submenu-icon" aria-hidden="true"></i>
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
        
                            <i class="bi bi-bar-chart sidebar-submenu-icon" aria-hidden="true"></i>
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
        
                <ul class="nav flex-column sidebar-mini-submenu">
        
                    <!-- Surat Izin -->
                    <li>
                        <a class="nav-link
                            <?= ($segment2 === 'izin')
                                ? 'active bg-primary bg-opacity-25 rounded text-white'
                                : 'text-white' ?>"
                          href="<?= base_url('/admin/izin') ?>">
        
                            <i class="bi bi-envelope sidebar-submenu-icon" aria-hidden="true"></i>
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
        
                            <i class="bi bi-chat-dots sidebar-submenu-icon" aria-hidden="true"></i>
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
        
                <ul class="nav flex-column sidebar-mini-submenu">
        
                    <!-- Tampilkan Pesan -->
                    <li>
                        <a class="nav-link
                            <?= ($segment2 === 'broadcast-view')
                                ? 'active bg-primary bg-opacity-25 rounded text-white'
                                : 'text-white' ?>"
                          href="<?= base_url('/admin/broadcast-view') ?>">
        
                            <i class="bi bi-chat-left-text sidebar-submenu-icon" aria-hidden="true"></i>
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
        
                            <i class="bi bi-plus-square sidebar-submenu-icon" aria-hidden="true"></i>
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
                <?= in_array($segment2, ['aplikasi', 'upload', 'wapikey', 'qrcode', 'license'])
                    ? 'active bg-primary bg-opacity-25 rounded text-white'
                    : 'text-white' ?>"
              data-bs-toggle="collapse"
              href="#settingDesktop">

                <i class="bi bi-gear me-2"></i>
                Pengaturan
                <i class="bi bi-chevron-down ms-auto"></i>
            </a>

            <div class="collapse <?= in_array($segment2, ['aplikasi', 'upload', 'wapikey', 'qrcode', 'license']) ? 'show' : '' ?>"
                id="settingDesktop">

                <ul class="nav flex-column sidebar-mini-submenu">

              <!-- Aplikasi -->
            <li>
                <a class="nav-link
                    <?= ($segment2 === 'aplikasi')
                        ? 'active bg-primary bg-opacity-25 rounded text-white'
                        : 'text-white' ?>"
                   href="<?= base_url('/admin/aplikasi') ?>">
            
                    <i class="bi bi-grid sidebar-submenu-icon" aria-hidden="true"></i>
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
            
                    <i class="bi bi-upload sidebar-submenu-icon" aria-hidden="true"></i>
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
            
                    <i class="bi bi-whatsapp sidebar-submenu-icon" aria-hidden="true"></i>
                    Whatsapp API
                </a>
            </li>
            
            <!-- Generate QR Code -->
            <li>
                <a class="nav-link
                    <?= ($segment2 === 'qrcode')
                        ? 'active bg-primary bg-opacity-25 rounded text-white'
                        : 'text-white' ?>"
                   href="<?= base_url('/admin/qrcode') ?>">
            
                    <i class="bi bi-qr-code sidebar-submenu-icon" aria-hidden="true"></i>
                    Generate QR Code
                </a>
            </li>
            
            <?php endif; ?>
            
            <!-- ADMIN + OPERATOR -->
            <?php if (in_array($role, ['admin', 'operator'], true)): ?>
            
            <!-- Manajemen User -->
            <li>
                <a class="nav-link
                    <?= ($segment2 === 'users')
                        ? 'active bg-primary bg-opacity-25 rounded text-white'
                        : 'text-white' ?>"
                   href="<?= base_url('/admin/users') ?>">
            
                    <i class="bi bi-person-gear sidebar-submenu-icon" aria-hidden="true"></i>
                    Manajemen User
                </a>
            </li>
            
            <?php endif; ?>
            
            <?php if ($role === 'admin'): ?>
            
            <!-- Lisensi -->
            <li>
                <a class="nav-link
                    <?= ($segment2 === 'license')
                        ? 'active bg-primary bg-opacity-25 rounded text-white'
                        : 'text-white' ?>"
                   href="<?= base_url('/admin/license') ?>">
            
                    <i class="bi bi-key sidebar-submenu-icon" aria-hidden="true"></i>
                    Lisensi
                </a>
            </li>
</ul>
            </div>

        </li>
        <?php endif; ?>

        

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
            <?php if ($role === 'admin'): ?>
            <li class="nav-item">
                <a class="nav-link text-white"
                   data-bs-toggle="collapse"
                   href="#masterMobile">

                    <i class="bi bi-pencil-square me-2"></i>
                    Master Data
                </a>

                <div class="collapse" id="masterMobile">
                    <ul class="nav flex-column ms-3">
                        <li><a class="nav-link text-white admin-nav-link" href="<?= base_url('/admin/tapel') ?>">Tahun Pelajaran</a></li>
                        <li><a class="nav-link text-white admin-nav-link" href="<?= base_url('/admin/jurusan') ?>">Data Jurusan</a></li>
                        <li><a class="nav-link text-white admin-nav-link" href="<?= base_url('/admin/kelas') ?>">Kelas</a></li>
                        <li><a class="nav-link text-white admin-nav-link" href="<?= base_url('/admin/guru') ?>">Guru</a></li>
                        <li><a class="nav-link text-white admin-nav-link" href="<?= base_url('/admin/siswa') ?>">Daftar Siswa</a></li>
                        <li><a class="nav-link text-white admin-nav-link" href="<?= base_url('/admin/libursekolah') ?>">Libur Sekolah</a></li>
                    </ul>
                </div>
            </li>
            <?php endif; ?>

            <!-- Mapping -->
            <?php if ($role === 'admin'): ?>
            <li class="nav-item">
                <a class="nav-link text-white"
                   data-bs-toggle="collapse"
                   href="#mappingMobile">
                    <i class="bi bi-diagram-3-fill me-2"></i> Mapping Kelas
                </a>

                <div class="collapse" id="mappingMobile">
                    <ul class="nav flex-column ms-3">
                        <li><a class="nav-link text-white admin-nav-link" href="<?= base_url('admin/mapping') ?>">Daftar Mapping</a></li>
                        <li><a class="nav-link text-white admin-nav-link" href="<?= base_url('admin/mapping/create') ?>">Tambah Mapping</a></li>
                    </ul>
                </div>
            </li>
            <?php endif; ?>

            <!-- Plotting -->
            <?php if ($role === 'admin'): ?>
            <li class="nav-item">
                <a class="nav-link text-white"
                   data-bs-toggle="collapse"
                   href="#plottingMobile">
                    <i class="bi bi-bar-chart-line me-2"></i> Plotting Data
                </a>

                <div class="collapse" id="plottingMobile">
                    <ul class="nav flex-column ms-3">
                        <li><a class="nav-link text-white admin-nav-link" href="<?= base_url('/admin/siswa-akademik') ?>">Data Siswa</a></li>
                        <li><a class="nav-link text-white admin-nav-link" href="<?= base_url('/admin/promote') ?>">Kenaikan Kelas</a></li>
                    </ul>
                </div>
            </li>
            <?php endif; ?>

            <!-- Presensi -->
            <li class="nav-item">
                <a class="nav-link text-white"
                   data-bs-toggle="collapse"
                   href="#presensiMobile">
                    <i class="bi bi-calendar-plus me-2"></i> Presensi
                </a>

                <div class="collapse" id="presensiMobile">
                    <ul class="nav flex-column ms-3">
                        <li><a class="nav-link text-white admin-nav-link" href="<?= base_url('/admin/presensi') ?>">Siswa</a></li>
                        <li><a class="nav-link text-white admin-nav-link" href="<?= base_url('/admin/kehadiran') ?>">Kehadiran</a></li>
                        <li><a class="nav-link text-white admin-nav-link" href="<?= base_url('/admin/laporan') ?>">Laporan</a></li>
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
                        <li><a class="nav-link text-white admin-nav-link" href="<?= base_url('/admin/izin') ?>">Surat Izin</a></li>
                        <li><a class="nav-link text-white admin-nav-link" href="<?= base_url('/admin/pesan') ?>">Notif Absensi</a></li>
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
                        <li><a class="nav-link text-white admin-nav-link" href="<?= base_url('/admin/broadcast-view') ?>">Tampilkan Pesan</a></li>
                        <li><a class="nav-link text-white admin-nav-link" href="<?= base_url('/admin/broadcast-create') ?>">Buat Pesan</a></li>
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

                <div class="collapse <?= in_array($segment2, ['aplikasi', 'upload', 'wapikey', 'qrcode', 'license']) ? 'show' : '' ?>" id="settingMobile">
                    <ul class="nav flex-column ms-3">

                        <li>
                            <a class="nav-link
                                <?= ($segment2 === 'aplikasi')
                                    ? 'active bg-primary bg-opacity-25 rounded text-white'
                                    : 'text-white' ?>"
                               href="<?= base_url('/admin/aplikasi') ?>">
                                Aplikasi
                            </a>
                        </li>

                        <li>
                            <a class="nav-link
                                <?= ($segment2 === 'upload')
                                    ? 'active bg-primary bg-opacity-25 rounded text-white'
                                    : 'text-white' ?>"
                               href="<?= base_url('/admin/upload') ?>">
                                Upload Data
                            </a>
                        </li>

                        <li>
                            <a class="nav-link
                                <?= ($segment2 === 'wapikey')
                                    ? 'active bg-primary bg-opacity-25 rounded text-white'
                                    : 'text-white' ?>"
                               href="<?= base_url('/admin/wapikey') ?>">
                                Whatsapp API
                            </a>
                        </li>

                        <li>
                            <a class="nav-link
                                <?= ($segment2 === 'qrcode')
                                    ? 'active bg-primary bg-opacity-25 rounded text-white'
                                    : 'text-white' ?>"
                               href="<?= base_url('/admin/qrcode') ?>">
                                Generate QR Code
                            </a>
                        </li>

                    <?php endif; ?>

                    <!-- ADMIN + OPERATOR MOBILE -->
                    <?php if(in_array($role, ['admin','operator'], true)): ?>

                        <li>
                            <a class="nav-link
                                <?= ($segment2 === 'users')
                                    ? 'active bg-primary bg-opacity-25 rounded text-white'
                                    : 'text-white' ?>"
                               href="<?= base_url('/admin/users') ?>">
                                Manajemen User
                            </a>
                        </li>

                        

                    <?php endif; ?>

                    <?php if($role==='admin'): ?>

                        <li>
                            <a class="nav-link
                                <?= ($segment2 === 'license')
                                    ? 'active bg-primary bg-opacity-25 rounded text-white'
                                    : 'text-white' ?>"
                               href="<?= base_url('/admin/license') ?>">
                                Lisensi
                            </a>
                        </li>
</ul>
                </div>
            </li>
            <?php endif; ?>

        

        </ul>

    </div>
</div>