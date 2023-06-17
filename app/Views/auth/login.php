
<?= $this->extend('auth/main') ?>

<?= $this->section('title') ?> Login <?= $this->endSection() ?>

<?= $this->section('styles')  ?>
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>/assets/css/login.css">
 <?=  $this->endSection() ?>


<?= $this->section('content') ?>
<div class="row">
    <div class="col s12">
        <div class="container">
            <div id="login-page" class="row">
                <div class="col s12 m6 l4 z-depth-4 card-panel border-radius-6 login-card bg-opacity-8">
                    <form class="login-form" action="<?= base_url() ?>/validation" method="POST">
                        <div class="row">
                            <div class="input-field col s12">
                            <?php if (session('success')): ?>
                                <div class="card-alert card green">
                                    <div class="card-content white-text">
                                        <?= session('success') ?>
                                    </div>
                                    <button type="button" class="close white-text" data-dismiss="alert"
                                            aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                </div>
                            <?php endif; ?>
                                <h5 class="ml-4 " style="text-align:center;">
                                    Nevado<small> lab</small>
                                    
                                </h5>
                            </div>
                        </div>
                        <div class="row margin">
                        
                            <div class="col s12">
                                <?php if (session('errors')): ?>
                                <div class="card-alert card red">
                                    <div class="card-content white-text">
                                        <p><?= session('errors') ?></p>
                                    </div>
                                    <button type="button" class="close white-text" data-dismiss="alert"
                                            aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                </div>

                            <?php endif; ?>
                            </div>
                            
                            <div class="input-field col s12">
                                <i class="material-icons prefix pt-2">person_outline</i>
                                <input id="username" type="text" name="username">
                                <label for="username" class="center-align">Nombre de Usuario</label>
                            </div>
                        </div>
                        <div class="row margin">
                            <div class="input-field col s12">
                                <i class="material-icons prefix pt-2">lock_outline</i>
                                <input id="password" type="password" name="password">
                                <label for="password">Contraseña</label>
                                <small class=""></small>
                            </div>
                        </div>
         
                        <div class="row">
                            <div class="input-field col s12">
                                <button class="btn waves-effect waves-light border-round gradient-45deg-purple-deep-orange col s12">
                                    Iniciar
                                </button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s6 m6 l6">
                                <p class="margin medium-small"><!--<a href="/register">Registrate</a>--></p>
                            </div>
                            <div class="input-field col s6 m6 l6">
                                <p class="margin right-align medium-small"><a href="<?= base_url() ?>/reset_password">¿Olvide
                                        mi contraseña?</a></p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="content-overlay"></div>
    </div>
</div>
<?= $this->endSection() ?>
