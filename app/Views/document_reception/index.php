<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?> Recepción de Documentos <?= $this->endSection() ?>
<?= $this->section('styles') ?>
    <link rel="stylesheet" href="<?= base_url('/css/dropzone.min.css') ?>">
<?= $this->endsection('styles') ?>
<?= $this->section('content') ?>
<div id="main">
    <div class="row">
        <div class="breadcrumbs-inline pt-3 pb-1" id="breadcrumbs-wrapper">
            <div class="container">
                <div class="row">
                    <div class="col s12">
                        <?= $this->include('layouts/alerts') ?>
                        <?= $this->include('layouts/notification') ?>
                        <div class="col s10 m6 l6 breadcrumbs-left">
                            <h5 class="breadcrumbs-title mt-0 mb-0 display-inline hide-on-small-and-down ">
                                <span>
                                    Recepción de documentos
                                    <a class="btn btn-small light-blue darken-1 sept-1 help btn-sm">Ayuda</a>
                                </span>
                            </h5>
                            <ol class="breadcrumbs mb-0">
                                <li class="breadcrumb-item"><a href="<?php base_url() ?>/home">Home</a></li>
                                <li class="breadcrumb-item active"><a href="#">Subir Documentos</a></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col s12">
            <div class="card">
                <div class="card-content">
                    <a class="waves-effect waves-light darken-1 pull-right btn modals-trigger sept-2  documents-download ml-1  active-red" href="<?= base_url('reception_email/0') ?>">Descargar de Email</a>
                    <a class="waves-effect waves-light purple darken-1 pull-right btn modal-trigger sept-2 active-red btn-sm right"  data-target="modal1">
                        <i class="material-icons right">inbox</i>
                        Cargar Factura Electronica
                    </a>
                    <div class="row">
                        <div class="col s12">
                            <table class="responsive-table">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th class="center">Tipo de documento</th>
                                        <th class="center sept-4">Estado</th>
                                        <th class="center">Documento</th>
                                        <th class="center sept-5">Cliente</th>
                                        <th class="center">Provedor</th>
                                        <th class="center">CUFE - CUDE</th>
                                        <th class="center sept-3">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php  foreach($documents as $document): ?>
                                <tr>
                                    <td>
                                        <?= $document->created_at ?>
                                    </td>
                                    <td class="center">
                                        <?= $document->type_document_name ?>
                                    </td>
                                    <td class="center">
                                        <span class="new badge tooltipped <?= $document->color_status ?>" data-position="top" data-badge-caption="" data-tooltip="<?= $document->status_description ?>">
                                            <?= $document->status ?>
                                        </span>
                                    </td>
                                    <td class="center">
                                        <?= $document->prefix.$document->resolution ?>
                                    </td>
                                    <td class="center">
                                        <?php
                                              if($document->status_id != 1) {
                                                  if(!empty($document->provider)) {
                                                      echo  '<span class="tooltipped"  data-position="top" data-badge-caption=""  data-tooltip="El proveedor no concuerda.">
                                                        <i class="material-icons small text-red red-text breadcrumbs-title" >brightness_1</i>
                                                    </span>'.$document->provider;
                                                  }else {
                                                      echo '<span class="tooltipped"  data-position="top" data-badge-caption=""  data-tooltip="Proveedor Ok">
                                                    <i class="material-icons small text-green green-text" >brightness_1</i>
                                                    </span>'.$document->company_name;
                                                  }
                                              }
                                        ?>
                                    </td>
                                    <td class="center">
                                        <?php
                                                if(isset($document->customer_id)) {
                                                    $errors = validationRowsNull($document->customer_id);
                                                    if(count($errors) > 0) {
                                                        $text = '';
                                                        foreach($errors as $error) {
                                                            $text.= $error.'<br>';
                                                        }
                                                        echo '<span class="tooltipped"  data-position="top" data-badge-caption=""  data-tooltip="'.$text.'">
                                                                <i class="material-icons small text-yellow yellow-text"  >brightness_1</i>
                                                                </span> '. $document->customer_name;
                                                    } else {
                                                        echo '<span class="tooltipped"  data-position="top" data-badge-caption=""  data-tooltip="Cliente Ok">
                                                                <i class="material-icons small text-green green-text" >brightness_1</i>
                                                        </span> '.$document->customer_name;
                                                    }
                                                }
                                        ?>
                                    </td>
                                    <td class="center">
                                        <?php

                                                if(!empty($document->uuid)) {
                                                    if( $document->status_uuid == 'false') {
                                                        echo '<span class="tooltipped"  data-position="top"   data-tooltip="Factura Invalida">
                                                            <i class="material-icons small text-red red-text" >brightness_1</i>
                                                        </span> '. substr($document->uuid,-5);
                                                    } else {
                                                        echo '<span class="tooltipped"  data-position="top" data-badge-caption=""  data-tooltip="CUFE o CUDE Ok"><i class="material-icons small text-green green-text">brightness_1</i></span> '.substr($document->uuid,0, 10);
                                                    }
                                                }
                                        ?>

                                    </td>
                                    <td class="center">
                                        <div class="btn-group z-depth-1">
                                            <?php if($document->status_id == 1): ?>
                                                <a href="<?= base_url('/documents/validations/'.$document->id.'/1') ?>" class="btn btn-small yellow darken-2 send tooltipped step-4 next-tour"  data-position="top"
                                                data-tooltip="Validar factura"><i class="material-icons">file_upload</i></a>
                                            <?php endif; ?>
                                            <?php if($document->status_id == 2): ?>
                                                <a href="<?= base_url().route_to('document-associate-product', $document->id) ?>" class="btn btn-small green tooltipped up-inventory step-5 next-tour"  data-position="top"
                                                data-tooltip="Subir al inventario"><i class="material-icons">assignment</i></a>
                                            <?php endif; ?>
                                            <?php if($document->status_id != 1 ): ?>
                                            <a href="<?= base_url().route_to('document-show', esc($document->invoices_id)) ?>" class="btn btn-small yellow darken-2 tooltipped step-8 modals-trigger documents-download"  data-position="top" data-tooltip="Ver Factura"
                                            ><i class="material-icons">visibility</i></a>
                                            <?php endif; ?>
                                            <?php if($document->status_id != 1 ): ?>
                                                <a href="<?= base_url().route_to('document-payment', $document->invoices_id) ?>" class="btn btn-small purple darken-2 tooltipped step-8 payment_upload" data-document_id="<?= $document->invoices_id ?>"  data-position="top"
                                                   data-tooltip="Subir Pago" data-target="modal2"><i class="material-icons">receipt</i>
                                                </a>
                                            <?php endif; ?>
                                            <form action="<?= base_url().route_to('document-delete', esc($document->id)) ?>" method="post">
                                                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" />
                                                <input type="hidden" name="_method" value="DELETE" />
                                                <button  class="btn btn-small  red darken-2 tooltipped step-8 btn-delete-document"  data-position="top"  data-tooltip="Eliminar factura">
                                                    <i class="material-icons">delete</i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                            <?php if(count($documents) == 0): ?>
                                <p class="center red-text" style="padding: 10px;">No hay ningún elemento en el módulo cargue de documentos.</p>
                            <?php endif; ?>
                            <?= $pager->links() ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--Modal encargado del cargue del documento -->
