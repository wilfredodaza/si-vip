<?= $this->extend('layouts/main') ?>


<?= $this->section('title') ?> Facturación <?= $this->endSection() ?>
<?= $this->section('styles') ?>

    <script src="https://unpkg.com/feather-icons"></script>
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>/app-assets/vendors/sweetalert/sweetalert.css">

    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>/app-assets/vendors/data-tables/css/jquery.dataTables.min.css">
      
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>/app-assets/vendors/data-tables/extensions/responsive/css/responsive.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>/app-assets/vendors/data-tables/css/select.dataTables.min.css">

    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>/app-assets/css/pages/data-tables.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>/app-assets/vendors/dropify/css/dropify.min.css">

    <link rel="stylesheet" href="<?= base_url() ?>/app-assets/vendors/select2/select2.min.css" type="text/css">
    <link rel="stylesheet" href="<?= base_url() ?>/app-assets/vendors/select2/select2-materialize.css" type="text/css">
    <link rel="stylesheet" type="text/css" href="<?= base_url() ?>/app-assets/css/pages/form-select2.css">
<style>
    table.striped>tbody>tr>td,
    table.striped>tbody>tr>td {
        padding: 5px !important;
    }
    .dropzone {
        border: #a53394 dashed 2px;
        height: 200px;
    }
</style>
<?= $this->endSection() ?>


<?= $this->section('content') ?>
<div id="main">
    <div class="row">
        <div class="breadcrumbs-inline  pb-1" id="breadcrumbs-wrapper">
            <div class="container">
                <div class="row">
                    <div class="col s12">
                        <?= $this->include('layouts/alerts') ?>
                        <?= $this->include('layouts/notification') ?>
                    </div>
                    <div class="col s10 m6 l6 breadcrumbs-left">
                        <h4>Portal de Compras</h2>
                        <h5 class="breadcrumbs-title mt-0 mb-0 display-inline hide-on-small-and-down ">
                            <span>
                               Proyectos 
                            </span>
                        </h5>
                    </div>
                </div>
            </div>
        </div>

        <?php foreach($indicadores as $key => $indicador): ?>
            <div class="col s12 m6 l3">
                <div class="card padding-4 animate fadeLeft shop">
                    <div class="row">
                        <div class="col s6" id="indicador-<?= $indicador->id ?>">
                            <h5 class="mb-0"><?= $indicador->total ?></h5>
                            <p class="no-margin"><?= $indicador->name ?></p>
                            <!-- <p class="mb-0 pt-8 tooltipped" data-position="bottom" data-delay="50" data-html="true" data-tooltip="  <i class='material-icons text-green green-text tiny'>brightness_1</i> 2 Por vencerse 
                            <br><i class='material-icons text-yellow yellow-text tiny'>brightness_1</i> 0 Por vencerse 
                            <br><i class='material-icons text-red red-text tiny'>brightness_1</i> 1 Por vencerse "> <strong> 3 </strong> por vencer <i class="material-icons text-red red-text tiny">brightness_1</i></p> -->
                        </div>
                        
                        <div class="col s6 icon">
                            <i class="material-icons <?= $indicador->color ?> background-round mt-5 white-text"><?= $indicador->icon ?></i>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach ?>

    </div>
    <div class="row">
        <div class="col s12">
            <a class="waves-effect waves-light darken-1 pull-right btn modals-trigger sept-2  documents-download ml-1  active-red" onclick="reception_email()" href="<?= base_url(['reception_email', 1]) ?>">Descargar de Email</a>
            <!-- <a href="<?= base_url(['shopping_email']) ?>">Descargar emails</a> -->
            <div class="card">
                <div class="card-content" style="margin-bottom: 70px">
                    <div class="row">
                        <div class="col s12">
                            <ul class="tabs">
                                <?php foreach ($estados as $key => $estado): ?>
                                    <li class="tab col m3"><a class="<?= $key == 0 ? 'active': '' ?>" href="#status_<?= $estado->id ?>" onclick="reinit(`<?= $estado->id ?>`)"><?= $estado->name ?></a></li>
                                <?php endforeach ?>
                            </ul>
                        </div>
                        
                        <?php foreach ($estados as $key => $estado): ?>
                            <div id="status_<?= $estado->id ?>" class="col s12 section-data-tables">
                                <table class="display" id="table_<?= $estado->id ?>">
                                    <thead>
                                        <tr>
                                            <th>
                                                <a class="modal-trigger button-modal tooltipped" href="#type_document" onclick="type_document(`<?= $estado->id ?>`)" data-position="bottom" data-tooltip="Tipos de documentos">Tipo -
                                                    <i data-feather="alert-circle" height="15" width="15"></i>
                                                </a>
                                            </th>
                                            <th>#</th>
                                            <th>Fecha</th>
                                            <th>Proveedor</th>
                                            <th>Cliente</th>
                                            <th>D. Requeridos</th>
                                            <th>Valor <br> a Pagar</th>
                                            <th>DIAN ID</th>
                                            <th>Vence</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        <?php endforeach ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
