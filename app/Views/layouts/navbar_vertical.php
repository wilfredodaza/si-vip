<aside class="sidenav-main nav-expanded nav-lock nav-collapsible  <?= getenv("DEVELOPMENT") == 'true' ? '' : 'sidenav-light' ?> navbar-full sidenav-active-rounded sidenav-light " style="background: #50D4F2 !important;">
    <div class="brand-sidebar "  style="background: #50D4F2;">
        <h1 class="logo-wrapper">
            <a class="brand-logo darken-1" href="<?= base_url('home') ?>" style="padding-top: 11px ; padding-bottom: 11px;">
                <img class="hide-on-med-and-down"  src="<?= base_url('/assets/img/logo-menu-horizontal.png') ?>" alt="materialize logo" style=" height: 40px;display: block;">
                <a class="navbar-toggler" href="#">
                    <i class="material-icons">radio_button_checked</i>
                </a>
            </a>
        </h1>
    </div>
    <ul  class="sidenav sidenav-collapsible leftside-navigation collapsible sidenav-fixed menu-shadow" id="slide-out" data-menu="menu-navigation" data-collapsible="menu-accordion">
        <li>
            <div class="user-view" style="height: 170px !important;">
                <div class="background" style="margin:0px;">
                    <img src="<?= base_url() ?>/assets/img/<?= configInfo()['logo_menu'] ?>" style="width: 100%; height: 100%">
                </div>
                <a href="#user" style="margin-right: 0px;"><div class="circle"  style="width: 50px; height:50px;"
                                                                src=""></div></a>
                <a href="#name" style="margin-right: 0px;"><small class="white-text name" style=" font-size: 12px !important;"><?= session('user')->name ?></small></a>
                <a href="#email" style="margin-right: 0px;" ><small class="white-text email" style="padding: 0px;"><?= session('user')->role_name  ?></small></a>
            </div>
        </li>
        <li class="navigation-header">
            <a class="navigation-header-text" style=" white-space: nowrap; overflow: hidden; text-overflow:ellipsis;  "><?= isset(company()->company) ? company()->company  : 'Administador' ?>
            </a><i class="navigation-header-icon material-icons">more_horiz</i>
        </li>
        <?php foreach (menu() as $item): ?>
            <li class="bold <?= isActive(urlOption($item->id)); ?>"><a class="waves-effect waves-cyan  <?= isActive(urlOption($item->id)); ?> <?= countMenu($item->id) ? 'collapsible-header' : ''; ?>"

                                                                       href="<?= countMenu($item->id) ? urlOption() :  urlOption($item->id) ?>"><i
                            class="material-icons"><?= $item->icon ?></i><span class="menu-title" data-i18n="Calendar"><?= $item->option ?></span></a>

                <?php if (countMenu($item->id)): ?>
                    <div class="collapsible-body">
                        <ul class="collapsible collapsible-sub" data-collapsible="accordion">
                            <?php foreach (submenu($item->id) as $submenu): ?>
                                <li class="<?= isActive(urlOption($submenu->id)); ?>"><a href="<?= urlOption($submenu->id) ?>" class="<?= isActive(urlOption($submenu->id)); ?>"><i
                                                class="material-icons">radio_button_unchecked</i><span
                                                data-i18n="Modern"><?= $submenu->option ?></span></a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
        <?php if(session('user')->role_id == 5): ?>
            <li class="bold <?= base_url(uri_string()) == base_url().'/home' ?'active':  '' ?>"><a class="waves-effect waves-cyan <?= base_url(uri_string()) == base_url().'/home' ? 'active': '' ?> " href="<?= base_url() ?>/home"><i
                            class="material-icons">settings_input_svideo</i><span class="menu-title" data-i18n="Calendar">Actualizar datos</span></a>
            </li>
        <?php endif; ?>
    </ul>
    <div class="navigation-background"></div>
    <a class="sidenav-trigger btn-sidenav-toggle btn-floating btn-medium waves-effect waves-light hide-on-large-only" href="#" data-target="slide-out" style="background:#17207A;">
        <i class="material-icons">menu</i>
    </a>
</aside>