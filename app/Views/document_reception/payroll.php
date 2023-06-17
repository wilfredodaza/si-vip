<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?> Pagos  <?= $this->endSection() ?>

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
                                    Cargar Pago
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
                                <button  class="btn btn-sm right payment_upload  modal-trigger btn-small tooltipped indigo ml-1 step-2 active-red"  data-position="top" data-tooltip="Añadir Pago" data-target="modal2" data-document_id="<?= $id ?>">
                                    Añadir <i class="material-icons right">add</i>
                                </button>
                                <a href="<?= base_url().route_to('document-index') ?>" class="btn btn-small btn-light-indigo right btn-sm" >
                                    Regresar <i class="material-icons right">keyboard_return</i>
                                </a>
                                <br><br>
                                <table class="table-responsive">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th class="center">Fecha</th>
                                            <th class="center">Descripcion</th>
                                            <th class="center">Documento</th>
                                            <th  width="100px" class="center">Pago</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 1;
                                        foreach ($invoices as $item): ?>
                                            <tr>
                                                <td><?= $i++  ?></td>
                                                <td class="center"><?= $item->created_at ?></td>
                                                <td class="center"><?= $item->message ?></td>
                                                <td class="center"><?= $item->title ?></td>
                                                <td  width="100px" class="center">
                                                    <div class="btn-group">
                                                        <a href="<?= base_url('documents/download_file/'.$item->file) ?>" class="btn btn-small tooltipped  indigo" data-position="top" data-tooltip="Descargar Pago" style="padding: 2px 10px;">
                                                            <i class="material-icons">file_download</i>
                                                        </a>
                                                        <a href="<?= base_url('documents/delete_file/'. $item->invoice_document_upload_id.'/'.$item->tracking_customer_id.'/'.$item->invoice_id) ?>"
                                                           data-position="top" data-tooltip="Eliminar Pago" class="btn btn-small red tooltipped darken-2">
                                                            <i class="material-icons">delete</i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <?php if(count($invoices) == 0): ?>
                                    <p class="center red-text pt-1" >No hay ningún pago registrado.</p>
                                <?php endif; ?>
                                <?= $pager->links(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- formuario para subir  archivo-->
<form action="<?=  base_url('/documents/payment_upload') ?>" id="payment_upload_files"  method="post" enctype="multipart/form-data">
    <div id="modal2" class="modal"  role="dialog">
        <div class="modal-content">
            <h5>Subir Pago</h5>
            <br>
            <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
            <div class="row">
                <div class="input-field col s12">
                    <textarea id="textarea2" name="description" class="materialize-textarea" placeholder="Descripción"></textarea>
                    <label for="textarea2">Descripción</label>
                </div>
                <div class="col s12">
                    <div class="file-field input-field">
                        <div class="btn btn-small purple">
                            <span>Pago</span>
                            <input type="file" name="file" multiple>
                        </div>
                        <div class="file-path-wrapper">
                            <input class="file-path validate" type="text" name="file_name" placeholder="Cargar archivo">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#!"
               class="modal-action modal-close  waves-effect btn-flat btn-light-indigo ">Cerrar</a>
            <button class="modal-action modal-close waves-effect waves-green indigo btn     btn-save-upload  next-tour step-3 " id="submit-payment-upload">
                Guardar
            </button>
        </div>
    </div>
</form>
<!-- end formuario para subir  archivo-->
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
    <script src="<?= base_url('/js/ui-alerts.js') ?>"></script>
    <script src="<?= base_url('/js/ui-alerts.js') ?>"></script>
    <script src="<?= base_url('/js/advance-ui-modals.js') ?>"></script>
    <script src="<?= base_url('/js/modules/document_reception/payment.js') ?>"></script>
<?= $this->endSection() ?>