</div>
<div id="add_file" class="modal modal-fixed-footer">
		<div class="modal-content">
			<h6>Cargar Archivos</h6>
			<br>
            <form action="<?= base_url(['shopping', 'file']) ?>" method="POST" enctype="multipart/form-data" id="form-file">
                <input type="hidden" name="invoices_id" id="invoices_id">
                <input type="hidden" name="exist" id="exist" value="false">
                <input type="hidden" name="shopping_id" id="shopping_id">
                <input type="hidden" name="url" value="<?= base_url(['shopping']) ?>">
                <div class="row">
                    <div class="input-field col s12">
                        <select id="select-archivo" name="type">
                            <option value="" disabled selected>Seleccione el tipo de archivo</option>
                            <?php foreach($types as $type): ?>
                                <option value="<?= $type->id ?>"><?= $type->name ?></option>
                            <?php endforeach ?>
                        </select>
                        <label>Tipo de archivo</label>
                    </div>
                    <div class="input-field col s12">
                            <input id="numero" type="text" name="numero" class="validate">
                            <label for="numero">Número</label>
                    </div>
                    <div class="input-field col s12">
                        <textarea id="observation" name="observation" class="materialize-textarea"></textarea>
                        <label for="observation">Observación</label>
                    </div>
                    <p>
                        <label>
                            <input class="with-gap" name="status" type="radio" value="Aceptado"/>
                            <span>Aceptar</span>
                        </label>
                        <label>
                            <input class="with-gap" name="status" type="radio" value="Rechazado"/>
                            <span>Rechazar</span>
                        </label>
                    </p>
                    <div class="row section">
                        <div class="col s12">
                                <input type="file" id="input-file" name="file" class="dropify-Es"/>
                        </div>
                    </div>
                </div>
            </form>
		</div>
		<div class="modal-footer">
				<a href="#!" class="modal-action modal-close waves-effect waves-purple btn-flat ">Cerrar</a>
				<a href="#!" class="modal-action btn purple waves-effect" onclick="guardar()">Guardar</a>
		</div>
</div>

<div id="created_product" class="modal modal-fixed-footer" style="height: 700px !important;">
    <div class="modal-content">
        <div class="row">
            <h6>Nuevo producto</h6>
            <form id="new_product_form">
                <input type="hidden" id="id_document">
                <input type="hidden" id="product_id">
                <input type="hidden" id="status">
                <div class="input-field col s12 m6">
                    <input type="text" class="validate" id="code" name="code" required>
                    <label for="code">Código <span class="text-red red-text darken-1">*</span></label>
                </div>
                <div class="input-field col s12 m6">
                    <input type="text"   class="validate" id="product" name="name" required>
                    <label for="product">Producto <span class="text-red red-text darken-1">*</span></label>
                </div>
                <div class="input-field col s12 m6">
                    <input type="number"   class="validate" id="value" name="value" required>
                    <label for="value">Valor <span class="text-red red-text darken-1">*</span></label>
                </div>
                <div class="input-field col s12 m6">
                    <select class="select2 browser-default validate" id="free" name="free">
                        <option value="false">No</option>
                        <option value="true">Si</option>
                    </select>
                    <label for="free">Gratis <span class="text-red red-text darken-1">*</span></label>
                </div>
    
                <div class="input-field col s12 m12" >
                    <textarea name="description" id="description" cols="30" rows="10" class="materialize-textarea"  class="validate" required></textarea>
                    <label for="description">Descripción <span class="text-red red-text darken-1">*</span></label>
                </div>
                <div class="input-field col s12 m6">
                    <label for="entry_credit" class="active">Ingreso <span class="text-red red-text darken-1">*</span></label>
                    <select class="select2 browser-default" name="entry_credit" id="entry_credit" required>
                    <?php foreach($entryCredit as $item): ?>
                        <option value="<?= $item->id ?>">[<?= $item->code ?>] - <?=  $item->name ?></option>
                    <?php endforeach; ?>
                    </select>
                </div>
                <div class="input-field col s12 m6">
                    <label for="entry_debit" class="active">Devolución <span class="text-red red-text darken-1">*</span></label>
                    <select class="select2 browser-default" name="entry_debit"  id="entry_debit"   class="validate" required>
                    <?php foreach($entryDebit as $item): ?>
                        <option value="<?= $item->id ?>"><?=  $item->name ?></option>
                    <?php endforeach; ?>
                    </select>
                </div>
                <div class="input-field col s12 m6">
                    <label for="iva" class="active">IVA <span class="text-red red-text darken-1">*</span></label>
                    <select  class="select2 browser-default" name="iva"  id="iva"   class="validate" required>
                        <?php foreach($taxPay as $item): ?>
                            <option value="<?= $item->id ?>">[<?= $item->code ?>] - <?=  $item->name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="input-field col s12 m6" >
                    <label for="retefuente" class="active">ReteFuente <span class="text-red red-text darken-1">*</span></label>
                    <select class="select2 browser-default"  name="retefuente"  id="retefuente"  class="validate" required>
                        <?php foreach($taxAdvance  as $item): ?>
                            <option value="<?= $item->id ?>">[<?= $item->code ?>] - <?=  $item->name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="input-field col s12 m6">
                    <label for="reteica" class="active">ReteICA <span class="text-red red-text darken-1">*</span></label>
                    <select class="select2 browser-default"  name="reteica"  id="reteica"  class="validate" required>
                        <?php foreach($taxAdvance as $item): ?>
                            <option value="<?= $item->id ?>">[<?= $item->code ?>] - <?=  $item->name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="input-field col s12 m6">
                    <label class="active" for="reteiva" >ReteIVA <span class="text-red red-text darken-1">*</span></label>
                    <select class="select2 browser-default"  name="reteiva"  id="reteiva"  class="validate" required>
                        <?php foreach($taxAdvance as $item): ?>
                            <option value="<?= $item->id ?>">[<?= $item->code ?>] - <?=  $item->name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="input-field col s12 m6">
                    <label for="account_pay" class="active">Cuenta por Cobrar <span class="text-red red-text darken-1">*</span></label>
                    <select  class="select2 browser-default"  name="account_pay"  id="account_pay"  class="validate" required>
                        <?php foreach($accountPay as $item): ?>
                            <option value="<?= $item->id ?>">[<?= $item->code ?>] - <?=  $item->name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="input-field col s12 m6">
                    <label for="cost_center" class="active">Centro de costo</label>
                    <select  class="select2 browser-default"  name="cost_center"  id="cost_center">
                        <option value="">No asignar</option>
                        <?php foreach($cost_center as $item): ?>
                            <option value="<?= $item->id ?>">[<?= $item->code ?>] - <?=  $item->name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="input-field col s12 m6" >
                    <input type="text"  name="brandname" value="No aplica"  class="validate" required>
                    <label class="active">Marca <span class="text-red red-text darken-1">*</span></label>
                </div>
                <div class="input-field col s12 m6" >
                    <input type="text" name="modelname" value="No aplica"  class="validate" required>
                    <label class="active">Modelo <span class="text-red red-text darken-1">*</span></label>
                </div>
            </form>
        </div>
    </div>
    <div class="modal-footer">
        <a href="#!" class="modal-action modal-close waves-effect waves-purple btn-flat ">Cerrar</a>
        <a href="#!" class="modal-action btn purple waves-effect " onclick="send_product()">Guardar</a>
    </div>
