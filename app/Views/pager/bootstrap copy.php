<?php $pager->setSurroundCount(1) ?>

<?php
$query = '';

if (isset($_GET['keyword'])) {
    $query .= '&keyword=' . urlencode($_GET['keyword']);
}

if (isset($_GET['perPage'])) {
    $query .= '&perPage=' . urlencode($_GET['perPage']);
}
?>

<nav aria-label="Page navigation">

    <ul class="pagination justify-content-center">

        <?php if ($pager->hasPrevious()) : ?>

            <li class="page-item">

                <a class="page-link"
                   href="<?= $pager->getPrevious() . $query ?>">

                    Prev

                </a>

            </li>

        <?php else: ?>

            <li class="page-item disabled">

                <span class="page-link">Prev</span>

            </li>

        <?php endif; ?>


        <?php foreach ($pager->links() as $link): ?>

            <li class="page-item <?= $link['active'] ? 'active' : '' ?>">

                <a class="page-link"
                   href="<?= $link['uri'] . $query ?>">

                    <?= $link['title'] ?>

                </a>

            </li>

        <?php endforeach; ?>


        <?php if ($pager->hasNext()) : ?>

            <li class="page-item">

                <a class="page-link"
                   href="<?= $pager->getNext() . $query ?>">

                    Next

                </a>

            </li>

        <?php else: ?>

            <li class="page-item disabled">

                <span class="page-link">Next</span>

            </li>

        <?php endif; ?>

    </ul>

</nav>