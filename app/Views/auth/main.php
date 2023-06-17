<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="MiFacturaLegal.com">
    <meta name="keywords" content="DIAN, Factura electrónica, Nomina Electrónica">
    <meta name="author" content="IPLanetColomba S.A.S">
    <title><?= $this->renderSection('title') ?>   - MiFacturaLegal.com</title>
    <link rel="stylesheet" type="text/css" href="<?= base_url('/assets/css/vendors.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('/assets/css/materialize.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('/assets/css/style.min.css') ?>">
    <link rel="shortcut icon" href="<?= base_url('/assets/img/favicon.png') ?>" type="image/png">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <?=  $this->renderSection('styles') ?>
</head>
<body class="vertical-layout page-header-light vertical-menu-collapsible vertical-dark-menu preload-transitions 1-column login-bg   blank-page blank-page"
      data-open="click" data-menu="vertical-dark-menu" data-col="1-column">

    
    <?= $this->renderSection('content') ?>



    <script>localStorage.setItem('url', '<?= base_url() ?>')</script>
    <script src="<?= base_url() ?>/assets/js/vendors.min.js"></script>
    <script src="<?= base_url() ?>/assets/js/plugins.min.js"></script>
    <?=  $this->renderSection('scripts') ?>
</body>
</html>