</div>

<!-- <div id="modal1" class="modal modal-fixed-footer" style="height: 700px !important;">
    <div class="modal-content">
        <div class="row">
            <div class="input-field col s12">
                <h6>Agregar Información</h6>
                <input placeholder="Entrada Almacen" id="first_name" type="text">
            </div>
            
            <div class="input-field col s12">
                <textarea id="textarea1" class="materialize-textarea" data-length="120"></textarea>
                <label for="textarea1">Observaciones</label>
            </div>
            <div class="col s12">
                <button class="btn indigo right">Guardar</button>
            </div>
            <div class="col s12">
            <table class="bordered striped centered responsive-table">
                    <tr>
                        <th style=" padding: 5px !important;">#</th>
                        <td style=" padding: 5px !important;">Número</td>
                        <td style=" padding: 5px !important;">Observación</td>
                    </tr>
                    <tr>
                        <th style=" padding: 5px !important;">1</th>
                        <td style=" padding: 5px !important;">8abd9f754d8fg</td>
                        <td style=" padding: 5px !important;">Entrada verifica</td>
                    </tr>
                    <tr>
                        <th style=" padding: 5px !important;">2</th>
                        <td style=" padding: 5px !important;">51089f2w4d8fg</td>
                        <td style=" padding: 5px !important;">Entrada verifica</td>
                    </tr>
                    <tr>
                        <th style=" padding: 5px !important;">3</th>
                        <td style=" padding: 5px !important;">7a8d9f7w4d8fg</td>
                        <td style=" padding: 5px !important;">Entrada verifica</td>
                    </tr>
                </table>
            </div>

            <div class="col s3">
                <p>
                    <label>
                        <input name="group1" type="radio" checked />
                        <span>Aceptada</span>
                    </label>
                </p>
                </div>
                <div class="col s9">
                <p>
                    <label>
                        <input name="group1" type="radio" />
                        <span>Rechazada</span>
                    </label>
                </p>
            </div>
            <div class="col s12">
                <h6>Datos de Entrada</h6>
                <table class="bordered striped centered responsive-table">
                    <tr>
                        <th style=" padding: 5px !important;">#</th>
                        <td style=" padding: 5px !important;">Cliente</td>
                        <td style=" padding: 5px !important;">Fecha</td>
                    </tr>
                    <tr>
                        <th style=" padding: 5px !important;">FEV-1026</th>
                        <td style=" padding: 5px !important;"> Pepito Perez</td>
                        <td style=" padding: 5px !important;">12-20-2021</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <a href="#!" class="modal-action modal-close waves-effect waves-purple btn-flat ">Cerrar</a>
        <a href="#!" class="modal-action btn purple modal-close waves-effect ">Guardar</a>
    </div>
</div> -->

<div id="modal-history" class="modal modal-fixed-footer" style="height: 700px !important;">
    <div class="modal-content">
        <h6>Historial Archivos</h6>
		<br>
        <div class="row">
            <div class="col s12 section-data-tables table-2">
                <table class="display table-2" id="table-observation">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Número - Observación</th>
                            <th>Usuario</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <a href="#!" class="modal-action modal-close waves-effect waves-purple btn-flat ">Cerrar</a>
    </div>
</div>

