<?php $this->extend('layouts/mainchat') ?>

<?php $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('/css/dropzone.min.css') ?>" type="text/css">
<link rel="stylesheet" href="<?= base_url('/css/styles_iplanet.css') ?>" type="text/css">
<link rel="stylesheet" href="<?= base_url('/css/select2.min.css') ?>"  type="text/css">
<link rel="stylesheet" href="<?= base_url('/css/select2-materialize.css') ?>" type="text/css">
<link rel="stylesheet" type="text/css" href="https://mischats.com/supportboard/css/tickets.css">
<link href="https://mischats.com/supportboard/media/icons/png/style.css" rel="stylesheet" type="text/css">
<link href="https://mischats.com/supportboard/media/icons/png/style-a.css" rel="stylesheet" type="text/css">
<?php $this->endSection() ?>


<?php $this->section('content') ?>
<div id="">
    <div id="sb-tickets">
</div>
<?php $this->endSection() ?>

<?php $this->section('scripts')?>
    <script src = "https://mischats.com/supportboard/js/main.js?mode=tickets"> </script>
    <?php $this->endSection()?>
