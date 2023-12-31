<?= $this->extend('layouts/main') ?>


<?= $this->section('title') ?> Entradas y salidas <?= $this->endSection() ?>
<?= $this->section('styles') ?>
    <link rel="stylesheet" type="text/css"
          href="<?= base_url() ?>/app-assets/vendors/data-tables/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css"
          href="<?= base_url() ?>/app-assets/vendors/data-tables/extensions/responsive/css/responsive.dataTables.min.css">
    <link rel="stylesheet" type="text/css"
          href="<?= base_url() ?>/app-assets/vendors/data-tables/css/select.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>/app-assets/css/pages/data-tables.css">
    <style>

        .dropzone {
            border: #a53394 dashed 2px;
            height: 200px;
        }

        .container-sprint-send {
            background: rgba(0, 0, 0, 0.51);
            z-index: 2000;
            position: absolute;
            width: 100%;
            top: 0px;
            height: 100vh;
            justify-content: center !important;
            align-content: center !important;
            flex-wrap: wrap;
            display: none;
        }
    </style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

    <div id="main">
        <div class="row">
            <div class="breadcrumbs-inline pt-3 pb-1" id="breadcrumbs-wrapper">
                <div class="container">
                    <div class="row">
                        <div class="col s12">
                            <?php if (session('success')): ?>
                                <div class="card-alert card green">
                                    <div class="card-content white-text">
                                        <?= session('success') ?>
                                    </div>
                                    <button type="button" class="close white-text" data-dismiss="alert"
                                            aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                </div>
                            <?php endif; ?>
                            <?php if (session('error')): ?>
                                <div class="card-alert card red
">
                                    <div class="card-content white-text">
                                        <?= session('error') ?>
                                    </div>
                                    <button type="button" class="close white-text" data-dismiss="alert"
                                            aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                </div>
                            <?php endif; ?>
                            <?php if (session('warning')): ?>
                                <div class="card-alert card yellow darken-2
