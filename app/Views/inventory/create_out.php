<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?> Salida por remisiòn <?= $this->endSection() ?>
<?= $this->section('styles') ?>
<!-- <link rel="stylesheet" href="<?= base_url() ?>/assets/js/out_create_inventory/styles.523cab83d3477d6d96ff.css"> -->
<link rel="stylesheet" href="<?= base_url() ?>/assets/js/out_create_inventory/styles.css">
<link rel="stylesheet" href="<?= base_url('css/angular.css') ?>">
<script>
    localStorage.setItem('rol_id', <?= session('user')->role_id ?>);
</script>
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

<script src="<?= base_url() ?>/assets/js/out_create_inventory/runtime.js" defer></script>
<script src="<?= base_url() ?>/assets/js/out_create_inventory/polyfills.js" defer></script>
<script src="<?= base_url() ?>/assets/js/out_create_inventory/main.js" defer></script>
<!-- <script src="<?= base_url() ?>/assets/js/out_create_inventory/runtime.58e5c3603de5b46c8a86.js" defer></script>
<script src="<?= base_url() ?>/assets/js/out_create_inventory/polyfills.dacd6549d6eac8a326be.js" defer></script>
<script src="<?= base_url() ?>/assets/js/out_create_inventory/main.e83d5b624684afcbe17b.js" defer></script> -->
<script>localStorage.setItem('manager', <?= $manager ?>)</script>
<!--<script src="<?= base_url() ?>/assets/js/out_create_inventory/runtime.58e5c3603de5b46c8a86.js" defer></script>
<script src="<?= base_url() ?>/assets/js/out_create_inventory/polyfills.dacd6549d6eac8a326be.js" defer></script>
<script src="<?= base_url() ?>/assets/js/out_create_inventory/main.7a94be7428125c8c6d33.js" defer></script>-->

<?= $this->endSection() ?>
