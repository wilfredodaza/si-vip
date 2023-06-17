<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="MiFacturaLegal.com">
    <meta name="keywords" content="DIAN, Factura electrónica, Nomina Electrónica">
    <meta name="author" content="IPLanetColomba S.A.S">
    <title><?= $this->renderSection('title') ?>   - Nevado lab</title>
    <link rel="stylesheet" type="text/css" href="<?= base_url('/css/vendors.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('/css/shepherd-theme-default.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('/css/materialize.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('/css/style.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('/css/styles_iplanet.css') ?>">
    <link rel="shortcut icon" href="<?= base_url('/images/favicon.png') ?>" type="image/png">
    <?=  $this->renderSection('styles') ?>
</head>
<body>

<?= $this->renderSection('content') ?>

<script>localStorage.setItem('url', '<?= base_url() ?>')</script>
<script src="<?= base_url('js/vendors.min.js') ?>"></script>
<script src="<?= base_url() ?>/assets/js/plugins.min.js"></script>
<?=  $this->renderSection('scripts') ?>
</body>
</html>
