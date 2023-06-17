<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?> Bancos <?= $this->endSection() ?>
<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('/dropify/css/dropify.min.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- BEGIN: Page Main-->
<style>

    .container-sprint-email, .container-sprint-send {
        background: rgba(0, 0, 0, 0.51);
        z-index: 2000;
        position: absolute;
        width: 100%;
        top: 0px;
        height: 100vh;
        justify-content: center !important;
        align-content: center !important;
        flex-wrap: wrap;
        display: none;
    }
</style>
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
                               Bancos
                            </span>
                        </h5>
                        <ol class="breadcrumbs mb-0">
                            <li class="breadcrumb-item"><a href="<?= base_url() ?>/home">Home</a></li>
                            <li class="breadcrumb-item "><a href="#">Nomina Electrónica</a></li>
                            <li class="breadcrumb-item active"><a href="#">Bancos</a></li>
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
                            <a href="#modal1" class="btn right  indigo mr-1 step-2 active-red modal-trigger">Nuevo</a>
                            <table class="table-responsive">
                                <thead>
                                <tr>
                                    <th class="center">id</th>
                                    <th class="center">Nombre Banco</th>
                                    <th class="center">Bancos</th>
                                    <th class="center">Estado</th>
                                    <th class="center">Acciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($other_banks as $item): ?>
                                    <tr>
                                        <td class="center"><?= $item->id ?></td>
                                        <td class="center"><?= $item->name ?></td>
                                        <td class="center">
                                            <?php foreach ($banks as $bank):?>
                                                    <?= ($bank->id == $item->bank_id) ? $bank->name : '' ?>
                                            <?php endforeach; ?>
                                        </td>
                                        <td class="center"><?= ($item->status == 'Active') ? 'Activo' : 'Inactivo' ?></td>
                                        <td class="center">
                                            <div class="btn-group">
                                                <a href="#<?= $item->id ?>"
                                                   class="btn btn-small pink darken-1 modal-trigger edit_btn"><i
                                                        class="material-icons">edit</i></a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                            <?php if (count($other_banks) == 0): ?>
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
<!-- modal created -->
<div id="modal1" class="modal">
    <form action="<?= base_url('other_banks/create') ?>" method="post">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Nuevo concepto</h3>
            </div>
            <div class="row">
                <div class="input-field col s6 m6 l6">
                    <input id="name" name="name" type="text" required class="validate">
                    <label for="name" class="active">Nombre banco</label>
                </div>
                <div class="input-field col l6 m6 s6">
                    <select id="status" class="select2 browser-default validate" name="status" required>
                        <option value="" disabled="" selected="">Seleccione estado</option>
                        <option value="Active">Activo</option>
                        <option value="Inactive">Inactivo</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="input-field col l12 m12 s12">
                    <h6>
                        Seleccione banco
                    </h6>
                    <select id="concept_type" class="select2 browser-default" name="bank">
                        <option selected disabled value="">Seleccione un banco</option>
                        <?php foreach ($banks as $bank): ?>
                            <option value="<?= $bank->id ?>"><?= $bank->name?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#" class="modal-action modal-close btn-flat ">Cancelar</a>
            <button type="submit" class="btn btn-small btn-light-indigo"><i class="material-icons left">save</i>Guardar
            </button>
        </div>
    </form>
</div>
<!-- modal filtro -->
<form action="" method="get">
    <div id="filter" class="modal" role="dialog" style="height:auto; width: 600px">
        <div class="modal-content">
            <h4>Filtrar</h4>
            <div class="row">
                <div class="col s12 m12 l12   campus input-field">
                    <label for="name" class="active">Buscar</label>
                    <input id="name" type="text" name="name" placeholder="Buscar por nombre de concepto">
                </div>
                <div class="col s12 m6   campus input-field">
                    <label for="status" class="active">Buscar por Estado</label>
                    <select class="browser-default" type="text" name="status" id="status">
                        <option value="" selected disabled>Seleccione estado ...</option>
                        <option value="Active">Activo</option>
                        <option value="Inactive">Inactivo</option>
                    </select>

                </div>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-action modal-close waves-effect  btn-flat btn-light-indigo ">Cerrar</a>
            <button class="modal-action waves-effect waves-green btn indigo">Buscar</button>
        </div>
    </div>
</form>
<!-- modal edit -->
<?php foreach ($other_banks as $items): ?>
    <div id="<?= $items->id ?>" class="modal modal-fixed-footer">
        <form action="<?= base_url('other_banks/edit/' . $items->id) ?>" method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>Editar concepto </h4>
                </div>
                <div class="row">
                    <div class="input-field col s6 m6 l6">
                        <input id="name" name="name" value="<?= $items->name ?>" type="text" required
                               class="validate">
                        <label for="name" class="active">Nombre Concepto</label>
                    </div>
                    <div class="input-field col l6 m6 s6">
                        <select id="status" class="select2 browser-default validate" name="status" required>
                            <?php if ($items->status == 'Active'): ?>
                                <option selected value="Active">Activo</option>
                                <option value="Inactive">Inactivo</option>
                            <?php else: ?>
                                <option value="Active">Activo</option>
                                <option selected value="Inactive">Inactivo</option>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="input-field col l12 m12 s12">
                        <h6>
                            Seleccione banco
                        </h6>
                        <select class="select2 browser-default concept_type_edit" name="bank">
                            <?php foreach ($banks as $bank): ?>
                                <option <?=($items->bank_id == $bank->id)?'selected':'';?> value="<?= $bank->id ?>"><?= $bank->name?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#" class="modal-action modal-close btn-flat ">Cancelar</a>
                <button type="submit" class="btn btn-small btn-light-indigo"><i class="material-icons left">save</i>Guardar
                </button>
            </div>
        </form>
    </div>
<?php endforeach; ?>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script src="<?= base_url('js/advance-ui-modals.js') ?>"></script>
<script src="<?= base_url('/js/shepherd.min.js') ?>"></script>
<script src="<?= base_url('js/views/dates.js') ?>"></script>
<script src="<?= base_url('/dropify/js/dropify.min.js') ?>"></script>
<script src="<?= base_url('/assets/js/sweetalert.min.js') ?>"></script>
<script>
    $(document).ready(function () {
        $(".select2").select2({
            placeholder: 'Seleccione una opcion ...'
        });
    });
</script>
<?= $this->endSection() ?>
