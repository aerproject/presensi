<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>


<style>
/* =========================================================
   PLOT SISWA - LAYOUT DESKTOP
   ========================================================= */

.plot-siswa-layout {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
    gap: 18px;
    align-items: start;
}

/* Panel kiri dan kanan */
.plot-siswa-panel {
    min-width: 0;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    background: #fff;
    overflow: hidden;
}

/* Header panel */
.plot-siswa-panel-header {
    padding: 12px 14px;
    border-bottom: 1px solid #dee2e6;
    background: #f8f9fa;
}

.plot-siswa-panel-header h5 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
}

/* Isi panel */
.plot-siswa-panel-body {
    padding: 14px;
}

/* =========================================================
   DAFTAR SISWA
   ========================================================= */

.plot-siswa-list {
    border: 1px solid #dee2e6;
    border-radius: 6px;
    overflow-y: auto;
    max-height: 430px;
    background: #fff;
}

/* SATU BARIS SISWA */
.plot-siswa-row {
    display: flex;
    align-items: center;
    gap: 10px;

    /* INI YANG MEMBUAT CHECKBOX TIDAK MENEMPEL KIRI */
    padding: 10px 14px;

    min-height: 42px;
    border-bottom: 1px solid #e9ecef;
    cursor: pointer;
    margin: 0;
}

.plot-siswa-row:last-child {
    border-bottom: none;
}

/* Checkbox */
.plot-siswa-row input[type="checkbox"] {
    flex: 0 0 auto;
    width: 18px;
    height: 18px;
    margin: 0;
    cursor: pointer;
}

/* Teks siswa */
.plot-siswa-row .siswa-label {
    flex: 1;
    min-width: 0;
    line-height: 1.4;
    cursor: pointer;
    color: #212529;
}

/* Hover */
.plot-siswa-row:hover {
    background: #f8f9fa;
}

/* =========================================================
   SELECT ALL
   ========================================================= */

.plot-siswa-select-all {
    display: flex;
    align-items: center;
    gap: 9px;
    padding: 8px 4px 12px;
    font-weight: 600;
}

.plot-siswa-select-all input[type="checkbox"] {
    width: 18px;
    height: 18px;
    margin: 0;
}

/* =========================================================
   PANEL KANAN
   ========================================================= */

