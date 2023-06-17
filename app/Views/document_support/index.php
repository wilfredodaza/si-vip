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
                    <div class="col s12 m12 l12 breadcrumbs-left">
                        <h5 class="breadcrumbs-title mt-0 mb-0 display-inline hide-on-small-and-down">
                            <span>
                                Documentos Soporte
                                <a class="btn btn-small light-blue darken-1 step-1 help" style="padding-left: 10px ; padding-right: 10px;">Ayuda</a>
                            </span>
                        </h5>
                        <ol class="breadcrumbs mb-0">
                            <li class="breadcrumb-item"><a href="<?= base_url() ?>/home">Home</a></li>
                            <li class="breadcrumb-item active"><a href="#">Documentos Soporte</a></li>
                        </ol>
                        <?php if(session('user')->role_id == 2): ?>
                            <a href="#import_data_excel" class="btn btn-small green darken-2 right step-2 modal-trigger" style="padding-left:15px;padding-right:15px;" <?php if (count($resolutions) == 0 && session('user')->role_id != 5) : ?> disabled <?php endif ?>>
                                <span class="left">Importar</span>
                                <svg style="width:15px; display: block; margin-top:5px; margin-left:20px;" class="right" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="file-excel" class="svg-inline--fa fa-file-excel fa-w-12" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512">
                                    <path fill="currentColor" d="M224 136V0H24C10.7 0 0 10.7 0 24v464c0 13.3 10.7 24 24 24h336c13.3 0 24-10.7 24-24V160H248c-13.2 0-24-10.8-24-24zm60.1 106.5L224 336l60.1 93.5c5.1 8-.6 18.5-10.1 18.5h-34.9c-4.4 0-8.5-2.4-10.6-6.3C208.9 405.5 192 373 192 373c-6.4 14.8-10 20-36.6 68.8-2.1 3.9-6.1 6.3-10.5 6.3H110c-9.5 0-15.2-10.5-10.1-18.5l60.3-93.5-60.3-93.5c-5.2-8 .6-18.5 10.1-18.5h34.8c4.4 0 8.5 2.4 10.6 6.3 26.1 48.8 20 33.6 36.6 68.5 0 0 6.1-11.7 36.6-68.5 2.1-3.9 6.2-6.3 10.6-6.3H274c9.5-.1 15.2 10.4 10.1 18.4zM384 121.9v6.1H256V0h6.1c6.4 0 12.5 2.5 17 7l97.9 98c4.5 4.5 7 10.6 7 16.9z"></path>
                                </svg>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col s12">
            <div class="container">
                <div class="section">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-title">
                                Documentos Soporte
                                <a href="<?= base_url().route_to('document_support.create') ?>" class="btn btn-small btn-sm indigo right step-2"
                                    <?php if (count($resolutions) == 0 && session('user')->role_id != 5) : ?> disabled <?php endif ?>>
                                    Crear Documento
                                    <i class="material-icons right">add</i>
                                </a>
                                <button data-target="filter" class="btn btn-small btn-light-indigo modal-trigger  right" style="margin-right: 5px;">
                                    Filtrar <i class="tiny material-icons right">filter_list</i>
                                </button>
                            </div>
                            <table class="table table-responsive">
                                <thead>
                                    <tr>
                                        <th class="center">#</th>
                                        <th class="center">Fecha de creación</th>
                                        <th class="center">Tipo de documento</th>
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
                                        if(($item->type_documents_id == 106 || $item->type_documents_id == 11 || $item->type_documents_id == 105) ):
                                    ?>
                                        <tr>
                                            <td class="center"><?= $item->prefix ?? '' ?> <?= $item->resolution ?? '' ?></td>
                                            <td class="center"><?= $item->created_at ?></td>
                                            <td class="center"><?= $item->type_document_name ?></td>
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
                                                        <a href="<?= base_url().route_to('document_support.edit', $item->id) ?>"  class="btn btn-small  yellow darken-1 tooltipped" data-position="top" data-tooltip="Editar Documento">
                                                            <i class="material-icons">create</i>
                                                        </a>
                                                    <?php endif ?>
                                                    <?php if($item->invoice_status_id == 8 && session('user')->role_id != 5): ?>
                                                        <a href="<?= base_url().route_to('document_support.send', $item->id) ?>" data-id="<?= $item->id ?>" data-target="resolution"  class="btn btn-small blue darken-1 send  modal-trigger tooltipped" data-position="top" data-tooltip="Enviar Documento">
                                                            <i class="material-icons">send</i>
                                                        </a>
                                                    <?php endif ?>
                                                    <?php if(($item->invoice_status_id == 9 || $item->invoice_status_id == 10) && session('user')->role_id != 5): ?>
                                                        <a href="<?= base_url().route_to('document_support.email', $item->id) ?>" class="btn btn-small blue darken-1 tooltipped" data-position="top"  data-tooltip="Enviar Email">
                                                            <i class="material-icons">email</i>
                                                        </a>
                                                        <a href="<?= base_url().route_to('document_support.upload_file', $item->id) ?>" class="btn btn-small  grey lighten-5 grey-text text-darken-4 tooltipped" data-position="top"  data-tooltip="Otras opciones">
                                                            <i class="material-icons">add</i>
                                                        </a>
                                                    <?php endif ?>
                                                </div>

                                            </td>
                                        </tr>
                                    <?php
                                        endif;
                                    endforeach; ?>
                                    <?php
                                    foreach ($documentSupports as $item) :
                                        if(($item->type_documents_id == 106) && session('user')->role_id == 5):
                                    ?>
                                    <tr>
                                        <td class="center"><?= $item->prefix ?? '' ?> <?= $item->resolution ?? '' ?></td>
                                        <td class="center"><?= $item->created_at ?></td>
                                        <td class="center"><?= $item->type_document_name ?></td>
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
                                                    <a href="<?= base_url().route_to('document_support.edit', $item->id) ?>"  class="btn btn-small  yellow darken-1 tooltipped" data-position="top" data-tooltip="Editar Documento">
                                                        <i class="material-icons">create</i>
                                                    </a>
                                                    <a href="<?= base_url().route_to('document_support.send', $item->id) ?>" data-id="<?= $item->id ?>" data-target="resolution"  class="btn btn-small blue darken-1 send  modal-trigger tooltipped" data-position="top" data-tooltip="Enviar Documento">
                                                        <i class="material-icons">send</i>
                                                    </a>
                                                <?php endif ?>
                                                <?php if(($item->invoice_status_id == 9 || $item->invoice_status_id == 10) && session('user')->role_id != 5): ?>
                                                    <a href="<?= base_url().route_to('document_support.email', $item->id) ?>" class="btn btn-small blue darken-1 tooltipped" data-position="top"  data-tooltip="Enviar Email">
                                                        <i class="material-icons">email</i>
                                                    </a>
                                                    <a href="<?= base_url().route_to('document_support.upload_file', $item->id) ?>" class="btn btn-small  grey lighten-5 grey-text text-darken-4 tooltipped" data-position="top"  data-tooltip="Otras opciones">
                                                        <i class="material-icons">add</i>
                                                    </a>
                                                <?php endif ?>
                                            </div>

                                        </td>
                                    </tr>
                                    <?php
                                        endif;
                                    endforeach; ?>
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
        </div>
    </div>
