<?= $this->extend('layouts/siswa') ?>

<?= $this->section('content') ?>

<style>
    .kehadiran-header {
        background: linear-gradient(135deg, #0d6efd, #1769e0);
        color: #fff;
        border-radius: 16px;
        padding: 18px;
        margin-bottom: 14px;
    }
    
    .student-info,
    .filter-card,
    .izin-list {
        background: #fff;
        border: 1px solid #e9edf3;
        border-radius: 14px;
        box-shadow: 0 3px 12px rgba(0,0,0,.05);
    }

    .student-info {
        padding: 15px;
        margin-bottom: 14px;
    }

    .student-name {
        font-weight: 700;
        font-size: 1rem;
    }

    .student-detail {
        color: #667085;
        font-size: .84rem;
        line-height: 1.6;
    }
    
    .filter-card {
        padding: 15px;
        margin-bottom: 14px;
    }

    .filter-title {
        font-weight: 700;
        margin-bottom: 12px;
    }

</style>

<?php
$namaBulan = [
    1  => 'Januari',
    2  => 'Februari',
    3  => 'Maret',
    4  => 'April',
    5  => 'Mei',
    6  => 'Juni',
    7  => 'Juli',
    8  => 'Agustus',
    9  => 'September',
    10 => 'Oktober',
    11 => 'November',
    12 => 'Desember',
];

$bulanAktif = (int) ($bulan ?? date('n'));
$tahunAktif = (int) ($tahun ?? date('Y'));

$labelBulan = $namaBulan[$bulanAktif] ?? '-';
?>

<div class="container-fluid px-0">

    <!-- HEADER -->
<div class="kehadiran-header">
        <div class="fw-bold fs-5">
            Riwayat Kehadiran
        </div>

        <div class="small opacity-75 mt-1">
            Rekap kehadiran siswa berdasarkan data akademik aktif
        </div>
    </div>


    <!-- IDENTITAS SISWA -->
    <div class="student-info">
    
        <div class="student-name">
            <?= esc($student['nama_siswa'] ?? '-') ?>
        </div>
    
        <div class="student-detail">
    
            NIS:
            <?= esc($student['nis'] ?? '-') ?>
    
            <br>
    
            <?= esc($student['nama_kelas'] ?? '-') ?>
    
            &nbsp;•&nbsp;
    
            <?= esc($student['nama_jurusan'] ?? '-') ?>
    
        </div>
    
    </div>



    <!-- FILTER -->
<div class="filter-card">

    <div class="filter-title">
        <i class="bi bi-funnel-fill me-1"></i>
        Filter Periode
    </div>

    <form
        method="get"
        action="<?= site_url('siswa/riwayat-kehadiran') ?>">

        <div class="row g-2">

            <div class="col-6">

                <label class="form-label small">
                    Bulan
                </label>

                <select
                    name="bulan"
                    class="form-select">

                    <?php foreach ($namaBulan as $nomor => $nama): ?>

                        <option
                            value="<?= $nomor ?>"
                            <?= ((int) $bulan === $nomor) ? 'selected' : '' ?>>

                            <?= esc($nama) ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <div class="col-6">

                <label class="form-label small">
                    Tahun
                </label>

                <select
                    name="tahun"
                    class="form-select">

                    <?php
                    $tahunSekarang = (int) date('Y');
                    ?>

                    <?php for (
                        $y = $tahunSekarang - 2;
                        $y <= $tahunSekarang + 1;
                        $y++
                    ): ?>

                        <option
                            value="<?= $y ?>"
                            <?= ((int) $tahun === $y) ? 'selected' : '' ?>>

                            <?= $y ?>

                        </option>

                    <?php endfor; ?>

                </select>

            </div>


            <div class="col-12 mt-2">

                <button
                    type="submit"
                    class="btn btn-primary w-100">

                    <i class="bi bi-search me-1"></i>
                    Tampilkan

                </button>

            </div>

        </div>

    </form>

</div>


    <!-- REKAP -->
    <div class="row g-2 mb-3">

        <div class="col-6 col-md-3">

            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">

                    <div class="text-success fs-4 fw-bold">
                        <?= (int) ($rekap['hadir'] ?? 0) ?>
                    </div>

                    <div class="small text-muted">
                        Hadir
                    </div>

                </div>
            </div>

        </div>


        <div class="col-6 col-md-3">

            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">

                    <div class="text-primary fs-4 fw-bold">
                        <?= (int) ($rekap['izin'] ?? 0) ?>
                    </div>

                    <div class="small text-muted">
                        Izin
                    </div>

                </div>
            </div>

        </div>


        <div class="col-6 col-md-3">

            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">

                    <div class="text-warning fs-4 fw-bold">
                        <?= (int) ($rekap['sakit'] ?? 0) ?>
                    </div>

                    <div class="small text-muted">
                        Sakit
                    </div>

                </div>
            </div>

        </div>


        <div class="col-6 col-md-3">

            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">

                    <div class="text-danger fs-4 fw-bold">
                        <?= (int) ($rekap['alpha'] ?? 0) ?>
                    </div>

                    <div class="small text-muted">
                        Alpha
                    </div>

                </div>
            </div>

        </div>

    </div>


    <!-- TABEL -->
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white border-0 pt-3">

            <div class="fw-bold">
                Kehadiran <?= esc($labelBulan) ?> <?= esc($tahunAktif) ?>
            </div>

            <div class="small text-muted mt-1">
                Data kehadiran siswa aktif
            </div>

        </div>


        <div class="card-body p-0">

            <?php if (empty($riwayatKehadiran)): ?>

                <div class="text-center text-muted py-5 px-3">

                    <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>

                    Tidak ada data kehadiran pada
                    <strong>
                        <?= esc($labelBulan) ?>
                        <?= esc($tahunAktif) ?>
                    </strong>.

                </div>

            <?php else: ?>

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">

                            <tr>

                                <th class="text-center text-nowrap" style="width: 52px;">No</th>

                                  <th class="text-nowrap">
                                    Tanggal
                                </th>

                                <th>
                                    Status
                                </th>

                                <th class="text-nowrap">
                                    Masuk
                                </th>

                                <th class="text-nowrap">
                                    Pulang
                                </th>

                                <th>
                                    Keterangan
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                          <?php
                          $currentPage = isset($pager)
                              ? (int) $pager->getCurrentPage('kehadiran')
                              : 1;

                          $perPage = 10;

                          $nomorAwal = (($currentPage - 1) * $perPage) + 1;
                          ?>

                          <?php foreach ($riwayatKehadiran as $index => $row): ?>

                            <?php
                            $status = strtolower(
                                trim($row['status'] ?? '')
                            );

                            $badgeClass = match ($status) {
                                'hadir' => 'bg-success',
                                'izin'  => 'bg-primary',
                                'sakit' => 'bg-warning text-dark',
                                'alpha' => 'bg-danger',
                                default => 'bg-secondary',
                            };

                            $tanggal = $row['tanggal'] ?? '';

                            $tanggalTampil = $tanggal;

                            if ($tanggal !== '') {
                                $timestamp = strtotime($tanggal);

                                if ($timestamp !== false) {
                                    $namaBulanIndonesia = [
                                          1  => 'Januari',
                                          2  => 'Februari',
                                          3  => 'Maret',
                                          4  => 'April',
                                          5  => 'Mei',
                                          6  => 'Juni',
                                          7  => 'Juli',
                                          8  => 'Agustus',
                                          9  => 'September',
                                          10 => 'Oktober',
                                          11 => 'November',
                                          12 => 'Desember',
                                      ];

                                      $tanggalTampil = date('d-m-Y', $timestamp);
                                }
                            }
                            ?>

                            
                                  <td class="text-center text-muted">
                                      <?= $nomorAwal + $index ?>
                                  </td>

                                  <td class="text-nowrap">
                                      <?= esc($tanggalTampil) ?>
                                  </td>

                                <td>
                                    <span
                                        class="badge <?= $badgeClass ?>">

                                        <?= esc(
                                            ucfirst($status ?: '-')
                                        ) ?>

                                    </span>
                                </td>

                                <td class="text-nowrap">
                                    <?= esc(
                                        $row['jam_masuk'] ?? '-'
                                    ) ?>
                                </td>

                                <td class="text-nowrap">
                                    <?= esc(
                                        $row['jam_pulang'] ?? '-'
                                    ) ?>
                                </td>

                                <td>
                                    <?= esc(
                                        $row['keterangan'] ?? '-'
                                    ) ?>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>


                <!-- PAGINATION -->
                <?php if (isset($pager)): ?>

                    <div class="p-3 border-top">

                        <?= $pager->links('kehadiran', 'bootstrap') ?>

                    </div>

                <?php endif; ?>

            <?php endif; ?>

        </div>

    </div>

</div>

<?= $this->endSection() ?>
