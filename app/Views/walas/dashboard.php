<?= $this->extend('layouts/walas') ?>
<?= $this->section('content') ?>

<style>
  /* =========================================================
     DASHBOARD WALI KELAS
     ========================================================= */

  .walas-dashboard {
    max-width: 1200px;
    margin: 0 auto;
  }

  /* Hero */
  .walas-hero {
    position: relative;
    overflow: hidden;
    border-radius: 18px;
    padding: 24px 26px;
    margin-bottom: 22px;
    background: linear-gradient(135deg, #172033 0%, #263b5e 100%);
    color: #fff;
    box-shadow: 0 8px 24px rgba(0, 0, 0, .12);
  }

  .walas-hero::after {
    content: "";
    position: absolute;
    width: 180px;
    height: 180px;
    right: -60px;
    top: -70px;
    border-radius: 50%;
    background: rgba(255, 255, 255, .08);
  }

  .walas-hero-icon {
    width: 52px;
    height: 52px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 14px;
    background: rgba(255, 255, 255, .13);
    font-size: 25px;
    margin-bottom: 12px;
  }

  .walas-hero h3 {
    margin: 0 0 5px;
    font-weight: 700;
    font-size: 1.35rem;
  }

  .walas-hero p {
    margin: 0;
    opacity: .82;
    font-size: .92rem;
  }

  /* Section title */
  .walas-section-title {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0 0 12px;
    font-size: 1rem;
    font-weight: 700;
    color: #263238;
  }

  .walas-section-title i {
    font-size: 1.1rem;
  }

  /* Informasi kelas */
  .kelas-card {
    border: 0;
    border-radius: 18px;
    overflow: hidden;
    background: #fff;
    box-shadow: 0 6px 20px rgba(0, 0, 0, .08);
    margin-bottom: 24px;
  }

  .kelas-card-header {
    padding: 17px 20px;
    border-bottom: 1px solid #edf0f3;
    background: #fff;
  }

  .kelas-card-header h5 {
    margin: 0;
    font-weight: 700;
    font-size: 1rem;
  }

  .kelas-card-body {
    padding: 18px 20px;
  }

  .kelas-main {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 18px;
  }

  .kelas-icon {
    flex: 0 0 52px;
    width: 52px;
    height: 52px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 15px;
    background: #eef4ff;
    color: #0d6efd;
    font-size: 24px;
  }

  .kelas-name {
    font-size: 1.2rem;
    font-weight: 700;
    line-height: 1.2;
    color: #172033;
  }

  .kelas-jurusan {
    margin-top: 4px;
    color: #6c757d;
    font-size: .88rem;
  }

  .kelas-stat {
    border: 1px solid #edf0f3;
    border-radius: 13px;
    padding: 12px 14px;
    background: #fafbfc;
  }

  .kelas-stat-label {
    color: #6c757d;
    font-size: .78rem;
    margin-bottom: 3px;
  }

  .kelas-stat-value {
    font-size: 1rem;
    font-weight: 700;
    color: #212529;
  }

  /* Ranking */
  .ranking-card {
    height: 100%;
    border: 0;
    border-radius: 17px;
    overflow: hidden;
    background: #fff;
    box-shadow: 0 6px 20px rgba(0, 0, 0, .07);
  }

  .ranking-header {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 15px 17px;
    color: #fff;
    font-weight: 700;
  }

  .ranking-header i {
    font-size: 1.1rem;
  }

  .ranking-alpha {
    background: linear-gradient(135deg, #dc3545, #b42333);
  }

  .ranking-izin {
    background: linear-gradient(135deg, #f5a900, #d88b00);
  }

  .ranking-sakit {
    background: linear-gradient(135deg, #0dcaf0, #087f9c);
  }

  .ranking-list {
    padding: 7px 10px 10px;
  }

  .ranking-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 11px 8px;
    border-bottom: 1px solid #f0f1f3;
  }

  .ranking-item:last-child {
    border-bottom: 0;
  }

  .ranking-number {
    width: 30px;
    height: 30px;
    flex: 0 0 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 9px;
    background: #f1f3f5;
    color: #495057;
    font-size: .78rem;
    font-weight: 700;
  }

  .ranking-name {
    flex: 1;
    min-width: 0;
    font-size: .9rem;
    font-weight: 600;
    color: #343a40;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .ranking-total {
    min-width: 30px;
    padding: 4px 8px;
    border-radius: 20px;
    background: #f1f3f5;
    text-align: center;
    font-size: .75rem;
    font-weight: 700;
    color: #495057;
  }

  .ranking-empty {
    padding: 25px 15px;
    text-align: center;
    color: #8a9299;
    font-size: .85rem;
  }

  .ranking-empty i {
    display: block;
    font-size: 25px;
    margin-bottom: 7px;
    opacity: .65;
  }

  /* Mobile */
  @media (max-width: 767.98px) {

    .walas-dashboard {
      width: 100%;
    }

    .walas-hero {
      border-radius: 15px;
      padding: 20px;
      margin-bottom: 18px;
    }

    .walas-hero-icon {
      width: 46px;
      height: 46px;
      font-size: 21px;
    }

    .walas-hero h3 {
      font-size: 1.12rem;
    }

    .walas-hero p {
      font-size: .82rem;
    }

    .kelas-card {
      border-radius: 15px;
      margin-bottom: 18px;
    }

    .kelas-card-header,
    .kelas-card-body {
      padding: 15px;
    }

    .kelas-main {
      margin-bottom: 14px;
    }

    .kelas-name {
      font-size: 1.05rem;
    }

    .ranking-card {
      margin-bottom: 14px;
    }

    .ranking-header {
      padding: 13px 15px;
    }

    .ranking-list {
      padding: 5px 8px 8px;
    }
  }
</style>

<div class="walas-dashboard">

  <!-- =======================================================
       HERO
       ======================================================== -->
  <div class="walas-hero">

    <div class="walas-hero-icon">
      <i class="bi bi-person-workspace"></i>
    </div>

    <h3>
      Halo, Bpk/Ibu <?= esc($walikelas['nama_walas']) ?> 👋
    </h3>

    <p>
      Selamat datang di Dashboard Wali Kelas.
      Pantau kondisi kehadiran siswa kelas Anda dengan mudah.
    </p>

  </div>


  <!-- =======================================================
       INFORMASI KELAS
       ======================================================== -->
  <?php if ($kelas): ?>

    <div class="walas-section-title">
      <i class="bi bi-mortarboard-fill"></i>
      Informasi Kelas
    </div>

    <div class="kelas-card">

      <div class="kelas-card-header">
        <h5>
          <i class="bi bi-building me-2"></i>
          Kelas yang Anda Kelola
        </h5>
      </div>

      <div class="kelas-card-body">

        <div class="kelas-main">

          <div class="kelas-icon">
            <i class="bi bi-people-fill"></i>
          </div>

          <div>
            <div class="kelas-name">
              <?= esc($kelas['nama_kelas']) ?>
            </div>

            <div class="kelas-jurusan">
              <?= esc($jurusan['nama_jurusan'] ?? '-') ?>
            </div>
          </div>

        </div>

        <div class="row g-2">

          <div class="col-12 col-sm-6">

            <div class="kelas-stat">

              <div class="kelas-stat-label">
                <i class="bi bi-diagram-3 me-1"></i>
                Jurusan
              </div>

              <div class="kelas-stat-value">
                <?= esc($jurusan['nama_jurusan'] ?? '-') ?>
              </div>

            </div>

          </div>

          <div class="col-12 col-sm-6">

            <div class="kelas-stat">

              <div class="kelas-stat-label">
                <i class="bi bi-person-lines-fill me-1"></i>
                Jumlah Siswa
              </div>

              <div class="kelas-stat-value">
                <?= count($siswa) ?> orang
              </div>

            </div>

          </div>

        </div>

      </div>

    </div>

  <?php endif; ?>


  <!-- =======================================================
       RANKING ABSENSI
       ======================================================== -->
  <div class="walas-section-title">
    <i class="bi bi-bar-chart-fill"></i>
    Ringkasan Kehadiran Siswa
  </div>

  <div class="row g-3">

    <!-- TOP ALPHA -->
    <div class="col-12 col-md-4">

      <div class="ranking-card">

        <div class="ranking-header ranking-alpha">
          <i class="bi bi-x-circle-fill"></i>
          <span>Top Alpha</span>
        </div>

        <div class="ranking-list">

          <?php if (!empty($alphaTop)): ?>

            <?php $no = 1; ?>

            <?php foreach ($alphaTop as $row): ?>

              <div class="ranking-item">

                <div class="ranking-number">
                  <?= $no++ ?>
                </div>

                <div class="ranking-name">
                  <?= esc($row['nama_siswa']) ?>
                </div>

                <div class="ranking-total">
                  <?= esc($row['total']) ?>
                </div>

              </div>

            <?php endforeach; ?>

          <?php else: ?>

            <div class="ranking-empty">
              <i class="bi bi-check-circle"></i>
              Belum ada data alpha.
            </div>

          <?php endif; ?>

        </div>

      </div>

    </div>


    <!-- TOP IZIN -->
    <div class="col-12 col-md-4">

      <div class="ranking-card">

        <div class="ranking-header ranking-izin">
          <i class="bi bi-envelope-paper-fill"></i>
          <span>Top Izin</span>
        </div>

        <div class="ranking-list">

          <?php if (!empty($izinTop)): ?>

            <?php $no = 1; ?>

            <?php foreach ($izinTop as $row): ?>

              <div class="ranking-item">

                <div class="ranking-number">
                  <?= $no++ ?>
                </div>

                <div class="ranking-name">
                  <?= esc($row['nama_siswa']) ?>
                </div>

                <div class="ranking-total">
                  <?= esc($row['total']) ?>
                </div>

              </div>

            <?php endforeach; ?>

          <?php else: ?>

            <div class="ranking-empty">
              <i class="bi bi-check-circle"></i>
              Belum ada data izin.
            </div>

          <?php endif; ?>

        </div>

      </div>

    </div>


    <!-- TOP SAKIT -->
    <div class="col-12 col-md-4">

      <div class="ranking-card">

        <div class="ranking-header ranking-sakit">
          <i class="bi bi-heart-pulse-fill"></i>
          <span>Top Sakit</span>
        </div>

        <div class="ranking-list">

          <?php if (!empty($sakitTop)): ?>

            <?php $no = 1; ?>

            <?php foreach ($sakitTop as $row): ?>

              <div class="ranking-item">

                <div class="ranking-number">
                  <?= $no++ ?>
                </div>

                <div class="ranking-name">
                  <?= esc($row['nama_siswa']) ?>
                </div>

                <div class="ranking-total">
                  <?= esc($row['total']) ?>
                </div>

              </div>

            <?php endforeach; ?>

          <?php else: ?>

            <div class="ranking-empty">
              <i class="bi bi-check-circle"></i>
              Belum ada data sakit.
            </div>

          <?php endif; ?>

        </div>

      </div>

    </div>

  </div>

</div>

<?= $this->endSection() ?>
