<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?> Facturación <?= $this->endSection() ?>

<?= $this->section('styles') ?>
    <link rel="stylesheet" href="<?= base_url() ?>/assets/js/payroll_edit/styles.67fa5cbb78e20607ec25.css">
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
<?=  $this->endSection() ?>


<?= $this->section('scripts') ?>
    <?= '<script>window.localStorage.setItem("id", "'.$id.'")</script>'?>
    <script src="<?= base_url() ?>/assets/js/payroll_edit/runtime-es2015.cdfb0ddb511f65fdc0a0.js" type="module"></script>
    <script src="<?= base_url() ?>/assets/js/payroll_edit/runtime-es5.cdfb0ddb511f65fdc0a0.js" nomodule defer></script>
    <script src="<?= base_url() ?>/assets/js/payroll_edit/polyfills-es5.0290b245fbcca09184ac.js" nomodule defer></script>
    <script src="<?= base_url() ?>/assets/js/payroll_edit/polyfills-es2015.ffa9bb4e015925544f91.js" type="module"></script>
    <script src="<?= base_url() ?>/assets/js/payroll_edit/scripts.44020321371347107e71.js" defer></script>
    <script src="<?= base_url() ?>/assets/js/payroll_edit/main-es2015.267cb8bb642b87fd99c6.js" type="module"></script>
    <script src="<?= base_url() ?>/assets/js/payroll_edit/main-es5.267cb8bb642b87fd99c6.js" nomodule defer></script>

<?=  $this->endSection() ?>