</div>

    <!--modal de  alerta fija--->
    <?php if (count($resolutions) == 0  && session('user')->role_id != 5) : ?>
    <div id="information" class="modal information open" role="dialog" style="height:auto; width: 600px; z-index: 1003; display: block; opacity: 1; top: 10%; transform: scaleX(1) scaleY(1);">
        <div class="modal-content">
            <h4 class="modal-title">Información</h4>
            <p> Actualmente no existe una resolución de docuemnto soporte por favor solicite la resolución en la DIAN y enviela a soporte@iplanetcolombia.com.</p>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modals-action modals-close waves-effect waves-red btn-flat mb-5 ">Cerrar</a>
        </div>
    </div>
    <div class="modal-overlay" style="z-index: 1002; display: block; opacity: 0.5;"></div>
<?php endif ?>
    <!--end modal de  alerta fija--->

    <!--modal de filtro de busqeuda-->
    <form action="" method="get">
    <div id="filter" class="modal" role="dialog" style="height:auto; width: 600px">
        <div class="modal-content">
            <h4 class="modal-title">Filtrar</h4>
            <div class="row">
                <div class="col s12 m6 input-field">
                    <input type="date" id="start_date" name="start_date" value="<?= isset($_GET['start_date']) ? $_GET['start_date'] : '' ?>">
                    <label for="start_date">Fecha de inicio</label>
                </div>
                <div class="col s12 m6 input-field">
                    <input id="end_date" type="date" name="end_date" value="<?= isset($_GET['end_date']) ? $_GET['end_date'] : '' ?>">
                    <label for="end_date">Fecha fin</label>
                </div>
                <div class="col s12 m6">
                    <label for="customer">Proveedor/Vendedor</label>
                    <select class="browser-default" id="customer" name="customer">
                        <option value="">Seleccione ...</option>
                        <?php foreach ($providers as $provider) : ?>
                            <option value="<?= $provider->id ?>" <?= (isset($_GET['customer']) && $_GET['customer']  == $provider->id) ? 'selected' : '' ?>>
                                [<?= $provider->identificationNumber ?>] - <?= $provider->name ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col s12 m6">
                    <label for="invoice_status">Estado de documento</label>
                    <select class="select2 browser-default" id="invoice_status" name="status">
                        <option value="0">Todos</option>
                        <?php foreach ($invoicesStatus as $item) : ?>
                            <option value="<?= $item->id ?>" <?= (isset($_GET['status']) && $_GET['status']  == $item->id) ? 'selected' : '' ?>>
                                <?= $item->name ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <a href="<?= base_url('document_support') ?>" class="modals-action modals-close waves-effect  btn-flat mb-5 btn-red">Limpiar</a>
            <a href="#!" class="modals-action modals-close waves-effect waves-red btn-flat mb-5 ">Cerrar</a>
            <button class="modals-action waves-effect waves-green btn indigo mb-5">Guardar</button>
        </div>
    </div>
