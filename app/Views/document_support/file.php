<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?> Documento Soporte <?= $this->endSection() ?>
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
                               Documentos Soporte
                            </span>
                            </h5>
                            <ol class="breadcrumbs mb-0">
                                <li class="breadcrumb-item"><a href="<?= base_url() ?>/home">Home</a></li>
                                <li class="breadcrumb-item active"><a href="#">Documentos Soporte</a></li>
                            </ol>

                        </div>
                    </div>
                </div>
            </div>
            <?php if(session('user')->role_id != 5): ?>
                <div class="col s12 m12">


                    <div class="card " >
                        <div class="card-content">
                            <div class="card-title">
                                Documento Soporte de Ajuste
                                <a  href="<?= base_url().route_to('document_support.index') ?>" class="btn btn-light-indigo btn-sm btn-small right">
                                    Regresar
                                    <i class="material-icons right">keyboard_return</i>
                                </a>
                                <a  href="<?=  base_url('document_support_adjust/create/'.$id) ?>" class="btn indigo btn-sm btn-small right mr-1">
                                    Crear
                                    <i class="material-icons right">add</i>
                                </a>
                            </div>
                            <div>
                                <table class="table table-responsive">
                                    <thead>
                                    <tr>
                                        <th class="center">#</th>
                                        <th class="center">Fecha de creación</th>
                                        <th class="center">Proveedor/Vendedor</th>
                                        <th class="center">Total</th>
                                        <th class="center step-4">Estado</th>
                                        <th class="center step-3">Acciones</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $i = count($documentSupports);
                                    foreach ($documentSupports as $item) :
                                        ?>
                                        <tr>
                                            <td class="center"><?= $item->prefix ?? '' ?> <?= $item->resolution ?? '' ?></td>
                                            <td class="center"><?= $item->created_at ?></td>
                                            <td class="center"><?= $item->customer ?></td>
                                            <td class="center"><?= number_format($item->total, '2', ',', '.') ?></td>
                                            <td class="center">
                                                <?php if ($item->invoice_status_id == 8): ?>
                                                    <span class="badge new pink darken-1" data-position="top" data-badge-caption="" data-tooltip="<?= $item->status ?>">
                                                      Guardado
                                                    </span>
                                                <?php endif ?>
                                                <?php if ($item->invoice_status_id == 9): ?>
                                                    <span class="new badge tooltipped yellow darken-2" data-position="top" data-badge-caption="" data-tooltip="<?= $item->status ?>">
                                                        <?=$item->type_documents_id == 106 || $item->type_documents_id == 105 ? $item->status   : 'Enviando a la DIAN' ?>
                                                    </span>
                                                <?php endif ?>
                                                <?php if ($item->invoice_status_id == 10): ?>
                                                    <span class="new badge tooltipped blue" data-position="top" data-badge-caption="" data-tooltip="<?= $item->status ?>">
                                                         <?=$item->type_documents_id == 106 || $item->type_documents_id == 105 ? $item->status   : 'Email enviado' ?>
                                                    </span>
                                                <?php endif ?>
                                                <?php if ($item->invoice_status_id == 11): ?>
                                                    <span class="new badge tooltipped red darken-2" data-position="top" data-badge-caption="" data-tooltip="<?= $item->status ?>">
                                                        <?= $item->status ?>
                                                    </span>
                                                <?php endif ?>
                                            </td>
                                            <td class="center">
                                                <!-- -->
                                                <div class="btn-group">
                                                    <?php if($item->invoice_status_id == 8): ?>
                                                        <a href="<?= base_url().route_to('document_support.previsualization', $item->id) ?>" class="btn btn-small pink darken-1 tooltipped" data-position="top" target="_blank" data-tooltip="Descargar PDF">
                                                            <i class="material-icons">insert_drive_file</i>
                                                        </a>
                                                    <?php else: ?>
                                                        <a href="<?= base_url().route_to('document_support.pdf', $item->id) ?>" class="btn btn-small pink darken-1 tooltipped" data-position="top" target="_blank" data-tooltip="Descargar PDF">
                                                            <i class="material-icons">insert_drive_file</i>
                                                        </a>
                                                    <?php endif ?>
                                                    <?php if($item->invoice_status_id == 8): ?>
                                                        <a href="<?= base_url().route_to('document_support.send', $item->id) ?>" data-id="<?= $item->id ?>" data-target="resolution"  class="btn btn-small blue darken-1 send  modal-trigger tooltipped" data-position="top" data-tooltip="Enviar Documento">
                                                            <i class="material-icons">send</i>
                                                        </a>
                                                    <?php endif ?>
                                                    <?php if($item->invoice_status_id == 9): ?>
                                                        <a href="<?= base_url().route_to('document_support.email', $item->id) ?>" class="btn btn-small blue darken-1 tooltipped" data-position="top"  data-tooltip="Enviar Email">
                                                            <i class="material-icons">email</i>
                                                        </a>
                                                    <?php endif ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <?php if (count($documentSupports) == 0) : ?>
                                    <p class="center red-text pt-1">No hay ningún elemento en el facturador.</p>
                                <?php endif ?>
                                <?= $pager->links(); ?>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col s12 m12">
                    <div class="container">
                        <div class="section">
                            <div class="card " >
                                <div class="card-content" style="height: auto;">
                                    <div class="card-title">
                                        Documentos Adjuntos
                                    </div>
                                    <div class="row">
                                        <div class="col s12 ">
                                            <table>
                                                <tbody>
                                                <?php if($customer->rut): ?>
                                                    <tr>
                                                        <th style="padding: 0px; text-overflow: ellipsis;  overflow: hidden;">Rut</th>
                                                        <td  style="padding: 5px; width: 100px;text-overflow: ellipsis;">
                                                            <a style="padding-left: 10px;padding-right: 10px; margin: 0px;" href="<?= base_url('upload/rut/'. $customer->rut); ?>" target="_bland" class="btn btn-small purple right">Descargar</a>
                                                        </td>
                                                    </tr>
                                                <?php endif;?>
                                                <?php if($customer->bank_certificate): ?>
                                                    <tr>
                                                        <th style="padding: 0px; text-overflow: ellipsis;  overflow: hidden;">Certificado Bancario</th>
                                                        <td style="padding: 5px; width: 100px;text-overflow: ellipsis;">
                                                            <a style="padding-left: 10px;padding-right: 10px; margin: 0px;" href="<?= base_url('upload/bank_certificate/'. $customer->bank_certificate); ?>"  target="_bland" class="btn btn-small purple right">Descargar</a>
                                                        </td>
                                                    </tr>
                                                <?php endif;?>
                                                <?php foreach($invoiceDocuments as $item): ?>
                                                    <tr>
                                                        <th style="padding: 0px; text-overflow: ellipsis;  overflow: hidden;">
                                                            <span style="display: block;text-overflow: ellipsis;" ><?= $item->title ?></span> </th>
                                                        <th style="padding: 5px; width: 100px;text-overflow: ellipsis;">
                                                            <a class="btn purple btn-small right" style="padding-left: 10px;padding-right: 10px; margin: 0px;" href="<?= base_url('upload/attached_document/'. $item->file); ?>" target="_bland">Descargar</a>
                                                        </th>
                                                    </tr>
                                                <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                            <?php if(count($invoiceDocuments) == 0 && is_null($customer->bank_certificate) && is_null($customer->rut)): ?>
                                                <p class="red-text center">No hay documentos adjuntos.</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col s12 m12">
                    <div class="container">
                        <div class="section">
                            <div class="card">
                                <div class="card-content">
                                    <div class="card-title">
                                        Subir Documentos Adicionales
                                    </div>
                                    <div class="row">
                                        <form action="<?= base_url('document_support/payroll_document_support/'.$id) ?>" method="post" enctype="multipart/form-data">
                                            <div class="col s12 m6 input-field ">
                                                <textarea id="textarea2" name="description" class="materialize-textarea" placeholder="Descripción"></textarea>
                                                <label for="textarea2">Descripción</label>
                                            </div>
                                            <div class="col s12 m5">
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
                                            <div class="col s1">
                                                <br>
                                                <button class="btn mt-6 btn-light-indigo right" style="padding-left:10px; padding-right: 10px; ">Guardar</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col s12">
                    <div class="card " >
                        <div class="card-content">
                            <div class="card-title">
                                Seguimiento del documento
                            </div>
                            <div class="row">
                                <table>
                                    <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Seguimiento</th>
                                        <th class="center">Adjunto</th>
                                        <th class="center">Eliminar</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach($trackings as $item): ?>
                                        <tr>
                                            <td><?= $item->created_at?></td>
                                            <td>	<?=  $item->message ?></td>
                                            <td class="center">
                                                <?php if($item->file): ?>
                                                    <a href="<?= base_url('upload/attached_document/'. $item->file); ?>" class="btn purple tooltipped"  target="_blank" data-position="top" data-tooltip="Descargar Pago" style="padding-left: 10px;padding-right: 10px;">
                                                        <i class="material-icons">cloud_download</i>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="center">Sin adjunto</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="center">
                                                <a href="<?= base_url('tracking/delete/'. $item->id.'/'.$id); ?>" class="btn red tooltipped"  data-position="top" data-tooltip="Eliminar Seguimiento" style="padding-left: 10px;padding-right: 10px;">
                                                    <i class="material-icons">delete</i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <?php if(count($trackings) == 0): ?>
                                    <p class="text-red red-text center" style="margin-top: 10px;">No hay ningún seguimiento registrado en el sistema. </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <?php if(session('user')->role_id == 5): ?>
                <div class="col s12 m6" >
                    <div class="card " >
                        <div class="card-content">
                            <div class="card-title">
                                Motivo de rechazo
                            </div>
                            <div class="row">
                                <table>
                                    <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Seguimiento</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach($trackings as $item): ?>
                                        <td><?= $item->created_at?></td>
                                        <td>	<?=  $item->message ?></td>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <?php if(count($trackings) == 0): ?>
                                    <p class="text-red red-text center" style="margin-top: 10px;">No hay ningún seguimiento registrado en el sistema. </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col s12 m6">
                    <div class="card " >
                        <div class="card-content">
                            <div class="card-title">
                                Retenciones
                                <a href="<?= base_url('document_support') ?>" class="btn indigo right">Regresar</a>
                            </div>

                            <div>
                                <Retention-component/>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col s12 m12">
                    <div class="container">
                        <div class="section">
                            <div class="card">
                                <div class="card-content">
                                    <div class="card-title">
                                        Documentos Soporte
                                    </div>
                                    <div class="row">
                                        <div class="col s12 m6" >
                                            <?php if($customer->rut): ?>
                                                <label for="">Rut</label><br>
                                                <a href="<?= base_url('upload/rut/'. $customer->rut); ?>" target="_bland" class="btn">Descargar</a>
                                                <br><br>
                                            <?php endif;?>
                                            <?php if($customer->bank_certificate): ?>
                                                <label for="" style="padding-top:20px;">Certificado Bancario</label><br>
                                                <a href="<?= base_url('upload/bank_certificate/'. $customer->bank_certificate); ?>"  target="_bland" class="btn">Descargar</a>
                                            <?php endif;?>
                                        </div>
                                        <div class="col s12 m6">
                                            <label for="">Documentos adjuntos</label>
                                            <?php foreach($invoiceDocuments as $item): ?>
                                                <p><?= $item->title ?> <a href="<?= base_url('upload/attached_document/'. $item->file); ?>" target="_bland">Descargar</a></p>
                                            <?php endforeach; ?>
                                            <?php if(count($invoiceDocuments) == 0): ?>
                                                <p class="red-text">No hay documentos adjuntos.</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <br><br><br>
    <script>
        localStorage.setItem('item', <?=  $id ?>)
    </script>

