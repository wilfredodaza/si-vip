<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?> Gastos <?= $this->endSection() ?>
<?= $this->section('content') ?>

<div id="main">
    <div class="row">
        <div class="breadcrumbs-inline pt-3 pb-1" id="breadcrumbs-wrapper">
            <div class="container">
                <div class="row">
                    <div class="col s12">
                        <?= view('layouts/alerts') ?>
                    </div>
                    <div class="col s12 m12 l12 breadcrumbs-left">
                        <h5 class="breadcrumbs-title mt-0 mb-0 display-inline hide-on-small-and-down">
                            <span>
                                Gastos
                            </span>
                        </h5>
                        <ol class="breadcrumbs mb-0">
                            <li class="breadcrumb-item"><a href="<?= base_url() ?>/home">Home</a></li>
                            <li class="breadcrumb-item active"><a href="#">Gastos</a></li>
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
                            <div class="card-title">
                                <?php
                                $total = 0;
                                foreach ($invoices as $invoice){
                                    $total += $invoice->line_extension_amount;
                                }
                                ?>
                                Gastos Total : $ <?= number_format($total, '2', ',', '.') ?>
                                <a href="<?= base_url() . route_to('expenses.create') ?>"
                                   class="btn btn-small btn-sm indigo right step-2">
                                    Crear gasto
                                    <i class="material-icons right">add</i>
                                </a>
                                <button data-target="filter" class="btn btn-small btn-light-indigo modal-trigger  right"
                                        style="margin-right: 5px;">
                                    Filtrar <i class="tiny material-icons right">filter_list</i>
                                </button>
                                <?php if (isset($_GET['start_date']) || isset($_GET['end_date']) || isset($_GET['customer']) || isset($_GET['product'])): ?>
                                    <a href="<?= base_url('expenses') ?>"
                                       class="btn right btn-light-red btn-small ml-1"
                                       style="padding-left: 10px;padding-right: 10px; margin-right: 10px; ">
                                        <i class="material-icons left">close</i>
                                        Quitar Filtro
                                    </a>
                                <?php endif; ?>
                            </div>
                            <table class="table table-responsive">
                                <thead>
                                <tr>
                                    <th class="center">#</th>
                                    <th class="center">Fecha</th>
                                    <th class="center">Sede</th>
                                    <th class="center">Producto</th>
                                    <th class="center">Total</th>
                                    <th class="center step-3">Acciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                foreach ($invoices as $item) : ?>
                                    <tr>
                                        <td class="center"><?= $item->id ?></td>
                                        <td class="center"><?= $item->start_date ?></td>
                                        <td class="center"><?= $item->customer ?></td>
                                        <td class="center"><?= $item->product_name ?></td>
                                        <td class="center">$ <?= number_format($item->line_extension_amount, '2', ',', '.') ?></td>
                                        <td class="center">
                                            <div class="btn-group" role="group">
                                                <!--<a href=""
                                                   class="btn btn-small  yellow darken-1  tooltipped" data-position="top" data-tooltip="Resumen producto">
                                                    <i class="material-icons">remove_red_eye</i>
                                                </a>-->
                                                <a href="<?= base_url().route_to('expenses.edit', $item->id) ?>"
                                                   class="btn btn-small pink send tooltipped" data-position="top"
                                                   data-tooltip="Editar" >
                                                    <i class="material-icons">edit</i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php
                                endforeach; ?>
                                </tbody>
                            </table>
                            <?php if (count($invoices) == 0) : ?>
                                <p class="center red-text pt-1">No hay ningún elemento.</p>
                            <?php endif ?>
                            <?= $pager->links(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--modal de filtro de busqeuda-->
<form action="" method="get">
    <div id="filter" class="modal" role="dialog" style="height:auto; width: 600px">
        <div class="modal-content">
            <h4 class="modal-title">Filtrar</h4>
            <div class="row">
                <div class="col s12 m6 input-field">
                    <input type="date" id="start_date" name="start_date"
                           value="<?= isset($_GET['start_date']) ? $_GET['start_date'] : '' ?>">
                    <label for="start_date">Fecha de inicio</label>
                </div>
                <div class="col s12 m6 input-field">
                    <input id="end_date" type="date" name="end_date"
                           value="<?= isset($_GET['end_date']) ? $_GET['end_date'] : '' ?>">
                    <label for="end_date">Fecha fin</label>
                </div>
            </div>
            <div class="row">
                <div class="col s12 m6">
                    <label for="customer">Cliente</label>
                    <select class="browser-default" id="customer" name="customer">
                        <option value="">Seleccione ...</option>
                        <?php foreach ($customers as $customer) : ?>
                            <option value="<?= $customer->id ?>" <?= (isset($_GET['customer']) && $_GET['customer']  == $customer->id) ? 'selected' : '' ?>>
                                <?= $customer->name ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col s12 m6">
                    <label for="product">Producto</label>
                    <select class="select2 browser-default" multiple="multiple" id="product" name="product[]">
                        <option value="">Todos</option>
                        <?php foreach ($products as $product) : ?>
                            <option value="<?= $product->id ?>" <?= (isset($_GET['product']) && $_GET['product']  == $product->id) ? 'selected' : '' ?>>
                                <?= $product->name ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-action modal-close waves-effect waves-red btn-flat mb-5 ">Cerrar</a>
            <button class="modals-action waves-effect waves-green btn indigo mb-5">Guardar</button>
        </div>
    </div>
</form>
<!--end modal de filtro de busqeuda-->

<!--sprint loader-->
<div class="container-sprint-send">
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
    <span class="text-insert"></span>
</div>
<!--end sprint loader -->
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
    $(".select2").select2({
        dropdownAutoWidth: true,
        width: '100%'
    });
</script>
<script src="<?= base_url('/js/shepherd.min.js') ?>"></script>
<script src="<?= base_url('/js/ui-alerts.js') ?>"></script>
<script src="<?= base_url('/js/advance-ui-modals.js') ?>"></script>
<script src="<?= base_url('/js/sprint.js') ?>"></script>
<?= $this->endSection() ?>
