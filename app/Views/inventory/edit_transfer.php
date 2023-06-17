<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?> Transferencia <?= $this->endSection() ?>
<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url() ?>/assets/js/edit_out_transfer/styles.523cab83d3477d6d96ff.css">
<link rel="stylesheet" href="<?= base_url('css/angular.css') ?>">
<?= $this->endSection() ?>
<?= $this->section('content') ?>


<div id="main">
    <div class="row">
        <div class="col s12">
            <div class="container">
                <app-root></app-root>
                <br><br><br>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>

<?= '<script>window.localStorage.setItem("id", "'.$id.'")</script>'?>
<script src="<?= base_url() ?>/assets/js/edit_out_transfer/runtime.58e5c3603de5b46c8a86.js" defer></script>
<script src="<?= base_url() ?>/assets/js/edit_out_transfer/polyfills.dacd6549d6eac8a326be.js" defer></script>
<script src="<?= base_url() ?>/assets/js/edit_out_transfer/main.b2d38db478d9083b2eaa.js" defer></script>

<?= $this->endSection() ?>
