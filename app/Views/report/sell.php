<?= $this->extend('layouts/main') ?>


<?= $this->section('title') ?> Reporte Ventas <?= $this->endSection() ?>

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
                            <h5 class="breadcrumbs-title mt-0 mb-0 display-inline hide-on-small-and-down">
                            <span>
                                Reporte de Ventas
                            </span>
                            </h5>
                            <ol class="breadcrumbs mb-0">
                                <li class="breadcrumb-item"><a href="<?= base_url() ?>/home">Home</a></li>
                                <li class="breadcrumb-item active"><a href="#"> Reporte Ventas</a></li>
                            </ol>

                        </div>
                    </div>
                </div>
            </div>
            <div class="col s12">
                <div class="container">
                    <ul class="collapsible mb-0">
                        <li class="active">
                            <div class="collapsible-header light-blue light-blue-text text-lighten-5">Filtro</div>
                            <div class="collapsible-body white lighten-5">
                                <form action="" method="get">
                                    <div class="row">
                                        <div class="input-field col s6">
                                            <input name="start_date" id="start_date" type="date" value="<?= isset($_GET['start_date']) ? $_GET['start_date'] :  date('Y-m-d') ?>">
                                            <label class="active" for="start_date">Fecha de inicio</label>
                                        </div>
                                        <div class="input-field col s6">
                                            <input name="end_date" id="end_date" type="date" value="<?= isset($_GET['end_date']) ? $_GET['end_date'] :  date('Y-m-d') ?>">
                                            <label class="active" for="end_date">Fecha de inicio</label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="input-field col s12 l4">
                                            <select name="headquarters_providers" class="select2 browser-default" <?=  (!$data->permiso) ? 'disabled' : '' ?>>
                                                <option value="">Todos</option>
                                                <?php foreach ($headquarters as $headquarter): ?>
                                                    <option <?= $data->permiso ? (isset($_GET['headquarters_providers']) && $_GET['headquarters_providers'] == $headquarter->id ? 'selected' : '' ) : (session('user')->companies_id == $headquarter->id) ? 'selected' : '' ?>
                                                            value="<?= $headquarter->id ?>"><?= $headquarter->company ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <label>Sede :</label>
                                        </div>
                                        <div class="input-field col s12 l4">
                                            <select name="user" class="select2 browser-default" <?=  (!$data->permiso) ? 'disabled' : '' ?>>
                                                <option value="">Todos</option>
                                                <?php foreach ($users as $user): ?>
                                                    <option <?= $data->permiso ? (isset($_GET['user']) && $_GET['user'] == $user->id  ? 'selected' : '') : (session('user')->id == $user->id) ? 'selected' : '' ?>
                                                            value="<?= $user->id ?>"><?= $user->name ?> [<?= $user->username ?>]</option>
                                                <?php endforeach; ?>
                                            </select>
                                            <label>Vendedores:</label>
                                        </div>
                                        <div class="input-field col s12 l4">
                                                <select name="type">
                                                    <option <?= (isset($_GET['type']) && $_GET['type'] == 'ventas') ? 'selected' : '' ?>
                                                            value="ventas">Ventas</option>
                                                        <!-- <option <?= (isset($_GET['type']) && $_GET['type'] == 'gastos') ? 'selected' : '' ?>
                                                                value="gastos">Gastos
                                                        </option> -->
                                                    <option <?= (!$data->permiso || session('user')->role_id != 15) ? 'disabled' : '' ?> <?= (isset($_GET['type']) && $_GET['type'] == 'utilidad') ? 'selected' : '' ?>
                                                            value="utilidad">Utilidad</option>
                                                    <option <?= (isset($_GET['type']) && $_GET['type'] == 'productos') ? 'selected' : '' ?>
                                                            value="productos">Productos</option>
                                            </select>
                                            <label>Tipo de informe:</label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <?php if((isset($_GET['type']) && $_GET['type'] == 'productos')): ?>
                                            <div class="input-field col s12 l4">
                                                <select name="orderBy">
                                                    <option <?= (isset($_GET['orderBy']) && $_GET['orderBy'] == 'quantity') ? 'selected' : '' ?>
                                                        value="quantity">Cantidad</option>
                                                    <option <?= (isset($_GET['orderBy']) && $_GET['orderBy'] == 'cost_amount') ? 'selected' : '' ?>
                                                        value="cost_amount">Valor costo</option>
                                                    <option <?= (isset($_GET['orderBy']) && $_GET['orderBy'] == 'price_amount') ? 'selected' : '' ?>
                                                        value="price_amount">Valor de venta</option>
                                                    <option <?= (isset($_GET['orderBy']) && $_GET['orderBy'] == 'utilidad') ? 'selected' : '' ?>
                                                        value="utilidad">Utilidad</option>
                                                </select>
                                                <label>Tipo de orden</label>
                                            </div>
                                            <div class="col s12 l4">
                                                <p>
                                                    <label>
                                                        <input class="with-gap" value="DESC" name="DESC" type="radio" checked />
                                                        <span>Descendente</span>
                                                    </label>
                                                    <label>
                                                        <input class="with-gap" value="ASC" name="DESC" type="radio" <?= (isset($_GET['DESC']) && $_GET['DESC'] == 'ASC') ? 'checked' : '' ?> />
                                                        <span>Ascendente</span>
                                                    </label>
                                                </p>
                                            </div>
                                        <?php endif ?>
                                        <div class="col s<?= (isset($_GET['type']) && $_GET['type'] == 'productos') ? '4' : '12' ?>">
                                            <button type="submit" style="margin: 0px !important;"
                                                    class="right btn  btn-light-indigo modal-trigger step-4 mb-2 active-red">
                                                Buscar <i class="material-icons right">filter_list</i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>
                    </ul>
                    <div class="section">
                        <div class="card">
                            <div class="card-content" style="margin-bottom:70px !important">
                                <div id="card-with-analytics" class="section"> 
                                    <h3 class="header center-align">Informe de <?= (!isset($_GET['type'])) ? 'Ventas': ucwords($_GET['type']) ?></h3>
                                    <div class="row center-align">
                                        <div class="col s12">
                                            <p>
                                                <b>Fecha Inicio: <?= isset($_GET['start_date']) ? $_GET['start_date'] :  date('Y-m-d') ?></b>
                                                /
                                                <b>Fecha Fin: <?= isset($_GET['end_date']) ? $_GET['end_date'] :  date('Y-m-d') ?></b></p>
                                        </div>
                                    </div>
                                    <?php switch($type_informe):
                                        case 'ventas':
                                        default:?>
                                            <div class="row left-align">
                                                <!-- <div class="col s12 l6"> -->
                                                    <h4>Ingresos</h4>
                                                    <div class="col l1"></div>
                                                    <div class="col l11">
                                                        <table class="mt-0">
                                                            <tbody>
                                                                <tr>
                                                                    <td><h6><b>Total Ventas</b></h6></td>
                                                                    <td class="right-align"><h6>$ <?= number_format($data->ventas->total, '2', ',', '.') ?></h6></td>
                                                                </tr>
                                                                <?php foreach($data->ventas->detail as $detail): ?>
                                                                    <?php if($detail->total > 0): ?>
                                                                    <tr>
                                                                        <td><?= $detail->name ?></td>
                                                                        <td class="right-align">
                                                                            $ <?= number_format($detail->total, '2', ',', '.') ?>
                                                                        </td>
                                                                    </tr>
                                                                    <?php endif ?>
                                                                <?php endforeach; ?>
                                                                <tr>
                                                                    <td><h6><b>Abono CxC</b></h6></td>
                                                                    <td class="right-align"><h6><b>$ <?= number_format($data->CxC->total, '2', ',', '.') ?></h6></td>
                                                                </tr>
                                                                <?php foreach($data->CxC->detail as $detail): ?>
                                                                    <?php if($detail->total > 0): ?>
                                                                    <tr>
                                                                        <td><?= $detail->name ?></td>
                                                                        <td class="right-align">
                                                                            $ <?= number_format($detail->total, '2', ',', '.') ?>
                                                                        </td>
                                                                    </tr>
                                                                    <?php endif ?>
                                                                <?php endforeach; ?>
                                                                <tr>
                                                                    <td><h5><b>Total de Ingresos</b></h5></td>
                                                                    <td class="right-align"><h5><b>$ <?= number_format(($data->ventas->total + $data->CxC->total), '2', ',', '.') ?></b></h5></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><h6><b>Efectivo</b></h6></td>
                                                                    <td class="right-align"><h6><b>$ <?= number_format(($data->ventas->detail->efectivo->total + $data->CxC->detail->efectivo->total), '2', ',', '.') ?></b></h6></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                <!-- </div>
                                                <div class="col s12 l6"> -->
                                                    <h4>Egresos</h4>
                                                    <div class="col l1"></div>
                                                    <div class="col l11">
                                                        <table class="mt-0">
                                                            <tbody>
                                                                <tr>
                                                                    <td><h6><b>Total Gastos</b></h6></td>
                                                                    <td class="right-align"><h6>$ <?= number_format($data->gastos->total, '2', ',', '.') ?></h6><b></b></td>
                                                                </tr>
                                                                <?php foreach($data->gastos->detail as $detail): ?>
                                                                    <?php if($detail->total > 0): ?>
                                                                        <tr>
                                                                            <td><?= $detail->name ?></td>
                                                                            <td class="right-align">
                                                                                $ <?= number_format($detail->total, '2', ',', '.') ?>
                                                                            </td>
                                                                        </tr>
                                                                    <?php endif ?>
                                                                <?php endforeach; ?>
                                                                <tr>
                                                                    <td><h6><b>Total Abonos CxP</b></h6></td>
                                                                    <td class="right-align"><h6>$ <?= number_format($data->CxP->total, '2', ',', '.') ?></h6><b></b></td>
                                                                </tr>
                                                                <?php foreach($data->CxP->detail as $detail): ?>
                                                                    <?php if($detail->total > 0): ?>
                                                                        <tr>
                                                                            <td><?= $detail->name ?></td>
                                                                            <td class="right-align">
                                                                                $ <?= number_format($detail->total, '2', ',', '.') ?>
                                                                            </td>
                                                                        </tr>
                                                                    <?php endif ?>
                                                                <?php endforeach; ?>
                                                                <tr>
                                                                    <td><h5><b>Total de Egresos</b></h5></td>
                                                                    <td class="right-align"><h5><b>$ <?= number_format(($data->gastos->total + $data->CxP->total), '2', ',', '.') ?></b></h5></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><h6><b>Efectivo</b></h6></td>
                                                                    <td class="right-align"><h6><b>$ <?= number_format(($data->gastos->detail->efectivo->total + $data->CxP->detail->efectivo->total), '2', ',', '.') ?></b></h6></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                <!-- </div> -->
                                                <h4>Total</h4>
                                                <div class="col l1"></div>
                                                <div class="col l11">
                                                    <table class="mt-0">
                                                        <tbody>
                                                            <tr>
                                                                <td><h5><b>Ingresos - Egresos</b></h5></td>
                                                                <td class="right-align"><h5><b>$ <?= number_format(($data->total->bruto), '2', ',', '.') ?></b></h5></td>
                                                            </tr>
                                                            <tr>
                                                                <td><h6><b>Efectivo</b></h6></td>
                                                                <td class="right-align"><h6><b>$ <?= number_format($data->total->efectivo, '2', ',', '.') ?></b></h6></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        <?php break;
                                        case 'utilidad': ?>
                                            <div class="row center-align">
                                                <h5>Utilidad</h5>
                                                <table class="centered">
                                                    <tbody>
                                                        <tr>
                                                            <td><h6><b>Total Ventas: </b></h6></td>
                                                            <td><h6>$ <?= number_format(($data->ventas->total), '2', ',', '.') ?></h6></td>
                                                        </tr>
                                                        <tr>
                                                            <td><h6><b>Total costos</b></h6></td>
                                                            <td><h6>$ <?= number_format(($data->ventas->total_costos), '2', ',', '.') ?></h6></td>
                                                        </tr>
                                                        <tr>
                                                            <td><h5><b>Utilidad Bruta</b></h5></td>
                                                            <td><h5><b>$ <?= number_format(($data->ventas->total - $data->ventas->total_costos), '2', ',', '.') ?></b></h5></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <h5>Gastos</h5>
                                                <table class="centered">
                                                    <tbody>
                                                        <?php foreach($data->gastos->detail as $detail): ?>
                                                            <?php if($detail->total > 0): ?>
                                                            <tr>
                                                                <td><?= $detail->name ?></td>
                                                                <td>
                                                                    $ <?= number_format($detail->total, '2', ',', '.') ?>
                                                                </td>
                                                            </tr>
                                                            <?php endif ?>
                                                        <?php endforeach; ?>
                                                        <tr>
                                                            <td><h5><b>Total Gastos</b></h5></td>
                                                            <td><h5><b>$ <?= number_format(($data->gastos->total), '2', ',', '.') ?></b></h5></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <h5>Utilidad Neta</h5>
                                                <?php $total_neta = ($data->ventas->total - $data->ventas->total_costos) - ($data->gastos->total) ?>
                                                <h4 class="m-0"><b>$ <?= number_format($total_neta, '2', ',', '.') ?></b></h4>
                                            </div>
                                        <?php break;
                                        case 'productos': ?>
                                        <table class="centered">
                                            <thead>
                                                <tr>
                                                    <th>Producto</th>
                                                    <th>Cantidad</th>
                                                    <?php if ($data->permiso): ?>
                                                        <th>Valor venta</th>
                                                        <th>Valor costo</th>
                                                        <th>Utilidad</th>
                                                    <?php endif ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($data->products as $product): ?>
                                                    <tr>
                                                        <td><?= $product->name_product ?></td>
                                                        <td><?= $product->quantity ?></td>
                                                        <?php if ($data->permiso): ?>
                                                            <td>$ <?= number_format($product->price_amount, '2', ',', '.') ?></td>
                                                            <td>$ <?= number_format($product->cost_amount, '2', ',', '.') ?></td>
                                                            <td>$ <?= number_format($product->price_amount - $product->cost_amount, '2', ',', '.') ?></td>
                                                            <?php endif ?>
                                                        </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                        <?= $data->pager->links() ?>
                                        <?php break;
                                    endswitch; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


<?php $this->endSection() ?>

<?php $this->section('scripts') ?>
    <script>
        $(document).ready(function () {
            $('.datepicker').datepicker();
            $(".select2").select2();
        });
    </script>
    <script src="<?= base_url('/js/advance-ui-modals.js') ?>"></script>
    <script src="<?= base_url('/js/shepherd.min.js') ?>"></script>
    <script src="<?= base_url('/js/views/wallet.js') ?>"></script>
<?php $this->endSection() ?>