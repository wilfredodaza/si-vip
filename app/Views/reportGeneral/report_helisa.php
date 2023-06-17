<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?> Reporte de Facturación Helisa <?= $this->endSection() ?>
<?= $this->section('content') ?>
<div id="main">
    <div class="row" id="filters">
        <div class="col s12">
            <?= $this->include('layouts/alerts') ?>
            <?= $this->include('layouts/notification') ?>
            <div class="container">
                <div class="section">
                    <div class="card">
                        <div class="card-content">
                            <form id="form" method="GET" action="" multiple="">
                                <div class="row">
                                    <div class="col s12 ">
                                        <div class="row">
                                            <ul class="collapsible collapsible-filter" style="box-shadow: none;">
                                                <li class="active">
                                                    <div class="collapsible-header">
                                                        <i class="material-icons">search</i>Filtrar Reporte Helisa</div>
                                                    <div class="collapsible-body ">
                                                        <div class="row">
                                                            <div class="col  s12 m4 input-field">
                                                                <input type="date" name="date_start" id="date_start"
                                                                       value="<?= isset($_GET['date_start'])  ? $_GET['date_start']: ''  ?>">
                                                                <label for="date_start">Fecha de inicio</label>
                                                            </div>
                                                            <div class="col s12 m4 input-field">
                                                                <input type="date" name="date_end" id="date_end"
                                                                       value="<?= isset($_GET['date_end'])  ? $_GET['date_end']: ''  ?>">
                                                                <label for="date_end">Fecha fin</label>
                                                            </div>
                                                            <div class="col s12 m4 input-field">

                                                                <?php
                                                                $typeDocument = '[';
                                                                if(isset($_GET['type_document'])):
                                                                foreach ($_GET['type_document'] as $item): ?>
                                                                    <?php $typeDocument.=$item.','; ?>
                                                                <?php endforeach; endif;  $typeDocument.=']'; ?>
                                                                <select multiple name="type_document[]" id="type_document" value="<?= isset($_GET['type_document'])  ? $typeDocument: ''  ?>">
                                                                    <option disabled value="">Seleccione ...</option>
                                                                    <?php foreach ($typeDocuments as $item): ?>
                                                                        <option value="<?= $item->id ?>"><?= $item->name ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                                <label for="type_document">Tipo de Documento</label>
                                                            </div>

                                                            <!-- -->
                                                        </div>
                                                    </div>
                                                </li>

                                            </ul>
                                        </div>
                                        <div class="row">
                                            <div class="col s12">
                                                <div class="col s12 input-field">
                                                    <button class="waves-effect waves-green btn indigo pull-right sprint-load" data-sprint-text="Consultando, esto puede tardar un rato.">
                                                        Consulta
                                                    </button>
                                                    <?php if(isset($_GET['date_start']) || isset($_GET['date_end']) || isset($_GET['type_document']) ): ?>
                                                        <button class="waves-effect waves-red red btn pull-right"
                                                                style="margin-right: 10px;">Quitar Filtro
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="row">
                                <div class="col s12">
                                    <div class="divider"></div>
                                    <span class="card-title" style="margin-top: 20px;margin-bottom: 0px;">
                                         <a href="<?= base_url() . '/report/helisa/download' ?>"
                                            class="btn modals-trigger btn-small grey lighten-5 grey-text text-darken-4 pull-right"
                                            target="_blank"
                                            style="margin-top: 10px;"
                                            data-toggle="modals">
                                            Descargas
                                            <i class="material-icons right">cloud_download</i>
                                        </a>
                                    </span>
                                    <div clas="divider"></div>
                                    <div class="table-response" style="overflow-x:auto;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--sprint loader-->
<div class="container-sprint-send" >
    <div class="preloader-wrapper big active">
        <div class="spinner-layer spinner-blue-only">
            <div class="circle-clipper left">
                <div class="circle"></div>
            </div>
            <div class="gap-patch">
                <div class="circle"></div>
            </div>
            <div class="circle-clipper right">
                <div class="circle"></div>
            </div>
        </div>
    </div>
    <span></span>
</div>
<!--end sprint loader -->

<?php  $this->endSection('content') ?>

<?php $this->section('scripts') ?>
<script src="<?= base_url() ?>/assets/js/vendors.min.js"></script>
<script src="<?= base_url() ?>/assets/js/select2.full.min.js"></script>
<script src="<?= base_url() ?>/assets/js/plugins.min.js"></script>
<script src="<?= base_url() ?>/assets/js/search.js"></script>
<script src="<?= base_url() ?>/assets/js/custom-script.min.js"></script>
<script src="<?= base_url() ?>/assets/js/form-select2.js"></script>
<script src="<?= base_url() ?>/assets/js/advance-ui-modals.js"></script>
<script src="<?= base_url() ?>/dropify/js/dropify.min.js"></script>
<script src="<?= base_url() ?>/assets/js/ui-alerts.js"></script>
<script src="<?= base_url('/js/sprint.js') ?>"></script>
<script src="<?= base_url('/js/views/invoice.js') ?>"></script>

<?php $this->endSection() ?>



