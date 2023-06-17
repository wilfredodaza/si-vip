<?= $this->extend('layouts/main') ?>


<?= $this->section('title') ?> Disponible <?= $this->endSection() ?>

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
                               Disponibilidad
                            </span>
                        </h5>
                        <ol class="breadcrumbs mb-0">
                            <li class="breadcrumb-item"><a href="<?= base_url() ?>/home">Home</a></li>
                            <li class="breadcrumb-item active"><a href="#">Disponibilidad</a></li>
                        </ol>
                    </div>
                </div>
            </div>
            <?php
            if ($manager):
                foreach ($indicadores as $indicador): ?>
                    <div class="col s12 m6 l6">
                        <div class="card padding-4 animate fadeLeft shop">
                            <div class="row">
                                <div class="col s6 align-items-center" id="indicador-<?= $indicador->id ?>">
                                    <h5 class="mb-0"><?= '$ ' . number_format(($indicador->total), '0', ',', '.') ?></h5>
                                    <p class="no-margin" style="line-height:1;"><?= $indicador->name ?><br>
                                        <span class="no-padding no-margin"
                                              style="font-size: 10px !important;"><?= $indicador->observaciones ?></span>
                                    </p>

                                    <!-- <p class="mb-0 pt-8 tooltipped" data-position="bottom" data-delay="50" data-html="true" data-tooltip="  <i class='material-icons text-green green-text tiny'>brightness_1</i> 2 Por vencerse
                                    <br><i class='material-icons text-yellow yellow-text tiny'>brightness_1</i> 0 Por vencerse
                                    <br><i class='material-icons text-red red-text tiny'>brightness_1</i> 1 Por vencerse "> <strong> 3 </strong> por vencer <i class="material-icons text-red red-text tiny">brightness_1</i></p> -->
                                </div>

                                <div class="col s6 icon align-items-center">
                                    <i class="material-icons <?= $indicador->color ?> background-round mt-5 white-text right"><?= $indicador->icon ?></i>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach;
            endif;
            ?>
        </div>
        <div class="col s12">
            <div class="container">
                <div class="section">
                    <div class="card">
                        <div class="card-content">
                            <p class="">
                                <?php if (isset($_GET['headquarter']) || isset($_GET['code']) || isset($_GET['name'])): ?>
                                    <a href="<?= base_url() . route_to('inventory-availability') ?>"
                                       class="btn right btn-light-red btn-small ml-1"
                                       style="padding-left: 10px;padding-right: 10px; margin-right: 10px; ">
                                        <i class="material-icons left">close</i>
                                        Quitar Filtro
                                    </a>
                                <?php endif; ?>
                                <button <?= (!$manager) ? 'disabled' : '' ?> data-target="filter"
                                                                             style="margin-left: 5px;"
                                                                             class="right btn btn-small btn-light-indigo modal-trigger step-5 active-red">
                                    Filtrar <i class="material-icons right">filter_list</i>
                                </button>
                                <a href="<?= base_url('inventory/out_transfer') ?>" <?= ($manager) ? 'disabled' : '' ?>
                                   class="btn-small  btn-light-indigo right"
                                   style="margin-bottom:20px; padding-right: 10px; padding-left: 10px;">
                                    <i class="material-icons right">add</i>
                                    Realizar transferencia
                                </a>
                                Aquí podrás ver la disponibilidad de los productos con los que actualmente cuentas.
                            </p>
                            <div class="row">
                                <div class="col s12">
                                    <table>
                                        <thead>
                                        <tr>
                                            <th>Productos</th>
                                            <th class="center">Entradas</th>
                                            <th class="center">Salidas</th>
                                            <th class="center">Disponibilidad</th>
                                            <!--<th class="center">Total de Disponibilidad</th>-->
                                            <th class="center">Acciones</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($products as $item):
                                            $input = $item->input + $item->inputTransfer;
                                            $output = $item->output + $item->outputTransfer;
                                            // $available = ($item->availability_input + $item->availability_input_transfer) - ($item->availability_output + $item->availability_output_transfer);
                                            ?>
                                            <tr>
                                                <td><?= $item->name ?> - <?= $item->tax_iva ?></td>
                                                <td class="center"><?= $input ?></td>
                                                <td class="center"><?= $output ?></td>
                                                <td class="center"><?= $input - $output ?></td>
                                                <!--<td class="center"><?= number_format(($output), 2, ',', '.') ?></td>-->
                                                <td class="center">
                                                    <div class="btn-group">
                                                        <?php if (isset($_GET['headquarter'])): ?>
                                                            <a href="<?= base_url('inventory/kardex/' . $item->id . '?headquarter=' . $_GET['headquarter']) ?>"
                                                               class="btn red darken-2 tooltipped" data-position="top"
                                                               data-tooltip="Kardex">
                                                                <i class="material-icons">description</i>
                                                            </a>
                                                        <?php else: ?>
                                                            <a href="<?= base_url('inventory/kardex/' . $item->id) ?>"
                                                               class="btn red darken-2 tooltipped" data-position="top"
                                                               data-tooltip="Kardex">
                                                                <i class="material-icons">description</i>
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                    <?php if (count($products) == 0): ?>
                                        <p class="center red-text pt-1">No hay ningún producto con disponibilidad.</p>
                                    <?php endif ?>
                                    <?= $pager->links() ?>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- modal transfer -->