">
                                    <div class="card-content white-text">
                                        <?= session('warning') ?>
                                    </div>
                                    <button type="button" class="close white-text" data-dismiss="alert"
                                            aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col s10 m6 l6 breadcrumbs-left">
                            <h5 class="breadcrumbs-title mt-0 mb-0 display-inline hide-on-small-and-down ">
                            <span>
                                Entradas y Salidas
                                <a class="btn btn-small light-blue darken-1 sept-1 help "
                                   style="padding-left: 10px; padding-right: 10px;">Ayuda</a>
                            </span>
                            </h5>
                            <ol class="breadcrumbs mb-0">
                                <li class="breadcrumb-item"><a href="<?php base_url() ?>/home">Home</a></li>
                                <li class="breadcrumb-item active"><a href="#">Subir Documentos</a></li>
                            </ol>

                        </div>
                        <div class="col s2 m6 l6">
                            <!--   <a class="btn  dropdown-settings waves-effect waves-light white  text-black black-text breadcrumbs-btn left"
                           style="vertical-align: center; position:relative; padding-left: 50px;  border-radius: 20px; margin: 5px;"
                           href="#!" data-target="dropdown1">
                            <img src="<?= base_url() ?>/assets/img/google_driver.png" alt="" width="25px" style="position:absolute;top:5px; left: 18px;">
                        </a>
                     <a class="btn  dropdown-settings waves-effect waves-light white  text-black black-text breadcrumbs-btn right"
                           style="vertical-align: center; position:relative; padding-left: 50px;  border-radius: 20px; margin: 5px;"
                           href="#!" data-target="dropdown1">
                            <img src="<?= base_url() ?>/assets/img/OneDriver.png" alt="" width="25px" style="position:absolute;top:5px; left: 18px;">
                            Outlook
                        </a>-->
                        </div>
                    </div>
                </div>
            </div>

            <?php $validation = (session('user')->role_id == 1 || session('user')->role_id == 15 || session('user')->role_id == 17) ? true : false ?>

            <div class="col s12">
                <div class="card">
                    <div class="card-content">
                        <?php if($validation || session('user')->role_id == 19): ?>
                            <a href="<?= base_url() ?>/inventory/create"
                            class="waves-effect waves-light blue darken-2 mr-1 darken-1 pull-right btn  sept-2 active-red"
                            style="margin-bottom:20px; padding-right: 10px; padding-left: 10px;" data-target="modal1">
                                <i class="material-icons right">add</i>
                                Entrada Remisión
                            </a>
                        <?php endif ?>
                        <a href="<?= base_url() ?>/inventory/create/out"
                           class="waves-effect waves-light red darken-2 mr-1 darken-1 pull-right btn  sept-2 active-red"
                           style="margin-bottom:20px; padding-right: 10px; padding-left: 10px;" data-target="modal1">
                            <i class="material-icons right">add</i>
                            Salida Remisión
                        </a>
                        <button data-target="filter"
                            style="margin-left: 5px;"
                            class="right btn btn-small btn-light-indigo modal-trigger step-5 active-red">
                            Filtrar <i class="material-icons right">filter_list</i>
                        </button>
                        <div class="row">
                            <table class="responsive-table">
                                <thead>
                                <tr>
                                    <th class="text-center">Fecha</th>
                                    <th class="text-center">Tipo de documento</th>
                                    <th class="text-center sept-4">Estado</th>
                                    <th class="text-center">Documento</th>
                                    <th class="text-center sept-5">Origen</th>
                                    <th class="text-center">Destino</th>
                                    <th class="text-center">Cliente/Proovedor</th>
                                    <th class="text-center">Valor Venta</th>
                                    <th class="text-center sept-3">Acciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($documents as $key => $document): ?>
                                    <tr>
                                        <td class="text-center">
                                            <?php if ($document->type_documents_id_invoices == 107 || $document->type_documents_id_invoices == 108 || $document->type_documents_id_invoices == 115 || $document->type_documents_id_invoices == 116 || $document->type_documents_id_invoices == 119): ?>
                                                <?= $document->created_at_invoice ?>
                                            <?php else: ?>
                                                <?= $document->created_at ?>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?= $document->type_documents_name ?? '' ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($document->type_documents_id_invoices == 107 || $document->type_documents_id_invoices == 108  || $document->type_documents_id_invoices == 119): ?>
                                                <?php if ($document->invoice_status_id_invoices == 28):?>
                                                    <span class="new badge tooltipped red darken-2"
                                                          data-position="top" data-badge-caption=""
                                                          data-tooltip="Anulado">Anulado</span>
                                                <?php else: ?>
                                                    <span class="new badge tooltipped purple" data-position="top"
                                                          data-badge-caption="" data-tooltip="Guardado">
                                                        Guardado
                                                   </span>
                                                <?php endif; ?>
                                            <?php elseif ($document->type_documents_id_invoices == 115 || $document->type_documents_id_invoices == 116):
                                                if ($document->invoice_status_id_invoices == 22):
                                                    ?>
                                                    <span class="new badge tooltipped yellow darken-2"
                                                          data-position="top" data-badge-caption=""
                                                          data-tooltip="Pendiente">Pendiente</span>
                                                <?php elseif ($document->invoice_status_id_invoices == 21): ?>
                                                    <span class="new badge tooltipped green" data-position="top"
                                                          data-badge-caption=""
                                                          data-tooltip="Finalizado">Finalizado</span>
                                                <?php elseif ($document->invoice_status_id_invoices == 20): ?>
                                                    <span class="new badge tooltipped red" data-position="top"
                                                          data-badge-caption=""
                                                          data-tooltip="Rechazado">Rechazadodo</span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="new badge tooltipped <?= $document->color_status ?>"
                                                      data-position="top" data-badge-caption=""
                                                      data-tooltip="<?= $document->status_description ?>">
                                                   <?= $document->status ?>
                                               </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?= $document->prefix . $document->resolution ?>
                                        </td>
                                        <td class="text-center">
                                            <?php
                                            if ($document->type_documents_id_invoices == 108 || $document->type_documents_id_invoices == 115 ) {
                                                echo $document->company_name;
                                            }else if($document->type_documents_id_invoices == 107 || $document->type_documents_id_invoices == 101 || $document->type_documents_id_invoices == 116 || $document->type_documents_id_invoices == 119){
                                                echo $document->company_name;
                                            } else {
                                                if ($document->status_id != 1) {
                                                    if (empty($document->provider)) {
                                                        echo '<span class="tooltipped"  data-position="top" data-badge-caption=""  data-tooltip="El proveedor no concuerda.">
                                                        <i class="material-icons small text-red red-text breadcrumbs-title" >brightness_1</i>
                                                    </span>' . $document->provider;
                                                    } else {
                                                        echo '<span class="tooltipped"  data-position="top" data-badge-caption=""  data-tooltip="Proveedor Ok">
                                                    <i class="material-icons small text-green green-text" >brightness_1</i>
                                                    </span>' . $document->company_name;
                                                    }
                                                } 
                                            }
                                            ?>
                                        </td>
                                        <td class="text-center">
                                            <?php
                                            if ($document->type_documents_id_invoices == 108 || $document->type_documents_id_invoices == 115  || $document->type_documents_id_invoices == 119) {
                                                echo $document->company_name_destination;
                                            }else if($document->type_documents_id_invoices == 107 || $document->type_documents_id_invoices == 101 || $document->type_documents_id_invoices == 116){
                                                echo $document->company_name_destination;
                                            } else {
                                                if (isset($document->customer_id)) {
                                                    $errors = validationRowsNull($document->customer_id);
                                                    if (count($errors) > 0) {
                                                        $text = '';
                                                        foreach ($errors as $error) {
                                                            $text .= $error . '<br>';
                                                        }
                                                        echo '<span class="tooltipped"  data-position="top" data-badge-caption=""  data-tooltip="' . $text . '">
                                                                <i class="material-icons small text-yellow yellow-text"  >brightness_1</i>
                                                                </span> ' . $document->customer_name;
                                                    } else {
                                                        echo '<span class="tooltipped"  data-position="top" data-badge-caption=""  data-tooltip="Cliente Ok">
                                                                <i class="material-icons small text-green green-text" >brightness_1</i>
                                                        </span> ' . $document->customer_name;
                                                    }
                                                }
                                            }
                                            ?>
                                        </td>
                                        <td class="text-center">
                                            <?= $document->type_documents_id_invoices == 115 ? $document->user_name : $document->customer_name ?>
                                        </td>
                                        <td class="text-center">
                                            <?= $document->type_documents_id_invoices == 107 ?  ($validation ?  '$ '.number_format($document->payable_amount, 2, ',', '.') : '') :'$ '.number_format($document->payable_amount, 2, ',', '.') ?>
                                        </td>
                                        <td style="display: flex; justify-content: center;">
                                            <div class="btn-group" role="group">
                                                
                                                <a href="<?= base_url() . '/reports/view/' . $document->invoices_id ?>"
                                                    target="_blank"
                                                   class="btn btn-small green darken-1  tooltipped" data-position="top" data-tooltip="ver detalle">
                                                    <i class="material-icons">insert_drive_file</i>
                                                </a>
                                            </div>
                                            <?php if (($document->type_documents_id_invoices == 107 || $document->type_documents_id_invoices == 108) ): ?>
                                                
                                                    <?php if($validation  && $document->invoice_status_id_invoices != 28): ?>
                                                    <a href="<?= base_url().'/inventory/edit/'.$document->invoices_id ?>"
                                                       class="btn btn-small yellow darken-2 send tooltipped step-4 next-tour"
                                                       style="padding:0px 10px;" data-position="top"
                                                       data-tooltip="Editar Remisión"><i
                                                                class="material-icons ">create</i></a>

                                                    <?php endif ?>
                                                    <?php if($validation && $document->type_documents_id_invoices == 108 && $document->invoice_status_id_invoices != 28): ?>
                                                        <a href="javascript:void(0)"
                                                       class="btn btn-small red darken-2 tooltipped"
                                                       onclick="CancelRemision(<?= $document->invoices_id ?>)"
                                                       style="padding:0px 10px;" data-position="top"
                                                       data-tooltip="Anular Remisión"><i
                                                                class="material-icons ">delete_forever</i></a>
                                                    <?php endif ?>
                                            <?php elseif ($document->type_documents_id_invoices == 115 || $document->type_documents_id_invoices == 116):
                                                if ($document->invoice_status_id_invoices != 22):
                                                    ?>
                                                    <!--<div class="group">
                                                        <a href="#"
                                                           class="btn btn-small btn-light-blue-grey tooltipped step-8"
                                                           download="" style="padding:0px 10px;" data-position="top"
                                                           data-tooltip="Descargar documento"><i class="material-icons">cloud_download</i></a>
                                                    </div>-->
                                                <?php else: ?>
                                                    <div class="group">
                                                        <a href="<?= base_url() ?>/inventory/edit_out_transfer/<?= $document->invoices_id ?>"
                                                           class="btn btn-small yellow darken-2 send tooltipped step-4 next-tour"
                                                           style="padding:0px 10px;" data-position="top"
                                                           data-tooltip="Editar Remisión"><i class="material-icons">create</i></a>
                                                    </div>
                                                <?php endif; ?>
                                            <?php elseif ($document->type_documents_id_invoices == 119): ?>
                                            <?php else: ?>
                                                <div class="btn-group z-depth-1">
                                                    <?php if ($document->status_id == 1): ?>
                                                        <a href="<?= base_url('/documents/validations/' . $document->id) ?>"
                                                           class="btn btn-small yellow darken-2 send tooltipped step-4 next-tour"
                                                           style="padding:0px 10px;" data-position="top"
                                                           data-tooltip="Validar factura"><i class="material-icons">file_upload</i></a>
                                                    <?php endif; ?>
                                                    <?php if ($document->status_id == 2): ?>
                                                        <a href="<?= base_url('/documents/products/' . $document->id) ?>"
                                                           class="btn btn-small green tooltipped up-inventory step-5 next-tour"
                                                           style="padding:0px 10px;" data-position="top"
                                                           data-tooltip="Subir al inventario"><i class="material-icons">assignment</i></a>
                                                    <?php endif; ?>


                                                    <?php if ($document->status_id != 1): ?>
                                                        <a href="<?=
                                                        isset($document->new_name) && !empty($document->new_name) ?
                                                            getenv('API') . '/download/' . $document->identification_number . '/' . $document->new_name
                                                            : 'https://catalogo-vpfe.dian.gov.co/document/searchqr?documentKey=' . $document->uuid
                                                        ?>" class="btn btn-small btn-light-blue-grey tooltipped step-8"
                                                           download="<?= $document->name ?>" style="padding:0px 10px;"
                                                           data-position="top"
                                                           data-tooltip="Descargar Factura"><i class="material-icons">cloud_download</i></a>
                                                    <?php endif; ?>
                                                    <?php if ($document->status_id != 1): ?>
                                                        <a href="<?= base_url('documents/payment/' . $document->invoices_id) ?>"
                                                           class="btn btn-small purple darken-2    tooltipped step-8 payment_upload"
                                                           data-document_id="<?= $document->invoices_id ?>"
                                                           style="padding:0px 10px;" data-position="top"
                                                           data-tooltip="Subir Pago" data-target="modal2"><i
                                                                    class="material-icons">receipt</i>
                                                        </a>
                                                    <?php endif; ?>
                                                    <a href="<?= base_url('/documents/delete/' . $document->id) ?>"
                                                       class="btn btn-small  red darken-2 tooltipped step-8"
                                                       style="padding:0px 10px;" data-position="top"
                                                       data-tooltip="Eliminar factura"><i
                                                                class="material-icons">delete</i>
                                                    </a>

                                                </div>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                            <?php if (count($documents) == 0): ?>
                                <p class="center red-text" style="padding: 10px;">No hay ningún elemento en el módulo
                                    cargue de documentos.</p>
                            <?php endif; ?>

                            <?= $pager->links() ?>
                            <!--<div id="modal1" class="modals"  role="dialog">
                                    <div class="modals-content">
                                        <form action="<?= base_url('documents/upload_files') ?>" class="dropzone dropzone_file " id="my-dropzone"  method="post">
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <a href="#!"
                                           class="modals-action modals-close  waves-effect btn-flat btn-light-indigo ">Cerrar</a>
                                        <button class="modals-action modal-close waves-effect waves-green indigo btn  btn-save-upload  next-tour step-3 " id="submit-all" >
                                            Guardar
                                        </button>
                                    </div>
                                </div>-->

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--<div class="modals" id="download" style="width: 450px">
        <div class="modals-content">
            <h6>Descargar Factura</h6>
            <br>
            <a  class="btn purple document-zip" style="display: block; width: 100%;">Descargar ZIP</a>
            <br>
            <a class="btn purple document-pdf" style="display: block; width: 100%;">Descargar PDF</a>
        </div>
        <div class="modal-footer">
            <a href="#!"
               class="modals-action modals-close  waves-effect btn-flat btn-light-indigo ">Cerrar</a>
        </div>
    </div>-->


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
        <span style="width: 100%; text-align: center; color: white;  display: block; ">Cargando documento</span>
    </div>

        <!-- modal filtro -->
