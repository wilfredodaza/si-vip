
<?= $this->extend('auth/main') ?>


<?= $this->section('title') ?> Recuperar Contraseña <?= $this->endSection() ?>


<?= $this->section('styles') ?> 
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>/assets/css/login.css">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col s12">
        <div class="container">
            <div id="login-page" class="row">
                <div class="col s12 m6 l4 z-depth-4 card-panel border-radius-6 login-card bg-opacity-8">
                    <?php if (session('danger')): ?>
                        <div class="card-alert card red">
                            <div class="card-content white-text">
                                <p><?= session('danger') ?></p>
                            </div>
                            <button type="button" class="close white-text" data-dismiss="alert"
                                    aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>

                    <?php endif; ?>
                    <?php if (session('success')): ?>
                        <div class="card-alert card green">
                            <div class="card-content white-text">
                                <p><?= session('success') ?></p>
                            </div>
                            <button type="button" class="close white-text" data-dismiss="alert"
                                    aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>

                    <?php endif; ?>
                    <form class="login-form" method="POST" action="/forgot_password">
                        <div class="row">
                            <div class="input-field col s12">
                                <h5 class="ml-4 center">Recuperar Contraseña</h5>
                                <p class="ml-4 center">Puedes restablecer tu contraseña</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s12">
                                <i class="material-icons prefix pt-2">person_outline</i>
                                <input id="email" type="email" name="email">
                                <label for="email" class="center-align">Correo Electronico</label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s12">
                                <button  class="btn waves-effect waves-light border-round gradient-45deg-purple-deep-orange col s12 mb-1">Recuperar
                                    Contraseña</button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s6 m6 l6">
                                <p class="margin medium-small"><a href="/">Inicio de Sesión</a></p>
                            </div>
                            <div class="input-field col s6 m6 l6">
                         
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


