<?php $pager->setSurroundCount(2) ?>
    <ul class="pagination">
        <?php if ($pager->hasPrevious()) : ?>
            <li>
                <a href="<?= $pager->getFirst() ?>" aria-label="<?= 'Primero' ?>">
                    <span aria-hidden="true"><?= 'Primero' ?></span>
                </a>
            </li>
            <li>
                <a href="<?= $pager->getPrevious() ?>" aria-label="<?= 'Anterior' ?>">
                    <span aria-hidden="true"><?= 'Anterior' ?></span>
                </a>
            </li>
        <?php endif ?>

        <?php foreach ($pager->links() as $link) : ?>
            <li <?= $link['active'] ? 'class="active"' : '' ?>>
                <a href="<?= $link['uri'] ?>">
                    <?= $link['title'] ?>
                </a>
            </li>
        <?php endforeach ?>

        <?php if ($pager->hasNext()) : ?>
            <li>
                <a href="<?= $pager->getNext() ?>" aria-label="<?= 'Siguiente'  ?>">
                    <span aria-hidden="true"><?= 'Siguiente' ?></span>
                </a>
            </li>
            <li>
                <a href="<?= $pager->getLast() ?>" aria-label="<?= 'Ultimo'?>">
                    <span aria-hidden="true"><?= 'Ultimo'?></span>
                </a>
            </li>
        <?php endif ?>
    </ul>
