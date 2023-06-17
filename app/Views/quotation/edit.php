<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?> Cotización <?= $this->endSection() ?>
<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/js/quotation_edit/styles.523cab83d3477d6d96ff.css') ?>">
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
<script src="<?= base_url('assets/js/quotation_edit/runtime.1306ddb3afdde7b8303d.js') ?>"></script>
<script src="<?= base_url('assets/js/quotation_edit/polyfills.dacd6549d6eac8a326be.js') ?>"></script>
<script src="<?= base_url('assets/js/quotation_edit/main.a60dfd56d4e4727f3f0d.js') ?>"></script>


<?= $this->endSection() ?>
