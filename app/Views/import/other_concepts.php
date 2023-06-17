<?= view('layouts/header') ?>
<?= view('layouts/navbar_vertical') ?>
<?= view('layouts/navbar_horizontal') ?>
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
                               Conceptos
                            </span>
                        </h5>
                        <ol class="breadcrumbs mb-0">
                            <li class="breadcrumb-item"><a href="<?= base_url() ?>/home">Home</a></li>
                            <li class="breadcrumb-item "><a href="#">Nomina Electrónica</a></li>
                            <li class="breadcrumb-item active"><a href="#">conceptos</a></li>
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
                                    class="right btn  btn-light-indigo modals-trigger step-5 active-red">
                                Filtrar <i class="material-icons right">filter_list</i>
                            </button>
                            <a href="#modal1" class="btn right  indigo mr-1 step-2 active-red modals-trigger">Nuevo</a>
                            <table class="table-responsive">
                                <thead>
                                <tr>
                                    <th class="center">id</th>
                                    <th class="center">Nombre Concepto</th>
                                    <th class="center">tipo de concepto</th>
                                    <th class="center">Concepto DIAN</th>
                                    <th class="center">Estado</th>
                                    <th class="center">Acciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($other_concepts as $item): ?>
                                    <tr>
                                        <td class="center"><?= $item->id ?></td>
                                        <td class="center"><?= $item->name ?></td>
                                        <td class="center"><?= $item->type_concept ?></td>
                                        <td class="center">
                                            <?php if ($item->type_concept == 'Devengado' && $item->status == 'Active'):
                                                foreach ($accrueds as $devengado):
                                                    ?>
                                                    <?= ($devengado->id == $item->concept_dian) ? $devengado->name : '' ?>
                                                <?php endforeach;
                                            elseif ($item->type_concept == 'Deduccion' && $item->status == 'Active'):
                                                foreach ($deductions as $deducido):?>
                                                    <?= ($deducido->id == $item->concept_dian) ? $deducido->name : '' ?>
                                                <?php endforeach;
                                            elseif ($item->status == 'Inactive'): ?>
                                                No aplica
                                            <?php endif; ?></td>
                                        <td class="center"><?= ($item->status == 'Active') ? 'Activo' : 'Inactivo' ?></td>
                                        <td class="center">
                                            <div class="btn-group">
                                                <a href="#<?= $item->id ?>"
                                                   class="btn btn-small pink darken-1 modals-trigger edit_btn"><i
                                                            class="material-icons">edit</i></a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                            <?php if (count($other_concepts) == 0): ?>
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
<!-- modal -->
<div id="modal1" class="modals modals-fixed-footer">
    <form action="<?= base_url('other_concepts/create') ?>" method="post">
        <div class="modals-content">
            <div class="modals-header">
                <h3>Nuevo concepto</h3>
            </div>
            <div class="row">
                <div class="input-field col s6 m6 l6">
                    <input id="concept_name" name="concept_name" type="text" required class="validate">
                    <label for="concept_name" class="active">Nombre Concepto</label>
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
                <div class="input-field col l6 m6 s6">
                    <h6>
                        Tipo de concepto
                    </h6>
                    <select id="concept_type" class="select2 browser-default" name="concept_type">
                        <option value="" disabled="" selected="">Seleccione una opción</option>
                        <option value="Devengado">Devengado</option>
                        <option value="Deduccion">Deducción</option>
                    </select>
                </div>
                <div id="dev" class="input-field col l6 m6 s6">
                    <h6>
                        Devengados DIAN
                    </h6>
                    <select id="accrueds" class="select2 browser-default" name="accrueds">
                        <option value="" disabled="" selected="">Seleccione una opción</option>
                        <?php foreach ($accrueds as $accrued): ?>
                            <option value="<?= $accrued->id ?>"><?= $accrued->name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div id="ded" class="input-field col l6 m6 s6">
                    <h6>
                        Deducciones DIAN
                    </h6>
                    <select id="deductions" class="select2 browser-default" name="deductions">
                        <option value="" disabled="" selected="">Seleccione una opción</option>
                        <?php foreach ($deductions as $deduction): ?>
                            <option value="<?= $deduction->id ?>"><?= $deduction->name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div id="type_incapa" class="input-field col l6 m6 s6">
                    <h6>
                        Tipo de incapacidad
                    </h6>
                    <select id="type_incapacidad" class="select2 browser-default" name="type_incapacidad">
                        <option value="" disabled="" selected="">Seleccione una opción</option>
                        <option value="Comun">Común</option>
                        <option value="Profesional">Profesional</option>
                        <option value="Laboral">Laboral</option>

                    </select>
                </div>
            </div>
        </div>
        <div class="modals-footer">
            <a href="#" class="modals-action modals-close btn-flat ">Cancelar</a>
            <button type="submit" class="btn btn-small btn-light-indigo"><i class="material-icons left">save</i>Guardar
            </button>
        </div>
    </form>
</div>

