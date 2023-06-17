<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?> Transferencia <?= $this->endSection() ?>
<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url() ?>/assets/js/search_product/styles.b8b0f0f738614b819d82.css">
<link rel="stylesheet" href="<?= base_url('css/angular.css') ?>">
<?= $this->endSection() ?>
<?= $this->section('content') ?>

<div id="main">
    <div class="row">
        <div class="col s12">
            <div class="container">
                <app-root></app-root>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script src="<?= base_url() ?>/assets/js/search_product/runtime-es2015.a4dadbc03350107420a4.js" type="module"></script>
<script src="<?= base_url() ?>/assets/js/search_product/runtime-es5.a4dadbc03350107420a4.js" nomodule defer></script>
<script src="<?= base_url() ?>/assets/js/search_product/polyfills-es5.e1ecc41e594ac0f343b4.js" nomodule defer></script>
<script src="<?= base_url() ?>/assets/js/search_product/polyfills-es2015.75c375c674ece37157aa.js" type="module"></script>
<script src="<?= base_url() ?>/assets/js/search_product/main-es2015.34d7b4b5a613c0f96316.js" type="module"></script>
<script src="<?= base_url() ?>/assets/js/search_product/main-es5.34d7b4b5a613c0f96316.js" nomodule defer></script>

<?= $this->endSection() ?>

