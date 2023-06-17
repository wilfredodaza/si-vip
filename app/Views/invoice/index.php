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
                    <div class="col s10 m12 l12 breadcrumbs-left">
                        <h5 class="breadcrumbs-title mt-0 mb-0 display-inline hide-on-small-and-down ">
                            <span>
                               Facturador
                                <a class="btn btn-small  darken-1 step-1 help purple"
                                   style="padding-left: 10px ; padding-right: 10px;">Ayuda</a>
                            </span>
                        </h5>
                        <ol class="breadcrumbs mb-0">
                            <li class="breadcrumb-item"><a href="<?= base_url() ?>/home">Home</a></li>
                            <li class="breadcrumb-item active"><a href="#">Facturador</a></li>
                        </ol>
                        <!-- <a href="#send-multiple" class="btn btn-small blue darken-2 right step-2 modal-trigger mr-1"
                           style="padding-left:15px;padding-right:15px;">
                            <span class="left">Emitir todo</span>
                            <i class="material-icons right">send</i>
                        </a> -->
                    </div>
                </div>
            </div>
        </div>
        <div class="col s12">
            <div class="container">
                <div class="section">
                    <div class="card">
                        <div class="card-content">
                            <button data-target="filter"
                                    class="right btn btn-small  btn-light-indigo modal-trigger step-5 active-red">
                                Filtrar <i class="material-icons right">filter_list</i>
                            </button>
                            <?php if (!$manager): ?>
                            <a href="<?= base_url('/invoice/create') ?>"
                               class="btn right  indigo mr-1 step-2 active-red">Crear Factura</a>
                            <?php endif;?>
                            <p class="">
                                Podrá realizar las facturas de venta, notas crédito y notas débito y el posterior envió
                                tanto al DIAN (Dirección de Impuestos y Aduanas Nacionales) como al cliente.
                            </p>
                            <table class="table responsive-table">
                                <thead>
                                <tr>
                                    <th class="center">Número</th>
                                    <th class="center">Fecha</th>
                                    <th class="center">Tipo Documento</th>
                                    <?php if ($manager): ?>
                                        <th class="center">Sede</th>
                                    <?php endif; ?>
                                    <th class="center">Cliente</th>
                                    <th class="center">Total</th>
                                    <th class="center step-4">Estado</th>
                                    <th class="center step-3 ">Acciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($invoices as $item): ?>

                                    <tr>
                                        <td class="center"><?= $item['resolution'] ?>
                                        <td class="center"><?= $item['created_at'] ?></td>
                                        <td class="center"><?= $item['type_document'] ?></td>
                                        <?php if ($manager): ?>
                                            <td class="center"><?= $item['company'] ?></td>
                                        <?php endif ?>
                                        <td class="center"><?= ucwords($item['customer']) ?></td>
                                        <td class="center" width="100px">
                                            $ <?= number_format($item['payable_amount'], '0', '.', '.') ?>
                                        </td>
                                        <td class="center state" width="150px">
                                            <?php switch ($item['status']) {
                                                case 'Guardada':
                                                    echo '<span class="badge new pink darken-1 " style="width:140px;" data-badge-caption="' . $item['status'] . '" ></span>';
                                                    break;
                                                case 'Enviada a la DIAN':
                                                    echo '<span  class="badge new yellow darken-2"  style="width:140px;" data-badge-caption="' . $item['status'] . '"></span>';
                                                    break;
                                                case 'Email Enviado':
                                                    echo '<span  class="badge new light-blue" style="width:140px;"  data-badge-caption="' . $item['status'] . '"></span>';
                                                    break;
                                                case 'Recibido por el cliente':
                                                    echo '<span class="badge new green lighten-1"  style="width:140px;"  data-badge-caption="' . $item['status'] . '"></span>';
                                                    break;
                                                case 'Rechazada':
                                                    echo '<span class="badge new red darken-2"  style="width:140px;"  data-badge-caption="' . $item['status'] . '"></span>';
                                                    break;
                                                case 'Cargando':
                                                    echo '<span class="badge new orange darken-2"  style="width:140px;"  data-badge-caption="' . $item['status'] . '"></span>';
                                                    break;
                                            }
                                            ?>
                                        </td>
                                        <td class="center">
                                            <div class="btn-group" role="group">
                                                <?php if ($item['status'] != 'Guardada' && $item['status'] != 'Rechazada' && $item['status'] != 'Cargando'): ?>
                                                    <a href="<?= base_url() ?>/invoice/pdf/<?= $item['id_invoice'] ?>"
                                                       class="btn btn-small  pink darken-1  tooltipped"
                                                       data-position="top" data-tooltip="Descargar factura">
                                                        <i class="material-icons">insert_drive_file</i>
                                                    </a>
                                                <?php endif; ?>
                                                <?php if ($item['status'] == 'Guardada' || $item['status'] == 'Rechazada'  || $item['status'] == 'Cargando'): ?>
                                                    <a href="<?= base_url() ?>/invoice/preview/<?= $item['id_invoice'] ?>"
                                                       class="btn btn-small pink darken-1  tooltipped"
                                                       data-position="top" data-tooltip="Descargar factura">
                                                        <i class="material-icons">insert_drive_file</i>
                                                    </a>
                                                <?php endif; ?>

                                                    <?php if ($item['status'] == 'Guardada' || $item['status'] == 'Rechazada'): ?>
                                                        <?php if ($item['type_document'] != 'Nota Crédito' && $item['type_document'] != 'Nota Débito' && $item['status'] != 'Cargando'): ?>

                                                        <a href="<?= base_url() ?>/invoice/edit/<?= $item['id_invoice'] ?>"
                                                           class="btn btn-small yellow darken-2 tooltipped"
                                                           data-position="top" data-tooltip="Editar factura"
                                                            <?= ($manager) ? 'disabled' : '' ?>>
                                                            <i class="material-icons">create</i>
                                                        </a>
                                                        <?php endif; ?>
                                                           <?php if ($item['status'] == 'Guardada'  || $item['status'] == 'Cargando' || $item['status'] == 'Rechazada'): ?>
                                                        <button href="<?= base_url() ?>/invoice/send/<?= $item['id_invoice'] ?>"
                                                           class="btn btn-small  light-blue send tooltipped modal-trigger"
                                                           data-position="top" data-id="<?= $item['id_invoice'] ?> "
                                                           data-target="<?php if($item['type_documents_id'] == 1 ||  $item['type_documents_id'] == 2 ):  echo 'resolutions-invoice'; elseif($item['type_documents_id'] == 4):  echo 'resolutions-credit';  elseif($item['type_documents_id'] == 5):  echo 'resolutions-debit'; endif ?>"
                                                           data-tooltip="Enviar Factura a la DIAN" <?= ($item['status'] != 'Guardada' && $item['status'] != 'Rechazada') ? 'disabled' : '' ?>>
                                                            <i class="material-icons">send</i>
                                                        </button>
                                                        <?php endif; ?>
                                                        <?php if ($item['status'] == 'Rechazada'): ?>
                                                        <?php $errors = json_decode($item['errors']); ?>
                                                            <button class="btn btn-small red darken-2  modal-trigger showError tooltipped"
                                                                    data-target="modalErrors" type="button" data-position="top"
                                                                    data-tooltip="Inconvenientes"
                                                                    data-error="<?= showErrors($errors->data, $errors->type) ?>"
                                                                    data-error-url="<?= base_url().route_to('invoice-validation', $item['id_invoice']) ?>" >
                                                                <i class="material-icons">info_outline</i>
                                                            </button>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                <?php if ($item['status'] != 'Cargando'  && $item['status'] != 'Rechazada'): ?>
                                                    <button
                                                            class="deleteInvoice btn btn-small red darken-2  tooltipped <?= (!is_null($item['resolution'])) ? 'hide' : '' ?>"
                                                            data-position="top"
                                                            data-id="<?= $item['id_invoice'] ?>"
                                                            data-tooltip="Eliminar factura">
                                                        <i class="material-icons">delete</i>
                                                    </button>
                                                <?php endif; ?>

                                          
                                                <?php if ($item['status'] != 'Guardada' && $item['status'] != 'Cargando'  && $item['status'] != 'Rechazada'): ?>

                                                    <a href="<?= base_url() ?>/invoice/email/<?= $item['companies_id'] ?>/<?= $item['id_invoice'] ?>"
                                                       class="btn btn-small blue darken-1 tooltipped email sprint-load"
                                                       data-position="top" data-tooltip="Enviar email"  data-sprint-text="Enviando email"
                                                        <?= $item['status'] == 'Recibido por el cliente' ? 'disabled' : '' ?> >
                                                        <i class="material-icons white-text">email</i>

                                                    </a>
                                                    <button class="btn btn-small grey lighten-3  modal-trigger otros tooltipped"
                                                            data-target="modal1"  type="button" data-position="top" data-tooltip="Mas opciones"
                                                             data-id="<?= $item['id_invoice'] ?>" data-type="<?= $item['type_document'] ?>">
                                                        <i class="material-icons grey-text text-darken-4">add</i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                            <?php if (count($invoices) == 0): ?>
                                <p class="center red-text pt-1">No hay ningún elemento en el facturador.</p>
                            <?php endif ?>
                            <?= $pager->links(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <form action="" method="get">
        <div id="filter" class="modal" role="dialog">
            <div class="modal-content">
                <h4>Filtrar</h4>
                <div class="row">
                    <div class="col s12 m6  resolution campus input-field" v-if="resolution">
                        <label for="Cliente" :class="{'active': true}">Buscar</label>
                        <input id="resolution" type="text" name="value" placeholder="Buscar">
                    </div>
                    <div class="col s12 m6  Tipo_de_factura campus input-field" v-if="Tipo_de_factura">
                        <label for="Tipo_de_factura" :class="{active: true}">Buscar</label>
                        <select class="browser-default" name="value" id="Tipo_de_factura">
                            <option value="1">Factura de Venta Nacional</option>
                            <option value="2">Factura de Exportación</option>
                            <option value="4">Nota Crédito</option>
                            <option value="5">Nota Débito</option>
                        </select>
                    </div>
                    <div class="col s12 m6  Cliente campus input-field" v-if="Cliente">
                        <label for="Cliente" :class="{active: true}">Buscar</label>
                        <select class="browser-default" type="text" name="value" id="Cliente">
                            <?php foreach ($customers as $customer): ?>
                                <option value="<?= $customer->id ?>">[<?= $customer->identification_number ?>]
                                    - <?= $customer->name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col s12 m6 Estado campus input-field" v-if="Estado">
                        <label for="Estado" class="active">Buscar</label>
                        <select class="browser-default" type="text" name="value" id="Estado">
                            <option value="1">Guardada</option>
                            <option value="2">Enviada a la DIAN</option>
                            <option value="3">Email Enviado</option>
                            <option value="4">Recibido por el cliente</option>
                        </select>

                    </div>
                    <div class="col s12 m6 input-field">
                        <label for="filter" class="active">Puedes filtrar por:</label>
                        <select name="campo" id="filters" class="browser-default " v-model="filter" @change="select()">
                            <option value="resolution">Número Factura</option>
                            <option value="Tipo_de_factura">Tipo de documento</option>
                            <option value="Cliente">Cliente</option>
                            <option value="Estado">Estado</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#!" class="modal-action modal-close waves-effect  btn-flat btn-light-indigo ">Cerrar</a>
                <button class="modal-action waves-effect waves-green btn indigo">Guardar</button>
            </div>
        </div>
    </form>
</div>


<div class="container-sprint-send" style="display:none;">
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
    <span style="width: 100%; text-align: center; color: white;  display: block; " class="text-insert">Validando documento y enviando a la DIAN</span>
</div>


<!--modal encargado de la resolucion del documento soporte-->
<form action="" method="post" class="form-resolution">
    <div id="resolutions-invoice" class="modal">
        <div class="modal-content">
            <div class="row">
                <div class="col s12 ">
                    <label for="customer">Resolución</label>
                    <select class="browser-default" id="resolution_id" name="resolution_id">
                        <option value="" disabled>Seleccione ...</option>
                        <?php foreach ($resolutions as $resolution): ?>
                            <?php if($resolution->type_documents_id == 1): ?>
                                <option value="<?= $resolution->id ?>"><?= $resolution->prefix ?> <?= $resolution->from . ' - ' . $resolution->to ?></option>
                            <?php endif; ?>
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


<!--modal encargado de la resolucion de nota credito-->
<form action="" method="post" class="form-resolution">
    <div id="resolutions-credit" class="modal">
        <div class="modal-content">
            <div class="row">
                <div class="col s12 ">
                    <label for="customer">Resolución</label>
                    <select class="browser-default" id="resolution_id" name="resolution_id">
                        <option value="" disabled>Seleccione ...</option>
                        <?php foreach ($resolutions as $resolution): ?>
                            <?php if($resolution->type_documents_id == 4): ?>
                                <option value="<?= $resolution->id ?>" selected><?= $resolution->prefix ?> <?= $resolution->from . ' - ' . $resolution->to ?></option>
                            <?php endif; ?>
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
<!--end modal encargado de la resolucion de nota credito->


<!--modal encargado de la resolucion de nota debito-->
<form action="" method="post" class="form-resolution">
    <div id="resolutions-debit" class="modal">
        <div class="modal-content">
            <div class="row">
                <div class="col s12 ">
                    <label for="customer">Resolución</label>
                    <select class="browser-default" id="resolution_id" name="resolution_id">
                        <option value="" disabled>Seleccione ...</option>
                        <?php foreach ($resolutions as $resolution): ?>
                            <?php if($resolution->type_documents_id == 5): ?>
                                <option value="<?= $resolution->id ?>" selected><?= $resolution->prefix ?> <?= $resolution->from . ' - ' . $resolution->to ?></option>
                           <?php endif; ?>
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
<!--end modal encargado de la resolucion de nota debito->


<!--modal encargado de la resolucion del documento soporte-->
<form action="<?= base_url() . route_to('invoice.sendMultiple') ?>" method="POST" >
    <div id="send-multiple" class="modal" style="width: 550px !important;">
        <div class="modal-content">
            <p>
                Al enviar masivamente las facturas debes esperar un momento mientras el sistema termina de cargar las
                nóminas, pero puedes seguir trabajando en los otros módulos de MiFacturaLegal.com sin ningún problema.
                <br>
                Puedes recargar la página para validar el cargue de las facturas.
            </p>
            <div class="row">
                <div class="col s12 ">
                    <label for="customer">Resolucion</label>
                    <select class="browser-default" id="resolution_id" name="resolution_id">
                        <option value="" disabled>Seleccione ...</option>
                        <?php foreach ($resolutions as $resolution): ?>
                              <?php if($resolution->type_documents_id == 1): ?>
                            <option value="<?= $resolution->id ?>" selected><?= $resolution->prefix ?> <?= $resolution->from . ' - ' . $resolution->to ?></option>
                            <?php endif ?>
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


<div class="container-sprint-email" style="display:none;">
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
    <span style="width: 100%; text-align: center; color: white;  display: block;">Enviando Email</span>
</div>


<!-- Modal Structure -->
<div id="modal1" class="modal" style="height: auto; width: 400px;">
    <div class="modal-content">
        <h4 class="modal-title">Opciones</h4>

        <a href="" class="btn btn-block yellow darken-2" id="noteCredit">Nota Crédito</a><br>
        <a href="" class="btn btn-block green darken-2" id="noteDebit">Nota Débito</a><br>
        <a href="" class="btn blue btn-block" id="DIAN" target="_blank" style="margin-bottom: 20px;">Valide con la
            DIAN</a>
        <a href="" class="btn  btn-block indigo" id="attached" target="_blank">Descargar Attachment Document</a>
    </div>
    <div class="modal-footer">
        <a href="#!" class="modal-action modal-close waves-effect waves-green btn-flat btn-light-indigo">Cerrar</a>
    </div>
</div>

<!-- Modal errors -->
<div id="modalErrors" class="modal" style="height: auto; width: 700px;">
    <div class="modal-content">
        <h4 class="modal-title">Inconvenientes</h4>
        <div id="divErrors">

        </div>
    </div>
    <div class="modal-footer">
        <a href="#!" class="modal-action modal-close waves-effect waves-green btn-flat btn-light-indigo">Cerrar</a>
    </div>
</div>


<div style="bottom: 50px; right: 19px;" class="fixed-action-btn direction-top"><a
            class="btn-floating btn-large gradient-45deg-light-blue-cyan gradient-shadow"><i
                class="material-icons">add</i></a>
    <ul>
        <li><a href="https://api.whatsapp.com/send?phone=+573013207088&text=Hola, tengo una duda en mifacturalegal."
               target="_blank" class="btn-floating light-green darken-3"><i class="fa fa-whatsapp"></i></a></li>
    </ul>
</div>
<!-- eliminar factura -->
<div class="container-sprint-delete" style="display:none;">
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
    <span style="width: 100%; text-align: center; color: white;  display: block;">Eliminando</span>
</div>


<?= $this->endSection() ?>



<?= $this->section('scripts') ?>
<script src="<?= base_url('/js/advance-ui-modals.js') ?>"></script>
<script src="<?= base_url('/js/shepherd.min.js') ?>"></script>
<script src="<?= base_url('/js/vue.js') ?>"></script>
<script src="<?= base_url('/assets/js/sweetalert.min.js') ?>"></script>
<script src="<?= base_url('/js/sprint.js') ?>"></script>

<script src="<?= base_url('/js/ui-alerts.js') ?>"></script>
<script src="<?= base_url('/js/views/invoice1.js') ?>"></script>
<?= $this->endSection() ?>



