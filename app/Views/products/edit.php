<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?> Facturación <?= $this->endSection() ?>
<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('/assets/css/dropify.css') ?>">
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
                    </div>
                    <div class="col s10 m6 l6 breadcrumbs-left">
                        <h5 class="breadcrumbs-title mt-0 mb-0 display-inline hide-on-small-and-down ">
                            <span>
                               Editar Producto
                            </span>
                        </h5>
                        <ol class="breadcrumbs mb-0">
                            <li class="breadcrumb-item"><a href="<?= base_url() ?>/home">Home</a></li>
                            <li class="breadcrumb-item"><a href="<?=  base_url().route_to('products-index') ?>">Productos</a></li>
                            <li class="breadcrumb-item active"><a href="#">Editar producto</a></li>
                        </ol>

                    </div>
                    <div class="col m6 l6 s12 ">
                        <a href="<?= base_url() . route_to('products-index') ?>" class="btn indigo right" style="padding-right: 10px; padding-left: 10px;">
                            <i class="material-icons left" >keyboard_arrow_left</i>
                            Regresar
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col s12">
            <div class="container">
                <div class="section">
                    <div class="card">
                        <div class="card-content">
                            <p class="">
                                Recordar todo dato marcado con "<span class="red-text"> * </span>" es obligatorio.
                            </p>
                            <div class="row">
                                <div class="divider"></div>
                                <br>
                            </div>
                            <div class="row">
                                <div class="col s12">
                                    <form action="<?=  base_url().route_to('products-update', $product->id) ?>" method="post" enctype="multipart/form-data">
                                        <div class="row">
                                            <div class="col s12 m3 l3">
                                                <div class="input-field">
                                                    <select class="select2 browser-default" id="provider_id"
                                                            onchange="ShowSelected('providers')" name="provider_id" required>
                                                        <option selected disabled value="">Seleccione proveedor
                                                        </option>
                                                        <?php foreach ($providers as $provider): ?>
                                                            <option <?= ($product->provider_id == $provider->id)?'selected':'' ?> value="<?= $provider->code ?>"><?= "[{$provider->code}] - {$provider->name_providers}" ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <label for="provider_id">Proveedor <span class='red-text'> * </span></label>
                                                </div>
                                            </div>
                                            <div class="col s12 m3 l3">
                                                <div class="input-field">
                                                    <select class="select2 browser-default" id="gender_id"
                                                            name="gender_id" onchange="ShowSelected('gender')" required>
                                                        <option selected disabled value="">Seleccione genero
                                                        </option>
                                                        <?php foreach ($gender as $item): ?>
                                                            <option <?= ($product->gender_id == $item->id)?'selected':'' ?> value="<?= $item->code ?>"><?= "[{$item->code}] - {$item->gender}" ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <label for="gender_id">Genero <span
                                                                class='red-text'> * </span></label>
                                                </div>
                                            </div>
                                            <div class="col s12 m3 l3">
                                                <div class="input-field">
                                                    <select class="select2 browser-default" id="group_id"
                                                            name="group_id" onchange="ShowSelected('groups')" required>
                                                        <option selected disabled value="">Seleccione grupo
                                                        </option>
                                                        <?php foreach ($groups as $group): ?>
                                                            <option <?= ($product->group_id == $group->id)?'selected':'' ?> value="<?= $group->code ?>"><?= "[{$group->code}] - {$group->name}" ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <label for="group_id">Grupo <span
                                                                class='red-text'> * </span></label>
                                                </div>
                                            </div>
                                            <div class="col s12 m3 l3">
                                                <div class="input-field">
                                                    <select class="select2 browser-default" id="sub_group_id"
                                                            name="sub_group_id" onchange="ShowSelected('subGroup')" required>
                                                        </option>
                                                        <?php foreach ($subGroup as $item): ?>
                                                            <option <?= ($product->sub_group_id == $item->id)?'selected':'' ?> value="<?= $item->code ?>"><?= "[{$item->code}] - {$item->name}" ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <label for="sub_group_id">Sub Grupo <span
                                                                class='red-text'> * </span></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col s12 m3 l3">
                                                <div class="input-field">
                                                    <select class="select2 browser-default" id="material_id"
                                                            name="material_id" onchange="ShowSelected('materials')" required>
                                                        <option selected disabled value="">Seleccione material
                                                        </option>
                                                        <?php foreach ($materials as $item): ?>
                                                            <option <?= ($product->material_id == $item->id)?'selected':'' ?> value="<?= $item->code ?>"><?= "[{$item->code}] - {$item->name}" ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <label for="material_id">Material <span class='red-text'> * </span></label>
                                                </div>
                                            </div>
                                            <div class="col s4 m3 l3">
                                                <div class="input-field">
                                                    <select class="select2 browser-default" id="code_item" onchange="ShowSelected('code_item')"
                                                            name="code_item" required>
                                                        <?php for($i = 0; $i <= 99; $i++):
                                                            $number = (strlen($i) == 1) ? "0{$i}" : "{$i}";
                                                            ?>
                                                            <option <?= ($product->code_item == $number)?'selected':'' ?> value="<?= $number  ?>"><?= $number ?></option>
                                                        <?php endfor;?>
                                                    </select>
                                                    <label for="code_item">Item<span class='red-text'> * </span></label>
                                                </div>
                                            </div>
                                            <div class="col s8 m3 l3">
                                                <div class="input-field">
                                                    <input value ="<?= $product->code ?>" id="product_code" name="product_code" type="text" class="validate" required>
                                                    <label for="product_code">Código Producto <span class='red-text'> * </span></label>
                                                </div>
                                            </div>
                                            <div class="col s12 m3 l3">
                                                <div class="input-field">
                                                    <input value ="<?= $product->name ?>" id="product_name" name="product_name" type="text" class="validate" required>
                                                    <label for="product_name">Nombre Producto <span class='red-text'> * </span></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col s12 m3 l3">
                                                <div class="input-field">
                                                    <input value ="<?= $product->cost ?>" id="product_cost" name="product_cost" type="text" class="validate" required>
                                                    <label for="product_cost">Costo Producto <span class='red-text'> * </span></label>
                                                </div>
                                            </div>
                                            <div class="col s12 m3 l3">
                                                <div class="input-field">
                                                    <input value ="<?= $product->valor ?>" id="product_value" name="product_value" type="text" class="validate" required>
                                                    <label for="product_value">Valor Producto <span class='red-text'> * </span></label>
                                                </div>
                                            </div>
                                            <div class="col s12 m2 l2">
                                                <div class="input-field">
                                                    <input value="<?= $product->value_one ?>" id="value_one" name="value_one"
                                                           type="text">
                                                    <label for="value_one">Valor tipo 1</label>
                                                </div>
                                            </div>
                                            <div class="col s12 m2 l2">
                                                <div class="input-field">
                                                    <input value="<?= $product->value_two ?>" id="value_two" name="value_two"
                                                           type="text">
                                                    <label for="value_two">Valor tipo 2</label>
                                                </div>
                                            </div>
                                            <div class="col s12 m2 l2">
                                                <div class="input-field">
                                                    <input value="<?= $product->value_three ?>" id="value_three" name="value_three"
                                                           type="text" >
                                                    <label for="value_three">Valor tipo 3</label>
                                                </div>
                                            </div>
                                            <!--<div class="col s12 m3 l3">
                                                <div class="input-field">
                                                    <input value ="<?= $product->brandname ?>" id="product_brand" name="product_brand" type="text" class="validate">
                                                    <label for="product_brand">Marca Producto </label>
                                                </div>
                                            </div>
                                            <div class="col s12 m3 l3">
                                                <div class="input-field">
                                                    <input value ="<?= $product->modelname ?>" id="product_model" name="product_model" type="text" class="validate">
                                                    <label for="product_model">Modelo Producto </label>
                                                </div>
                                            </div>-->
                                        </div>
                                        <div class="row">
                                            <div class="col s12 m6 l6">
                                                <div class="input-field">
                                                    <input type="file" class="dropify" name="photo" value="<?= $product->foto ?>" data-height="125" data-default-file="<?= base_url('assets/upload/products/'.$product->foto) ?>" />
                                                </div>
                                            </div>
                                            <div class="col s12 m6 l6">
                                                <div class="input-field">
                                                    <textarea style="height: 89px !important;" id="description" rows="20" name="description" required><?= $product->description ?></textarea>
                                                    <label for="description" class="active">Descripción del producto <span class="red-text"> * </span> </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <!--<div class="col s12 m3 l3">
                                                <div class="input-field">
                                                    <select class="select2 browser-default" id="category" name="category">
                                                        <option selected disabled value="">Seleccione una categoría</option>
                                                        <?php foreach($categories as $category): ?>
                                                            <option <?= ($product->category_id == $category->id)?'selected':'' ?> value="<?= $category->id ?>"><?= $category->name ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <label for="category">Categoría <span class='red-text'> * </span></label>
                                                </div>
                                            </div>-->
                                            <div class="col s12 m3 l3">
                                                <div class="input-field">
                                                    <select class="select2 browser-default" id="unitMeasure" name="unitMeasure">
                                                        <?php foreach($unitMeasures as $unitMeasure): ?>
                                                            <option <?= ($unitMeasure->id == $product->unit_measures_id)?'selected':''; ?> value="<?= $unitMeasure->id ?>"><?= $unitMeasure->name ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <label for="unitMeasure">Unidad de medida <span class='red-text'> * </span></label>
                                                </div>
                                            </div>
                                            <div class="col s12 m3 l3">
                                                <div class="input-field">
                                                    <select class="select2 browser-default" id="typeItemDocument" name="typeItemDocument">
                                                        <?php foreach($typeItemIdentifications as $typeItemIdentification): ?>
                                                            <option <?= ($typeItemIdentification->id == $product->type_item_identifications_id)?'selected':''; ?> value="<?= $typeItemIdentification->id ?>"><?= $typeItemIdentification->name ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <label for="typeItemDocument">Tipo de documento <span class='red-text'> * </span></label>
                                                </div>
                                            </div>
                                            <div class="col s12 m3 l3">
                                                <div class="input-field">
                                                    <select class="select2 browser-default" id="product_free" name="product_free">
                                                        <option <?= ($product->free_of_charge_indicator == 'false')?'selected':'' ?> value="no">no</option>
                                                        <option <?= ($product->free_of_charge_indicator == 'true')?'selected':'' ?> value="si">si</option>
                                                    </select>
                                                    <label for="product_free">Producto Gratis <span class='red-text'> * </span></label>
                                                </div>
                                            </div>
                                            <div class="col s12 m3 l3">
                                                <div class="input-field">
                                                    <select class="select2 browser-default" style="z-index: 3" id="entry_credit" name="entry_credit">
                                                        <?php foreach($accountingAccounts as $accountingAccount):
                                                            if($accountingAccount->nature == 'Crédito' && $accountingAccount->type_accounting_account_id == 1):
                                                                ?>
                                                                <option <?= ($accountingAccount->id == $product->entry_credit)?'selected':'' ?> value="<?= $accountingAccount->id ?>"><?= $accountingAccount->name ?></option>
                                                            <?php
                                                            endif;
                                                        endforeach; ?>
                                                    </select>
                                                    <label for="entry_credit">Entrada <span class='red-text'> * </span></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col s12 m3 l3">
                                                <div class="input-field">
                                                    <select class="select2 browser-default" id="entry_debit" name="entry_debit">
                                                        <?php foreach($accountingAccounts as $accountingAccount):
                                                            if($accountingAccount->nature == 'Débito' && $accountingAccount->type_accounting_account_id == 1):
                                                                ?>
                                                                <option <?= ($accountingAccount->id == $product->entry_debit)?'selected':'' ?> value="<?= $accountingAccount->id ?>"><?= $accountingAccount->name ?></option>
                                                            <?php
                                                            endif;
                                                        endforeach; ?>
                                                    </select>
                                                    <label for="entry_debit">Devolución<span class='red-text'> * </span></label>
                                                </div>
                                            </div>
                                            <div class="col s12 m3 l3">
                                                <div class="input-field">
                                                    <select class="select2 browser-default" id="iva" name="iva">
                                                        <?php foreach($accountingAccounts as $accountingAccount):
                                                            if($accountingAccount->type_accounting_account_id == 2):
                                                                ?>
                                                                <option <?= ($accountingAccount->id == $product->iva)?'selected':'' ?> value="<?= $accountingAccount->id ?>"><?= $accountingAccount->name ?></option>
                                                            <?php
                                                            endif;
                                                        endforeach; ?></select>
                                                    <label for="iva">Iva <span class='red-text'> * </span></label>
                                                </div>
                                            </div>
                                            <div class="col s12 m3 l3">
                                                <div class="input-field">
                                                    <select class="select2 browser-default" id="reteFuente" name="reteFuente">
                                                        <?php foreach($accountingAccounts as $accountingAccount):
                                                            if($accountingAccount->type_accounting_account_id == 3):
                                                                ?>
                                                                <option <?= ($accountingAccount->id == $product->retefuente)?'selected':'' ?> value="<?= $accountingAccount->id ?>"><?= $accountingAccount->name ?></option>
                                                            <?php
                                                            endif;
                                                        endforeach; ?>
                                                    </select>
                                                    <label for="reteFuente">Retención de fuente <span class='red-text'> * </span></label>
                                                </div>
                                            </div>
                                            <div class="col s12 m3 l3">
                                                <div class="input-field">
                                                    <select class="select2 browser-default" id="reteIca" name="reteIca">
                                                        <?php foreach($accountingAccounts as $accountingAccount):
                                                            if($accountingAccount->type_accounting_account_id == 3):
                                                                ?>
                                                                <option <?= ($accountingAccount->id == $product->reteica)?'selected':'' ?> value="<?= $accountingAccount->id ?>"><?= $accountingAccount->name ?></option>
                                                            <?php
                                                            endif;
                                                        endforeach; ?>
                                                    </select>
                                                    <label for="reteIca">ReteICA<span class='red-text'> * </span></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col s12 m3 l3">
                                                <div class="input-field">
                                                    <select class="select2 browser-default" id="reteIva" name="reteIva">
                                                        <?php foreach($accountingAccounts as $accountingAccount):
                                                            if($accountingAccount->type_accounting_account_id == 3):
                                                                ?>
                                                                <option <?= ($accountingAccount->id == $product->reteiva)?'selected':'' ?> value="<?= $accountingAccount->id ?>"><?= $accountingAccount->name ?></option>
                                                            <?php
                                                            endif;
                                                        endforeach; ?>
                                                    </select>
                                                    <label for="reteIva">ReteIVA <span class='red-text'> * </span></label>
                                                </div>
                                            </div>
                                            <div class="col s12 m3 l3">
                                                <div class="input-field">
                                                    <select class="select2 browser-default" id="account_pay" name="account_pay">
                                                        <?php foreach($accountingAccounts as $accountingAccount):
                                                            if($accountingAccount->type_accounting_account_id == 4):
                                                                ?>
                                                                <option <?= ($accountingAccount->id == $product->account_pay)?'selected':'' ?> value="<?= $accountingAccount->id ?>"><?= $accountingAccount->name ?></option>
                                                            <?php
                                                            endif;
                                                        endforeach; ?></select>
                                                    <label for="account_pay">Cuenta por cobrar <span class='red-text'> * </span></label>
                                                </div>
                                            </div>
                                            <div class="col s12 m3 l3">
                                                <div class="input-field">
                                                    <select class="select2 browser-default" id="typeGenerationTransmition" name="typeGenerationTransmition">
                                                        <?php foreach($typeGenerationTransmitions as $typeGenerationTransmition):?>
                                                            <option <?= ($typeGenerationTransmition->id == $product->type_generation_transmition_id)?'selected':'' ?> value="<?= $typeGenerationTransmition->id ?>"><?= $typeGenerationTransmition->name ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <label for="typeGenerationTransmition">Tipo de operación <span class='red-text'> * </span></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col s2 m12 l12">
                                                <button type="submit"
                                                        class="right btn btn-light-indigo step-5 active-red">
                                                    Actualizar <i class="material-icons right">save</i>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                    <br>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- formulario de filtro -->
    <form action="" method="get">
        <div id="filter" class="modal" role="dialog" style="height:auto; width: 600px">
            <div class="modal-content">
                <h4>Filtrar</h4>
                <div class="row">
                    <div class="col s12 m6  resolution campus input-field" v-if="resolution">
                        <label for="Cliente" :class="{'active': true}">Buscar</label>
                        <input id="resolution" type="text" name="value" placeholder="Buscar">
                    </div>
                    <div class="col s12 m6  Tipo_de_factura campus input-field" v-if="Tipo_de_factura">
                        <label for="Tipo_de_factura" :class="{active: true}">Buscar</label>
                        <select  class="browser-default"  name="value" id="Tipo_de_factura">
                            <option value="1">Factura de Venta Nacional</option>
                            <option value="2">Factura de Exportación</option>
                            <option value="4">Nota Crédito</option>
                            <option value="5">Nota Débito</option>
                        </select>
                    </div>
                    <div class="col s12 m6  Cliente campus input-field" v-if="Cliente">
                        <label for="Cliente" :class="{active: true}">Buscar</label>
                        <select  class="browser-default" type="text" name="value" id="Cliente">

                        </select>
                    </div>
                    <div class="col s12 m6 Estado campus input-field" v-if="Estado">
                        <label for="Estado" class="active">Buscar</label>
                        <select  class="browser-default" type="text" name="value"  id="Estado">
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

