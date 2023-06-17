<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?> Presupuestos<?= $this->endSection() ?>

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
                                Presupuestos
                            </span>
                        </h5>
                        <ol class="breadcrumbs mb-0">
                            <li class="breadcrumb-item"><a href="<?= base_url() ?>/home">Home</a></li>
                            <li class="breadcrumb-item active"><a href="#">presupuestos</a></li>
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
                            <div class="col s12 m3 right">
                                <button data-target="register" class="btn btn-light-indigo right  modal-trigger step-5 active-red">
                                    Registrar <i class="material-icons right">add</i>
                                </button>
                            </div>
                                <table class="table-responsive">
                                    <thead>
                                        <tr>
                                            <th class="center">Mes</th>
                                            <th class="center">Año</th>
                                            <th class="center">Presupuestado</th>
                                            <th class="center">Causado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($budgets as $item): ?>
                                        <tr>
                                            <td class="center"><?= $month[$item->id] ?>
                                            <td class="center"><?= $item->year ?></td>
                                            <td class="center"> $ <?= number_format($item->value, '0', '.', '.') ?></td>
                                            <td class="center">
                                                $ <?= number_format($causados[$item->id], '0', '.', '.') ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <?php if (count($budgets) == 0): ?>
                                    <p class="red-text center py-2" >No hay ningun elemento registrado.</p>
                                <?php endif; ?>
                            <?= $pager->links() ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<form action="<?=  base_url().route_to('purchaseOrder-createBudget') ?>" method="post">
    <div id="register" class="modal" role="dialog" style="height:auto; width: 600px">
        <div class="modal-content">
            <h4 class="modal-title">Nuevo presupuesto</h4>
            <div class="row">
                <div class="input-field col l6 m6 s12 browser-default">
                    <?php
                    $cont = date('Y');
                    ?>
                    <select class="select2 browser-default validate" id="year" name="year" required>
                        <option value="" disabled="" selected="">Seleccione un año</option>
                        <?php while ($cont >= 2023) { ?>
                            <option value="<?php echo($cont); ?>"><?php echo($cont); ?></option>
                            <?php $cont = ($cont-1); } ?>
                    </select>
                    <label for="year" class="active">Año</label>
                </div>
                <div class="col s12 m6 input-field">
                    <select name="month" id="month" class="browser-default" required>
                        <option value="" disabled="" selected="">Seleccione un mes</option>
                        <?php foreach ($months as $month): ?>
                            <option value="<?= $month->id ?>"><?= $month->name ?></option>
                        <?php endforeach;?>
                    </select>
                    <label for="month" class="active">Filtro</label>
                </div>
                <div class="col s12 m12 input-field">
                    <input id="value" type="text" name="value" required>
                    <label for="value" class="active">Valor</label>
                </div>
            </div>
        </div>
        <div class="modal-footer" >
            <a href="#!" class="modal-action modal-close waves-effect waves-red btn-flat mb-5 ">Cerrar</a>
            <button class="modal-action waves-effect waves-green btn indigo mb-5">Guardar</button>
        </div>
    </div>
</form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
    <script src="<?= base_url('/js/shepherd.min.js') ?>"></script>
    <script src="<?= base_url('js/advance-ui-modals.js') ?>"></script>
    <script src="<?= base_url().'/js/views/quotation.js' ?>"></script>

<?= $this->endSection() ?>
