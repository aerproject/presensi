<?= $this->extend('layouts/siswa') ?>

<?= $this->section('content') ?>

<style>
    .izin-header {
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

    .izin-item {
        padding: 16px;
        border-bottom: 1px solid #edf0f4;
    }

    .izin-item:last-child {
        border-bottom: 0;
    }

    .izin-date {
        color: #667085;
        font-size: .8rem;
        margin-bottom: 8px;
    }

    .izin-message {
        color: #344054;
        font-size: .9rem;
        line-height: 1.55;
        margin-top: 10px;
        white-space: pre-line;
    }

    .izin-footer {
        margin-top: 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        flex-wrap: wrap;
    }

    .attachment-link {
        font-size: .8rem;
        text-decoration: none;
    }

    .empty-state {
        padding: 45px 15px;
        text-align: center;
        color: #667085;
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
?>

<!-- =====================================================
     HEADER
     ===================================================== -->

<div class="izin-header">

    <div class="fw-bold fs-5">
        Riwayat Pengajuan Izin
    </div>

    <div class="small opacity-75 mt-1">
        Surat izin dan sakit yang diajukan orang tua
    </div>

</div>


<!-- =====================================================
     INFORMASI SISWA
     ===================================================== -->

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


<!-- =====================================================
     FILTER
     ===================================================== -->

<div class="filter-card">

    <div class="filter-title">
        <i class="bi bi-funnel-fill me-1"></i>
        Filter Periode
    </div>

    <form
        method="get"
        action="<?= site_url('siswa/riwayat-izin') ?>">

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


<!-- =====================================================
     DAFTAR PENGAJUAN
     ===================================================== -->

<div class="izin-list">

    <div class="p-3 border-bottom">

        <strong>
            Pengajuan
            <?= esc($namaBulan[(int) $bulan] ?? '') ?>
            <?= esc($tahun) ?>
        </strong>

    </div>


    <?php if (!empty($riwayatIzin)): ?>

        <?php foreach ($riwayatIzin as $izin): ?>

            <?php
            $jenis = strtolower(trim($izin['jenis'] ?? ''));
            $status = strtolower(trim($izin['status'] ?? ''));

            if ($jenis === 'sakit') {
                $jenisLabel = 'Sakit';
                $jenisClass = 'bg-danger-subtle text-danger';
            } else {
                $jenisLabel = 'Izin';
                $jenisClass = 'bg-primary-subtle text-primary';
            }

            switch ($status) {

                case 'disetujui':
                    $statusLabel = 'Disetujui';
                    $statusClass = 'bg-success';
                    break;

                case 'ditolak':
                    $statusLabel = 'Ditolak';
                    $statusClass = 'bg-danger';
                    break;

                case 'ditangguhkan':
                    $statusLabel = 'Ditangguhkan';
                    $statusClass = 'bg-warning text-dark';
                    break;

                default:
                    $statusLabel = 'Diajukan';
                    $statusClass = 'bg-secondary';
                    break;
            }

            $tanggal = $izin['tanggal_absensi'] ?? null;

            if (
                $tanggal &&
                $tanggal !== '0000-00-00'
            ) {

                $timestamp = strtotime($tanggal);

                $tanggalTampil =
                    date('d', $timestamp)
                    . ' '
                    . ($namaBulan[(int) date('n', $timestamp)] ?? '')
                    . ' '
                    . date('Y', $timestamp);

            } else {

                $tanggalTampil = '-';

            }
            ?>

            <div class="izin-item">

                <div class="izin-date">

                    <i class="bi bi-calendar3 me-1"></i>

                    <?= esc($tanggalTampil) ?>

                </div>


                <div>

                    <span class="badge <?= esc($jenisClass) ?>">

                        <?= esc($jenisLabel) ?>

                    </span>

                </div>


                <div class="izin-message">

                    <?= esc($izin['isi_pesan'] ?? '-') ?>

                </div>


                <div class="izin-footer">

                    <div>

                        <div class="small text-muted mb-1">
                            Status pengajuan
                        </div>

                        <span class="badge <?= esc($statusClass) ?>">

                            <?= esc($statusLabel) ?>

                        </span>

                    </div>


                    <?php if (!empty($izin['upload_surat'])): ?>

                        <a
                            href="<?= base_url(
                                'uploads/surat/'
                                . rawurlencode($izin['upload_surat'])
                            ) ?>"
                            target="_blank"
                            rel="noopener"
                            class="attachment-link">

                            <i class="bi bi-paperclip me-1"></i>
                            Lihat surat

                        </a>

                    <?php endif; ?>

                </div>

            </div>

        <?php endforeach; ?>


        <!-- =================================================
             PAGINATION CI4.6
             ================================================= -->

        <?php if (!empty($pager)): ?>

            <div class="p-3 border-top">

                <?= $pager->links('izin', 'bootstrap') ?>

            </div>

        <?php endif; ?>


    <?php else: ?>

        <div class="empty-state">

            <i class="bi bi-envelope-paper fs-1 d-block mb-3"></i>

            <div class="fw-semibold">
                Belum ada pengajuan
            </div>

            <div class="small mt-1">
                Tidak ada surat izin atau sakit
                pada periode yang dipilih.
            </div>

        </div>

    <?php endif; ?>

</div>

<?= $this->endSection() ?>