<div id="products" class="modal modal-fixed-footer">
    <div class="modal-content">
        <h6>Asociar Producto/Servicio</h6>
        <hr>
        <div class="row">
            <div class="col s12 section-data-tables table-2">
                <table class="display table-2" id="table-products">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <input type="hidden" name="id_invoice">
    <div class="modal-footer">
        <a href="#!" class="modal-action modal-close waves-effect waves-purple btn-flat">Cancelar</a>
        <a href="#!" class="modal-action btn purple modal-close waves-effect">Aceptar</a>
    </div>
</div>

<div id="type_document" class="modal modal-fixed-footer">
    <div class="modal-content">
        <h6>Tipos de documentos</h6>
        <hr>
        <table id="table_type_document" class="striped centered bordered">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Abreviación</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    <input type="hidden" name="id_invoice">
    <div class="modal-footer">
        <a href="#!" class="modal-action btn purple modal-close waves-effect">Cerrar</a>
    </div>
</div>

<div id="modal3" class="modal modal-fixed-footer" style="height: 200px !important; width: 450px !important;">
    <div class="modal-content">
        <h6>Rechazar Documento</h6>
        Por favor valide que la información se correcta antes de dar clic en aceptar.
    </div>
    <div class="modal-footer">
        <a href="#!" class="modal-action modal-close waves-effect waves-purple btn-flat ">Cancelar</a>
        <a href="#!" class="modal-action btn purple modal-close waves-effect ">Aceptar</a>
    </div>
</div>



<div id="modal4" class="modal modal-fixed-footer" style="height: 700px !important;">
    <div class="modal-content">
        <h6>Radicados</h6>
        <div class="row">  
            <div class="col s12">
                <label>Sistema</label>
                <select class="browser-default">
                    <option value="" disabled selected>Seleccione una opción</option>
                    <option value="1">Workmanager</option>
                    <option value="2">UNOE</option>
                    <option value="2">Otros</option>
                </select>
            </div>
            <div class="input-field col s12">
        
                <input placeholder="Numero de Radicado" id="first_name" type="text">
            </div>
            <div class="input-field col s12">
                <textarea id="textarea1" class="materialize-textarea" data-length="120"></textarea>
                <label for="textarea1">Observaciones</label>
            </div>

            <div class="col s12">
                <button class="btn indigo right">Guardar</button>
            </div>
            <div class="col s12">
            <table class="bordered striped centered responsive-table">
                    <tr>
                        <th style=" padding: 5px !important;">#</th>
                        <td style=" padding: 5px !important;">Fecha</td>
                        <td style=" padding: 5px !important;">Número</td>
                        <td style=" padding: 5px !important;">Sistema</td>
                        <td style=" padding: 5px !important;">Observaciones</td>

                    </tr>
                    <tr>
                        <th style=" padding: 5px !important;">1</th>
                        <td style=" padding: 5px !important;">23/12/2021</td>
                        <td style=" padding: 5px !important;">8abd9f754d8fg</td>
                        <td style=" padding: 5px !important;">UNOE</td>
                        <td style=" padding: 5px !important;">Entrada verifica</td>
                    </tr>
                    <tr>
                        <th style=" padding: 5px !important;">2</th>
                        <td style=" padding: 5px !important;">23/12/2021</td>
                        <td style=" padding: 5px !important;">51089f2w4d8fg</td>
                        <td style=" padding: 5px !important;">Otros</td>
                        <td style=" padding: 5px !important;">Entrada verifica</td>
                    </tr>
                    <tr>
                        <th style=" padding: 5px !important;">3</th>
                        <td style=" padding: 5px !important;">23/12/2021</td>
                        <td style=" padding: 5px !important;">7a8d9f7w4d8fg</td>
                        <td style=" padding: 5px !important;">Workmanager</td>
                        <td style=" padding: 5px !important;">Entrada verifica</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <a href="#!" class="modal-action modal-close waves-effect waves-purple btn-flat ">Cerrar</a>
        <a href="#!" class="modal-action btn purple modal-close waves-effect ">Guardar</a>
    </div>
</div>
<?= $this->endSection() ?>


<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>
<script src="<?= base_url('/js/advance-ui-modals.js') ?>"></script>
<script src="<?= base_url() ?>/app-assets/vendors/data-tables/js/jquery.dataTables.min.js"></script>
<script src="<?= base_url() ?>/app-assets/vendors/data-tables/extensions/responsive/js/dataTables.responsive.min.js"></script>
<script src="<?= base_url() ?>/app-assets/vendors/data-tables/js/dataTables.select.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?= base_url() ?>/assets/js/new_scripts/funciones.js"></script>
<script src="<?= base_url() ?>/app-assets/vendors/dropify/js/dropify.min.js"></script>
<script src="<?= base_url() ?>/app-assets/js/scripts/form-file-uploads.js"></script>

