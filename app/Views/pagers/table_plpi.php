<?php

use CodeIgniter\Pager\PagerRenderer;

/**
 * @var PagerRenderer $pager
 */
$pager->setSurroundCount(2);
?>

<?php if ($pager->getPageCount() > 1): ?>
<div class="table-pagination-nav">
    <?php if ($pager->hasPrevious()): ?>
        <a class="table-page-btn" href="<?= $pager->getFirst() ?>" aria-label="Halaman pertama">&laquo;</a>
        <a class="table-page-btn" href="<?= $pager->getPrevious() ?>" aria-label="Sebelumnya">&lsaquo;</a>
    <?php else: ?>
        <span class="table-page-btn disabled">&laquo;</span>
        <span class="table-page-btn disabled">&lsaquo;</span>
    <?php endif; ?>

    <?php foreach ($pager->links() as $link): ?>
        <?php if ($link['active']): ?>
            <span class="table-page-btn current"><?= $link['title'] ?></span>
        <?php else: ?>
            <a class="table-page-btn" href="<?= $link['uri'] ?>"><?= $link['title'] ?></a>
        <?php endif; ?>
    <?php endforeach; ?>

    <?php if ($pager->hasNext()): ?>
        <a class="table-page-btn" href="<?= $pager->getNext() ?>" aria-label="Berikutnya">&rsaquo;</a>
        <a class="table-page-btn" href="<?= $pager->getLast() ?>" aria-label="Halaman terakhir">&raquo;</a>
    <?php else: ?>
        <span class="table-page-btn disabled">&rsaquo;</span>
        <span class="table-page-btn disabled">&raquo;</span>
    <?php endif; ?>
</div>
<?php endif; ?>
