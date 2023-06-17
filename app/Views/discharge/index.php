<?= $this->extend('layouts/main') ?>


<?= $this->section('title') ?> Pago proveedores <?= $this->endSection() ?>

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
                                 Pago proveedores
                            </span>
                        </h5>
                        <ol class="breadcrumbs mb-0">
                            <li class="breadcrumb-item"><a href="<?= base_url() ?>/home">Home</a></li>
                            <li class="breadcrumb-item active"><a href="#"> Pago proveedores</a></li>
                        </ol>

                    </div>
                </div>
            </div>
            <?php
            $balance = 0;
            $count = 0;
            $collected = 0;

            foreach ($total as $value):
                $balance += $value->payable_amount - ($value->withholdings + $value->balance);
                $collected += $value->balance;
                $count += 1;
            endforeach;
            $indicadores[0]->total = $balance;
            $indicadores[1]->total = $collected;
            $indicadores[2]->total = ($balance - $collected);
            ?>
            <?php foreach ($indicadores as $indicador): ?>
                <div class="col s12 m4 l4">
                    <div class="card padding-4 animate fadeLeft shop">
                        <div class="row">
                            <div class="col s6 align-items-center" id="indicador-<?= $indicador->id ?>">
                                <h6 class="mb-0"><?= '$ ' . number_format(($indicador->total), '0', ',', '.') ?></h6>
                                <p class="no-margin"><?= $indicador->name ?></p>
                                <!-- <p class="mb-0 pt-8 tooltipped" data-position="bottom" data-delay="50" data-html="true" data-tooltip="  <i class='material-icons text-green green-text tiny'>brightness_1</i> 2 Por vencerse
                                <br><i class='material-icons text-yellow yellow-text tiny'>brightness_1</i> 0 Por vencerse
                                <br><i class='material-icons text-red red-text tiny'>brightness_1</i> 1 Por vencerse "> <strong> 3 </strong> por vencer <i class="material-icons text-red red-text tiny">brightness_1</i></p> -->
                            </div>

                            <div class="col s6 icon align-items-center ">
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
                            <button data-target="filter"
                                    class="right btn  btn-light-indigo modal-trigger step-4 mb-2 active-red">
                                Filtrar <i class="material-icons right">filter_list</i>
                            </button>
                            <?php if (isset($_GET['customer']) || isset($_GET['resolution_id']) || isset($_GET['resolution']) || isset($_GET['start_date']) || isset($_GET['end_date']) || isset($_GET['start_date_collect']) || isset($_GET['end_date_collect']) || isset($_GET['status']) || isset($_GET['account']) || isset($_GET['headquarters'])): ?>
                                <a href="<?= base_url('discharge') ?>"
                                   class="btn right btn-light-red btn-small ml-1"
                                   style="padding-left: 10px;padding-right: 10px; margin-right: 10px; ">
                                    <i class="material-icons left">close</i>
                                    Quitar Filtro
                                </a>
                            <?php endif; ?>
                            <div class="row">

                                <div class="col s12">
                                    <table class="table responsive-table">
                                        <thead>
                                        <tr>
                                            <th class="center">Fecha</th>
                                            <th class="center">Sede</th>
                                            <th class="center">Proveedor - cliente</th>
                                            <th class="center">Total</th>
                                            <th class="center">Saldo</th>
                                            <th class="center step-3">Estado</th>
                                            <th class="center step-2">Acciones</th>
                                        </tr>
                                        </thead>
                                        <tbody>


                                        <?php foreach ($wallets as $item): ?>
                                            <tr>

                                                <?php if (($item->payable_amount - $item->withholdings - $item->balance - ($item->credit_note - $item->credit_note_withholdings)) <= 0) {
                                                    statusPay($item->id);
                                                } ?>
                                                <td class="center"><?= $item->created_at ?></td>
                                                <td class="center"><?= ucwords($item->nameCompany) ?></td>
                                                <td class="center"><?= ucwords($item->name) ?></td>
                                                <td class="center">
                                                    $<?= number_format($item->payable_amount - $item->withholdings, '2', ',', '.') ?></td>
                                                <td class="center">
                                                    $<?= number_format($item->payable_amount - $item->withholdings - $item->balance - ($item->credit_note - $item->credit_note_withholdings), '2', ',', '.') ?></td>
                                                <td class="center">
                                                    <?php
                                                    switch ($item->status_wallet) {
                                                        case 'Pendiente':
                                                            echo '<span class="badge new pink darken-1 " style="width: 120px;" data-badge-caption="' . $item->status_wallet . '" ></span>';
                                                            break;
                                                        case 'Paga':
                                                            echo '<span class="badge new green lighten-1" style="width: 120px;"  data-badge-caption="' . $item->status_wallet . '"></span>';
                                                            break;
                                                    }
                                                    ?>
                                                </td>
                                                <td class="center">
                                                    <div class="btn-group center" role="group">
                                                        <a href="#update-file"
                                                           class="btn btn-small sm green lighten-1 modal-trigger edit tooltipped"
                                                           data-id="<?= $item->id ?>" data-toggle="modal"
                                                           data-position="top" data-tooltip="Registrar pago"
                                                           data-customer="<?= $item->name ?>">
                                                            <i class="material-icons">attach_money</i>
                                                        </a>
                                                        <a href="<?= base_url() . '/discharge/show/' . $item->id ?>"
                                                           class="btn btn-small yellow darken-2 tooltipped"
                                                           data-position="top" data-tooltip="Detalle del Pago">
                                                            <i class="material-icons">visibility</i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <?php if (count($wallets) == 0): ?>
                                            <tr>
                                                <td colspan="7">
                                                    <p class="center red-text py-2">No hay ningún elemento en el módulo
                                                        cartera.</p>
                                                </td>
                                            </tr>
                                        <?php endif ?>
                                        <tr>
                                            <th colspan="4">Total Egresos</th>
                                            <td class="center">$ <?= number_format($balance, '2', ',', '.') ?></td>
                                            <td colspan="2"></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <?= $pager->links() ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form action="" method="get">
        <div id="filter" class="modal" role="dialog" style="">
            <div class="modal-content">
                <h4 class="modal-title">Filtrar</h4>
                <div class="row">
                    <div class="col s12 m6">
                        <label for="customer">Prefijo</label>
                        <select class="browser-default" id="resolution_id" name="resolution_id">
                            <option value="">Seleccione ...</option>
                            <?php foreach ($resolutions as $resolution): ?>
                                <option value="<?= $resolution->resolution ?>"><?= $resolution->prefix ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col s12 m6 input-field">
                        <input type="number" id="number" name="resolution">
                        <label for="number">N° Factura</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col s12 m6 input-field">
                        <input type="date" id="start_date" name="start_date">
                        <label for="start_date">Fecha de inicio</label>
                    </div>
                    <div class="col s12 m6 input-field">
                        <input id="end_date" type="date" name="end_date">
                        <label for="end_date">Fecha fin</label>
                    </div>
                    <div class="col s12 m6 input-field">
                        <label for="customer" class="active">Proveedores</label>
                        <select class="select2 browser-default" id="customer" name="customer">
                            <option value="">Seleccione ...</option>
                            <?php foreach ($customers as $customer): ?>
                                <option value="<?= $customer->id ?>"><?= $customer->name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col s12 m6 input-field">
                        <label for="invoice_status" class="active">Estado de Factura</label>
                        <select class="select2 browser-default" id="invoice_status" name="status">
                            <option value="Pendiente">Pendiente</option>
                            <option value="Paga">Paga</option>
                            <option value="Todos">Todos</option>
                        </select>
                    </div>
                </div>
                <div class="row mt-1">
                    <div class="col s12 m6 input-field">
                        <label for="payroll_method_id" class="active">Metodo de pago</label>
                        <select class="select2 browser-default" name="account" id="payroll_method_id">
                            <option value="">Seleccione ...</option>
                            <?php foreach ($paymentMethod as $item): ?>
                                <option value="<?= $item->id ?>"><?= $item->name . ' (' . $item->percent . '%)' ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col s12 m6 input-field">
                        <label for="headquarters" class="active">Sedes</label>
                        <select class="select2 browser-default" name="headquarters" id="headquarters">
                            <option value="">Seleccione ...</option>
                            <?php foreach ($headquarters as $item): ?>
                                <option value="<?= $item->id ?>"><?= $item->company ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#!" class="modal-action modal-close waves-effect waves-red btn-flat mb-5 ">Cerrar</a>
                <button class="modal-action waves-effect waves-green btn indigo mb-5">Filtrar</button>
            </div>
        </div>
    </form>

    <form action="" method="post" id="form" enctype="multipart/form-data">
        <div id="update-file" class="modal" role="dialog" style="height:auto; width: 600px">
            <div class="modal-content">
                <h4 class="modal-title">Registrar pago <small
                            class="right left-align-sm"><?= ucfirst('[<span id="name_customer" style="display:inline;  width: 100px; overflow: hidden; height: 16px;"></span>]'); ?></small>
                </h4>
                <div class="row">
                    <div class="input-field col s12 m6">
                        <input id="value" class="valid" name="value" type="number" step="any"
                               placeholder="Valor a Pagar" required>
                        <label for="value">Valor <span class="text-red red-text darken-1">*</span></label>
                    </div>
                    <div class="input-field col s12 m6">

                        <select class="browser-default" id="payment_method_id" name="payment_method_id">
                            <option value="" disabled>Seleccione ...</option>
                            <optgroup label="Metodos">
                                <?php foreach ($paymentMethod as $item): ?>
                                    <option value="<?= $item->id ?>">[<?= $item->code ?>]
                                        - <?= ucfirst($item->name) ?> </option>
                                <?php endforeach; ?>
                            </optgroup>
                            <optgroup label="Facturas">
                                <?php foreach ($paysInvoices as $paysInvoice): ?>
                                    <?php if ($paysInvoice->payable_amount > 0): ?>
                                        <option value="<?= "fact-".$paysInvoice->id ?>">[<?= $paysInvoice->resolution ?>]
                                            - $<?= number_format($paysInvoice->payable_amount, '2', ',', '.') ?> </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </optgroup>
                        </select>
                        <label for="payment_method_id" class="active">Medio de pago <span
                                    class="text-red red-text darken-1">*</span></label>
                    </div>
                    <div class="col s12 input-field">

                        <textarea id="description" name="description" placeholder="Descripción"
                                  class="materialize-textarea validate"
                                  required></textarea>
                        <label for="description">Descripción <span class="text-red red-text darken-1">*</span></label>
                    </div>

                    <div class="col s12">
                        <div class="file-field input-field">
                            <div class="btn indigo">
                                <span>Soporte</span>
                                <input type="file" name="soport">
                            </div>
                            <div class="file-path-wrapper">
                                <input class="file-path validate"
                                       type="text"
                                       name="nameFile"
                                       placeholder="Subir soporte"
                                       id="soporte">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#!"
                   class="modal-action modal-close waves-effect waves-red btn-flat mb-5 btn-light-indigo ">Cerrar</a>
                <button class="modal-action     waves-effect waves-green btn indigo mb-5">
                    Guardar
                </button>
            </div>
        </div>
    </form>
</div>


<?php $this->endSection() ?>

<?php $this->section('scripts') ?>
<script src="<?= base_url('/js/advance-ui-modals.js') ?>"></script>
<script src="<?= base_url('/js/shepherd.min.js') ?>"></script>
<script src="<?= base_url('/js/views/discharge.js') ?>"></script>
<?php $this->endSection() ?>