<?php foreach ($other_concepts as $items): ?>
    <div id="<?= $items->id ?>" class="modals modals-fixed-footer">
        <form action="<?= base_url('other_concepts/edit/' . $items->id) ?>" method="post">
            <div class="modals-content">
                <div class="modals-header">
                    <h4>Editar concepto </h4>
                </div>
                <div class="row">
                    <div class="input-field col s6 m6 l6">
                        <input id="concept_name" name="concept_name" value="<?= $items->name ?>" type="text" required
                               class="validate">
                        <label for="concept_name" class="active">Nombre Concepto</label>
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
                    <div class="input-field col l6 m6 s6">
                        <h6>
                            Tipo de concepto
                        </h6>
                        <select class="select2 browser-default concept_type_edit" name="concept_type">
                            <?php if ($items->type_concept == 'Devengado'): ?>
                                <option selected value="Devengado">Devengado</option>
                                <option value="Deduccion">Deducción</option>
                            <?php else: ?>
                                <option value="Devengado">Devengado</option>
                                <option selected value="Deduccion">Deducción</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="dev_edit input-field col l6 m6 s6">
                        <h6>
                            Devengados DIAN
                        </h6>
                        <select id="" class="accrueds_edit select2 browser-default" name="accrueds">
                            <?php foreach ($accrueds as $accrued):
                                ?>
                                <option <?= ($accrued->id == $items->concept_dian) ? 'Selected' : ''; ?>
                                        value="<?= $accrued->id ?>"><?= $accrued->name ?></option>
                            <?php
                            endforeach; ?>
                        </select>
                    </div>
                    <div class="deduc_edit input-field col l6 m6 s6">
                        <h6>
                            Deducciones DIAN
                        </h6>
                        <select class="deductions_edit select2 browser-default" name="deductions">
                            <?php foreach ($deductions as $deduction):
                                ?>
                                <option <?= ($deduction->id == $items->concept_dian) ? 'Selected' : ''; ?>
                                        value="<?= $deduction->id ?>"><?= $deduction->name ?></option>
                            <?php
                            endforeach; ?>
                        </select>
                    </div>
                    <div class="type_incapa_edit input-field col l6 m6 s6 "
                         style="display: <?= ($items->type_concept == 'Devengado' && $items->concept_dian == 17) ? 'block' : 'none'; ?>;">
                        <h6>
                            Tipo de incapacidad
                        </h6>
                        <select class="type_incapacidad select2 browser-default" name="type_incapacidad">
                            <?php if ($items->type_other == 'Comun'): ?>
                                <option selected value="Comun">Común</option>
                                <option value="Profesional">Profesional</option>
                                <option value="Laboral">Laboral</option>
                            <?php elseif ($items->type_other == 'Profesional'): ?>
                                <option value="Comun">Común</option>
                                <option selected value="Profesional">Profesional</option>
                                <option value="Laboral">Laboral</option>
                            <?php elseif ($items->type_other == 'Laboral'): ?>
                                <option value="Comun">Común</option>
                                <option value="Profesional">Profesional</option>
                                <option selected value="Laboral">Laboral</option>
                            <?php else: ?>
                                <option value="" disabled="" selected="">Seleccione una opción</option>
                                <option value="Comun">Común</option>
                                <option value="Profesional">Profesional</option>
                                <option value="Laboral">Laboral</option>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modals-footer">
                <a href="#" class="modals-action modals-close btn-flat ">Cancelar</a>
                <button type="submit" class="btn btn-small btn-light-indigo"><i class="material-icons left">save</i>Guardar
                </button>
            </div>
        </form>
    </div>
<?php endforeach; ?>
<form action="" method="get">
    <div id="filter" class="modals" role="dialog" style="height:auto; width: 600px">
        <div class="modals-content">
            <h4>Filtrar</h4>
            <div class="row">
                <div class="col s12 m12 l12   campus input-field">
                    <label for="name" class="active">Buscar</label>
                    <input id="name" type="text" name="name" placeholder="Buscar por nombre de concepto">
                </div>
                <div class="col s12 m6  campus input-field">
                    <label for="Estado" class="active">Buscar por tipo de concepto</label>
                    <select class="browser-default" type="text" name="type_concept" id="Estado">
                        <option value="" selected disabled>Seleccione tipo de concepto ...</option>
                        <option value="Devengado">Devengado</option>
                        <option value="Deduccion">Deducción</option>
                    </select>
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
        <div class="modals-footer">
            <a href="#!" class="modals-action modals-close waves-effect  btn-flat btn-light-indigo ">Cerrar</a>
            <button class="modals-action waves-effect waves-green btn indigo">Buscar</button>
        </div>
    </div>
</form>


<?= view('layouts/footer') ?>
<script>
    $(document).ready(function () {
        $('#dev').hide();
        $('#ded').hide();
        $('#type_incapa').hide();
        $('.dev_edit').hide();
        $('.deduc_edit_edit').hide();

        $("#concept_type").change(function () {
            conceptType = $(this).val();
            if (conceptType == 'Devengado') {
                $('#dev').show();
                $('#ded').hide();
            } else if (conceptType == 'Deduccion') {
                $('#dev').hide();
                $('#ded').show();
            }
        });
        $("#accrueds").change(function () {
            valor = $(this).val();
            if (valor == 17) {
                $('#type_incapa').show();
            } else {
                $('#type_incapa').hide();
                $('#type_incapa_edit').hide();
            }
        });
        $(".edit_btn").click(function () {
            console.log($('.concept_type_edit').val());
            if ($('.concept_type_edit').val() == 'Devengado') {
                $('.dev_edit').show();
                $('.deduc_edit').hide();
            } else if ($('.concept_type_edit').val() == 'Deduccion') {
                $('.deduc_edit').show();
                $('.dev_edit').hide();
            }
        });

        $(".concept_type_edit").change(function () {
            conceptType = $(this).val();
            if (conceptType == 'Devengado') {
                $('.dev_edit').show();
                $('.deduc_edit').hide();
            } else if (conceptType == 'Deduccion') {
                $('.dev_edit').hide();
                $('.deduc_edit').show();
            }
        });
        $(".accrueds_edit").change(function () {
            valor = $(this).val();
            if (valor == 17) {
                $('.type_incapa_edit').show();
            } else {
                $('.type_incapa_edit').hide();
            }
        });
    });
</script>
