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
                    <div class="col s10 m6 l6 breadcrumbs-left">
                        <h5 class="breadcrumbs-title mt-0 mb-0 display-inline hide-on-small-and-down ">
                            <span>
                               Productos
                            </span>
                        </h5>
                        <ol class="breadcrumbs mb-0">
                            <li class="breadcrumb-item"><a href="<?= base_url() ?>/home">Home</a></li>
                            <li class="breadcrumb-item active"><a href="<?= base_url().route_to('products-index') ?>">Productos</a></li>
                        </ol>

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
                                    class="right btn  btn-light-indigo modal-trigger step-5 active-red">
                                Filtrar <i class="material-icons right">filter_list</i>
                            </button>
                            <a href="<?=  base_url().route_to('products-create') ?>" class="btn right  indigo mr-1 step-2 active-red">Crear Producto</a>
                            <p class="">
                                <?php if (isset($_GET['product_code']) || isset($_GET['product_name']) || isset($_GET['category'])): ?>
                                    <a href="<?= base_url() . route_to('products-index') ?>"
                                       class="btn right btn-light-red btn-small ml-1"
                                       style="padding-left: 10px;padding-right: 10px; margin-right: 10px; ">
                                        <i class="material-icons left">close</i>
                                        Quitar Filtro
                                    </a>
                                <?php endif; ?>
                                Podrás ver los productos que tienes, crear productos nuevos y cambiarles la informaciòn.
                            </p>
                            <div class="row">
                                <div class="col s12">
                                    <table class="responsive-table striped">
                                        <thead>
                                        <tr>
                                            <th class="center">Grupo</th>
                                            <th class="center">Subgrupo</th>
                                            <th class="center">Producto</th>
                                            <th class="center">Código</th>
                                            <th class="center">Valor</th>
                                            <th class="center step-3 ">Acciones</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($products as $product): ?>
                                            <tr>
                                                <td class="center"><?= $product['group']?></td>
                                                <td class="center"><?= $product['subGroup']?></td>
                                                <td class="center"><?= $product['producto'] ?> - <?= $product['tax_iva'] ?></td>
                                                <td class="center"><?= $product['code'] ?></td>
                                                <td class="center" width="100px">
                                                    $ <?= number_format($product['valor'], '0', '.', '.') ?>
                                                </td>
                                                <td class="center" >
                                                    <div class="btn-group" role="group">
                                                        <a href="<?=  base_url().route_to('products-show', $product['productId']) ?>"
                                                           class="btn btn-small  yellow darken-1  tooltipped" data-position="top" data-tooltip="Resumen producto">
                                                            <i class="material-icons">remove_red_eye</i>
                                                        </a>
                                                        <a href="<?=  base_url().route_to('products-edit', $product['productId']) ?>"
                                                           class="btn btn-small pink send tooltipped" data-position="top"
                                                           data-tooltip="Editar" >
                                                            <i class="material-icons">edit</i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                    <?php if(count($products) == 0): ?>
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
    <!--------------------------- modal de busqueda ------------------------------->
    <form action="" method="GET" autocomplete="off">
        <div id="filter" class="modal" role="dialog" style="height:auto; width: 600px">
            <div class="modal-content">
                <h5>Filtrar Pedido</h5>
                <div class="row">
                    <div class="col s12 m6 input-field">
                        <label for="product_name">Nombre producto</label>
                        <input type="text" id="product_name" name="product_name" value="<?= $_GET['product_name'] ?? '' ?>">
                    </div>
                    <div class="col s12 m6 input-field">
                        <label for="product_code">Còdigo producto</label>
                        <input type="text" id="product_code" name="product_code" value="<?= $_GET['product_code'] ?? '' ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col s12 m6 input-field">
                        <select name="category" id="category">
                            <option selected disabled value="">Seleccione una Categoria</option>
                            <?php foreach($categories as $category): ?>
                                <option <?= (isset($_GET['category']) && $_GET['category'] == $category->id)?'selected':'' ?> value="<?= $category->id ?>"><?= $category->name ?></option>
                            <?php endforeach;?>
                        </select>
                        <label for="status">Categorìa del producto</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#!" class="modal-action modal-close waves-effect  btn-flat btn-light-indigo ">Cerrar</a>
                <button class="btn indigo">Buscar</button>

            </div>
        </div>
    </form>
    <br><br><br>
</div>

<?=  $this->endSection() ?>



<?= $this->section('scripts') ?>
<script src="<?= base_url('/js/advance-ui-modals.js') ?>"></script>
<script src="<?= base_url('/js/shepherd.min.js') ?>"></script>
<script src="<?= base_url('/js/vue.js')?>"></script>
<script src="<?= base_url('/js/ui-alerts.js') ?>"></script>
<script src="<?= base_url('/assets/js/sweetalert.min.js') ?>"></script>
<script src="<?= base_url('/js/views/invoice.js') ?>"></script>
<?=  $this->endSection() ?>



