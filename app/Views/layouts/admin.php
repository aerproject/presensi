<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Admin Panel') ?> | Absensiku</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icon -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <!-- CSS -->
    <link href="<?= base_url('css/style.css') ?>?v=<?= time() ?>" rel="stylesheet">
</head>
<body>

<?= $this->include('_partials/navbar') ?>

<div class="layout-wrapper">
    <?= $this->include('_partials/sidebar') ?>

    <main class="content-wrapper">
        <?= $this->renderSection('content') ?>
    </main>
</div>

<footer class="footer">
    © <?= date('Y') ?> AerProject. All rights reserved.
</footer>

<!-- JQUERY -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- extra script dari view -->
<?= $this->renderSection('scripts') ?>

</body>
</html>