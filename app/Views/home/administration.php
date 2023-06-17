<div id="main">
    <div class="row">
        <div class="col s12">
            <div class="container">
                <div class="section">
                    <!--card stats start-->
                    <div id="card-stats" class="pt-0">
                        <div class="row">
                            <div class="col s12 pl-2 pr-2">
                                <br><br><br>
                                <div class="row">
                                    <div class="col s6">
                                       Bienvenido <?= strtoupper(session('user')->username) ?> - <?= session('user')->role_name ?>
                                    </div>
                                    <div class="col s6">
                                        <h6 style="color:#022858;" class="right">MODULOS</h6>
                                    </div>
                                </div>
                                <hr>
                               <!-- <div class="row center">
                                    <a href="#"  data-url="<?= base_url() ?>" class="module" data-position="">
                                        <div class="col s12 m3 l2" >
                                            <div class="card">
                                                <div class="card-content">
                                                    <div class="center">
                                                        <img src="<?= base_url('assets/img/inventario.png') ?>" alt="" style="width: 100%; display: block;">
                                                    </div>
                                                    <p class="center" style="color: #022858;"><b>Inventario</b></p>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                    <a href="#"  data-url="<?= base_url() ?>" class="module" data-position="">
                                        <div class="col s12 m3 l2" >
                                            <div class="card">
                                                <div class="card-content">
                                                    <div class="center">
                                                        <img src="<?= base_url('assets/img/gventa.png') ?>" alt="" style="width: 100%; display: block;">
                                                    </div>
                                                    <p class="center" style="color: #022858;"><b>Gestion de venta</b></p>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                    <a href="#"  data-url="<?= base_url() ?>" class="module" data-position="">
                                        <div class="col s12 m3 l2" >
                                            <div class="card">
                                                <div class="card-content">
                                                    <div class="center">
                                                        <img src="<?= base_url('assets/img/informes.png') ?>" alt="" style="width: 100%; display: block;">
                                                    </div>
                                                    <p class="center" style="color: #022858;"><b>Informes</b></p>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                        <a href="#"  data-url="<?= base_url() ?>" class="module" data-position="">
                                            <div class="col s12 m3 l2" >
                                                <div class="card">
                                                    <div class="card-content">
                                                        <div class="center">
                                                            <img src="<?= base_url('assets/img/soporte.png') ?>" alt="" style="width: 100%; display: block;">
                                                        </div>
                                                        <p class="center" style="color: #022858;"><b>Soporte</b></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>

                                </div> -->
                            </div>
                        </div>
                    </div>
                    <!--card stats end-->
                    <!--yearly & weekly revenue chart start-->
                </div><!-- START RIGHT SIDEBAR NAV -->
                <!-- END RIGHT SIDEBAR NAV -->
            </div>
            <div class="content-overlay"></div>
        </div>
    </div>
</div>