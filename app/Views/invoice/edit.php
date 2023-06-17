<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?> Factura Electrónica <?= $this->endSection() ?>
<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('js/edit_invoice/styles.523cab83d3477d6d96ff.css') ?>">
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

<script>localStorage.setItem('id', <?= $id ?>)</script>
<script src="<?= base_url('/js/edit_invoice/runtime.58e5c3603de5b46c8a86.js') ?>"></script>
<script src="<?= base_url('/js/edit_invoice/polyfills.dacd6549d6eac8a326be.js') ?>"></script>
<script src="<?= base_url('/js/edit_invoice/main.96c72b79a52a07f7f603.js') ?>"></script>


<?= $this->endSection() ?>



