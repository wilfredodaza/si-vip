<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?> Ordenes de Compra <?= $this->endSection() ?>

<?= $this->section('content') ?>

<div id="main">
    <div class="row">
        <div class="breadcrumbs-inline pt-3 pb-1" id="breadcrumbs-wrapper">
            <div class="container">
                <div class="row">
                    <div class="col s12">
                        <?= view('layouts/alerts') ?>
                    </div>
                    <div class="col s10 m6 l6 breadcrumbs-left">
                        <h5 class="breadcrumbs-title mt-0 mb-0 display-inline hide-on-small-and-down ">
                            <span>
                                Ordenes de compra
                            </span>
                        </h5>
                        <ol class="breadcrumbs mb-0">
                            <li class="breadcrumb-item"><a href="<?= base_url() ?>/home">Home</a></li>
                            <li class="breadcrumb-item active"><a href="#">Ordenes de compra</a></li>
                        </ol>
                    </div>
                </div>
            </div>
            <?php foreach ($indicadores as $indicador): ?>
                <div class="col s12 m3 l3">
                    <div class="card padding-4 animate fadeLeft shop">
                        <div class="row">
                            <div class="col s6 align-items-center" id="indicador-<?= $indicador->id ?>">
                                <h6 class="mb-0"><?= '$ ' . number_format(($indicador->total), '0', ',', '.') ?></h6>
                                <p class="no-margin" style="line-height:1;"><?= $indicador->name ?><br>
                                    <span class="no-padding no-margin" style="font-size: 10px !important;"><?= $indicador->observaciones ?></span></p>

                                <!-- <p class="mb-0 pt-8 tooltipped" data-position="bottom" data-delay="50" data-html="true" data-tooltip="  <i class='material-icons text-green green-text tiny'>brightness_1</i> 2 Por vencerse
                                <br><i class='material-icons text-yellow yellow-text tiny'>brightness_1</i> 0 Por vencerse
                                <br><i class='material-icons text-red red-text tiny'>brightness_1</i> 1 Por vencerse "> <strong> 3 </strong> por vencer <i class="material-icons text-red red-text tiny">brightness_1</i></p> -->
                            </div>

                            <div class="col s6 icon align-items-center">
                                <i class="material-icons <?= $indicador->color ?> background-round mt-5 white-text right"><?= $indicador->icon ?></i>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="col s12">
            <div class="container">
                <div class="section">
                    <div class="card">
                        <div class="card-content">
                            <div class="col s12 m3 right">
                                <button data-target="filter"
                                        class="btn btn-light-indigo right  modal-trigger step-5 active-red">
                                    Filtrar <i class="material-icons right">filter_list</i>
                                </button>
                                <a <?= (!$manager) ? 'disabled' : '' ?>
                                        href="<?= base_url() . route_to('purchaseOrder-create') ?>"
                                        class="btn indigo right mr-3 step-2 active-red">Registrar</a>
                            </div>
                            <table class="table-responsive">
                                <thead>
                                <tr>
                                    <th class="center">Número</th>
                                    <th class="center">Fecha</th>
                                    <th class="center">Proveedor</th>
                                    <th class="center">Total Oc</th>
                                    <th class="center">Total ejecutado</th>
                                    <th class="center step-4">Estado</th>
                                    <th class="center step-3">Acciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($invoices as $item): ?>
                                    <tr>
                                        <td class="center"><?= $item['resolution'] ?>
                                        <td class="center"><?= $item['created_at'] ?></td>
                                        <td class="center"><?= ucwords($item['customer']) ?></td>
                                        <td class="center" width="100px">
                                            $ <?= number_format($item['payable_amount'], '0', '.', '.') ?>
                                        </td>
                                        <td class="center" width="100px">
                                            $ <?= number_format($valuesExecuted[$item['id_invoice']], '0', '.', '.') ?>
                                        </td>
                                        <td class="center">
                                            <?php
                                            switch ($item['invoice_status_id']) {
                                                case 5:
                                                    echo '<span class="badge new green darken-1 "  data-badge-caption="' . $item['status'] . '" ></span>';
                                                    break;
                                                case 6:
                                                    echo '<span class="badge new red darken-1 "  data-badge-caption="' . $item['status'] . '" ></span>';
                                                    break;
                                            }
                                            ?>
                                        </td>
                                        <td width="100px">
                                            <div class="btn-group" role="group">
                                                <a href="<?= base_url() ?>/purchaseOrder/view/<?= $item['id_invoice'] ?>"
                                                   class="btn btn-small  pink darken-1  tooltipped"
                                                   data-position="top"
                                                   data-tooltip="Descargar Orden"
                                                ><i class="material-icons">insert_drive_file</i></a>

                                                <a href="<?= base_url() . route_to('purchaseOrder-edit', $item['id_invoice']) ?>"
                                                   class="btn btn-small yellow darken-2 tooltipped"
                                                   data-position="top"
                                                    <?= ($item['invoice_status_id'] == 6) ? 'disabled' : '' ?>
                                                   data-tooltip="Editar orden"><i
                                                            class="material-icons">create</i></a>
                                                <a href="<?= base_url() . route_to('purchaseOrder-email', $item['id_invoice']) ?>"
                                                   class="btn btn-small tooltipped email  blue darken-1"
                                                   data-position="top" data-tooltip="Enviar correo electrónico">
                                                    <i class="material-icons">email</i>
                                                </a>
                                                <a href="<?= base_url() . route_to('purchaseOrder-tracking', $item['id_invoice']) ?>"
                                                   class="btn btn-small green tooltipped "
                                                   data-position="top" data-tooltip="Seguimiento de la orden">
                                                    <i class="material-icons">assignment</i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                            <?php if (count($invoices) == 0): ?>
                                <p class="red-text center py-2">No hay ningun elemento registrado.</p>
                            <?php endif; ?>
                            <?= $pager->links() ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<form action="" method="get">
    <div id="filter" class="modal" role="dialog" style="height:auto; width: 600px">
        <div class="modal-content">
            <h4 class="modal-title">Filtrar</h4>
            <div class="row">
                <div class="input-field col l6 m6 s12 browser-default">
                    <label for="year" class="active">Año</label>
                    <select class="select2 browser-default validate" id="year" name="year">
                        <option value="" disabled="" selected="">Seleccione un año</option>
                        <?php foreach ($years as $year): ?>
                            <option <?= (isset($_GET['year']) && $_GET['year'] == $year->year?'selected':'') ?> value="<?= $year->year ?>"><?= $year->year ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col s12 m6 input-field browser-default">
                    <select name="month" id="month" class="browser-default">
                        <option value="" disabled="" selected="">Seleccione un mes</option>
                        <?php foreach ($months as $month): ?>
                            <option value="<?= $month->id ?>"><?= $month->name ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label for="month" class="active">Mes</label>
                </div>
                <div class="col s12 m6 input-field">
                    <select name="status" id="status" class="browser-default">
                        <option value="" disabled="" selected="">Seleccione un estado</option>
                        <option value="5">Abierto</option>
                        <option value="6">Cerrado</option>
                    </select>
                    <label for="status" class="active">Estado</label>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-action modal-close waves-effect waves-red btn-flat mb-5 ">Cerrar</a>
            <button class="modal-action waves-effect waves-green btn indigo mb-5">Guardar</button>
        </div>
    </div>
</form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('/js/shepherd.min.js') ?>"></script>
<script src="<?= base_url('js/advance-ui-modals.js') ?>"></script>
<script src="<?= base_url() . '/js/views/quotation.js' ?>"></script>

<?= $this->endSection() ?>