.plot-siswa-right {
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.plot-siswa-form-group {
    margin-bottom: 0;
}

.plot-siswa-form-group label {
    display: block;
    margin-bottom: 6px;
    font-weight: 600;
}

/* =========================================================
   RINGKASAN
   ========================================================= */

.plot-siswa-summary {
    padding: 12px 14px;
    border: 1px solid #b6d4fe;
    border-radius: 6px;
    background: #cfe2ff;
    color: #084298;
}

.plot-siswa-summary strong {
    font-weight: 700;
}

/* =========================================================
   DAFTAR SISWA TERPILIH
   ========================================================= */

.plot-siswa-selected-list {
    border: 1px solid #dee2e6;
    border-radius: 6px;
    max-height: 250px;
    overflow-y: auto;
    background: #fff;
}

.plot-siswa-selected-item {
    padding: 9px 12px;
    border-bottom: 1px solid #e9ecef;
    font-size: 14px;
}

.plot-siswa-selected-item:last-child {
    border-bottom: none;
}

/* =========================================================
   RESPONSIVE
   ========================================================= */

@media (max-width: 900px) {
    .plot-siswa-layout {
        grid-template-columns: 1fr;
    }
}


/* =========================================================
   FIX: PADDING KHUSUS BARIS DAFTAR SISWA
   ========================================================= */

.siswa-item.border-bottom {
    padding-left: 12px;
    padding-right: 12px;
}

.siswa-item.border-bottom .form-check {
    padding-left: 0 !important;
    padding-right: 0 !important;
    margin: 0;
}

.siswa-item.border-bottom .form-check-input {
    margin-left: 0;
    margin-right: 10px;
}

.siswa-item.border-bottom .form-check-label {
    padding-left: 0;
    cursor: pointer;
}


/* =========================================================
   FIX DAFTAR SISWA
   Checkbox + NIS + Nama + Tahun Masuk = SATU BARIS
   ========================================================= */

.siswa-item.border-bottom {
    padding-left: 12px;
    padding-right: 12px;
}

.siswa-item.border-bottom .form-check {
    display: flex !important;
    align-items: center;
    flex-wrap: nowrap !important;
    width: 100%;
    min-height: 40px;
    margin: 0;
    padding: 7px 0 !important;
}

.siswa-item.border-bottom .form-check-input {
    position: static !important;
    flex: 0 0 auto;
    margin: 0 10px 0 0 !important;
}

.siswa-item.border-bottom .form-check-label {
    display: block !important;
    flex: 1 1 auto;
    width: auto !important;
    margin: 0 !important;
    padding: 0 !important;
    white-space: nowrap !important;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.4;
}

</style>


<div class="container-fluid py-3">

    <!-- =========================================================
         HEADER
    ========================================================== -->

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="mb-1"><?= esc($title) ?></h3>

            <?php if (isset($tapel)): ?>
                <div class="text-muted">
                    Tahun Pelajaran Aktif:
                    <strong>
                        <?= esc($tapel['tahun_pelajaran']) ?>
                        -
                        <?= esc($tapel['semester']) ?>
                    </strong>
                </div>
            <?php endif; ?>
        </div>

    </div>


    <!-- =========================================================
         FLASH MESSAGE
    ========================================================== -->

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= esc(session()->getFlashdata('success')) ?>

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= esc(session()->getFlashdata('error')) ?>

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>
        </div>
    <?php endif; ?>


    <!-- =========================================================
         FORM
    ========================================================== -->

    <form action="<?= base_url('admin/siswa-akademik/store') ?>"
          method="post"
          id="formPlotSiswa">

        <?= csrf_field() ?>

        <div class="row g-3">

            <!-- =================================================
                 KOLOM KIRI
            ================================================== -->

            <div class="col-lg-7">

                <div class="card shadow-sm h-100">

                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">

                            <div>
                                <strong>Daftar Siswa</strong>

                                <div class="small opacity-75">
                                    Hanya siswa yang belum diplot
                                </div>
                            </div>

                            <span class="badge bg-light text-dark"
                                  id="jumlahDipilih">
                                0 dipilih
                            </span>

                        </div>
                    </div>


                    <div class="card-body">

                        <!-- SEARCH -->

                        <div class="mb-3">

                            <label for="searchSiswa"
                                   class="form-label fw-semibold">
                                Cari Siswa
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    🔎
                                </span>

                                <input type="text"
                                       id="searchSiswa"
                                       class="form-control"
                                       placeholder="Cari NIS atau nama siswa...">

                                <button type="button"
                                        class="btn btn-outline-secondary"
                                        id="clearSearch">
                                    Reset
                                </button>

                            </div>

                        </div>


                        <!-- SELECT ALL -->

                        <div class="d-flex justify-content-between
                                    align-items-center mb-2">

                            <div class="form-check">

                                <input type="checkbox"
                                       class="form-check-input"
                                       id="selectAll">

                                <label class="form-check-label fw-semibold"
                                       for="selectAll">
                                    Pilih semua yang tampil
                                </label>

                            </div>

                            <small class="text-muted">
                                <?= count($siswa ?? []) ?> siswa tersedia
                            </small>

                        </div>


                        <!-- LIST SISWA -->

                        <div id="daftarSiswa"
                             class="border rounded"
                             style="max-height: 520px; overflow-y: auto;">

                            <?php if (!empty($siswa)): ?>

                                <?php foreach ($siswa as $row): ?>

                                    <div class="siswa-item border-bottom"
                                         data-search="<?= esc(
                                             strtolower(
                                                 ($row['nis'] ?? '') . ' ' .
                                                 ($row['nama_siswa'] ?? '') . ' ' .
                                                 ($row['tahun_masuk'] ?? '')
                                             ),
                                             'attr'
                                         ) ?>">

                                        <div class="form-check px-3 py-2">

                                            <input
                                                class="form-check-input siswa-checkbox"
                                                type="checkbox"
                                                name="siswa_id[]"
                                                value="<?= esc($row['id']) ?>"
                                                id="siswa_<?= esc($row['id']) ?>">

                                            <label
                                                class="form-check-label w-100"
                                                for="siswa_<?= esc($row['id']) ?>"
                                                style="cursor:pointer;">

                                                <?= esc($row['nis']) ?>
                                                -
                                                <?= esc($row['nama_siswa']) ?>
                                                -
                                                <?= esc($row['tahun_masuk'] ?? '-') ?>

                                            </label>

                                        </div>

                                    </div>

                                <?php endforeach; ?>

                            <?php else: ?>

                                <div class="text-center text-muted py-5">

                                    <div class="fs-1 mb-2">
                                        ✓
                                    </div>

                                    <div class="fw-semibold">
                                        Semua siswa sudah diplot
                                    </div>

                                    <small>
                                        Tidak ada siswa yang tersedia
                                        untuk Tahun Pelajaran aktif.
                                    </small>

                                </div>

                            <?php endif; ?>

                        </div>

                    </div>

                </div>

            </div>


            <!-- =================================================
                 KOLOM KANAN
            ================================================== -->

            <div class="col-lg-5">

                <div class="card shadow-sm">

                    <div class="card-header bg-success text-white">
                        <strong>Penempatan Siswa</strong>
                    </div>

                    <div class="card-body">

                        <!-- KELAS -->

                        <div class="mb-3">

                            <label for="kelas_id"
                                   class="form-label fw-semibold">
                                Kelas
                            </label>

                            <select name="kelas_id"
                                    id="kelas_id"
                                    class="form-select"
                                    required>

                                <option value="">
                                    Pilih Kelas
                                </option>

                                <?php foreach ($kelas as $k): ?>

                                    <option value="<?= esc($k['id']) ?>">
                                        <?= esc($k['nama_kelas']) ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <!-- JURUSAN -->

                        <div class="mb-3">

                            <label for="jurusan_id"
                                   class="form-label fw-semibold">
                                Jurusan
                            </label>

                            <select name="jurusan_id"
                                    id="jurusan_id"
                                    class="form-select"
                                    required>

                                <option value="">
                                    Pilih Jurusan
                                </option>

                                <?php foreach ($jurusan as $j): ?>

                                    <option value="<?= esc($j['id']) ?>">
                                        <?= esc($j['nama_jurusan']) ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <!-- JAM BELAJAR -->

                        <div class="mb-4">

                            <label for="jam_belajar_id"
                                   class="form-label fw-semibold">
                                Jam Belajar / Shift
                            </label>

                            <select name="jam_belajar_id"
                                    id="jam_belajar_id"
                                    class="form-select"
                                    required>

                                <option value="">
                                    Pilih Shift
                                </option>

                                <?php foreach ($jam_belajar as $jb): ?>

                                    <option value="<?= esc($jb['id']) ?>">

                                        <?= esc(ucfirst($jb['shift'])) ?>

                                        (
                                        <?= esc($jb['jam_masuk']) ?>
                                        -
                                        <?= esc($jb['jam_pulang']) ?>
                                        )

                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <!-- RINGKASAN -->

                        <div class="alert alert-info">

                            <div class="fw-semibold mb-1">
                                Ringkasan Penempatan
                            </div>

                            <div class="small">

                                Siswa dipilih:
                                <strong id="summaryJumlah">
                                    0
                                </strong>

                            </div>

                            <div class="small">
                                Kelas:
                                <strong id="summaryKelas">
                                    -
                                </strong>
                            </div>

                            <div class="small">
                                Jurusan:
                                <strong id="summaryJurusan">
                                    -
                                </strong>
                            </div>

                        </div>


                        <!-- BUTTON -->

                        <button type="submit"
                                class="btn btn-success w-100"
                                id="btnSimpan">

                            Simpan Penempatan

                        </button>

                        <a href="<?= base_url('admin/siswa-akademik') ?>"
                           class="btn btn-outline-secondary w-100 mt-2">

                            Batal / Kembali

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </form>

