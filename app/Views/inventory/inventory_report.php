<?= $this->extend('layouts/main') ?>


<?= $this->section('title') ?> Reporte Inventario<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div id="main">
    <div class="row">
        <div class="breadcrumbs-inline pt-3 pb-1" id="breadcrumbs-wrapper">
            <div class="container">
                <div class="row">
                    <div class="col s12">
                        <?= $this->include('layouts/alerts') ?>
                        <?= $this->include('layouts/notification') ?>
                    </div>
                    <div class="col s10 m6 l6 breadcrumbs-left">
                        <h5 class="breadcrumbs-title mt-0 mb-0 display-inline hide-on-small-and-down ">
                            <span>
                               Reporte de Operaciones
                            </span>
                        </h5>
                        <!--<ol class="breadcrumbs mb-0">
                            <li class="breadcrumb-item"><a href="<?= base_url() ?>/home">Home</a></li>
                            <li class="breadcrumb-item active"><a href="#">Inventary</a></li>
                        </ol>-->

                    </div>
                </div>
            </div>
        </div>
        <form action="<?= base_url() ?>/inventory/report_result" method="post">
            <div class="col s12">
                <div class="container">
                    <div class="section">
                        <div class="card">
                            <div class="card-content">
                                <div class="row">
                                    <div class="input-field col s12 m6 l6">
                                        <input placeholder="" id="date_init" type="date" name="date_init" class="validate">
                                        <label for="date_init" class="active">Fecha Inicio</label>
                                    </div>
                                    <div class="input-field col s12 m6 l6">
                                        <input placeholder="" id="date_end" type="date" name="date_end" class="validate">
                                        <label for="date_end" class="active">Fecha fin</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="input-field col s12 m6 l6">
                                        <h6>
                                            Proveedores
                                        </h6>
                                        <select class="select2 browser-default" name="providers">
                                            <option value="" disabled="" selected="">Seleccione una opción</option>
                                            <?php foreach($providers as $provider): ?>
                                                <option value="<?= $provider->id ?>"><?= $provider->name.' - '.$provider->identification_number?></option>
                                            <?php endforeach;?>
                                        </select>
                                    </div>
                                    <div class="input-field col s12 m6 l6">
                                        <h6>
                                            Tipo de Operación
                                        </h6>
                                        <select class="select2 browser-default" name="operation">
                                            <option value="" disabled="" selected="">Seleccione una opción</option>
                                            <?php foreach($type_documents as $type_document): ?>
                                                <option value="<?= $type_document->id ?>"><?= $type_document->name?></option>
                                            <?php endforeach;?>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="input-field col s12 m12 l12">
                                        <button type="submit" class="btn btn-small purple right">Consultar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>




<?=  $this->endSection() ?>



<?= $this->section('scripts') ?>
<script src="<?= base_url('/js/advance-ui-modals.js') ?>"></script>
<script src="<?= base_url('/js/shepherd.min.js') ?>"></script>
<script src="<?= base_url('/js/vue.js')?>"></script>
<script src="<?= base_url('/js/ui-alerts.js') ?>"></script>
<?=  $this->endSection() ?>
