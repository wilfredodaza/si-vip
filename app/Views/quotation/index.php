
<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?> Cotizaciones <?= $this->endSection() ?>

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
                                Cotizaciones
                                <a class="btn-small light-blue darken-1 step-1 help" style="padding-right: 10px; padding-left: 10px;">Ayuda</a>
                            </span>
                        </h5>
                        <ol class="breadcrumbs mb-0">
                            <li class="breadcrumb-item"><a href="<?= base_url() ?>/home">Home</a></li>
                            <li class="breadcrumb-item active"><a href="#">Cotizaciones</a></li>
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
                            <div class="col s12 m3 right">
                                <button data-target="filter" class="btn btn-light-indigo right  modal-trigger step-5 active-red">
                                    Filtrar <i class="material-icons right">filter_list</i>
                                </button>
                                <a href="<?= base_url('/quotation/create') ?>"
                                   class="btn indigo right mr-3 step-2 active-red">Registrar</a>
                            </div>
                                <table class="table-responsive">
                                    <thead>
                                        <tr>
                                            <th class="center">Número</th>
                                            <th class="center">Fecha</th>
                                            <th class="center">Cliente</th>
                                            <th class="center">Total factura</th>
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
                                                    <a href="<?= base_url() ?>/reports/view/<?= $item['id_invoice'] ?>"
                                                       class="btn btn-small  pink darken-1  tooltipped"
                                                      data-position="top"
                                                       data-tooltip="Descargar cotización" target="_blank"
                                                    ><i class="material-icons">insert_drive_file</i></a>
                                                    <a href="<?= base_url() ?>/quotation/edit/<?= $item['id_invoice'] ?>"
                                                       class="btn btn-small yellow darken-2 tooltipped"
                                                       data-position="top"
                                                       data-tooltip="Editar factura"><i
                                                                class="material-icons">create</i></a>
                                                    <a href="<?= base_url() ?>/quotation/email/<?= $item['id_invoice'] ?>"
                                                       class="btn btn-small tooltipped email  blue darken-1"
                                                       data-position="top" data-tooltip="Enviar correo electrónico">
                                                        <i class="material-icons">email</i>
                                                    </a>
                                                    <a href="<?= base_url() ?>/tracking/quotation/<?= $item['id_invoice'] ?>"
                                                       class="btn btn-small green tooltipped "
                                                       data-position="top" data-tooltip="Seguimiento de la cotización">
                                                        <i class="material-icons">assignment</i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <?php if (count($invoices) == 0): ?>
                                    <p class="red-text center py-2" >No hay ningun elemento registrado.</p>
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
                <div class="col s12 m6 input-field">
                    <input id="end_date" type="text" name="value">
                    <label for="end_date">Buscar</label>
                </div>
                <div class="col s12 m6 input-field">

                    <select name="campo" id="filter" class="browser-default">
                        <option value="resolution">#</option>
                        <option value="Tipo de factura">Tipo de documento</option>
                        <option value="Cliente">Cliente</option>
                        <option value="Estado">Estado</option>
                    </select>
                    <label for="filter" class="active">Filtro</label>
                </div>
            </div>
        </div>
        <div class="modal-footer" >
            <a href="#!" class="modal-action modal-close waves-effect waves-red btn-flat mb-5 ">Cerrar</a>
            <button class="modal-action waves-effect waves-green btn indigo mb-5">Guardar</button>
        </div>
    </div>
</form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
    <script src="<?= base_url('/js/shepherd.min.js') ?>"></script>
    <script src="<?= base_url('js/advance-ui-modals.js') ?>"></script>
    <script src="<?= base_url().'/js/views/quotation.js' ?>"></script>

<?= $this->endSection() ?>