</div>


<!-- =============================================================
     JAVASCRIPT
============================================================== -->

<script>

document.addEventListener('DOMContentLoaded', function () {

    const searchInput = document.getElementById('searchSiswa');
    const clearSearch = document.getElementById('clearSearch');
    const selectAll = document.getElementById('selectAll');

    const siswaItems = document.querySelectorAll('.siswa-item');
    const checkboxes = document.querySelectorAll('.siswa-checkbox');

    const jumlahDipilih = document.getElementById('jumlahDipilih');
    const summaryJumlah = document.getElementById('summaryJumlah');

    const kelasSelect = document.getElementById('kelas_id');
    const jurusanSelect = document.getElementById('jurusan_id');

    const summaryKelas = document.getElementById('summaryKelas');
    const summaryJurusan = document.getElementById('summaryJurusan');

    function updateCounter() {

        const checked = document.querySelectorAll(
            '.siswa-checkbox:checked'
        ).length;

        jumlahDipilih.textContent = checked + ' dipilih';
        summaryJumlah.textContent = checked;

    }


    function updateSelectAllState() {

        const visibleCheckboxes = [];

        siswaItems.forEach(function (item) {

            if (item.style.display !== 'none') {

                const checkbox = item.querySelector(
                    '.siswa-checkbox'
                );

                if (checkbox) {
                    visibleCheckboxes.push(checkbox);
                }

            }

        });


        if (visibleCheckboxes.length === 0) {

            selectAll.checked = false;
            selectAll.indeterminate = false;
            return;

        }


        const checkedCount = visibleCheckboxes.filter(
            checkbox => checkbox.checked
        ).length;


        selectAll.checked =
            checkedCount === visibleCheckboxes.length;

        selectAll.indeterminate =
            checkedCount > 0 &&
            checkedCount < visibleCheckboxes.length;

    }


    function filterSiswa() {

        const keyword =
            searchInput.value
                .toLowerCase()
                .trim();


        siswaItems.forEach(function (item) {

            const searchText =
                item.dataset.search || '';

            item.style.display =
                searchText.includes(keyword)
                    ? ''
                    : 'none';

        });


        updateSelectAllState();

    }


    searchInput.addEventListener(
        'input',
        filterSiswa
    );


    clearSearch.addEventListener(
        'click',
        function () {

            searchInput.value = '';

            filterSiswa();

            searchInput.focus();

        }
    );


    selectAll.addEventListener(
        'change',
        function () {

            siswaItems.forEach(function (item) {

                if (item.style.display !== 'none') {

                    const checkbox =
                        item.querySelector(
                            '.siswa-checkbox'
                        );

                    if (checkbox) {
                        checkbox.checked =
                            selectAll.checked;
                    }

                }

            });

            updateCounter();

        }
    );


    checkboxes.forEach(function (checkbox) {

        checkbox.addEventListener(
            'change',
            function () {

                updateCounter();
                updateSelectAllState();

            }
        );

    });


    kelasSelect.addEventListener(
        'change',
        function () {

            const option =
                kelasSelect.options[
                    kelasSelect.selectedIndex
                ];

            summaryKelas.textContent =
                option && option.value
                    ? option.text
                    : '-';

        }
    );


    jurusanSelect.addEventListener(
        'change',
        function () {

            const option =
                jurusanSelect.options[
                    jurusanSelect.selectedIndex
                ];

            summaryJurusan.textContent =
                option && option.value
                    ? option.text
                    : '-';

        }
    );


    document.getElementById(
        'formPlotSiswa'
    ).addEventListener(
        'submit',
        function (event) {

            const selected =
                document.querySelectorAll(
                    '.siswa-checkbox:checked'
                ).length;


            if (selected === 0) {

                event.preventDefault();

                alert(
                    'Pilih minimal 1 siswa terlebih dahulu.'
                );

                return;

            }


            if (!kelasSelect.value) {

                event.preventDefault();

                alert(
                    'Silakan pilih kelas.'
                );

                kelasSelect.focus();

                return;

            }


            if (!jurusanSelect.value) {

                event.preventDefault();

                alert(
                    'Silakan pilih jurusan.'
                );

                jurusanSelect.focus();

                return;

            }


            if (!document.getElementById(
                'jam_belajar_id'
            ).value) {

                event.preventDefault();

                alert(
                    'Silakan pilih jam belajar / shift.'
                );

                return;

            }

        }
    );


    updateCounter();
    updateSelectAllState();

});

</script>

<?= $this->endSection() ?>
