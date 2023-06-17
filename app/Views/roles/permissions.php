<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?> Permisos <?= $this->endSection() ?>
<?= $this->section('content') ?>

<div id="main">
    <div class="row">
        <div class="breadcrumbs-inline pt-3 pb-1" id="breadcrumbs-wrapper">
            <div class="container">
                <div class="row">
                    <div class="col s12">
                        <?= $this->include('layouts/alerts') ?>
                    </div>
                    <div class="col s10 m6 l6 breadcrumbs-left">
                        <h5 class="breadcrumbs-title mt-0 mb-0 display-inline hide-on-small-and-down ">
                            <span>
                               Permisos
                                <a class="btn btn-small  darken-1 step-1 help purple" style="padding-left: 10px ; padding-right: 10px;">Ayuda</a>
                            </span>
                        </h5>
                        <ol class="breadcrumbs mb-0">
                            <li class="breadcrumb-item"><a href="<?= base_url() ?>/roles">Permisos</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="col s12">
            <div class="container">
                <div class="section">
                    <div class="card">
                        <div class="card-content">
                            <h5>Permisos
                                <a href="<?= base_url('/roles') ?>" class="right btn btn-light-indigo" style="padding-left:5px; padding-right:10px;">
                                    <i class="material-icons left">chevron_left</i>  Regresar
                                </a>
                            </h5>

                            <br>
                            <form action="<?= base_url('permissions/'.$id) ?>" method="post">
                                <div class="row">
                                    <?php foreach($options as $option): ?>
                                        <div class="col s12 m6 l3">
                                            <label >
                                                <input type="checkbox"  name="permissions_id[]"  <?= in_array($option->id, $activeOptions) ? 'checked="checked"' : '' ?> value="<?= $option->id ?>"/>
                                                <span><?= $option->option ?></span>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <br>
                                <div class="divider "></div>
                                <br>
                                <button class="btn indigo my-5">Guardar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?=  $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('/js/ui-alerts.js') ?>"></script>
<?=  $this->endSection() ?>
