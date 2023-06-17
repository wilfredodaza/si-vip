<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="nevao.net">
    <meta name="keywords" content="">
    <meta name="author" content="IPLanetColomba S.A.S">
    <title>Home  - Loono</title>
    <link rel="stylesheet" type="text/css" href="<?= base_url('/css/vendors.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('/css/shepherd-theme-default.min.css') ?>">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <?php if(getenv("DEVELOPMENT") == 'false'): ?>
        <link rel="stylesheet" type="text/css" href="<?= base_url('/css/materialize_horizontal.css') ?>">
        <link rel="stylesheet" type="text/css" href="<?= base_url('/css/style_horizontal.css') ?>">
        <link rel="stylesheet" type="text/css" href="<?= base_url('/css/style-horizontal.css') ?>">
    <?php else: ?>
        <link rel="stylesheet" type="text/css" href="<?= base_url('/css/materialize_horizontal.css') ?>">
        <link rel="stylesheet" type="text/css" href="<?= base_url('/css/style_horizontal.css') ?>">
        <link rel="stylesheet" type="text/css" href="<?= base_url('/css/style-horizontal.css') ?>">
    <?php endif; ?>
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>/assets/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url('/css/styles_iplanet.css') ?>">
    <link rel="shortcut icon" href="<?= base_url('/assets/img/nevao.jpg') ?>" type="image/png">
    <link rel="stylesheet" href="<?= base_url('/assets/css/select2.min.css') ?>" type="text/css">
    <link rel="stylesheet" href="<?= base_url('/assets/css/select2-materialize.css') ?>" type="text/css">
    <link rel="stylesheet" type="text/css" href="<?= base_url('/assets/css/dashboard.css') ?>">
    <link rel="stylesheet" href="https://unpkg.com/materialize-stepper@3.1.0/dist/css/mstepper.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>/assets/css/custom.min.css">
    <meta http-equiv="Expires" content="0">
    <meta http-equiv="Last-Modified" content="0">
    <meta http-equiv="Cache-Control" content="no-cache, mustrevalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <link rel="stylesheet" href="<?= base_url('/app-assets/css/pages/intro.css') ?>" type="text/css">
    <?=  $this->renderSection('styles') ?>
    <style>
        .modal{
            width: 500px !important;
        }
    </style>
</head>
<body class="horizontal-layout page-header-light horizontal-menu preload-transitions 2-columns   " data-open="click" data-menu="horizontal-menu" data-col="2-columns">
<style>
    .body-notification p, .body-notification div {
        padding: 0px;
        margin: 0px;
    }
</style>


<header class="page-topbar" id="header">
    <div class="navbar navbar-fixed  z-depth-2">
        <nav class="navbar-main navbar-color nav-collapsible sideNav-lock navbar-dark z-depth-2 gradient-shadow" style="background:  #50D4F2;color:white !important;">
            <div class="nav-wrapper">
                <ul class="navbar-list left">
                    <li style="width: 100px;">
                        <h1 class="logo-wrapper">
                            <a class="brand-logo darken-1" href="index.html" style="padding-top: 11px ; padding-bottom: 11px;">
                                <img src="<?= base_url('') ?>" alt="materialize logo" style="height: 40px;">
                            </a>
                        </h1>
                    </li>
                </ul>
                <ul class="navbar-list right">
                    <?php if(getenv("DEVELOPMENT") == "true"):?>
                        <!--<li>
                            <a class="waves-effect waves-block waves-light " href="javascript:void(0);" >
                                <span  class="z-depth-1" style="background: indigo; color:white; padding: 5px 10px; border-radius:5px; font-size: 12px; ">|--- SISTEMA DE PRUEBAS ----|</span>
                            </a>
                        </li>-->
                    <?php endif; ?>
                    <li class="hide-on-med-and-down">
                        <a class="waves-effect waves-block waves-light module" data-url="<?= base_url('table/customers') ?>"  data-position="12" href="#"  style="height: 64px;">
                            <i class="material-icons">settings</i>
                        </a>
                    </li>
                    <li>
                        <a class="waves-effect waves-block waves-light notification-button" href="javascript:void(0);" data-target="notifications-dropdown">
                            <i class="material-icons">notifications_none
                                <?php
                                $m = 0;
                                foreach (notification('companies') as $item):
                                    if($item->view == 'false'):
                                        $m++;
                                    endif;
                                endforeach;
                                ?>
                                <small class="notification-badge"><?= $m ?></small>
                            </i>
                        </a>
                    </li>
                    <li>
                        <a class="waves-effect waves-block waves-light profile-button" href="javascript:void(0);" data-target="profile-dropdown">
                            <span class="avatar-status avatar-online">
                                <img  style="height: 29px !important;" src="<?= session('user') && session('user')->photo ? base_url().'/assets/upload/images/'.session('user')->photo : base_url().'/assets/img/'.'user.png' ?>" alt="avatar">
                            </span>
                            <small style="float: right; padding-left: 10px; font-size: 16px;" class="new badge"><?= session('user')->username ?></small>
                        </a>
                    </li>
                </ul>
                <ul class="dropdown-content" id="notifications-dropdown">
                    <li>
                        <h6>Notificaciones <span class="badge blue"><a href="<?= base_url('notification/index') ?>">Ver Todas</a></span></h6>
                    </li>
                    <li class="divider"></li>
                    <?php foreach (notification('companies') as $item): ?>
                        <?php  if($item->view != 'true'): ?>
                            <li class="notification-active" onclick="closeNotification(<?= $item->id ?>)"
                                data-id="<?= $item->id ?>">
                                <a class="black-text" href="<?= base_url($item->url) ?>">
                                    <span class="material-icons icon-bg-circle <?= $item->color ?> small"><?= $item->icon ?></span> <?= $item->title ?>
                                </a>
                                <time class="media-meta grey-text darken-2 "
                                      datetime="2015-06-12T20:50:48+08:00"><?= strip_tags($item->body) ?> <br>
                                    <div style="text-align: right"><?= $item->created_at ?></div>
                                </time>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <li class="divider"></li>
                </ul>
                <!-- profile-dropdown-->
                <ul class="dropdown-content" id="profile-dropdown">
                    <li>
                        <a class="grey-text text-darken-1" href="<?= base_url() ?>/perfile"><i class="material-icons">person_outline</i>Perfil</a>
                    </li>
                    <li>
                        <a class="grey-text text-darken-1" href="<?= base_url()?>/about"><i class="material-icons">help_outline</i> About</a>
                    </li>
                    <?php  if(session()->get('user')->role_id == 1): ?>
                        <li>
                            <a class="grey-text text-darken-1" href="<?= base_url()?>/config/configurations"><i class="material-icons">settings</i>Configure</a>
                        </li>
                        <li>
                            <a class="grey-text text-darken-1" href="<?= base_url()?>/config/modules"><i class="material-icons">contact_mail</i>Modulos</a>
                        </li>
                        <li>
                            <a class="grey-text text-darken-1" href="<?= base_url()?>/config/module_role"><i class="material-icons">contact_mail</i>Modulo R</a>
                        </li>
                        <li>
                            <a class="grey-text text-darken-1" href="<?= base_url()?>/config/menus"><i class="material-icons">menu</i>Menu</a>
                        </li>
                        <li>
                            <a class="grey-text text-darken-1" href="<?= base_url()?>/config/permissions"><i class="material-icons">lock_outline</i>Permisos</a>
                        </li>
                        <li>
                            <a class="grey-text text-darken-1" href="<?= base_url()?>/config/notifications"><i class="material-icons">contact_mail</i>Notificar</a>
                        </li>
                        <li class="divider"></li>
                    <?php  endif; ?>
                    <li>
                        <a class="grey-text text-darken-1" href="<?= base_url() ?>/logout"><i class="material-icons">keyboard_tab</i> Logout</a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</header>

