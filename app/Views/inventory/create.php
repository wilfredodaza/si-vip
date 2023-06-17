<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?> Entrada por remisiòn <?= $this->endSection() ?>
<?= $this->section('styles') ?>
<!-- <link rel="stylesheet" href="<?= base_url() ?>/assets/js/create_inventory_invoice/styles.25bb18442147bf115d76.css"> -->
<link rel="stylesheet" href="<?= base_url() ?>/assets/js/create_inventory_invoice/styles.css">
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


<script src="<?= base_url() ?>/assets/js/create_inventory_invoice/runtime.js" defer></script>
<script src="<?= base_url() ?>/assets/js/create_inventory_invoice/polyfills.js" defer></script>
<script src="<?= base_url() ?>/assets/js/create_inventory_invoice/main.js" defer></script>

<!-- <script src="<?= base_url() ?>/assets/js/create_inventory_invoice/runtime.58e5c3603de5b46c8a86.js" defer></script>
<script src="<?= base_url() ?>/assets/js/create_inventory_invoice/polyfills.dacd6549d6eac8a326be.js" defer></script>
<script src="<?= base_url() ?>/assets/js/create_inventory_invoice/main.fa90fd2da4b548e7323d.js" defer></script> -->

<?= $this->endSection() ?>
