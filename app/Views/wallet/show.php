<?= $this->extend('layouts/main') ?>


<?= $this->section('title') ?> Cartera <?= $this->endSection() ?>

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
                                Cartera
                                <a class="btn btn-small light-blue darken-1 step-1 help" style="padding-left: 10px; padding-right: 10px;">Ayuda</a>
                            </span>
                            </h5>
                            <ol class="breadcrumbs mb-0">
                                <li class="breadcrumb-item"><a href="<?= base_url() ?>/home">Home</a></li>
                                <li class="breadcrumb-item active"><a href="#"> Cartera</a></li>
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
                                <a href="<?= base_url('/wallet') ?>" class="btn  btn-light-indigo step-3 active-red right" >
                                    <i class="material-icons left">chevron_left</i> Regresar
                                </a>
                                <div class="clearfix"></div>
                                <div class="row mt-1">
                                    <div class="col s6 m4 l2">
                                        <strong>Número </strong>
                                    </div>
                                    <div class="col s6 m8 l10">
                                        <?= $invoice->resolution ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col s6 m4 l2">
                                        <strong>Total de Factura</strong>
                                    </div>
                                    <div class="col s6 m8 l10">
                                        <strong>$ <?= number_format($invoice->payable_amount - ($invoice->withholdings ), '2', ',', '.') ?></strong>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col s6 m4 l2">
                                        <strong>Saldo</strong>
                                    </div>
                                    <div class="col s6 m10">
                                        <strong>$ <?= number_format($invoice->payable_amount - ($invoice->withholdings + $invoice->balance + ($invoice->credit_note - $invoice->credit_note_withholdings)), '2', ',', '.') ?></strong>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col s6 m4 l2">
                                        Cliente
                                    </div>
                                    <div class="col s6 m8 l10">
                                        <?= $invoice->name ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col s6 m2">
                                        <strong>Fecha</strong>
                                    </div>
                                    <div class="col s6 m8 l10">
                                        <?= $invoice->created_at ?>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col s12">
                                        <div class="divider"></div>
                                        <h6>Pagos</h6>
                                        <div class="divider"></div>
                                        <table class="responsive-table">
                                            <thead>
                                            <tr>
                                                <th class="center">#</th>
                                                <th class="center">Fecha</th>
                                                <th class="center">Descripción</th>
                                                <th class="center">Valor</th>
                                                <th class="center step-2">Acciones</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php  $i = 1; foreach ($payments as $item): ?>
                                                <tr>
                                                    <td class="center"><?= $i++ ?></td>
                                                    <td class="center"><?= $item->created_at ?></td>
                                                    <td class="center"><?= $item->description ?> </td>
                                                    <td class="center">$ <?= number_format( $item->value , '2', ',', '.') ?></td>
                                                    <td class="center">
                                                        <div class="btn-group z-depth-1">
                                                            <a href="#update-file" class="btn  modal-trigger edit yellow darken-2 btn-small tooltipped" data-position="top" data-tooltip="Editar pago" data-id="<?= $item->id ?>" data-invoice="<?= $item->invoices_id ?>" data-toggle="modal" >
                                                                <i class="material-icons">create</i>
                                                            </a>
                                                            <?php if (!empty($item->soport)): ?>
                                                                <a target="_blank" class="btn blue btn-small tooltipped" data-position="top" data-tooltip="Descargar soporte" href="<?= base_url('/wallet/download/' . $item->soport) ?>">
                                                                    <i class="material-icons">file_download</i>
                                                                </a>
                                                            <?php endif; ?>
                                                            <button class="deleteInvoice btn btn-small red darken-2  tooltipped" data-position="top" data-id="<?= $item->id ?>" data-tooltip="Eliminar Pago">
                                                                <i class="material-icons">delete</i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php  $i++; endforeach; ?>
                                            </tbody>
                                        </table>
                                        <?php if (count($payments) == 0): ?>
                                            <p class="red-text red-text text-center center py-2 mt-1" >No hay datos
                                                registrados.</p>
                                        <?php endif; ?>
                                        <div class="divider mt-2"></div>
                                        <h6>Notas Crédito</h6>
                                        <div class="divider"></div>
                                        <table class="responsive-table">
                                            <thead>
                                            <tr>
                                                <th class="center">#</th>
                                                <th class="center">Fecha</th>
                                                <th class="center">Descripción</th>
                                                <th class="center">Valor</th>
                                                <th class="center step-2">Acciones</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php  $i = 1; foreach ($creditNotes as $item): ?>
                                                <tr>
                                                    <td class="center"><?= $i++ ?></td>
                                                    <td class="center"><?= $item->created_at ?></td>
                                                    <td class="center"><?= 'Nota Crédito - '. $item->prefix.'-'.$item->resolution ?> </td>
                                                    <td class="center">$ <?= number_format( $item->payable_amount - $item->credit_note_withholdings, '2', ',', '.') ?></td>
                                                    <td class="center">
                                                        <div class="btn-group z-depth-1">
                                                            <a href="<?= base_url('invoice/pdf/'.$item->id) ?>" data-position="top" data-tooltip="Descargar factura" class="btn btn-small red  darken-1 tooltipped">
                                                                <i class="material-icons">insert_drive_file</i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php  $i++; endforeach; ?>
                                            </tbody>
                                        </table>
                                        <?php if (count($creditNotes) == 0): ?>
                                            <p class="red-text red-text text-center center py-2 mt-1" >No hay datos
                                                registrados.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br><br><br><br><br>
    <form action="" method="post" id="form" enctype="multipart/form-data">
        <div id="update-file" class="modal" role="dialog"
             style="height:auto;  width: 600px;">
            <div class="modal-content">
                <h4 class="modal-title">Editar</h4>
                <div class="row">
                    <div class="col s12 m6 input-field">
                        <input id="value" class="valid" name="value" type="number" step="any" placeholder="Valor a Pagar" required>
                        <label for="value"  class="active">Valor <span class="text-red red-text darken-1">*</span></label>
                    </div>
                    <div class="col s12 m6 input-field">
                        <label for="payment_method_id" class="active">Medio de pago <span class="text-red red-text darken-1">*</span></label>
                        <select class="browser-default" id="payment_method_id" name="payment_method_id">
                            <option value="" disabled>Seleccione ...</option>
                            <?php foreach ($paymentMethod as $item): ?>
                                <option value="<?= $item->id ?>">[ <?= $item->code ?> ] -<?= $item->name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col s12 input-field">
                    <textarea id="description" name="description"
                              class="materialize-textarea validate" placeholder="Descripción" required></textarea>
                        <label for="description"  class="active">Descripción <span class="text-red red-text darken-1">*</span></label>
                    </div>
                    <div class="col s12">
                        <div class="file-field input-field ">
                            <div class="btn indigo">
                                <span>Soporte</span>
                                <input type="file" name="soport">
                            </div>
                            <div class="file-path-wrapper">
                                <input class="file-path validate" type="text" name="nameFile"
                                       placeholder="Subir Soporte"
                                       id="soporte">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" >
                <a href="#!" class="modal-action modal-close waves-effect waves-red btn-flat btn-light-indigo">Cerrar</a>
                <button class="modal-action waves-effect waves-green btn indigo">
                    Guardar
                </button>
            </div>
        </div>
    </form>

<?php $this->endSection() ?>

<?php $this->section('scripts') ?>
    <script src="<?= base_url('/js/advance-ui-modals.js') ?>"></script>
    <script src="<?= base_url('/js/shepherd.min.js') ?>"></script>
    <script src="<?= base_url('/assets/js/sweetalert.min.js') ?>"></script>
    <script src="<?= base_url('/js/views/wallet_show.js') ?>"></script>
<?php $this->endSection()?>