<div class="row">
    <div class="col s12 pl-2 pr-2">
        <div class="row">
            <div class="col s6 m6">
                <h6 style="color:#022858;" class="left">MENU</h6>
            </div>
            <div class="col s6 m6">
                <a href="" class="right vertical-align mt-1" style="color: #17207a;">
                    Centro de ayuda
                    <svg xmlns="http://www.w3.org/2000/svg"  width="16" height="16" fill="currentColor" class="bi bi-question-octagon-fill" viewBox="0 0 16 16">
                        <path d="M11.46.146A.5.5 0 0 0 11.107 0H4.893a.5.5 0 0 0-.353.146L.146 4.54A.5.5 0 0 0 0 4.893v6.214a.5.5 0 0 0 .146.353l4.394 4.394a.5.5 0 0 0 .353.146h6.214a.5.5 0 0 0 .353-.146l4.394-4.394a.5.5 0 0 0 .146-.353V4.893a.5.5 0 0 0-.146-.353L11.46.146zM5.496 6.033a.237.237 0 0 1-.24-.247C5.35 4.091 6.737 3.5 8.005 3.5c1.396 0 2.672.73 2.672 2.24 0 1.08-.635 1.594-1.244 2.057-.737.559-1.01.768-1.01 1.486v.105a.25.25 0 0 1-.25.25h-.81a.25.25 0 0 1-.25-.246l-.004-.217c-.038-.927.495-1.498 1.168-1.987.59-.444.965-.736.965-1.371 0-.825-.628-1.168-1.314-1.168-.803 0-1.253.478-1.342 1.134-.018.137-.128.25-.266.25h-.825zm2.325 6.443c-.584 0-1.009-.394-1.009-.927 0-.552.425-.94 1.01-.94.609 0 1.028.388 1.028.94 0 .533-.42.927-1.029.927z"/>
                    </svg>
                </a>
            </div>
        </div>
        <hr>
        <div class="row center">
            <?php foreach($modules as $module): ?>
                <?php if($module->status == "Active"): ?>
                <a href="#"  data-url="<?= $module->url != '=soporte' ? base_url($module->url) : 'https://lonoo.co/support.php' ?>" class="module" data-position="<?= $module->id ?>">
                    <div class="col s6 m3 l2" >
                        <div class="card">
                            <div class="card-content">
                                <div class="center">
                                    <img src="<?= base_url('assets/img/'.$module->img) ?>" alt="" style="width: 100%; display: block;">
                                </div>
                                <p class="center" style="color: #022858; height: 40px;"><b><?= $module->name ?></b></p>
                            </div>
                        </div>
                    </div>
                </a>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>


<!--<div id="intro ">
    <div class="row">
        <div class="col s1">
            <div id="info-modal" class="modal white" tabindex="0" style="z-index: 1003; display: none; opacity: 1; top: 10%; transform: scaleX(1) scaleY(1); ">
                <div class="modal-content" style="padding: 0px !important;width:  500px !important; height: 500px !important;  overflow: clip; margin: 0px !important;">
                    <p class="modal-header right modal-close" style="position: absolute; z-index: 1004; color: white; right: 20px;">
                        Cerrar <span class="right"><i class="material-icons right-align">clear</i></span>
                    </p>
                    <img src="<?= base_url() ?>/assets/img/intro.png" alt="" class="" style="margin: 0px !important;">
                </div>
            </div>
        </div>
    </div>

</div>-->


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
    <script src="<?= base_url('app-assets/js/scripts/intro.js') ?>"></script>
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
    <?=  $this->renderSection('scripts') ?>
</body>
</html>
