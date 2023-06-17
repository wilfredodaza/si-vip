<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?> Integraciones <?= $this->endSection() ?>

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
                               Integraciones
                            </span>
                        </h5>
                        <ol class="breadcrumbs mb-0">
                            <li class="breadcrumb-item"><a href="<?= base_url('integrations/') ?>">Aplicaciones</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <br>
        <div class="col s12">
            <div class="container">
                <div class="section">
                    <div class="row">
                        <!-- Blog Style One -->
                        <?php foreach ($apps as $app): ?>
                            <!-- Fashion Card -->
                            <div class="col s12 m6 l4">
                                <div class="card-panel border-radius-6 mt-10 card-animation-1">
                                    <a href="<?= base_url('integrations/'.$app->name) ?>"><img class="responsive-img border-radius-8 z-depth-4 image-n-margin"
                                                     src="<?= base_url('/assets/img/' . $app->icon) ?>" alt=""></a>
                                    <h6 class="deep-purple-text text-darken-3 mt-5"><a
                                                href="<?= base_url('integrations/'.$app->name) ?>"><?= ucfirst($app->name) ?></a></h6>
                                    <span><?= $app->description ?></span>
                                    <div class="display-flex justify-content-between flex-wrap mt-4">
                                        <div class="display-flex align-items-center mt-1">
                                            <?php if ($app->statusCompany): ?>
                                                <span class="chip green lighten-5">
                                                    <span class="green-text"><h6 class="green-text">Activado</h6></span>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="display-flex mt-3 right-align social-icon">
                                            <?php if (!$app->statusCompany): ?>
                                                <a href="<?= base_url('/integrations/' . $app->name) ?>"
                                                   class="btn btn-light-indigo box-shadow-none border-round mr-1 mb-1">Comenzar</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('/js/advance-ui-modals.js') ?>"></script>
<script src="<?= base_url('/js/ui-alerts.js') ?>"></script>
<script src="<?= base_url('/js/jquery.validate.js') ?>"></script>
<script src="<?= base_url('/js/jquery.validate.js') ?>"></script>
<?= $this->endSection() ?>

