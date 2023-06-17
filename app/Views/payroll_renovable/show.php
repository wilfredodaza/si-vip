<?= $this->extend('layouts/main') ?>


<?= $this->section('title') ?> Facturación <?= $this->endSection() ?>

<?= $this->section('content') ?>
<div id="main">
    <div class="row">
        <div class="breadcrumbs-inline pt-3 pb-1" id="breadcrumbs-wrapper">
            <div class="container">
                <div class="row">
                    <div class="col s12">
                        <?= view('layouts/alerts') ?>
                    </div>
                    <div class="col s12 m6 l6 hide-on-med-only hide-on-large-only">
                        <a href="<?= base_url('periods') ?>"  data-target="filter" class="btn btn-light-indigo right  btn-small" style="padding-left:5px; padding-right:10px;">
                            <i class="material-icons left">keyboard_arrow_left</i> Regresar
                        </a>
                    </div>
                    <div class="col s12  breadcrumbs-left">
                        <h5 class="breadcrumbs-title mt-0 mb-0 display-inline hide-on-small-and-down ">
                            <span>
                                Desprendibles de Nómina
                                <a class="btn btn-small light-blue darken-1 step-1 help active-red" style="padding-left: 10px ; padding-right: 10px;">Ayuda</a>
                            </span>
                            <?php if(count($searchShow) != 0): ?>
                                <a href="<?= base_url('payroll_removable/'.$id) ?>" class="btn right btn-light-red btn-small mr-1"   style="padding-left: 10px;padding-right: 10px; ">
                                    <i class="material-icons left">close</i>
                                    Quitar Filtro
                                </a>
                            <?php endif; ?>
                            <button  class="btn right btn-small btn-light-indigo modal-trigger tooltipped step-7 mr-1 ml-1"  data-position="top" data-tooltip="Filtrar Empleados" style="padding-left:5px; padding-right:10px;" data-target="filter">
                                <i class="material-icons left">filter_list </i> Filtrar
                            </button>
                            <a href="<?= base_url('payroll_removable') ?>"  data-target="filter" class="btn btn-light-indigo right  btn-small" style="padding-left:5px; padding-right:10px;">
                                <i class="material-icons left">keyboard_arrow_left</i> Regresar
                            </a>

                        </h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col s12">
            <div class="container">
                <div class="section">
                    <div class="card">
                        <div class="card-content">
                            <table>
                                <thead>
                                <tr>
                                    <th>Nombres y Apellidos</th>
                                    <th class="center">Tipo de documento </th>
                                    <th class="center">Numero de documento</th>
                                    <th class="center">Devengados</th>
                                    <th class="center">Deducidos</th>
                                    <th class="center">Total</th>
                                    <th class="center">Estado</th>
                                    <th class="center">Acciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($detachables  as $item): ?>
                                <tr>
                                    <td><?= $item->first_name.' '.$item->second_name.' '.$item->surname .' '.$item->second_surname ?></td>
                                    <td class="center"><?= $item->type_document_identification_name ?></td>
                                    <td class="center"><?= $item->identification_number ?></td>
                                    <td class="center">$ <?= number_format($item->accrueds, '2', ',', '.') ?></td>
                                    <td class="center">$ <?= number_format($item->deductions, '2', ',', '.')  ?></td>
                                    <td class="center">$ <?= number_format($item->accrueds - $item->deductions, '2', ',', '.')  ?></td>
                                    <td class="center">
                                        <?php if($item->invoice_status_id == 17 && ($item->validate == 'FALSE' || is_null($item->validate))): ?>
                                            <span class="badge new  pink darken-1 " style="width:140px;" data-badge-caption="En espera" ></span>
                                        <?php elseif($item->invoice_status_id == 18 && ($item->validate == 'FALSE' || is_null($item->validate))): ?>
                                            <span class="badge new yellow darken-2 " style="width:140px;" data-badge-caption="Consolidado" ></span>
                                        <?php elseif($item->validate == 'TRUE'): ?>
                                            <span class="badge new green " style="width:140px;" data-badge-caption="Enviado a la DIAN" ></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="center">
                                        <div class="btn-group">
                                            <?php if($item->invoice_status_id == 17 && session('user')->role_id != 10): ?>
                                            <a href="<?= base_url('payroll_removable/'.$item->invoice_id.'/edit'); ?>" class="btn btn-small blue light-blues">
                                                <i class="material-icons">mode_edit</i>
                                            </a>
                                            <?php endif; ?>
                                            <a href="<?= base_url('payroll_removable/previsualization/'.previsualizationPdf($item->invoice_id)); ?>" class="btn btn-small  pink darken-2">
                                                <i class="material-icons">insert_drive_file</i>
                                            </a>
                                        </div>
                                    </td>

                                </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                            <?php if(count($detachables) == 0): ?>
                                <p class="center purple-text pt-1">No hay ningún elemento registrado en la tabla.</p>
                            <?php endif; ?>
                            <?= $pager->links(); ?>
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
            <h5>Filtrar Empleado</h5>
            <div class="row">
                <div class="col s12 m6 input-field">
                    <label for="first_name">Primer Nombre</label>
                    <input type="text" id="first_name" name="first_name" value="<?= isset($_GET['first_name']) ? $_GET['first_name'] : '' ?>">
                </div>
                <div class="col s12 m6 input-field">
                    <label for="second_name">Segundo Nombre</label>
                    <input type="text" id="second_name" name="second_name" value="<?= isset($_GET['second_name']) ? $_GET['second_name'] : '' ?>">
                </div>
            </div>
            <div class="row">
                <div class="col s12 m6 input-field">
                    <label for="surname">Primer Apellido</label>
                    <input type="text" id="second_name" name="surname" value="<?= isset($_GET['surname']) ? $_GET['surname'] : '' ?>">
                </div>
                <div class="col s12 m6 input-field">
                    <label for="second_surname">Segundo Apellido</label>
                    <input type="text" id="second_surname" name="second_surname" value="<?= isset($_GET['second_surname']) ? $_GET['second_surname'] : '' ?>">
                </div>
            </div>
            <div class="row">
                <div class="col s12 m6 input-field">
                    <select name="type_document_id" class="browser-default">
                        <option value=""  >Elige tu opción</option>
                        <?php foreach($typeDocumentIdentifications as $item):  ?>
                            <option value="<?= $item->id ?>"    <?= isset($_GET['type_document_id']) ? ($_GET['type_document_id']  == $item->id ? 'selected': ''): '' ?> ><?= $item->name ?></option>
                        <?php endforeach ?>
                    </select>
                    <label class="active">Tipo de documento</label>
                </div>


                <div class="col s12 m6 input-field">
                    <input type="text" id="identification_number" name="identification_number" value="<?= isset($_GET['identification_number']) ? $_GET['identification_number'] : '' ?>">
                    <label for="identification_number">Numero de documento</label>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-action modal-close waves-effect  btn-flat btn-light-indigo ">Cerrar</a>
            <button class="btn indigo">Buscar</button>

        </div>
    </div>
</form>
<!-------------------------- end modal de busqueda ---------------------------->

<?= $this->endSection() ?>

<?= $this->section('scripts')?>
<script src="<?= base_url('js/advance-ui-modals.js') ?>"></script>
<?= $this->endSection() ?>