<div id="modalTransfer" class="modal" role="dialog" style="height:auto; width: 600px">
    <div class="modal-content">
        <form action="<?= base_url() . route_to('inventory-transfer') ?>" method="post" id="transfer">
            <div class="row">
                <div class="col s12 m12 l12">
                    <div class="input-field">
                        <select class="select2 browser-default validate" id="headquarterId" name="headquarterId"
                                required>
                            <option selected disabled value="">Seleccione una sede</option>
                            <?php foreach ($headquarters as $headquarter): ?>
                                <option <?= ($headquarter->id == company()->id) ? 'disabled' : '' ?>
                                        value="<?= $headquarter->id ?>"><?= $headquarter->company ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="headquarterId">Sede a transferir <span class='red-text'> * </span></label>
                    </div>
                </div>
                <div class="col s12 m6 l6">
                    <div class="input-field">
                        <select class="select2 browser-default validate" id="productId" name="productId" required>
                            <option selected disabled value="">Seleccione un producto</option>
                            <?php foreach ($productsTransfer as $productTransfer): ?>
                                <option value="<?= $productTransfer->id ?>"><?= $productTransfer->name ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="productId">Producto a transferir <span class='red-text'> * </span></label>
                    </div>
                </div>
                <div class="col s12 m6 l6">
                    <div class="input-field">
                        <input placeholder="" id="quantity" name="quantity" type="text" class="validate" required>
                        <label for="quantity">Cantidad a transferir <span class='red-text'> * </span></label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col s12 m12 l12">
                    <button type="submit" class="btn btn-light-indigo right ml-2">Transferir</button>
                    <a href="#!" onclick="clearForm()"
                       class="modal-action modal-close btn btn-light-red right">Cancelar</a>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- modal filtro -->
<form action="" method="GET" autocomplete="off">
    <div id="filter" class="modal" role="dialog" style="height:auto; width: 600px">
        <div class="modal-content">
            <h5>Filtrar por sede</h5>
            <div class="row">
                <div class="col s12 m6 input-field">
                    <select class="select2 browser-default validate" name="headquarter" id="headquarter">
                        <option selected disabled value="">Seleccione una Sede</option>
                        <?php foreach ($headquarters as $headquarter): ?>
                            <option <?= (isset($_GET['headquarter']) && $_GET['headquarter'] == $headquarter->id) ? 'selected' : '' ?>
                                    value="<?= $headquarter->id ?>"><?= $headquarter->company ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label for="headquarter">Sedes</label>
                </div>
                <div class="col s12 m6 input-field">
                    <label for="name">Nombre producto</label>
                    <input type="text" id="name" name="name" value="<?= $_GET['name'] ?? '' ?>">
                </div>
                <div class="col s12 input-field">
                    <select class="select2 browser-default validate" name="orderBy" id="orderBy">
                        <option selected disabled value="">Seleccione un tipo de orden</option>
                        <option <?= (isset($_GET['orderBy']) && $_GET['orderBy'] == 'entry') ? 'selected' : '' ?>
                                    value="entry">Entrada</option>
                        <option <?= (isset($_GET['orderBy']) && $_GET['orderBy'] == 'sales') ? 'selected' : '' ?>
                                    value="sales">Salida</option>
                        <option <?= (isset($_GET['orderBy']) && $_GET['orderBy'] == 'disponibility') ? 'selected' : '' ?>
                                    value="disponibility">Disponibilidad</option>
                    </select>
                    <label for="orderBy">Sedes</label>
                </div>
                <div class="col s12" style="display: flex;justify-content: space-around;">
                        <label>
                            <input  class="with-gap" value="DESC" name="DESC" type="radio" <?= (isset($_GET['DESC']) && $_GET['DESC'] == 'DESC') ? 'checked' : '' ?>/>
                            <span>Descendente</span>
                        </label>
                        <label>
                            <input  class="with-gap" value="ASC" name="DESC" type="radio" <?= (isset($_GET['DESC']) && $_GET['DESC'] == 'ASC') ? 'checked' : '' ?>/>
                            <span>Ascendente</span>
                        </label>
                </div>
            </div>
            <div class="row">
                <!--<div class="col s12 m6 input-field">
                    <label for="code">Còdigo producto</label>
                    <input type="text" id="code" name="code" value="<?= $_GET['code'] ?? '' ?>">
                </div>-->
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
<script>
    $(".select2").select2({
        dropdownAutoWidth: true,
        width: '100%'
    });

    function clearForm() {
        document.getElementById('transfer').reset();
    }
</script>
<?= $this->endSection() ?>