<?=  $this->endSection() ?>



<?= $this->section('scripts') ?>
<script src="<?= base_url('/js/advance-ui-modals.js') ?>"></script>
<script src="<?= base_url('/js/shepherd.min.js') ?>"></script>
<script src="<?= base_url('/js/vue.js')?>"></script>
<script src="<?= base_url('/js/ui-alerts.js') ?>"></script>
<script src="<?= base_url('/assets/js/sweetalert.min.js') ?>"></script>
<script src="<?= base_url('/assets/js/dropify.js') ?>"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/23.1.0/classic/ckeditor.js"></script>
<script>
    $(".select2").select2({
        dropdownAutoWidth: true,
        width: '100%'
    });
    $('.dropify').dropify({
        messages: {
            'default': 'Arrastre y suelte o de click',
            'replace': 'Arrastre y suelte o de click para reemplazar',
            'remove':  'Eliminar',
            'error':   'Se ha encontrado un problema.'
        }
    });
    var codeCa = '';
    var providers = document.getElementById("provider_id").value;
    var gender = document.getElementById("gender_id").value;
    var groups = document.getElementById("group_id").value;
    var subGroup = document.getElementById("sub_group_id").value;
    var materials = document.getElementById("material_id").value;
    var code_item = document.getElementById("code_item").value;
    document.getElementById("product_code").value = `${providers}${gender}${groups}${subGroup}${materials}${code_item}`;
    // valideCode(`${providers}${gender}${groups}${prices}${subGroup}${materials}${code_item}`);
    function ShowSelected(table) {
        switch (table) {
            case 'providers':
                providers = document.getElementById("provider_id").value;
                break;
            case 'gender':
                gender = document.getElementById("gender_id").value;
                break;
            case 'groups':
                groups = document.getElementById("group_id").value;
                var options = document.querySelectorAll('#sub_group_id option');
                options.forEach(o => o.remove());
                subGroups(groups);
                break;
            case 'subGroup':
                subGroup = document.getElementById("sub_group_id").value;
                break;
            case 'materials':
                materials = document.getElementById("material_id").value;
                break;
            case 'code_item':
                code_item = document.getElementById("code_item").value;
                break;
        }
        code();

    }
    function code() {
        document.getElementById("product_code").value = `${providers}${gender}${groups}${subGroup}${materials}${code_item}`;
        if(providers !== '' && gender !== '' && groups !== '' && subGroup !== '' && materials !== ''  && code_item !== ''){
            valideCode(`${providers}${gender}${groups}${subGroup}${materials}${code_item}`);
        }
    }

    function valideCode(code) {
        console.log('entre');
        $.post("<?= base_url() ?>/products_jsoncode", {code: code} ,function(data, status){
            const values = JSON.parse(data);
            console.log(values);
            if(!values.validate){
                var cerrar = document.querySelectorAll('#code_item option');
                cerrar.forEach(o => o.remove());
            }
            const select = document.querySelector("#code_item");

            const option = document.createElement("option")
            option.value = ""
            option.innerHTML = "Seleccione item codigo"
            select.appendChild(option)
            values.items.forEach(obj => {
                const $option = document.createElement("option")
                $option.value = obj.id
                $option.innerHTML = `${obj.id}`
                select.appendChild($option)
            })
        });
    }
    function subGroups(id) {
        subGroup = '';
        $.post("<?= base_url() ?>/products/subgroup", {id: id} ,function(data, status){
            console.log(data);
            const values = JSON.parse(data);
            const select = document.querySelector("#sub_group_id");

            const option = document.createElement("option")
            option.value = ""
            option.innerHTML = "Seleccione un subgrupo"
            select.appendChild(option)
            values.forEach(obj => {
                const $option = document.createElement("option")
                $option.value = obj.code
                $option.innerHTML = `[${obj.code}] - ${obj.name}`
                select.appendChild($option)
            })
        });
    }
</script>
<?=  $this->endSection() ?>




