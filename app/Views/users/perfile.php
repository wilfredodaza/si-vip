<?= $this->extend('layouts/main') ?>


<?= $this->section('title') ?> Perfil <?= $this->endSection() ?>


<?= $this->section('styles') ?>
<link rel="stylesheet" type="text/css" href="<?= base_url('css/pricing.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php



setlocale(LC_TIME, 'spanish'); ?>
<div id="main">
    <div class="row">
        <div class="col s12">
            <div class="container">
                <div class="section">
                    <div class="section" id="user-profile">
                        <div class="row">
                            <div class="col s12  <?= session('user')->role_id == 2 ? 'm8 l9' : 'm12 l12' ?>">
                                <div class="row">
                                    <div class="col s12">
                                        <div class="card user-card-negative-margin z-depth-1" id="feed">
                                            <div class="card-content card-border-gray">
                                                <div class="row">
                                                    <div class="col s12">
                                                        <h5>IPlanetColombia S.A.S</h5>
                                                        <p>Administrador</p>
                                                        <br>
                                                        <form action="">
                                                            <div class="input-field col s12 m6">
                                                                <input placeholder="Nombres y Apellidos" id="name" type="text" value="<?= session('user')->name  ?>" readonly>
                                                                <label for="name">Nombres y Apellidos</label>
                                                            </div>
                                                            <div class="input-field  col s12 m6">
                                                                <input placeholder="Nombre de Usuario" id="username" type="text" value="<?= session('user')->username  ?>" readonly>
                                                                <label for="username">Nombre de usuario</label>
                                                            </div>
                                                            <div class="input-field  col s12 m6">
                                                                <input placeholder="Correo Electronico" id="email" type="email" value="<?= session('user')->email  ?>" readonly>
                                                                <label for="email">Correo Electrónico</label>
                                                            </div>
                                                            <div class="input-field c col s12 m6">
                                                                <input placeholder="Rol" id="role" type="text" value="<?= session('user')->role_name  ?>" readonly>
                                                                <label for="role">Rol</label>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col s12">
                                        <div class="card user-card-negative-margin z-depth-1" id="feed">
                                            <div class="card-content card-border-gray">
                                                <div class="row">
                                                    <div class="col s12">
                                                        <h5>Cambiar Contraseña</h5>
                                                        <br>
                                                        <form action="<?= base_url('/new_password/' . session('user')->id) ?>" method="POST">
                                                            <div class="input-field col s12 m12 l4">
                                                                <input placeholder="Contraseña Actual" id="current_password" type="password" name="current_password" class="<?= session('errors.current_password') ||  session('error') ? 'invalid' : '' ?>" />
                                                                <label for="current_password">Contraseña Actual</label>
                                                                <input type="hidden" name="_method" value="PUT" />
                                                                <?php if (session('errors.current_password')) : ?>
                                                                    <span class="helper-text" data-error="<?= session('errors.current_password') ?>"></span>
                                                                <?php endif; ?>
                                                                <?php if (session('error')) : ?>
                                                                    <span class="helper-text" data-error="<?= session('error') ?>"></span>
                                                                <?php endif; ?>
                                                            </div>
                                                            <div class="input-field  col s12 m12  l4">
                                                                <input placeholder="Nueva Contraseña" id="new_password" type="password" name="new_password" class=" <?= session('errors.new_password') || session('error_password_exist') ? 'invalid' : '' ?> " />
                                                                <label for="new_password">Nueva Contraseña</label>
                                                                <?php if (session('errors.new_password')) : ?>
                                                                    <span class="helper-text" data-error="<?= session('errors.new_password') ?>"></span>
                                                                <?php endif; ?>
                                                                <?php if (session('error_password_exist')) : ?>
                                                                    <span class="helper-text" data-error="<?= session('error_password_exist') ?>"></span>
                                                                <?php endif; ?>
                                                            </div>
                                                            <div class="input-field  col s12 m12  l4">
                                                                <input placeholder="Confirmar Contraseña" id="confirm_password" type="password" name="confirm_password" class=" <?= session('errors.confirm_password') ? 'invalid' : '' ?> " />
                                                                <label for="confirm_password">Confirmar Contraseña</label>
                                                                <?php if (session('errors.confirm_password')) : ?>
                                                                    <span class="helper-text" data-error="<?= session('errors.confirm_password') ?>"></span>
                                                                <?php endif; ?>
                                                            </div>
                                                            <div class="input-field  col s12 m12  l12">
                                                                <button class="btn right indigo">Actualizar</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php if (session('user')->role_id == 2) : ?>

                                <?php if (!is_null($subscription)) : ?>
                                    <div class="col s12 m4 l3 user-section-negative-margin">
                                        <div class="row">
                                            <div class="col s12 center-align">
                                                <img class="responsive-img circle z-depth-5" width="120" src="<?= session('user')->photo == null ||  session('user')->photo == '' ? base_url('/assets/img/user.png') :  base_url('/assets/upload/images/' . session('user')->photo)   ?>" alt="">
                                                <br>
                                                <a class="waves-effect waves-light btn mt-5  modal-trigger border-radius-4" href="#modal1">Cambiar Foto</a>
                                            </div>
                                        </div>
                                        <div class="row mt-5">
                                            <div class="col s6">
                                                <h6>Paquete</h6>
                                                <h5 class="m-0"><a href="#"><?= $subscription->package_quantity ?></a></h5>
                                            </div>
                                            <div class="col s6">
                                                <h6>Disponibles</h6>
                                                <h5 class="m-0"><a href="#"><?= $subscription->package_quantity - $subscription->total  ?></a></h5>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col s12">
                                                <p class="m-0">Paquete <span class="d-block right"> <?= ucfirst($subscription->package_name) ?></span></p>
                                                <p class="m-0">Fecha de Inicio <span class="d-block right"><a href="#"> <?= strftime(" %d de %B de %Y", strtotime($subscription->start_date)) ?></a></span></p>
                                                <p class="m-0">Fecha de terminacion <span class="d-block right"><a href="#"> <?= strftime(" %d de %B de %Y", strtotime($subscription->end_date)) ?></a></span></p>
                                            </div>
                                        </div>
                                        <hr class="mt-5">
                                        <div class="row">
                                            <div class="col s12">
                                                <button class="btn indigo btn-large btn-block">
                                                    Cambiar Paquete
                                                    <i class="material-icons right">present_to_all</i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php else : ?>
                                    <div class="col s12 m4 l3">
                                        <div class="card-alert card orange">
                                            <div class="card-content white-text">
                                                <p>No hay ninguna subscripción registrada.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <br><br><br>
                                   
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <form action="<?= base_url('/update_photo') ?>" method="post" enctype="multipart/form-data">
                <div id="modal1" class="modal modal-fixed-footer" style="height:250px;">
                    <div class="modal-content">
                        <h4>Subir Foto</h4>
                        <div class="file-field input-field">
                            <div class="btn">
                                <span>Imagen</span>
                                <input type="file" name="photo" placeholder="Subir Imagen">
                            </div>
                            <div class="file-path-wrapper">
                                <input class="file-path validate" type="text" placeholder="Subir Imagen de Perfil">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="#!" class="modal-action modal-close waves-effect waves-green btn-flat ">Cerrar</a>
                        <button class="modal-action btn btn-indigo waves-effect waves-green ">Actualizar</button>
                    </div>
                </div>
            </form>
            <?= $this->endSection() ?>




            <?= $this->section('scripts') ?>
            <script src="<?= base_url('/js/advance-ui-modals.js') ?>"></script>
            <script src="<?= base_url('/js/vue.js') ?>"></script>
            <script src="<?= base_url('/js/ui-alerts.js') ?>"></script>
            <?= $this->endSection() ?>