<!--modal encargado de la resolucion del documento soporte-->
<form action="" method="POST" id="form-resolution">
    <div id="resolution" class="modal">
        <div class="modal-content">
            <div class="row">
                <div class="col s12 ">
                    <label for="customer">Resolucion</label>
                    <select class="browser-default" id="resolution_id" name="resolution_id">
                        <option value="" disabled>Seleccione ...</option>
                        <?php foreach ($resolutions as $resolution): ?>
                            <option value="<?= $resolution->id ?>"><?= $resolution->prefix ?> <?= $resolution->from.' - '.$resolution->to ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-action modal-close waves-effect waves-green btn-flat btn-light-indigo">Cerrar</a>
            <button class="btn indigo sprint-load" data-sprint-text="Enviado documento a la DIAN">Enviar</button>
        </div>
    </div>
</form>
<!--end modal encargado de la resolucion del documento soporte-->


<!--sprint loader-->
<div class="container-sprint-send">
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
    <span class="text-insert"></span>
</div>
<!--end sprint loader -->
<?= $this->endSection('content') ?>
<?= $this->section('scripts') ?>
<script src="<?= base_url('/js/shepherd.min.js') ?>"></script>
<script src="<?= base_url('/js/ui-alerts.js') ?>"></script>
<script src="<?= base_url('/js/advance-ui-modals.js') ?>"></script>
<script src="<?= base_url('/js/modules/document_support/file.js') ?>"></script>
<script src="<?= base_url('/js/sprint.js') ?>"></script>
<?= $this->endSection() ?>
