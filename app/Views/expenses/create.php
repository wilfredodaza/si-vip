<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?> Gastos <?= $this->endSection() ?>
<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('js/angular/expenses_create/styles.523cab83d3477d6d96ff.css') ?>">
<link rel="stylesheet" href="<?= base_url('css/angular.css') ?>">
<?= $this->endSection() ?>
<?= $this->section('content') ?>

<div id="main">
    <div class="row">
        <div class="col s12">
            <div class="container">
                <app-root></app-root>
                <br>
                <br>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script src="<?= base_url('js/angular/expenses_create/runtime.58e5c3603de5b46c8a86.js') ?>" defer></script>
<script src="<?= base_url('js/angular/expenses_create/polyfills.dacd6549d6eac8a326be.js') ?>" defer></script>
<script src="<?= base_url('js/angular/expenses_create/main.1e69778c2de33b907ba0.js') ?>" defer></script>


<?= $this->endSection() ?>