<form action="" method="GET" autocomplete="off">
    <div id="filter" class="modal" role="dialog" style="height:auto; width: 600px">
        <div class="modal-content">
            <h5>Filtrar</h5>
            <div class="row">
                <div class="col s6 input-field">
                    <select class="select2 browser-default validate" name="customers" id="customers_id">
                        <option value="">Seleccione un Cliente/Proveedor</option>
                        <?php foreach($customers as $customer): ?>
                            <option value="<?= $customer->id ?>" <?= isset($_GET['customers']) && !empty($_GET['customers']) && $_GET['customers'] == $customer->id ? 'selected' : ''  ?> ><?= $customer->name ?></option>
                        <?php endforeach ?>
                    </select>
                    <label for="customers_id">Cliente/Proveedor</label>
                </div>
                <div class="col s6 input-field">
                    <select class="select2 browser-default validate" name="type_documents" id="type_documents_id">
                        <option value="">Seleccione un tipo de documento</option>
                        <?php foreach($type_documents_name as $type): ?>
                            <option value="<?= $type->id ?>" <?= isset($_GET['type_documents']) && !empty($_GET['type_documents']) && $_GET['type_documents'] == $type->id ? 'selected' : ''  ?>><?= $type->name ?></option>
                        <?php endforeach ?>
                    </select>
                    <label for="type_documents_id">Tipo de documento</label>
                </div>
            </div>
            <div class="row">
                <div class="col s12 m6 input-field">
                    <input type="date" id="start_date" name="start_date"
                           value="<?= $_GET['start_date'] ?? '' ?>">
                    <label for="start_date">Fecha de inicio</label>
                </div>
                <div class="col s12 m6 input-field">
                    <input id="end_date" type="date" name="end_date"
                           value="<?= $_GET['end_date'] ?? '' ?>">
                    <label for="end_date">Fecha fin</label>
                </div>
            </div>
            <div class="row">
                <div class="col s12 input-field">
                    <label for="resolution">Documento</label>
                    <input type="text" id="resolution" name="resolution" value="<?= $_GET['resolution'] ?? '' ?>">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-action modal-close waves-effect  btn-flat btn-light-indigo ">Cerrar</a>
            <button class="btn indigo">Buscar</button>

        </div>
    </div>
