<style>
    .body-notification p, .body-notification div {
        padding: 0px;
        margin: 0px;
    }

    ul.sidenav  li a.active {
        background: #50D4F2 !important;
        box-shadow: 3px 3px 20px 0 rgb(80 212 242 / 50%) !important;
    }
</style>

<header class="page-topbar" id="header">
    <div class="navbar navbar-fixed">
        <nav class="navbar-main navbar-color nav-collapsible sideNav-lock gradient-shadow" style="background:  #50D4F2;">
            <div class="nav-wrapper">
                <ul class="navbar-list right">

                    <?php if(getenv("DEVELOPMENT") == "true"):?>
                        <!--<li>
                            <a class="waves-effect waves-block waves-light " href="javascript:void(0);" >
                                <span  class="z-depth-1" style="background: indigo; color:white; padding: 5px 10px; border-radius:5px; font-size: 12px; ">|--- SISTEMA DE PRUEBAS ----|</span>
                            </a>
                        </li>-->
                    <?php endif; ?>
                    <li>
                        <a class="waves-effect waves-block waves-light " href="<?= base_url('home')?>" >
                                <span  class="z-depth-1">
                                    <button class="btn " style="background: #022858">menu</button>
                                </span>
                        </a>
                    </li>
                    <li class="hide-on-med-and-down">
                        <a class="waves-effect waves-block waves-light module" data-url="<?= session('module') != 12 ? base_url('table/customers') : $_SERVER['HTTP_REFERER'] ?>"  data-position="<?= session('module') != 12 ? 12 : session('module_after')?>" href="#"  style="height: 64px;">
                            <?php if(session('module') != 12): ?><i class="material-icons">settings</i><?php else: ?><i class="material-icons">keyboard_return</i><?php endif; ?>
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
                            <li class="notification-active"  data-id="<?= $item->id ?>">
                                <a class="black-text" href="#">
                                    <span class="material-icons icon-bg-circle <?= $item->color ?> small"><?= $item->icon ?></span> <?= $item->title ?>
                                </a>
                                <time class="media-meta grey-text darken-2 " datetime="2015-06-12T20:50:48+08:00"><?= strip_tags($item->body) ?> <br>
                                    <div style="text-align: right"><?= $item->created_at     ?></div>
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

                    <?php if(session()->get('user')->role_id <= 2): ?>
                        <!--<li>
                            <a class="grey-text text-darken-1" href="<?= base_url()?>/roles"><i class="material-icons">face</i>Roles</a>
                        </li>
                        <li>
                            <a class="grey-text text-darken-1" href="<?= base_url()?>/config/users"><i class="material-icons">peoples</i>Usuarios</a>
                        </li>-->
                    <?php endif; ?>
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