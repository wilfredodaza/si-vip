<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="ltr">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Factural Electronica">
    <meta name="keywords" content="facturacion electronica, DIAN, habilitacion ante DIAN">
    <title><?= isset(configInfo()['name_app']) ? configInfo()['name_app'] : '' ?></title>
    <!--Styles-->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>/assets/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>/assets/css/materialize2.min.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>/assets/css/style.min.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>/assets/css/custom.min.css">
    <link rel="shortcut icon" href="<?= base_url('assets/img/42x42.png'); ?>">
    <!--end Styles-->
    <?=  $this->renderSection('styles') ?>
</head>
<body class="vertical-layout page-header-light vertical-menu-collapsible vertical-dark-menu preload-transitions 2-columns"
      data-open="click" data-menu="vertical-dark-menu" data-col="2-columns">


<?= $this->renderSection('content') ?>


<footer class="page-footer footer footer-static footer-light navbar-border navbar-shadow" style="position:fixed; bottom: 0px; width: 100%;">
    <div class="footer-copyright">
        <div class="container"><span><?= isset(configInfo()['footer']) ? configInfo()['footer'] : '' ?></span></div>
    </div>
</footer>
<script>localStorage.setItem('url', '<?= base_url() ?>')</script>
<script src="<?= base_url('js/vendors.min.js') ?>"></script>
<script src="<?= base_url('js/plugins.min.js') ?>"></script>
<script src="<?= base_url('js/search.min.js') ?>"></script>
<script src="<?= base_url('js/custom-script.min.js') ?>"></script>
<?=  $this->renderSection('scripts') ?>

</body>
</html>