</form>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
            <script src="<?= base_url('/js/advance-ui-modals.js') ?>"></script>
    <script src="<?= base_url() ?>/app-assets/vendors/data-tables/js/jquery.dataTables.min.js"></script>
    <script src="<?= base_url() ?>/app-assets/vendors/data-tables/extensions/responsive/js/dataTables.responsive.min.js"></script>
    <script src="<?= base_url() ?>/app-assets/vendors/data-tables/js/dataTables.select.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?= base_url(['assets', 'js', 'new_scripts', 'funciones.js']) ?>"></script>
    <script>
        $(document).ready(function () {
            const table = [];
            table['inventory'] = $(`#table_inventory`).DataTable({
                "ajax": {
                    "url": `<?= base_url() ?>/inventory/table`,
                    "dataSrc": ''
                },
                "columns": [
                    {data: 'tableDate', "width": "20%"},
                    {data: 'tableDocumentName'},
                    {data: 'tableStatus'},
                    {data: 'tableDocument'},
                    {data: 'tableClient'},
                    {data: 'tableActions'},
                    {data: 'tableProvider'},
                    {data: 'tableUuid'},

                ],
                "responsive": true,
                "scrollX": true,
                "ordering": false,
                "autoWidth": false,
                language: {url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"},
                initComplete: (data) => {
                    console.log(data)
                }
            });
            table['inventory'].on('draw', function () {
                $('.material-tooltip').remove();
                $('.tooltipped').tooltip();
                $('.dropdown-trigger').dropdown({
                    inDuration: 300,
                    outDuration: 225,
                    constrainWidth: false, // Does not change width of dropdown to that of the activator
                    hover: false, // Activate on hover
                    gutter: 0, // Spacing from edge
                    coverTrigger: false, // Displays dropdown below the button
                    alignment: 'center', // Displays dropdown with edge aligned to the left of button
                    stopPropagation: false // Stops event propagation
                });
            })
        });

        function CancelRemision(id){
            Swal.fire({
                title: '¿Esta seguro de anular esta remisión?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Si',
                cancelButtonText: 'No'
            }).then(async(result) => {
                if (result.isConfirmed) {
                    const { value: respuesta } = await Swal.fire({
                        title: 'Motivo de anulación',
                        html: `
                        <p>
                            <textarea id="motivo" class="materialize-textarea"></textarea>
                            <label for="motivo">Motivo</label>
                        </p>`,
                        preConfirm: () => {
                            const description = $('#motivo').val();
                            return {'motivo': description};
                        }
                    });
                    var data = JSON.stringify(respuesta);

                    Swal.fire({
                        title: 'Cancelando remisión',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading()
                        },
                    });
                    var url = `${ base_url(['api', 'v2', 'inventories', id]) }`;
                    var result = proceso_fetch(url, data, 'DELETE');
                    result.then((result) => {
                        console.log(result);
                        if(result.status !== 201){
                            throw Error(result.data);
                        }
                        Swal.fire({
                            title: 'La remision fue anulada con exito',
                            icon: 'success',
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: 'Ok'
                        }).then((result) => {
                            location.reload();
                        })
                    }).catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: error.message,
                        })
                    })
                }
            })
        }
    </script>
<?= $this->endSection() ?>