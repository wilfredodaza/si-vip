<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?> Cotización <?= $this->endSection() ?>
<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('/assets/js/quotation/styles.523cab83d3477d6d96ff.css') ?>">
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


<script src="<?= base_url('/assets/js/quotation/runtime.8a0d85ac5d6090c1b45f.js') ?>"></script>
<script src="<?= base_url('/assets/js/quotation/polyfills.f3159b30db96eccd9ca2.js') ?>"></script>
<script src="<?= base_url('/assets/js/quotation/main.73d14d50b526fd70508c.js') ?>"></script>


<?= $this->endSection() ?>
