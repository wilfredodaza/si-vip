<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Lonoo">
    <meta name="keywords" content="DIAN, Factura electrónica, Nomina Electrónica">
    <meta name="author" content="IPLanetColomba S.A.S">
    <title><?= $this->renderSection('title') ?> Nevao</title>
    <link rel="stylesheet" type="text/css" href="<?= base_url('/css/vendors.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('/css/shepherd-theme-default.min.css') ?>">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <?php if(getenv("DEVELOPMENT") == 'false'): ?>
        <link rel="stylesheet" type="text/css" href="<?= base_url('/css/materialize.min.css') ?>">
        <link rel="stylesheet" type="text/css" href="<?= base_url('/css/style.min.css') ?>">
    <?php else: ?>
        <link rel="stylesheet" type="text/css" href="<?= base_url('/css/materialize-development.min.css') ?>">
        <link rel="stylesheet" type="text/css" href="<?= base_url('/css/style-development.min.css') ?>">
    <?php endif; ?>
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>/assets/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url('/css/styles_iplanet.css') ?>">
    <link rel="shortcut icon" href="<?= base_url('assets/img/icono-32x32.png'); ?>">
    <link rel="stylesheet" href="<?= base_url('/assets/css/select2.min.css') ?>" type="text/css">
    <link rel="stylesheet" href="<?= base_url('/assets/css/select2-materialize.css') ?>" type="text/css">
    <link rel="stylesheet" type="text/css" href="<?= base_url('/assets/css/dashboard.css') ?>">
    <link rel="stylesheet" href="https://unpkg.com/materialize-stepper@3.1.0/dist/css/mstepper.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>/assets/css/custom.min.css">
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-L98EZDF67Z"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-L98EZDF67Z');
    </script>

    <meta http-equiv="Expires" content="0">
    <meta http-equiv="Last-Modified" content="0">
    <meta http-equiv="Cache-Control" content="no-cache, mustrevalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <?=  $this->renderSection('styles') ?>
</head>
<body>
<?= (!isset($_GET['shop']))?$this->include('layouts/navbar_horizontal'):'' ?>
<?= (!isset($_GET['shop']))?$this->include('layouts/navbar_vertical'):'' ?>

<?= $this->renderSection('content') ?>


<footer class="page-footer footer footer-static footer-light navbar-border navbar-shadow" style="position:fixed; bottom: 0px; width: 100%; z-index:1;">
    <div class="footer-copyright">
        <div class="container"><span><?= isset(configInfo()['footer']) ? configInfo()['footer'] : '' ?></span></div>
    </div>
</footer>


<script>localStorage.setItem('url', '<?= base_url() ?>')</script>
<script src="<?= base_url('js/vendors.min.js') ?>"></script>
<script src="<?= base_url('/assets/js/plugins.min.js') ?>"></script>
<script src="<?= base_url('/assets/js/select2.full.min.js') ?>"></script>
<script src="https://unpkg.com/materialize-stepper@3.1.0/dist/js/mstepper.min.js"></script>
<?=  $this->renderSection('scripts') ?>
<script>
    $(document).ready(function() {
        $('.module').click(function (e){
            const position = $(this).data('position');
            const url = $(this).data('url');
            $.get(`${localStorage.getItem('url')}/module/ubication/${position}`, function( data ) {
                window.location.href = url;
            });
            e.preventDefault();
        });
    });
</script>
</body>
</html>