<div id="modal1" class="modal"  role="dialog">
    <div class="modal-content">
        <form action="<?=  base_url().route_to('document-create') ?>" class="dropzone dropzone_file" id="my-dropzone"  method="post">
        </form>
    </div>
    <div class="modal-footer">
        <a href="#!"
           class="modal-action modal-close  waves-effect btn-flat btn-light-indigo ">Cerrar</a>
        <button class="modal-action modal-close waves-effect waves-green indigo btn  btn-save-upload  next-tour step-3 " id="submit-all" >
            Guardar
        </button>
    </div>
</div>
<!--end modal encargado del cargue del documento -->

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
    <span></span>
</div>
<!--end sprint loader -->
<?= $this->endSection('content') ?>

<?= $this->section('scripts') ?>
    <script src="<?= base_url('/js/advance-ui-modals.js') ?>"></script>
    <script src="<?= base_url('/js/ui-alerts.js') ?>"></script>
    <script src="<?= base_url('/js/dropzone.js') ?>"></script>
    <script src="<?= base_url('/js/modules/document_reception/index.js') ?>"></script>
    <script src="<?= base_url('/js/shepherd.min.js') ?>"></script>
    <script src="<?= base_url('/js/jquery.validate.js') ?>"></script>
    <script src="<?= base_url('/js/sweetalert.min.js') ?>"></script>
<?= $this->endSection('scripts') ?>


