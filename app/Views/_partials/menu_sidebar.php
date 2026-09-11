
<ul class="nav flex-column">

    <!-- Dashboard -->
    <li class="nav-item mb-1 mt-2">
        <a class="nav-link d-flex align-items-center text-white"
           href="http://localhost:8080/admin/dashboard">
            <i class="bi bi-speedometer2 me-2"></i> Dashboard
        </a>
    </li>


    <!-- Master Data -->
    <li class="nav-item mb-1">
        <a class="nav-link d-flex align-items-center text-white"
           data-bs-toggle="collapse"
           href="#submenuMaster">

            <i class="bi bi-pencil-square me-2"></i>
            Master Data
            <i class="bi bi-chevron-down ms-auto"></i>
        </a>

        <div class="collapse "
             id="submenuMaster">

            <ul class="nav flex-column ms-3 border-start ps-2">

                <li>
                    <a class="nav-link small "
                       href="http://localhost:8080/admin/tapel">
                        Tahun Pelajaran
                    </a>
                </li>

                <li>
                    <a class="nav-link small "
                       href="http://localhost:8080/admin/jurusan">
                        Data Jurusan
                    </a>
                </li>

                <li>
                    <a class="nav-link small "
                       href="http://localhost:8080/admin/kelas">
                        Kelas
                    </a>
                </li>

                <li>
                    <a class="nav-link small "
                       href="http://localhost:8080/admin/guru">
                        Guru
                    </a>
                </li>

                <li>
                    <a class="nav-link small "
                       href="http://localhost:8080/admin/siswa">
                        Daftar Siswa
                    </a>
                </li>

                <li>
                    <a class="nav-link small "
                       href="http://localhost:8080/admin/libursekolah">
                        Libur Sekolah
                    </a>
                </li>

            </ul>
        </div>
    </li>


    <!-- Mapping -->
    <li class="nav-item">

        <a class="nav-link d-flex align-items-center text-white"
           data-bs-toggle="collapse"
           href="#submenuMapping">

            <i class="bi bi-diagram-3-fill me-2"></i>
            Mapping Kelas
            <i class="bi bi-chevron-down ms-auto"></i>
        </a>

        <div class="collapse "
             id="submenuMapping">

            <ul class="nav flex-column ms-3 border-start ps-2">

                <li>
                    <a class="nav-link text-white"
                       href="http://localhost:8080/admin/mapping">
                        Daftar Mapping
                    </a>
                </li>

                <li>
                    <a class="nav-link text-white"
                       href="http://localhost:8080/admin/mapping/create">
                        Tambah Mapping
                    </a>
                </li>

            </ul>
        </div>

    </li>


    <!-- Plotting -->
    <li class="nav-item">

        <a class="nav-link d-flex align-items-center text-white"
           data-bs-toggle="collapse"
           href="#submenuPlotting">

            <i class="bi bi-bar-chart-line me-2"></i>
            Plotting Data
            <i class="bi bi-chevron-down ms-auto"></i>
        </a>

        <div class="collapse "
             id="submenuPlotting">

            <ul class="nav flex-column ms-3 border-start ps-2">

                <li>
                    <a class="nav-link text-white"
                       href="http://localhost:8080/admin/plotkelas">
                        Data Siswa
                    </a>
                </li>

                <li>
                    <a class="nav-link text-white"
                       href="http://localhost:8080/admin/promote">
                        Kenaikan Kelas
                    </a>
                </li>

            </ul>
        </div>

    </li>


    <!-- Presensi -->
    <li class="nav-item">

        <a class="nav-link d-flex align-items-center text-white"
           data-bs-toggle="collapse"
           href="#submenuPresensi">

            <i class="bi bi-calendar-plus me-2"></i>
            Presensi
            <i class="bi bi-chevron-down ms-auto"></i>
        </a>

        <div class="collapse show"
             id="submenuPresensi">

            <ul class="nav flex-column ms-3 border-start ps-2">

                <li>
                    <a class="nav-link text-white"
                       href="http://localhost:8080/admin/presensi">
                        Siswa
                    </a>
                </li>

                <li>
                    <a class="nav-link text-white"
                       href="http://localhost:8080/admin/kehadiran">
                        Kehadiran
                    </a>
                </li>

                <li>
                    <a class="nav-link text-white"
                       href="http://localhost:8080/admin/laporan">
                        Laporan
                    </a>
                </li>

            </ul>
        </div>

    </li>


    <!-- Notifikasi -->
    <li class="nav-item">

        <a class="nav-link d-flex align-items-center text-white"
           data-bs-toggle="collapse"
           href="#submenuNotifikasi">

            <i class="bi bi-bell me-2"></i>
            Notifikasi
            <i class="bi bi-chevron-down ms-auto"></i>
        </a>

        <div class="collapse "
             id="submenuNotifikasi">

            <ul class="nav flex-column ms-3 border-start ps-2">

                <li>
                    <a class="nav-link text-white"
                       href="http://localhost:8080/admin/izin">
                        Surat Izin
                    </a>
                </li>

                <li>
                    <a class="nav-link text-white"
                       href="http://localhost:8080/admin/pesan">
                        Notif Absensi
                    </a>
                </li>

            </ul>
        </div>

    </li>


    <!-- Whatsapp -->
    <li class="nav-item">

        <a class="nav-link d-flex align-items-center text-white"
           data-bs-toggle="collapse"
           href="#submenuWhatsapp">

            <i class="bi bi-whatsapp me-2"></i>
            Pesan Whatsapp
            <i class="bi bi-chevron-down ms-auto"></i>
        </a>

        <div class="collapse "
             id="submenuWhatsapp">

            <ul class="nav flex-column ms-3 border-start ps-2">

                <li>
                    <a class="nav-link text-white"
                       href="http://localhost:8080/admin/broadcast-view">
                        Tampilkan Pesan
                    </a>
                </li>

                <li>
                    <a class="nav-link text-white"
                       href="http://localhost:8080/admin/broadcast-create">
                        Buat Pesan
                    </a>
                </li>

            </ul>
        </div>

    </li>


    <!-- Pengaturan -->
    
    <li class="nav-item">

        <a class="nav-link d-flex align-items-center text-white"
           data-bs-toggle="collapse"
           href="#submenuPengaturan">

            <i class="bi bi-gear me-2"></i>
            Pengaturan
            <i class="bi bi-chevron-down ms-auto"></i>
        </a>

        <div class="collapse "
             id="submenuPengaturan">

            <ul class="nav flex-column ms-3 border-start ps-2">

                <li><a class="nav-link text-white" href="http://localhost:8080/admin/aplikasi">Aplikasi</a></li>

                <li><a class="nav-link text-white" href="http://localhost:8080/admin/upload">Upload Data</a></li>

                <li><a class="nav-link text-white" href="http://localhost:8080/admin/wapikey">Whatsapp API</a></li>

                <li><a class="nav-link text-white" href="http://localhost:8080/admin/qrcode">Generate QR</a></li>

            </ul>
        </div>

    </li>

    

    <!-- Logout -->
    <li class="nav-item mt-3">

        <a class="nav-link d-flex align-items-center text-white"
           href="/auth/logout">

            <i class="bi bi-box-arrow-right me-2"></i>
            Keluar

        </a>

    </li>

</ul>