</form>
    <!--end modal de filtro de busqeuda-->

    <!--modal de rechazo del documento-->
    <form action="" method="POST" id="form-cancel">
        <div id="modal-rejection" class="modal">
            <div class="modal-content">
                <h6>Rechazar documento soporte</h6>
                <label for="">Por favor escriba el motivo del rechazo</label>
                <textarea id="textarea1" class="materialize-textarea" name="observation"></textarea>
            </div>
            <div class="modal-footer">
                <a href="#!" class="modal-action  modal-close waves-effect waves-green btn-flat btn-light-indigo">Cerrar</a>
                <button class="btn indigo">Guardar</button>
            </div>
        </div>
    </form>
    <!--end de rechazo del documento-->

    <!--modal de importe por excel del documento soporte-->
    <form action="<?= base_url() ?>/document_support/upload_file_excel" method="POST" id="form-upload_document" enctype="multipart/form-data">
        <!-- Modal Structure -->
        <div id="import_data_excel" class="modal">
            <div class="modal-content">
                <h6>Cargar Archivo</h6>
                <di class="row">
                    <div class="col s12">
                        <div class="file-field input-field">
                            <div class="btn indigo">
                                <span>Cargar Excel</span>
                                <input type="file" name="file">
                            </div>
                            <div class="file-path-wrapper">
                                <input class="file-path validate" type="text">
                            </div>
                        </div>
                    </div>
                </di>
            </div>
            <div class="modal-footer">
                <a href="#!" class="modal-action  modal-close waves-effect waves-green btn-flat btn-light-indigo">Cerrar</a>
                <button class="btn indigo">Guardar</button>
            </div>
        </div>
    </form>
    <!--end modal de importe por excel del documento soporte-->

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
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
    <script src="<?= base_url('/js/shepherd.min.js') ?>"></script>
    <script src="<?= base_url('/js/ui-alerts.js') ?>"></script>
    <script src="<?= base_url('/js/advance-ui-modals.js') ?>"></script>
    <script src="<?= base_url('/js/modules/document_support/index.js') ?>"></script>
    <script src="<?= base_url('/js/sprint.js') ?>"></script>
<?= $this->endSection() ?>