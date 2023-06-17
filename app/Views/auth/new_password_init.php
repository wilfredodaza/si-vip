
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
                    <form class="login-form" action="<?= base_url('/new_password/'.session('user')->id) ?>" method="POST">
                        <div class="row">
                            <div class="input-field col s12">
                                <h5 class="ml-4 center" >
                                    Nevado <small> lab</small>
                                </h5>
                                <h6 class="center">Nueva Contraseña</h6>
                            </div>
                        </div>
                        <div class="row margin">
                            <div class="input-field col s12">
                                <i class="material-icons prefix pt-2">lock_outline</i>
                                <input id="current_password" type="password" name="current_password"  class=" <?= session('errors.current_password') ||  session('error') ? 'invalid' : '' ?> " >
                                <label for="current_password" class="center-align">Contraseña Actual</label>
                                <input type="hidden" name="_method" value="PUT" />
                                <?php if(session('errors.current_password')): ?>
                                    <span class="helper-text" data-error="<?= session('errors.current_password') ?>"></span>
                                <?php endif; ?>
                                <?php if(session('error')): ?>
                                    <span class="helper-text" data-error="<?= session('error') ?>"></span>
                                <?php endif; ?>
                            </div>
                            <div class="input-field col s12">
                                <i class="material-icons prefix pt-2">lock_outline</i>
                                <input id="new_password" type="password" name="new_password" class=" <?= session('errors.new_password') || session('error_password_exist') ? 'invalid' : '' ?> ">
                                <label for="new_password" class="center-align">Nueva Contraseña</label>
                                <?php if(session('errors.new_password')): ?>
                                    <span class="helper-text" data-error="<?= session('errors.new_password') ?>"></span>
                                <?php endif; ?>
                                <?php if(session('error_password_exist')): ?>
                                    <span class="helper-text" data-error="<?= session('error_password_exist') ?>"></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="row margin">
                            <div class="input-field col s12">
                                <i class="material-icons prefix pt-2">lock_outline</i>
                                <input id="confirm_password" type="password" name="confirm_password" class=" <?= session('errors.confirm_password') ? 'invalid' : '' ?> ">
                                <label for="confirm_password">Confirmar Contraseña</label>
                                <?php if(session('errors.confirm_password')): ?>
                                    <span class="helper-text" data-error="<?= session('errors.confirm_password') ?>"></span>
                                <?php endif; ?>
                            </div>
                        </div>
         
                        <div class="row">
                            <div class="input-field col s12">
                                <button class="btn waves-effect waves-light border-round gradient-45deg-purple-deep-orange col s12">
                                   Nueva Contraseña
                                </button>
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

