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
                    <div class="section">
                        <div class="card">
                            <div class="card-content">

                                <div class="row">
                                    <div class="col s12 m12 l12">
                                        <?php $hoy = date('Y-m-d'); $end = date('Y-m-d');  ?>
                                        <form action="" method="get">
                                            <table class="striped">
                                                <tbody>
                                                <tr>
                                                    <td>Fecha inicio : </td>
                                                    <td class="users-view-name">
                                                        <div class="col s9 m9"
                                                             style="margin: 0px !important; padding: 0px !important">
                                                            <input name="start_date" type="date" value="<?= isset($_GET['start_date']) ? $_GET['start_date'] : $hoy ?>">
                                                        </div>
                                                    </td>
                                                    <td>Fecha final :</td>
                                                    <td class="users-view-email">
                                                        <div class="col s9 m9"
                                                             style="margin: 0px !important; padding: 0px !important">
                                                            <input name="end_date" type="date" value="<?= isset($_GET['end_date']) ? $_GET['end_date'] : $end ?>">
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Sede :</td>
                                                    <td class="users-view-username">
                                                        <div class="col s9 m9"
                                                             style="margin: 0px !important; padding: 0px !important">
                                                            <select id="headquarters" name="headquarters"
                                                                    class="select2 browser-default" style="z-index: -1 !important;">
                                                                <option value="todos" <?= (isset($_GET['headquarters'])) ? '' : 'selected' ?>>
                                                                    Todos
                                                                </option>
                                                                <?php foreach ($headquarters as $headquarter): ?>
                                                                    <option <?= (isset($_GET['headquarters']) && $_GET['headquarters'] == $headquarter->id) ? 'selected' : '' ?>
                                                                            value="<?= $headquarter->id ?>"><?= $headquarter->company ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                    </td>
                                                    <td>Tipo de informe:</td>
                                                    <td>
                                                        <div class="col s9 m9"
                                                             style="margin: 0px !important; padding: 0px !important">
                                                            <select id="type" name="type"
                                                                    class="select2 browser-default">
                                                                <option value="balance" <?= (!isset($_GET['type']) || $_GET['type'] == 'balance') ? '' : 'selected' ?>>
                                                                    Balance
                                                                </option>
                                                                <option <?= (isset($_GET['type']) && $_GET['type'] == 'ventas') ? 'selected' : '' ?>
                                                                        value="ventas">Ventas
                                                                </option>
                                                                <option <?= (isset($_GET['type']) && $_GET['type'] == 'gastos') ? 'selected' : '' ?>
                                                                        value="gastos">Gastos
                                                                </option>
                                                                <option <?= (isset($_GET['type']) && $_GET['type'] == 'utilidad') ? 'selected' : '' ?>
                                                                        value="utilidad">Utilidad
                                                                </option>
                                                            </select>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4">
                                                        <button type="submit" style="margin: 0px !important;"
                                                                class="right btn  btn-light-indigo modal-trigger step-4 mb-2 active-red">
                                                            Filtrar <i class="material-icons right">filter_list</i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4" class="center">
                                                       <h5> <?= (!isset($_GET['type']))?'Balance': ucwords($_GET['type']) ?></h5>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </form>
                                    </div>
                                </div>
                                <style>
                                    .titulo {
                                        width: 70% !important;
                                    }

                                    .valor {
                                        width: 30% !important;
                                    }
                                </style>
                                <div class="row">
                                    <div class="col s12 m12 l12">
                                        <h6 class="mb-2 mt-2 black-text"><i
                                                    class="material-icons">remove_circle_outline</i> Ingresos</h6>
                                        <table class="striped">
                                            <tbody>
                                            <tr>
                                                <td class="titulo" colspan="2">Ventas :</td>
                                                <td class="valor center black-text ">
                                                    $ <?= number_format($sellTotal, '2', ',', '.') ?></td>
                                            </tr>
                                            <?php $cash = 0;
                                            foreach ($sell as $item): ?>
                                                <?php
                                                if ($item['name'] == 'Efectivo') {
                                                    $cash += $item['total'];
                                                }
                                                ?>
                                                <?php if ($item['total'] > 0): ?>
                                                    <tr class="<?= (isset($_GET['type']) && $_GET['type'] == 'utilidad')?'hide':'' ?>">
                                                        <td class="titulo center" colspan="2"><?= $item['name'] ?></td>
                                                        <td class="valor left-align ">
                                                            $ <?= number_format($item['total'], '2', ',', '.') ?></td>
                                                    </tr>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                            <tr class="<?= (isset($_GET['type']) && $_GET['type'] == 'utilidad')?'':'hide' ?>">
                                                <td class="titulo" colspan="2">Costos:</td>
                                                <td class="valor center black-text">
                                                    $ <?= number_format($cost, '2', ',', '.') ?></td>
                                            </tr>
                                            <tr class="<?= (isset($_GET['type']) && $_GET['type'] == 'utilidad')?'hide':'' ?>">
                                                <?php $pay = (is_null($pays->total))?0:$pays->total; ?>
                                                <td class="titulo" colspan="2">Abonos CXC:</td>
                                                <td class="valor center black-text">
                                                    $ <?= number_format($pay, '2', ',', '.') ?></td>
                                            </tr>
                                            <tr class="<?= (isset($_GET['type']) && $_GET['type'] == 'utilidad')?'hide':'' ?>">
                                                <th class="titulo black-text" colspan="2">Total Ingresos:</th>
                                                <th class="valor center black-text">
                                                    $ <?= number_format(($pay + $sellTotal), '2', ',', '.') ?></th>
                                            </tr>
                                            <tr class="<?= (isset($_GET['type']) && $_GET['type'] == 'utilidad')?'':'hide' ?>">
                                                <th class="titulo black-text" colspan="2">Total :</th>
                                                <th class="valor center black-text">
                                                    $ <?= number_format(($sellTotal - $cost), '2', ',', '.') ?></th>
                                            </tr>
                                            <tr class="<?= (isset($_GET['type']) && $_GET['type'] == 'utilidad')?'hide':'' ?>">
                                                <td class="titulo" colspan="2">Total Efectivo:</td>
                                                <td class="valor center black-text">
                                                    $ <?= number_format(($pay + $cash), '2', ',', '.') ?></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col s12 m12 l12">
                                        <h6 class="mb-2 mt-2 black-text"><i class="material-icons">remove_circle</i>
                                            Egresos</h6>
                                        <table class="striped">
                                            <tbody>
                                            <?php
                                            $expenses = 0;
                                            foreach ($bills as $item) {
                                                $expenses += $item->payable_amount;
                                            }
                                            ?>
                                            <tr>
                                                <td class="titulo" colspan="2">Gastos :</td>
                                                <td class="valor center black-text ">
                                                    $ <?= number_format($expenses, '2', ',', '.') ?></td>
                                            </tr>
                                            <?php foreach ($bills as $item): ?>
                                                <tr class="<?= (isset($_GET['type']) && $_GET['type'] == 'utilidad')?'hide':'' ?>">
                                                    <td class="titulo center" colspan="2"><?= $item->name ?></td>
                                                    <td class="valor left-align ">
                                                        $ <?= number_format($item->payable_amount, '2', ',', '.') ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                            <tr class="<?= (isset($_GET['type']) && $_GET['type'] == 'utilidad')?'hide':'' ?>">
                                                <td class="titulo" colspan="2">Pagos CXP:</td>
                                                <td class="valor center black-text">
                                                    $ <?= number_format($payments, '2', ',', '.') ?></td>
                                            </tr>
                                            <tr class="<?= (isset($_GET['type']) && $_GET['type'] == 'utilidad')?'hide':'' ?>">
                                                <th class="titulo black-text" colspan="2">Total Egresos:</th>
                                                <th class="valor center black-text">
                                                    $ <?= number_format(($expenses + $payments), '2', ',', '.') ?></th>
                                            </tr>
                                            <tr class="<?= (isset($_GET['type']) && $_GET['type'] == 'utilidad')?'':'hide' ?>">
                                                <th class="titulo black-text" colspan="2">Total :</th>
                                                <th class="valor center black-text">
                                                    $ <?= number_format(($expenses), '2', ',', '.') ?></th>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col s12 m12 l12">
                                        <h6 class="mb-2 mt-2 black-text"><i class="material-icons">attach_money</i>
                                            Totales</h6>
                                        <table class="striped">
                                            <tbody>
                                            <tr class="<?= (isset($_GET['type']) && $_GET['type'] == 'utilidad')?'hide':'' ?>">
                                                <th class="titulo black-text" colspan="2">Total reporte:</th>
                                                <th class="valor center black-text">
                                                    $ <?= number_format((($pay + $sellTotal) - ($expenses + $payments)), '2', ',', '.') ?></th>
                                            </tr>
                                            <tr class="<?= (isset($_GET['type']) && $_GET['type'] == 'utilidad')?'hide':'' ?>">
                                                <td class="titulo" colspan="2">Total Efectivo:</td>
                                                <td class="valor center black-text">
                                                    $ <?= number_format((($pay + $cash) - ($expenses + $payments)), '2', ',', '.') ?></td>
                                            </tr>
                                            <tr class="<?= (isset($_GET['type']) && $_GET['type'] == 'utilidad')?'':'hide' ?>">
                                                <th class="titulo black-text" colspan="2">Total Reporte:</th>
                                                <th class="valor center black-text">
                                                    $ <?= number_format(($sellTotal - $cost) - $expenses, '2', ',', '.') ?></th>
                                            </tr>
                                            </tbody>
                                        </table>
                                        <br><br>
                                    </div>
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
        $(".select2").select2({
            // dropdownAutoWidth: true,
            width: '50%',
            theme: 'classic'
        });
        $(document).ready(function () {
            $('.datepicker').datepicker();
        });
    </script>
    <script src="<?= base_url('/js/advance-ui-modals.js') ?>"></script>
    <script src="<?= base_url('/js/shepherd.min.js') ?>"></script>
    <script src="<?= base_url('/js/views/wallet.js') ?>"></script>
<?php $this->endSection() ?>