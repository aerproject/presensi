<?= $this->extend('layouts/ortu') ?>

<?= $this->section('content') ?>

<style>
    .pesan-header {
        background: linear-gradient(135deg, #0d6efd, #1769e0);
        color: #fff;
        border-radius: 16px;
        padding: 18px;
        margin-bottom: 14px;
    }

    .student-info,
    .filter-info,
    .pesan-list {
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

    .filter-info {
        padding: 15px;
        margin-bottom: 14px;
    }

    .filter-title {
        font-weight: 700;
        margin-bottom: 5px;
    }

    .filter-description {
        color: #667085;
        font-size: .82rem;
    }

    .pesan-item {
        padding: 16px;
        border-bottom: 1px solid #edf0f4;
    }

    .pesan-item:last-child {
        border-bottom: 0;
    }

    .pesan-date {
        color: #667085;
        font-size: .8rem;
        margin-bottom: 9px;
    }

    .pesan-content {
        color: #344054;
        font-size: .9rem;
        line-height: 1.6;
        white-space: pre-line;
    }

    .pesan-icon {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #eaf2ff;
        color: #0d6efd;
        flex-shrink: 0;
    }

    .pesan-body {
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .empty-state {
        padding: 45px 15px;
        text-align: center;
        color: #667085;
    }
</style>


<!-- =====================================================
     HEADER
     ===================================================== -->

<div class="pesan-header">

    <div class="fw-bold fs-5">
        Pesan &amp; Notifikasi
    </div>

    <div class="small opacity-75 mt-1">
        Pesan yang berkaitan dengan kehadiran siswa
    </div>

</div>


<!-- =====================================================
     INFORMASI SISWA
     ===================================================== -->

<div class="student-info">

    <div class="student-name">
        <?= esc($namaSiswa ?? '-') ?>
    </div>

    <div class="student-detail">

        <?= esc($namaKelas ?? '-') ?>

        &nbsp;•&nbsp;

        <?= esc($namaJurusan ?? '-') ?>

    </div>

</div>


<!-- =====================================================
     INFORMASI PESAN
     ===================================================== -->

<div class="filter-info">

    <div class="filter-title">
        <i class="bi bi-bell-fill me-1"></i>
        Notifikasi Kehadiran Siswa
    </div>

    <div class="filter-description">
        Riwayat pesan dan notifikasi yang berkaitan dengan kehadiran siswa.
    </div>

</div>


<!-- =====================================================
     DAFTAR PESAN
     ===================================================== -->

<div class="pesan-list">

    <?php if (!empty($riwayatMessage)): ?>

        <?php foreach ($riwayatMessage as $row): ?>

            <?php
            $tanggal = $row['created_at'] ?? null;

            if (
                $tanggal &&
                $tanggal !== '0000-00-00 00:00:00'
            ) {

                $timestamp = strtotime($tanggal);

                $tanggalTampil = (
                    $timestamp !== false
                )
                    ? date('d-m-Y H:i', $timestamp)
                    : '-';

            } else {

                $tanggalTampil = '-';

            }

            $pesan = $row['message']
                ?? $row['pesan']
                ?? '-';
            ?>


            <div class="pesan-item">

                <div class="pesan-body">

                    <div class="pesan-icon">

                        <i class="bi bi-bell"></i>

                    </div>


                    <div class="flex-grow-1">

                        <div class="pesan-date">

                            <i class="bi bi-calendar3 me-1"></i>

                            <?= esc($tanggalTampil) ?>

                        </div>


                        <div class="pesan-content">

                            <?= esc($pesan) ?>

                        </div>

                    </div>

                </div>

            </div>

        <?php endforeach; ?>


        <!-- =================================================
             PAGINATION
             ================================================= -->

        <?php if (!empty($pager)): ?>

            <div class="p-3 border-top">

                <?= $pager->links('message', 'bootstrap') ?>

            </div>

        <?php endif; ?>


    <?php else: ?>

        <div class="empty-state">

            <i class="bi bi-bell-slash fs-1 d-block mb-3"></i>

            <div class="fw-semibold">
                Belum ada pesan atau notifikasi
            </div>

            <div class="small mt-1">
                Belum ada pesan yang berkaitan dengan kehadiran siswa.
            </div>

        </div>

    <?php endif; ?>

</div>

<?= $this->endSection() ?>
