<?= $this->extend('layouts/main') ?>


<?= $this->section('title') ?> Facturación <?= $this->endSection() ?>

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
                                Suscripciones
                            </span>
                        </h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col s12">
            <div class="container">
                <div class="section">
                    <div class="card">
                        <div class="card-content">
                            <a href="<?= base_url("expired_subscription_export?company_id=".($_GET['company_id'] ?? '' )."&start_date=".($_GET['start_date'] ?? '' )."&end_date=".($_GET['end_date'] ?? '' )."") ?>" class="right btn green step-5 active-red ml-1">
                                Descargar Excel <i class="material-icons right">filter_list</i>
                            </a>
                            <button data-target="filter" class="right btn  btn-light-indigo modal-trigger step-5 active-red">
                                Filtrar <i class="material-icons right">filter_list</i>
                            </button>
                            <?php if(isset($_GET['start_date']) || isset($_GET['end_date']) || isset($_GET['start_date'])): ?>
                                <a href="<?= base_url('expired_subscription') ?>" class="right btn btn-light-red step-5 active-red mr-1">
                                    Quitar Filtro <i class="material-icons right">filter_list</i>
                            </a>
                            <?php endif; ?>
                            <table class="table-responsive">
                                <thead>
                                    <tr>
                                        <th>Compañía</th>
                                        <th class="center">Fecha de V. Certificado</th>
                                        <th class="center">Fecha de V. Suscripción</th>
                                        <th class="center">Paquete</th>
                                        <th class="center">Paquete Actual</th>
                                        <th class="center">Gastados</th>
                                        <th class="center step-4">Disponibles</th>
                                        <th class="center step-4">Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($subscriptions as $item) : ?>
                                        <tr>
                                            <td><?= $item->company ?>
                                            <td class="center"><?= $item->date_due_certificate ?></td>
                                            <td class="center"><?= $item->end_date ?></td>
                                            <td class="center"><?= $item->package_name ?></td>
                                            <td class="center"><?= $item->quantity_document ?></td>
                                            <td class="center"><?= $item->count_invoices + $item->wallet ?></td>
                                            <td class="center"><?= $avaliable = $item->quantity_document  -  ($item->count_invoices + $item->wallet) ?></td>
                                            <td class="center">
                                                <?php
                                                    $now = time(); // or your date as well
                                                    $your_date = strtotime($item->end_date);
                                                    $datediff = $your_date -$now;
                                                    $days = round($datediff / (60 * 60 * 24));
                                                ?>
                                                <?php if($days <= 0): ?>
                                                    <span class="new badge red" data-badge-caption="V. Fecha <?= $days ?>"></span>
                                                <?php endif; ?>
                                                <?php if($avaliable <= 0): ?>
                                                    <span class="new badge yellow darken-2"  data-badge-caption="V. Documentos <?= $avaliable ?>"></span>
                                                <?php endif; ?>
                                                <?php if($days > 0 && $avaliable >0): ?>
                                                    <span class="new badge green" data-badge-caption="Activo"></span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <?php if (count($subscriptions) == 0) : ?>
                                <p class="center red-text pt-1">No hay ningún elemento.</p>
                            <?php endif ?>
                            <?= $pager->links(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form action="" method="GET">
        <div id="filter" class="modal" role="dialog" style="height:auto; width: 600px">
            <div class="modal-content">
                <h6>Filtrar</h6>
                <div class="row">
                    <div class="col s12 m12  input-field" >
                        <label for="company_id" class="active">Empresa</label>
                        <select class="browser-default" name="company_id" id="company_id">
                        <option value="" >Seleccione una Opcion</option>
                        <?php foreach($companies as $company): ?>
                            <option value="<?= $company->id ?>" <?= isset($_GET['company_id']) && !empty($_GET['company_id']) &&  $company->id == $_GET['company_id'] ? 'selected' : '' ?>><?= $company->company?></option>
                        <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col s6 m6  input-field" >
                        <input type="date" name="start_date" id="start_date"  value="<?= isset($_GET['start_date']) ? $_GET['start_date'] : '' ?>">
                        <label for="start_date" class="active">Fecha de Inicio</label>
                    </div>
                    <div class="col s6 m6  input-field" >
                        <input type="date" name="end_date" id="end_date"  value="<?= isset($_GET['end_date']) ? $_GET['end_date'] : '' ?>">
                        <label for="end_date" class="active">Fecha de Final</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#!" class="modal-action modal-close waves-effect  btn-flat btn-light-indigo ">Cerrar</a>
                <button class="modal-action waves-effect waves-green btn indigo">Guardar</button>
            </div>
        </div>
    </form>
    <?= $this->endSection() ?>



    <?= $this->section('scripts') ?>
    <script src="<?= base_url('/js/advance-ui-modals.js') ?>"></script>
    <script src="<?= base_url('/js/shepherd.min.js') ?>"></script>
    <script src="<?= base_url('/js/vue.js') ?>"></script>
    <script src="<?= base_url('/js/views/invoice.js') ?>"></script>
    <script src="<?= base_url('/js/ui-alerts.js') ?>"></script>
    <?= $this->endSection() ?>