<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?> Documento Soporte de Ajuste <?= $this->endSection() ?>
<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/angular/document_support_adjust_create/styles.523cab83d3477d6d96ff.css') ?>">
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
<script>localStorage.setItem('id', <?= $id ?>)</script>
<script src="<?= base_url('js/angular/document_support_adjust_create/runtime.58e5c3603de5b46c8a86.js') ?>" defer></script>
<script src="<?= base_url('js/angular/document_support_adjust_create/polyfills.dacd6549d6eac8a326be.js') ?>" defer></script>
<script src="<?= base_url('js/angular/document_support_adjust_create/main.a03f9bc80e5ca52170a5.js') ?>" defer></script>
<?= $this->endSection() ?>
