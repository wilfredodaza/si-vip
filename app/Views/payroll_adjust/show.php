<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?> Nomina de Ajuste <?= $this->endSection() ?>

<?= $this->section('styles') ?>
    <link rel="stylesheet" href="<?= base_url() ?>/js/views/payroll_adjust/styles.425139f0e808a4615233.css">
<?php $this->endSection() ?>

<?php $this->section('content') ?>
    <div id="main">
        <div class="row">
            <div class="col s12">
                <div class="container">
                    <div class="section">
                        <app-root></app-root>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->endSection() ?>

<?php $this->section('scripts'); ?>
    <script src="<?= base_url('js/shepherd.min.js') ?>"></script>
    <script>localStorage.setItem('id', <?= $id ?>);</script>


<script src="<?= base_url() ?>/js/views/payroll_adjust/runtime-es2015.0c9827f3240b7afa6fbd.js" type="module"></script>
<script src="<?= base_url() ?>/js/views/payroll_adjust/runtime-es5.0c9827f3240b7afa6fbd.js" nomodule defer></script>
<script src="<?= base_url() ?>/js/views/payroll_adjust/polyfills-es5.84b570ed6dddec529dbf.js" nomodule defer></script>
<script src="<?= base_url() ?>/js/views/payroll_adjust/polyfills-es2015.f32360df441376d2c1a7.js" type="module"></script>
<script src="<?= base_url() ?>/js/views/payroll_adjust/main-es2015.7eb3d1b9a91424dd909d.js" type="module"></script>
<script src="<?= base_url() ?>/js/views/payroll_adjust/main-es5.7eb3d1b9a91424dd909d.js" nomodule defer></script>


<?php $this->endSection(); ?>