<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?> Asociar Productos  <?= $this->endSection() ?>

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
                            <div class="card-alert card red">
                                <div class="card-content white-text">
                                    <?= session('error') ?>
                                </div>
                                <button type="button" class="close white-text" data-dismiss="alert"
                                        aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col s10 m6 l6 breadcrumbs-left">
                        <h5 class="breadcrumbs-title mt-0 mb-0 display-inline hide-on-small-and-down">
                             <span>
                                Asociar Productos
                                <a class="btn-floating btn-small light-blue darken-1 step-1 help">?</a>
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
        <div class="col s12">
            <div class="card">
                <div class="card-content" >
                    <a href="<?= base_url().route_to('document-index') ?>" class="btn btn-small btn-light-indigo right btn-sm" >
                        Regresar <i class="material-icons right">keyboard_return</i>
                    </a>
                    <div class="row">
                        <div class="col s12 mt-2">
                            <table class="responsive-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th class="center">Documento</th>
                                        <th class="center">Código</th>
                                        <th class="center">Producto</th>
                                        <th class="center">Valor</th>
                                        <th class="center">Cantidad</th>
                                        <th class="center">Total</th>
                                        <th class="center step-3">Estado</th>
                                        <th class="center step-2">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $i = count($lineInvoices);
                                    foreach($lineInvoices as $lineInvoice): ?>
                                    <tr>
                                        <td><?= $i-- ?></td>
                                        <td  class="center"><?=  $lineInvoice->resolution ?></td>
                                        <td  class="center"><?= $lineInvoice->code ?></td>
                                        <td  class="center"><?= $lineInvoice->description ?></td>
                                        <td  class="center">$ <?= number_format($lineInvoice->price_amount, '2', ',', '.')  ?> </td>
                                        <td  class="center"><?= $lineInvoice->quantity ?> </td>
                                        <td  class="center">$ <?= number_format($lineInvoice->line_extension_amount, '2', ',', '.') ?> </td>
                                        <td  class="center">

                                        <?php if($lineInvoice->upload == 'Cargado' || $lineInvoice->upload ==  'Sin Referencia'): ?>
                                        <span class="new badge tooltipped " data-position="top" data-badge-caption="" data-tooltip="Producto Asociado correctamente">
                                             <?= $lineInvoice->upload ?>
                                        </span>
                                        <?php else: ?>
                                            <span class="new badge tooltipped yellow darken-2 " data-position="top" data-badge-caption="" data-tooltip="Producto sin asociar">
                                                Sin cargar
                                            </span>
                                        <?php endif; ?>
                                        </td>
                                        <td  class="center">

                                        <?php if($lineInvoice->upload != 'Cargado' && $lineInvoice->upload != 'Sin Referencia'): ?>
                                            <div class="btn-group">
                                                <button class="btn btn-small tooltipped  modal-trigger modals-triggers2 step-6 next-tour" style="padding-left: 10px; padding-right:10px;"  data-position="top" data-tooltip="Asociar producto" data-target="modal1" @click="url(<?= $lineInvoice->id ?>, <?= $id ?>, `<?= strip_tags($lineInvoice->description) ?>`)">
                                                    <i class="material-icons">add_shopping_cart</i>
                                                </button>
                                                <button class="btn red darken-2 btn-small tooltipped not-refence" data-line="<?= $lineInvoice->id ?>" data-id="<?= $id ?>" data-position="top" data-tooltip="No asociarle producto">
                                                    <i class="material-icons">remove_shopping_cart</i>
                                                </button>
                                            </div>
                                        <?php else: ?>
                                            <small>Sin acciones</small>
                                        <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                            <?= $pager->links() ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--formulario para asociar producto -->
     <form  :action="urlForm" method="post">
        <div id="modal1" class="modal modal-fixed-footer" v-bind:style="{height: height +'px' }" style="width: 600px;">
            <div class="modal-content" >
                <h4 style="font-size: 24px;font-weight: 300;">{{  nameProduct }}</h4>
                <div v-if="active">
                    <div class="row">
                        <div class="col s12 m6">
                            <p>Asignar Producto</p>
                        </div>
                        <div class="col s12 m6">
                            <select name="id_product"  class="browser-default" required>
                                <option value="" disabled selected>Seleccione...</option>
                                <?php foreach($products as $item): ?>
                                    <option value="<?= $item->id ?>">[<?= $item->code ?>] - <?= $item->name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div v-if="!active">
                    <div class="row" >
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
                            <select type="text "   class="browser-default" class="validate" id="free" name="free">
                                <option value="false">No</option>
                                <option value="true">Si</option>
                            </select>
                            <label for="free" class="active">Gratis <span class="text-red red-text darken-1">*</span></label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12 m6" >
                            <textarea name="description" id="description" cols="30" rows="10" class="materialize-textarea"  class="validate" required></textarea>
                            <label for="description">Descripción <span class="text-red red-text darken-1">*</span></label>
                        </div>
                        <div class="input-field col s12 m6">
                            <label for="entry_credit" class="active">Ingreso <span class="text-red red-text darken-1">*</span></label>
                            <select class="browser-default" name="entry_credit" id="entry_credit" required>
                                <?php foreach($entryCredit as $item): ?>
                                    <option value="<?= $item->id ?>">[<?= $item->code ?>] - <?=  $item->name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12 m6">
                            <label for="entry_debit" class="active">Devolución <span class="text-red red-text darken-1">*</span></label>
                            <select class="browser-default" name="entry_debit"  id="entry_debit"   class="validate" required>
                                <?php foreach($entryDebit as $item): ?>
                                    <option value="<?= $item->id ?>"><?=  $item->name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="input-field col s12 m6">
                            <label for="iva" class="active">IVA <span class="text-red red-text darken-1">*</span></label>
                            <select  class="browser-default" name="iva"  id="iva"   class="validate" required>
                                <?php foreach($taxPay as $item): ?>
                                    <option value="<?= $item->id ?>">[<?= $item->code ?>] - <?=  $item->name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="input-field col s12 m6" >
                            <label for="retefuente" class="active">ReteFuente <span class="text-red red-text darken-1">*</span></label>
                            <select class="browser-default"  name="retefuente"  id="retefuente"  class="validate" required>
                                <?php foreach($taxAdvance  as $item): ?>
                                    <option value="<?= $item->id ?>">[<?= $item->code ?>] - <?=  $item->name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="input-field col s12 m6">
                            <label for="reteica" class="active">ReteICA <span class="text-red red-text darken-1">*</span></label>
                            <select class="browser-default"  name="reteica"  id="reteica"  class="validate" required>
                                <?php foreach($taxAdvance as $item): ?>
                                    <option value="<?= $item->id ?>">[<?= $item->code ?>] - <?=  $item->name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="input-field col s12 m6">
                            <label class="active" for="reteiva" >ReteIVA <span class="text-red red-text darken-1">*</span></label>
                            <select class="browser-default"  name="reteiva"  id="reteiva"  class="validate" required>
                                <?php foreach($taxAdvance as $item): ?>
                                    <option value="<?= $item->id ?>">[<?= $item->code ?>] - <?=  $item->name ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="input-field col s12 m6">
                            <label for="account_pay" class="active">Cuenta por Cobrar <span class="text-red red-text darken-1">*</span></label>
                            <select  class="browser-default"  name="account_pay"  id="account_pay"  class="validate" required>
                                <?php foreach($accountPay as $item): ?>
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
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button class="btn  left red step-7">Guardar</button>
                <button class="btn  left green"  style="margin-left: 5px;" id="create" type="button" @click="create()" v-if="active">Crear Producto</button>
                <button class="btn  left btn-light-indigo" style="margin-left: 5px;"  id="return" type="button" @click="create()"  v-if="!active">Regresar</button>
                <a href="#!" class="modal-close waves-effect waves-green btn-flat btn-light-indigo" >Cerrar</a>
            </div>
        </div>
    </form>
    <!-- end formulario para asociar producto -->
</div>
<?= $this->endSection('content') ?>

<?= $this->section('scripts') ?>
    <script src="<?= base_url('js/vue.js') ?>"></script>
    <script src="<?= base_url('/js/shepherd.min.js') ?>"></script>
    <script src="<?= base_url('/js/ui-alerts.js') ?>"></script>
    <script src="<?= base_url('/js/sweetalert.min.js') ?>"></script>
    <script src="<?= base_url('/js/advance-ui-modals.js') ?>"></script>
    <script src="<?= base_url('/js/modules/document_reception/associate_product.js') ?>"></script>
<?= $this->endSection('scripts') ?>