<script src="<?= base_url() ?>/app-assets/vendors/select2/select2.full.min.js"></script>
<script src="<?= base_url() ?>/app-assets/js/scripts/form-select2.min.js"></script>
<script>
    const table = [];
    const table_observation = [];
    const table_products = [];
    let invoices = [];
    $(document).ready(function(){
        feather.replace();
        var estados = <?= json_encode($estados) ?>;
        estados.forEach(estado =>{
            table[estado.id] = $(`#table_${estado.id}`).DataTable({
                "ajax": {
                    "url": `<?= base_url() ?>/shopping/table/${estado.id}`,
                    "dataSrc":'tables'
                },
                "columns": [
                    { data: 'type_documents_prefix' },
                    { data: 'resolution' },
                    { data: 'created_at' },
                    { data: 'company_name' },
                    { data: 'customer_name' },
                    { data: 'd_requiridos' },
                    { data: 'valor' },
                    { data: 'dian' },
                    { data: 'vence' },
                    { data: 'action' },
                ],
                "responsive": false,
                "scrollX": true,
                "ordering": false,
                language: { url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"},
                initComplete: (data) => {
                    if(estado.id == 'todos'){
                        var type_documents =  Object.entries(data.json.type_document);
                        var body = ``;
                        type_documents.forEach((value, id) => {
                            value = value[1];
                            body += `
                                <tr>
                                    <td>
                                        ${value.name}
                                    </td>
                                    <td>
                                        ${value.prefix}
                                    </td>
                                </tr>`;
                        });
                        $('#table_type_document tbody').html(body);
                        invoices = data.json.invoices;
                    } 
                }
            });
            table[estado.id].on('draw', function(){
                $('.material-tooltip').remove();
                $('.tooltipped').tooltip();
                $('.dropdown-trigger').dropdown({
                    inDuration: 300,
                    outDuration: 225,
                    constrainWidth: false, // Does not change width of dropdown to that of the activator
                    hover: false, // Activate on hover
                    gutter: 0, // Spacing from edge
                    coverTrigger: false, // Displays dropdown below the button
                    alignment: 'left', // Displays dropdown with edge aligned to the left of button
                    stopPropagation: false // Stops event propagation
                });
            })
        });
    });
    function reinit(id){
        table[`${id}`].ajax.reload((data) => {
            invoices = data.invoices;
            var type_documents =  Object.entries(data.type_document);
            var body = ``;
            type_documents.forEach((value, id) => {
                value = value[1];
                body += `
                    <tr>
                        <td>
                            ${value.name}
                        </td>
                        <td>
                            ${value.prefix}
                        </td>
                    </tr>`;
            });
            $('#table_type_document tbody').html(body);

            var indicadores = data.indicadores;
            indicadores.forEach(indicador => {
                $(`#indicador-${indicador.id} h5`).html(indicador.total);
            });
            $('.material-tooltip').remove();
            $('.tooltipped').tooltip();
            $('.dropdown-trigger').dropdown({
                inDuration: 300,
                outDuration: 225,
                constrainWidth: false, // Does not change width of dropdown to that of the activator
                hover: false, // Activate on hover
                gutter: 0, // Spacing from edge
                coverTrigger: false, // Displays dropdown below the button
                alignment: 'left', // Displays dropdown with edge aligned to the left of button
                stopPropagation: false // Stops event propagation
            });
        });
    }

    function reception_email(){
        Swal.fire({
            title: 'Descargando información',
            html:'<b>Este proceso puede ser demorado.</b>',
            didOpen: () => Swal.showLoading(),
            allowOutsideClick: false
        });
    }

    function aceppt(id, accept, status){
        if(accept){
            var opc = {
                title:'Aceptar documento',
                text:'Por favor valide que la información sea correcta antes de dar clic en aceptar.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Aceptar',
                cancelButtonText: `Cancelar`,
            }
        } else{
            var typeRejections = <?= json_encode($typeRejections) ?>;
            var html = `
                    <div class="input-field">
                        <textarea id="motivo" class="materialize-textarea"></textarea>
                        <label for="motivo">Motivo</label>
                    </div>
                    <div class="input-field">
                        <select class="select2 browser-default" id="id-rechazar">`;
                            typeRejections.forEach(value => {
                                html += `<option value="${value.id}">[${value.code}] - ${value.name}</option>`;
                            })
                            html += `
                        </select>
                        <label>Tipo de rechazo</label>
                    </div>`;
            var opc = {
                title: 'Rechazar documento',
                icon: 'warning',
                html:html,
                showCancelButton: true,
                confirmButtonText: 'Aceptar',
                cancelButtonText: `Cancelar`,
            }
        }
        Swal.fire(opc).then((result) =>{
            if (result.isConfirmed) {
                // console.log(accept);
                if(accept){
                    var form_data = new URLSearchParams({
                        id_invoice: id,
                        accept: accept,
                        table: status
                    });
                    Swal.fire({
                        title: 'Actualizando',
                        didOpen: () => Swal.showLoading(),
                    });
                    form_data = form_data.toString();
                    var acuse = proceso_fetch_get(`<?=  base_url(['documents/event']) ?>/${id}/1/0/1`);
                    acuse.then(data => {
                        if(!data.status){
                            if(data.message != 'Regla: 90, Rechazo: Documento procesado anteriormente.' && data.message != 'Ya se registro este evento para este documento.')
                                throw Error(data.message)
                            else alert('<span class="green-text"><b>Acuse De Recibido</b> Procesado anteriormente ante la DIAN.</span>', 'green lighten-5', 2000)
                        } else alert('<span class="green-text"><b>Acuse De Recibido</b> Procesado correctamente.</span>', 'green lighten-5', 3000)
                        var acuse = proceso_fetch_get(`<?=  base_url(['documents/event']) ?>/${id}/3/0/1`);
                        acuse.then(data => {
                            if(!data.status){
                                if(data.message != 'Regla: 90, Rechazo: Documento procesado anteriormente.' && data.message != 'Ya se registro este evento para este documento.')
                                    throw Error(data.message)
                                else alert('<span class="green-text"><b>Recepción De Bienes</b> Procesado anteriormente ante la DIAN.</span>', 'green lighten-5', 2000)
                            } else alert('<span class="green-text"><b>Recepción De Bienes</b> Procesado correctamente.</span>', 'green lighten-5', 3000)
                            var acuse = proceso_fetch_get(`<?=  base_url(['documents/event']) ?>/${id}/4/0/1`);
                            acuse.then(data => {
                                if(!data.status){
                                    if(data.message != 'Regla: 90, Rechazo: Documento procesado anteriormente.' && data.message != 'Ya se registro este evento para este documento.')
                                        throw Error(data.message)
                                    else alert('<span class="green-text"><b>Documento</b> Aceptado anteriormente ante la DIAN.</span>', 'green lighten-5', 2000)
                                } else alert('<span class="green-text"><b>Documento</b> Aceptado correctamente.</span>', 'green lighten-5', 3000)
                                var respuesta = proceso_fetch('<?= base_url(['shopping', 'update']) ?>', form_data);
                                respuesta.then(response => {
                                    console.log(response);
                                    reinit(`${response.table}`);
                                    Swal.fire({
                                        title: 'Documento aceptado',
                                        icon: 'success',
                                        confirmButtonText: 'Aceptar'
                                    });
                                });
                            }).catch(error => {
                                alert_sweet(error)
                            })
                        }).catch(error => {
                            alert_sweet(error)
                        });
                    }).catch(error => {
                        alert_sweet(error)
                    })
                } else{
                    var type = $('#id-rechazar').val();
                    var observation = $('#motivo').val();
                    var form_data = new URLSearchParams({
                        id_invoice: id,
                        accept: accept,
                        table: status,
                        type: type,
                        observation: observation
                    });
                    Swal.fire({
                        title: 'Actualizando',
                        didOpen: () => Swal.showLoading(),
                    });
                    // console.log(type);return null;
                    form_data = form_data.toString();
                    if(type != 0){
                        var acuse = proceso_fetch_get(`<?=  base_url(['documents/event']) ?>/${id}/1/0/1`);
                        acuse.then(data => {
                            if(!data.status){
                                if(data.message != 'Regla: 90, Rechazo: Documento procesado anteriormente.' && data.message != 'Ya se registro este evento para este documento.')
                                    throw Error(data.message)
                                else alert('<span class="green-text"><b>Acuse De Recibido</b> Procesado anteriormente ante la DIAN.</span>', 'green lighten-5', 2000)
                            } else alert('<span class="green-text"><b>Acuse De Recibido</b> Procesado correctamente.</span>', 'green lighten-5', 3000)
                            var acuse = proceso_fetch_get(`<?=  base_url(['documents/event']) ?>/${id}/3/0/1`);
                            acuse.then(data => {
                                if(!data.status){
                                    if(data.message != 'Regla: 90, Rechazo: Documento procesado anteriormente.' && data.message != 'Ya se registro este evento para este documento.')
                                        throw Error(data.message)
                                    else alert('<span class="green-text"><b>Recepción De Bienes</b> Procesado anteriormente ante la DIAN.</span>', 'green lighten-5', 2000)
                                } else alert('<span class="green-text"><b>Recepción De Bienes</b> Procesado correctamente.</span>', 'green lighten-5', 3000)
                                var acuse = proceso_fetch_get(`<?=  base_url(['documents/event']) ?>/${id}/2/${type}/1`);
                                acuse.then(data => {
                                    if(!data.status){
                                        if(data.message != 'Regla: 90, Rechazo: Documento procesado anteriormente.' && data.message != 'Ya se registro este evento para este documento.')
                                            throw Error(data.message)
                                        else alert('<span class="green-text"><b>Documento</b> Rechazado anteriormente ante la DIAN.</span>', 'green lighten-5', 2000)
                                    } else alert('<span class="green-text"><b>Documento</b> Rechazado correctamente.</span>', 'green lighten-5', 3000)
                                    var respuesta = proceso_fetch('<?= base_url(['shopping', 'update']) ?>', form_data);
                                    respuesta.then(response => {
                                        console.log(response);
                                        reinit(`${response.table}`);
                                        Swal.fire({
                                            title: response.message,
                                            icon: 'success',
                                            confirmButtonText: 'Aceptar'
                                        });
                                    });
                                }).catch(error => {
                                    alert_sweet(error)
                                })
                            }).catch(error => {
                                alert_sweet(error)
                            });
                        }).catch(error => {
                            alert_sweet(error)
                        })
                    }else{
                        var respuesta = proceso_fetch('<?= base_url(['shopping', 'update']) ?>', form_data);
                        respuesta.then(response => {
                            console.log(response);
                            reinit(`${response.table}`);
                            Swal.fire({
                                title: response.message,
                                icon: 'success',
                                confirmButtonText: 'Aceptar'
                            });
                        });
                    }
                }
                
            } 
        })
        $(".select2").select2({
            dropdownAutoWidth: true,
            width: '100%',
        })
    }
    function asignar(id_email, status){
        var input = `
            <div class="input-field">
                <select class="select2 browser-default" id="id-invoice">`;
                    invoices.forEach(invoice => {
                        input += `<option value="${invoice.id}">${invoice.name}</option>`;
                    })
                    
                input += `</select>
            </div>`;
        Swal.fire({
            title:'Asignar documento',
            icon: 'question',
            html: input,
            confirmButtonText: 'Aceptar',
            cancelButtonText: `Cancelar`,
        }).then((result) =>{
            if (result.isConfirmed) {
                var value = $('#id-invoice').val();
                var invoice = invoices.filter(invoice => invoice.id == value);
                Swal.fire({
                    title: `Asignando a ${invoice[0].name}`,
                    didOpen: () => Swal.showLoading(),
                });
                var data = new URLSearchParams({
                    id_invoice: value,
                    id: id_email,
                });
                data = data.toString();
                var respuesta = proceso_fetch('<?= base_url(['shopping', 'assign']) ?>', data);
                respuesta.then(response => {
                    // console.log(response);
                    reinit(status);
                    Swal.fire({
                        title: 'Documento asignado',
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    });
                });
            } 
        });
        $(".select2").select2({
            dropdownAutoWidth: true,
            width: '100%',
        })
    }

    function add_file(invoices_id, email_id, exist){
        if(exist){
            Swal.fire({
                title: 'Opciones',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Cargar documento',
                cancelButtonText: `Ver historial`,
                confirmButtonColor: '#1e88e5',
                cancelButtonColor: '#5e35b1'
            }).then((result) =>{
                if (result.isConfirmed) {
                    $('#invoices_id').val(invoices_id);
                    $('#shopping_id').val(email_id);
                    $('#exist').val(exist);
                    $('#add_file').modal('open');
                }else if (result.dismiss == 'cancel') {
                    $('#modal-history').modal('open');
                    if(table_observation[`table`] != undefined) table_observation[`table`].ajax.url(`<?= base_url() ?>/shopping/table/history/${email_id}`).load();
                    else{
                        table_observation[`table`] = $(`#table-observation`).DataTable(
                            {
                                "ajax": {
                                    "url": `<?= base_url() ?>/shopping/table/history/${email_id}`,
                                    "dataSrc":""
                                },
                                "columns": [
                                    { data: 'id' },
                                    { data: 'observation' },
                                    { data: 'name' }
                                ],
                                pageLength : 5,
                                lengthMenu: [[5, 10, 20, -1], [5, 10, 20, 'Todos']],
                                searching: false,
                                "responsive": false,
                                "scrollX": true,
                                "ordering": false,
                                language: { url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"}
                            });
                    }
                }
            })
        }else{
            $('#invoices_id').val(invoices_id);
            $('#shopping_id').val(email_id);
            $('#exist').val(exist);
            $('#add_file').modal('open');
        }
    }

    function guardar(){
        if ($(`#select-archivo`).val() === null) {
            return alert('<span class="red-text">Debe seleccionar un tipo de archivo</span>', 'red lighten-5');
        }
        if ($(`#numero`).val() === '') {
            return alert('<span class="red-text">Debe ingresar un número</span>', 'red lighten-5');
        }
        $('#form-file').submit();
    }

    function reinit_product(id_document, status){
        table_products['table'].ajax.url(`<?= base_url() ?>/shopping/table/product/${id_document}/${status}`).load(() => {
            $('.material-tooltip').remove();
            $('.tooltipped').tooltip();
            $('.dropdown-trigger').dropdown({
                inDuration: 300,
                outDuration: 225,
                constrainWidth: false, // Does not change width of dropdown to that of the activator
                hover: false, // Activate on hover
                coverTrigger: true, // Displays dropdown below the button
                stopPropagation: false // Stops event propagation
            });
        });
    }

    function product(id_document, status){
        $('#products').modal('open');
        if(table_products['table']  != undefined) reinit_product(id_document, status);
        else{
            table_products['table'] = $(`#table-products`).DataTable({
                "ajax": {
                    "url": `<?= base_url() ?>/shopping/table/product/${id_document}/${status}`,
                    "dataSrc":""
                },
                "columns": [
                    { data: 'code' },
                    { data: 'description' },
                    { data: 'quantity' },
                    { data: 'upload' },
                    { data: 'action' },
                ],
                pageLength : 5,
                lengthMenu: [[5, 10, 20, -1], [5, 10, 20, 'Todos']],
                searching: false,
                "responsive": false,
                "scrollX": true,
                "ordering": false,
                language: { url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"},
                initComplete: function(){}
            });
            table_products['table'].on('draw', function(){
                $('.material-tooltip').remove();
                $('.tooltipped').tooltip();
                $('.dropdown-trigger').dropdown({
                    inDuration: 300,
                    outDuration: 225,
                    constrainWidth: false, // Does not change width of dropdown to that of the activator
                    hover: false, // Activate on hover
                    coverTrigger: true, // Displays dropdown below the button
                    stopPropagation: false // Stops event propagation
                });
            })
        }
    }

    function add_product(product_id, id_document, product, status){
        $('#products').modal('close');
        var products = <?= json_encode($products) ?>;
        var cost_center = <?= json_encode($cost_center) ?>;
        var input = `
            <div class="input-field">
                <select class="select2 browser-default" id="id-product">`;
                    products.forEach(product => {
                        input += `<option value="${product.id}">[${product.code}] - ${product.name}</option>`;
                    })
                    
                input += `</select>
                <label>Producto</label>
            </div>
            <div class="input-field">
                <select class="select2 browser-default" id="id-cost">
                    <option value="">No asignar</option>
                `;
                    cost_center.forEach(cost => {
                        input += `<option value="${cost.id}">[${cost.code}] - ${cost.name}</option>`;
                    })
                    
                input += `</select>
                <label>Centro de costo</label>
            </div>
            `;
        Swal.fire({
            title:`<h6>Asociar Producto/Servicio | <span class="grey-text">${product}</span></h6>`,
            icon: 'question',
            html: input,
            confirmButtonText: 'Aceptar',
            cancelButtonText: `Cancelar`,
        }).then((result) =>{
            if (result.isConfirmed) {
                var value = $('#id-product').val();
                var cost_center = $('#id-cost').val();
                var product = products.filter(product => product.id == value);
                Swal.fire({
                    title: `<h6>Asignando a | <span class="grey-text"> [${product[0].code}] ${product[0].name}</span><h6>`,
                    didOpen: () => Swal.showLoading(),
                });
                var data = new URLSearchParams({
                    id_product: value,
                    cost_center: cost_center
                });
                data = data.toString();
                var url = `<?= base_url(['documents', 'product_created']) ?>/${product_id}/${id_document}/1`;
                var respuesta = proceso_fetch(url, data);
                respuesta.then(response => {
                    reinit_product(id_document, status);
                    // setTimeout(() => {
                    Swal.fire({
                        title: 'Producto asignado',
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    }).then((result) => {
                        if(!result.isDenied){
                            $('#products').modal('open');
                            reinit_product(id_document, status);
                        }
                        console.log(result);
                    })
                    // }, 1000);
                    reinit(status);
                });
            } 
        });
        $(".select2").select2({
            dropdownAutoWidth: true,
            width: '100%',
        })
    }

    function not_reference(product_id, id_document, product, status){
        $('#products').modal('close');
        Swal.fire({
            title:'Desasociar',
            text:'Desea no asociar el producto al inventario.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Aceptar',
            cancelButtonText: `Cancelar`,
        }).then((data) => { 
            if(data.isConfirmed) {
                console.log(data);
                var url = `<?= base_url(['documents', 'product_created']) ?>/${product_id}/${id_document}/1`;
                var data = new URLSearchParams({
                    id_product: 2712,
                });
                data = data.toString();
                var respuesta = proceso_fetch(url, data);
                respuesta.then(response => {
                    reinit(status);
                    reinit_product(id_document, status);
                    Swal.fire({
                        title: 'Producto no asociado al inventario',
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    }).then((result) => {
                        if(!result.isDenied){
                            $('#products').modal('open');
                        }
                        console.log(result);
                    })
                });
            }else{
                $('#products').modal('open');
            }
        })
    }

    function created_product(product_id, id_document, product, status){
        $('#created_product').modal('open');
        $('#products').modal('close');
        $('#product_id').val(product_id);
        $('#id_document').val(id_document);
        $('#status').val(status);
    }

    function send_product(){
        var product_id = $('#product_id').val();
        var id_document = $('#id_document').val();
        var status = $('#status').val();
        var form = $('#new_product_form').serializeArray();
        var result = form.every(value => {
            if(value.value != '' || value.name == 'cost_center') return true;
            return false;
        });
        if(!result) return alert('<span class="red-text">Verifique que todos los campos no se encuentren vacios</span>', 'red lighten-5');
        var data = $('#new_product_form').serialize();
        var url = `<?= base_url(['documents', 'product_created']) ?>/${product_id}/${id_document}/1`;
        var respuesta = proceso_fetch(url, data);
        respuesta.then(response => {
            reinit(status);
            reinit_product(id_document, status);
            $('#created_product').modal('close');
            Swal.fire({
                title: 'Producto creado y asociado al inventario',
                icon: 'success',
                confirmButtonText: 'Aceptar'
            }).then((result) => {
                if(!result.isDenied){
                    $('#products').modal('open');
                }
                console.log(result);
            })
        });
    }

    function file_pendiente(id,email_id, status){
        Swal.fire({
            title:'Actualizar documento',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Aceptar',
            cancelButtonText: `Rechazar`,
        }).then((data) => { 
            var validation = false;
            if(data.isConfirmed) {
                var status_file = 'Aceptado';
                validation = true; 
            }else if (data.dismiss == 'cancel') {
                var status_file = 'Rechazado';
                validation = true; 
            }
            var data = new URLSearchParams({
                status: status_file,
                update: 1,
                email_id: email_id,
                id: id
            });
            if(validation){
                data = data.toString();
                var url = `<?= base_url(['shopping', 'file']) ?>`;
                var respuesta = proceso_fetch(url, data);
                respuesta.then(response => {
                    reinit(status);
                    if(response.update){
                        Swal.fire({
                            title: 'Documento actualizado con exito',
                            icon: 'success',
                            confirmButtonText: 'Aceptar'
                        })
                    }else{
                        Swal.fire({
                            title: 'Oops..',
                            text: 'Hubo algun error al actualizar el documento.',
                            icon: 'warning',
                            confirmButtonText: 'Aceptar'
                        })
                    }
                });
            }

        })

    }

</script>
<?= $this->endSection() ?>