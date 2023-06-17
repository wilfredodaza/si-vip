<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?> Nomina <?= $this->endSection() ?>
<?= $this->section('content') ?>
<div id="main">
    <div class="row">
        <div class="breadcrumbs-inline pt-3 pb-1" id="breadcrumbs-wrapper">
            <div class="container">
                <div class="row">
                    <div class="col s12">
                        <?= view('layouts/alerts') ?>
                    </div>
                    <div class="col s10 m12 l12 breadcrumbs-left">
                        <h5 class="breadcrumbs-title mt-0 mb-0 display-inline hide-on-small-and-down ">
                            <span>
                               Nomina
                            </span>
                        </h5>
                        <ol class="breadcrumbs mb-0">
                            <li class="breadcrumb-item"><a href="<?= base_url() ?>/home">Home</a></li>
                            <li class="breadcrumb-item active"><a href="#">Nomina </a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="col s12">
            <div class="container">
                <div class="section">
                    <div class="card-panel">
                        <div class="row">
                            <div class="col s12 m12">
                                <form action="" method="get">
                                    <div class="row">
                                        <div class="col s12 m4 ">
                                            <label for="customer">Vendedor</label>
                                            <select class="browser-default" id="customer" name="customer" required>
                                                <option value="">Seleccione ...</option>
                                                <?php foreach ($customers as $item) : ?>
                                                    <option value="<?= $item->id ?>" <?= (isset($_GET['customer']) && $_GET['customer'] == $item->id) ? 'selected' : '' ?>>
                                                        <?= $item->name ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col s12 m2 ">
                                            <label for="date">Mes</label>
                                            <select class="browser-default" id="date" name="date" required>
                                                <option value="">Seleccione ...</option>
                                                <?php foreach ($months as $month) : ?>
                                                    <option value="<?= $month->id ?>" <?= (isset($_GET['date']) && $_GET['date'] == $month->id) ? 'selected' : '' ?>>
                                                        <?= $month->name ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col s12 m2 ">
                                            <label for="date">Año</label>
                                            <?php
                                            $cont = date('Y');
                                            ?>
                                            <select class="select2 browser-default validate" id="year" name="year" required>
                                                <option value="" disabled="" selected="">Seleccione una opción</option>
                                                <?php while ($cont >= 2023) { ?>
                                                    <option <?= (isset($_GET['year']) && $_GET['year'] == $cont) ? 'selected' : '' ?> value="<?php echo($cont); ?>"><?php echo($cont); ?></option>
                                                    <?php $cont = ($cont-1); } ?>
                                            </select>
                                        </div>
                                        <div class="col s12 m4">
                                            <button type="submit" class="modals-action btn indigo mt-5 right">Filtrar</button>
                                            <?php if (isset($_GET['date']) || isset($_GET['customer'])): ?>
                                                <a href="<?= base_url('payrolls') ?>"
                                                   class="btn right btn-light-red btn mr-1 mt-5"
                                                   style="padding-left: 10px;padding-right: 10px; margin-right: 10px; ">
                                                    <i class="material-icons left">close</i>
                                                    Quitar Filtro
                                                </a>
                                            <?php endif; ?>

                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                    <?php if(isset($_GET['customer'])): ?>
                        <div class="card">
                            <div class="card-content">
                                <div class="row">
                                    <div class="col s12 m12 l12">
                                        <a href="javascript: history.go(-1)"
                                           class=" btn btn-light-indigo left invoice-print">
                                            <i class="material-icons left">reply</i>
                                            <span>Retroceder</span>
                                        </a>
                                        <a onclick="printDiv('invoice')"
                                           class=" btn btn-light-indigo right invoice-print">
                                            <i class="material-icons right">local_printshop</i>
                                            <span>Imprimir</span>
                                        </a>
                                    </div>
                                </div>
                                <div class="card-content invoice-print-area" id="invoice">
                                    <!-- header section -->
                                    <div class="row">
                                        <div class="col s12 m4">
                                            <h3>Información</h3>
                                            <table class="striped">
                                                <tbody>
                                                <tr>
                                                    <td>Nombre:</td>
                                                    <td><?= $customer->name ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Salario:</td>
                                                    <td class="users-view-latest-activity">$ <?= number_format($customer->salary, '2', ',', '.') ?></td>
                                                </tr>
                                                <tr>
                                                    <td>teléfono :</td>
                                                    <td class="users-view-verified"><?= $customer->phone ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Dirección:</td>
                                                    <td class="users-view-role"><?= $customer->address ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Estado:</td>
                                                    <td><span class=" users-view-status chip green lighten-5 green-text"><?= $customer->status ?></span>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col s12 m8">
                                            <h3>Valores</h3>
                                            <table class="responsive-table">
                                                <thead>
                                                <tr>
                                                    <th class="center">Dia</th>
                                                    <th class="center">Items</th>
                                                    <th class="center">Valor</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td class="center"> <?= date('Y-m-d') ?></td>
                                                    <td class="center">Salario</td>
                                                    <td class="center">$ <?= number_format($customer->salary, '2', ',', '.') ?></td>
                                                </tr>
                                                <?php
                                                $expenses = 0;
                                                $payroll = true;
                                                foreach ($data as $item):
                                                    $expenses += $item->line_extension_amount;
                                                    if($item->product_name == 'Pago Nomina'){
                                                        $payroll = false;
                                                    }
                                                    ?>
                                                    <tr>
                                                        <td class="center"><?= $item->start_date ?></td>
                                                        <td class="center"><?= $item->product_name ?></td>
                                                        <td class="center">$ <?= number_format($item->line_extension_amount, '2', ',', '.') ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                                <tr>
                                                    <th colspan="2" class="black-text right-align">Total Salario</th>
                                                    <th class="black-text center" >$ <?= number_format($customer->salary, '2', ',', '.') ?></th>
                                                </tr>
                                                <tr>
                                                    <th colspan="2" class="black-text right-align">Total Gastos</th>
                                                    <th class="black-text center" >- $ <?= number_format($expenses, '2', ',', '.') ?></th>
                                                </tr>
                                                <tr>
                                                    <th colspan="2" class="black-text right-align">Total </th>
                                                    <th class="black-text center" >$ <?= number_format(($customer->salary - $expenses), '2', ',', '.') ?></th>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col s12 m12 l12">
                                        <form action="<?= base_url('payrolls/payment/'.$_GET['customer'].'/'.($customer->salary - $expenses)) ?>" method="post">
                                            <input type="text" name="year" class="hide" value="<?= $_GET['year'] ?>">
                                            <input type="text" name="date" class="hide" value="<?= $_GET['date'] ?>">
                                            <button type="submit" <?= ($payroll)?'':'disabled' ?> class="btn btn-light-indigo right">Pagar</button>
                                        </form>
                                    </div>
                                </div>


                            </div>
                        </div>
                    <?php else: ?>
                    <div class="card">
                        <div class="card-content">
                            <p>Por favor utilize los filtro que se encuentra en la parte superior para buscar la información de nomina</p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
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
<script>
    $(document).ready(function(){
        $('.datepicker').datepicker();
    });
    function printDiv(nombreDiv) {
        var contenido = document.getElementById(nombreDiv).innerHTML;
        var contenidoOriginal = document.body.innerHTML;

        document.body.innerHTML = contenido;

        window.print();

        document.body.innerHTML = contenidoOriginal;
    }

</script>
<?= $this->endSection() ?>




