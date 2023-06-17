<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?> Facturación <?= $this->endSection() ?>
<?= $this->section('styles')  ?>
<script src="<?= base_url('/js/ckeditor/ckeditor.js') ?>"></script>
<?= $this->endSection() ?>
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
                               Productos
                            </span>
                        </h5>
                        <ol class="breadcrumbs mb-0">
                            <li class="breadcrumb-item"><a href="<?= base_url() ?>/home">Home</a></li>
                            <li class="breadcrumb-item"><a href="<?= base_url() . route_to('products-index') ?>">Productos</a>
                            </li>
                            <li class="breadcrumb-item active"><a href="#">Producto</a></li>
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
                            <div class="card-panel">
                                <div class="row">
                                    <div class="col s12 m7">
                                        <div class="display-flex media">
                                            <?php if (!is_null($product->foto) && !empty($product->foto)): ?>
                                                <a href="#" class="avatar">
                                                    <img src="<?= base_url('assets/upload/products/' . $product->foto) ?>"
                                                         alt="users view avatar" class="z-depth-4 circle" height="64"
                                                         width="64">
                                                </a>
                                            <?php endif; ?>
                                            <div class="media-body">
                                                <h6 class="media-heading">
                                                    <span class="users-view-name grey-text">Producto: </span>
                                                    <span class="users-view-username"><?= $product->producto ?></span>
                                                </h6>
                                                <span>Código:</span>
                                                <span class="users-view-id"><?= $product->code ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col s12 m5 quick-action-btns display-flex justify-content-end align-items-center pt-2">
                                        <a href="" data-target="create_detail" class="btn-small modal-trigger btn-light-indigo">Agregar Politica</a>
                                        <a style="margin-left: 5px !important; " href="<?=  base_url().route_to('products-edit', $product->productId) ?>" class="btn-small indigo">Editar</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-content">
                                    <div class="row">
                                        <div class="col s12 m4">
                                            <h6 class="media-heading">
                                                <span class="users-view-name grey-text">Resumen de producto </span>
                                            </h6>
                                            <table class="striped">
                                                <tbody>
                                                <tr>
                                                    <td>Categoría</td>
                                                    <td><?= $product->categoria ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Costo</td>
                                                    <td class="users-view-latest-activity">$ <?= $product->cost ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Valor venta</td>
                                                    <td class="users-view-verified"> $  <?= $product->valor ?> </td>
                                                </tr>
                                                <tr>
                                                    <td>Marca </td>
                                                    <td class="users-view-role"><?= $product->brandname ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Modelo</td>
                                                    <td class="users-view-role"><?= $product->modelname ?></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col s12 m8">
                                            <h6 class="media-heading">
                                                <span class="users-view-name grey-text">Resumen de entradas </span>
                                            </h6>
                                            <table class="responsive-table">
                                                <thead>
                                                <tr>
                                                    <th>Fecha</th>
                                                    <th>Código Remisión </th>
                                                    <th>Politica</th>
                                                    <th>Costo</th>
                                                    <th>Estado</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php foreach($details as $detail):
                                                    if(isset($detail['invoiceCreate']) && !empty($detail['invoiceCreate'])){
                                                        $date = $detail['invoiceCreate'];
                                                    }else{
                                                        $date = $detail['created_at'];
                                                    }
                                                    ?>
                                                    <tr>
                                                        <td><?= date("d-m-Y", strtotime($date)); ?></td>
                                                        <td><?= $detail['resolution'] ?></td>
                                                        <td><?= $detail['policy_type'] ?></td>
                                                        <td><?= $detail['cost_value'] ?></td>
                                                        <td>
                                                            <form action="">
                                                                <div class="switch">
                                                                    <label>
                                                                        Inactivo
                                                                        <input
                                                                               type="checkbox"
                                                                               name='changeStatus[<?= $detail['id_products_details'] ?>]'
                                                                               id='changeStatus-<?= $detail['id_products_details'] ?>'
                                                                               onclick="changeStatus('<?= $detail['id_products_details'] ?>','<?= $detail['status'] ?>', '<?= $detail['id_product'] ?>')"
                                                                            <?= ($detail['status'] == 'active')?'checked':'' ?> >
                                                                        <span class="lever"></span>
                                                                        Activo
                                                                    </label>
                                                                </div>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                            <?php if(count($details) == 0): ?>
                                                <p class="center red-text pt-1" >No hay ningún producto.</p>
                                            <?php endif ?>
                                            <?= $pager->links(); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--------------------------- modal de crear detalle ------------------------------->
<form action="<?=  base_url().route_to('productsDetails-create', $product->productId) ?>" method="post" autocomplete="off">
    <div id="create_detail" class="modal" role="dialog" style="height:auto; width: 600px">
        <div class="modal-content">
            <h5>Agregar politica</h5>
            <div class="row">
                <div class="col s12 m6 l6 input-field">
                    <select name="policy_type" id="policy_type" class="validate" required>
                        <option selected value="personalizado">Personalizado</option>
                    </select>
                    <label for="policy_type">Tipo de politica</label>
                </div>
                <div class="col s12 m6 l6 input-field">
                    <label for="cost_value">Valor costo producto</label>
                    <input type="number" id="cost_value" name="cost_value" class="validate" required>
                </div>
                <div class="col s12 m12 l12 " >
                    <label for="editorCreate">Observaciones</label>
                    <textarea  id="snippet-classic-editor" class="editor" rows="15" cols="5" name="observations" ></textarea>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-action modal-close waves-effect  btn-flat btn-light-indigo ">Cerrar</a>
            <button type="submit" class="btn indigo">agregar</button>
        </div>
    </div>
</form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('/js/jquery.validate.js') ?>"></script>
<script src="<?= base_url('/js/advance-ui-modals.js') ?>"></script>
<script src="<?= base_url('/js/shepherd.min.js') ?>"></script>
<script src="<?= base_url('/js/vue.js') ?>"></script>
<script src="<?= base_url('/js/ui-alerts.js') ?>"></script>
<script src="<?= base_url('/assets/js/sweetalert.min.js') ?>"></script>
<script>
    $(document).ready(function () {
        const created = CKEDITOR.replace('snippet-classic-editor', {
            extraPlugins: 'notification',
        });
    });
    function changeStatus(id = null, status = null, idProduct = null) {

        var idElement = '#changeStatus['+id+']';
        $.post('<?=  base_url() ?>/products_details_status',
            {
                idDetail: id,
                status: status,
                idProduct: idProduct
            },
            function (data, status) {
                var resp = JSON.parse(data);
                if (resp.status == 200) {
                    swal({
                        title: "Procesado con éxito!",
                        text: resp.observation,
                        icon: "success",
                    });
                    /*resp.dataUpdate.forEach(function (valor, indice) {
                        let change = `#changeStatus-${valor.id_products_details}`;
                        console.log(change);
                        $(change).removeAttr('checked');
                    });*/
                    window.location.replace(`<?=  base_url().route_to('products-show', $product->productId) ?>`);
                } else {
                    swal({
                        title: "No se realizó el proceso",
                        text: resp.observation,
                        icon: "warning",
                    });
                }
            });
    }
</script>
<?= $this->endSection() ?>



