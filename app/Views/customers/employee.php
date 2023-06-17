<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?> Empleado <?= $this->endSection() ?>
<?= $this->section('styles') ?>
<link rel="stylesheet" type="text/css"
      href="<?= base_url('/app-assets/vendors/data-tables/css/jquery.dataTables.min.css') ?>">
<link rel="stylesheet" type="text/css"
      href="<?= base_url('/app-assets/vendors/data-tables/extensions/responsive/css/responsive.dataTables.min.css') ?>">
<link rel="stylesheet" type="text/css"
      href="<?= base_url('/app-assets/vendors/data-tables/css/select.dataTables.min.css') ?>">
<link rel="stylesheet" type="text/css"
      href="<?= base_url('/app-assets/vendors/data-tables/css/dataTables.uikit.min.css') ?>">
<link rel="stylesheet" type="text/css"
      href="<?= base_url('/app-assets/vendors/data-tables/css/uikit.min.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= base_url('/app-assets/css/pages/data-tables.css') ?>">
<style>

</style>
<?= $this->endSection() ?>
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
                                Datos del empleado
                            </span>
                        </h5>
                        <ol class="breadcrumbs mb-0">
                            <li class="breadcrumb-item"><a href="<?= base_url('home') ?>">Home</a></li>
                            <li class="breadcrumb-item active"><a
                                        href="<?= base_url('table/employees') ?>">Empleados</a></li>
                            <li class="breadcrumb-item active"><a href="#">Datos empleados</a></li>
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
                                <div class="row">
                                    <div class="col s12 m6 l6">
                                        Información personal
                                    </div>
                                    <div class="col s12 m6 l6">
                                        <a href="<?= base_url('table/employees') ?>"
                                           class=" btn btn-light-indigo right invoice-print">
                                            <i class="material-icons left">reply</i>
                                            <span>Retroceder</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="divider"></div>
                            <br>
                            <div class="row">
                                <div class="col s12 m12 l12">
                                    <form action="<?= base_url() . route_to('customer.updateData', $employee->id) ?>"
                                          method="post">
                                        <div class="row">
                                            <div class="col s12 m12 l12">
                                                <div class="row">
                                                    <div class="col m3 s12 input-field">
                                                        <?php
                                                        $surmane = (!is_null($employee->surname)) ? $employee->surname : '';
                                                        ?>
                                                        <input type="text" class="black-text" disabled name="name"
                                                               id="name"
                                                               value="<?= $employee->name . ' ' . $surmane ?>">
                                                        <label class="active" for="name">Nombre</label>
                                                    </div>
                                                    <div class="col m3 s12 input-field">
                                                        <input type="text" class="black-text" disabled
                                                               name="type_identification"
                                                               id="type_identification"
                                                               value="<?= $employee->type_identification ?>">
                                                        <label class="active" for="type_identification">Tipo
                                                            de identificación</label>
                                                    </div>
                                                    <div class="col m3 s12 input-field">
                                                        <input type="text" class="black-text" disabled
                                                               name="identification"
                                                               id="identification"
                                                               value="<?= $employee->identification ?>">
                                                        <label class="active"
                                                               for="identification">Identificación</label>
                                                    </div>
                                                    <div class="col m3 s12 input-field">
                                                        <input type="text" class="black-text"  name="phone"
                                                               id="phone"
                                                               value="<?= $employee->phone ?>">
                                                        <label class="active"
                                                               for="phone">Teléfono</label>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col m3 s12 input-field">
                                                        <input type="text" class="black-text" name="address"
                                                               id="address"
                                                               value="<?= $employee->address ?>">
                                                        <label class="active"
                                                               for="address">Dirección</label>

                                                    </div>
                                                    <div class="col m3 s12 input-field">
                                                        <input type="text" class="black-text" name="neighborhood"
                                                               id="neighborhood"
                                                               value="<?= $employee->neighborhood ?>">
                                                        <label class="active" for="neighborhood">Barrio</label>
                                                    </div>
                                                    <div class="col m3 s12 input-field">
                                                        <input type="email" class="black-text" name="email" id="email"
                                                               value="<?= $employee->email ?>">
                                                        <label class="active" for="email">Correo</label>
                                                    </div>
                                                    <div class="col m3 s12 input-field">
                                                        <input type="number" class="black-text" name="number_people"
                                                               id="number_people"
                                                               value="<?= $employee->number_people ?>">
                                                        <label class="active" for="number_people">Número de personas con
                                                            quien vive</label>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col m3 s12 input-field">
                                                        <input type="date" class="black-text" name="birthday"
                                                               id="birthday"
                                                               value="<?= $employee->birthday ?>">
                                                        <label class="active" for="birthday">Fecha de Nacimiento</label>
                                                    </div>
                                                    <div class="col m3 s12 input-field">
                                                        <input type="date" class="black-text" name="admision_date"
                                                               id="admision_date"
                                                               value="<?= $employee->admision_date ?>">
                                                        <label class="active" for="admision_date">Fecha de
                                                            Ingreso</label>
                                                    </div>
                                                    <div class="col m3 s12 input-field">
                                                        <input type="date" class="black-text" name="retirement_date"
                                                               id="retirement_date"
                                                               value="<?= $employee->retirement_date ?>">
                                                        <label class="active" for="retirement_date">Fecha de
                                                            Retiro</label>
                                                    </div>
                                                    <div class="col m3 s12 input-field">
                                                        <input type="text" class="black-text" name="salary" id="salary"
                                                               value="<?= $employee->salary ?>">
                                                        <label class="active" for="salary">Salario</label>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col m6 s12 input-field">
                                                        <input type="text" class="black-text" name="work" id="work"
                                                               value="<?= $employee->work ?>">
                                                        <label class="active" for="work">Cargo</label>
                                                    </div>
                                                    <div class="col s12 m6 l6 input-field">
                                                        <label for="withdrawal_reason" class="active">Motivo del Retiro
                                                            <span class="red-text"> </span> </label>
                                                        <div class="mt-1">
                                                            <textarea style="height: 89px !important;" id="withdrawal_reason"
                                                                      rows="20" name="withdrawal_reason" ><?= ($employee->withdrawal_reason) ?></textarea>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="step-actions">
                                            <!-- Here goes your actions buttons -->
                                            <!--<button type="button" class="btn next-step right btn-light-red">Siguiente</button>-->
                                            <button type="submit" class="btn btn-light-indigo left">
                                                Actualizar
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--modal de filtro de busqeuda-->
<form action="?c=c" method="get">
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
                <input type="text" value="c" name="option" class="hide">
            </div>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-action modal-close waves-effect waves-red btn-flat mb-5 ">Cerrar</a>
            <button class="modals-action waves-effect waves-green btn indigo mb-5">Filtrar</button>
        </div>
    </div>
</form>
<!--modal de filtro de busqeuda-->
<form action="" method="get">
    <div id="filterProduct" class="modal" role="dialog" style="height:auto; width: 600px">
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
                <input type="text" value="p" name="option" class="hide">
            </div>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-action modal-close waves-effect waves-red btn-flat mb-5 ">Cerrar</a>
            <button class="modals-action waves-effect waves-green btn indigo mb-5">Filtrar</button>
        </div>
    </div>
</form>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script src="<?= base_url('/app-assets/vendors/data-tables/js/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('/app-assets/vendors/data-tables/extensions/responsive/js/dataTables.responsive.min.js') ?>"></script>
<script src="<?= base_url('/app-assets/vendors/data-tables/js/dataTables.select.min.js') ?>"></script>
<script src="<?= base_url('/js/shepherd.min.js') ?>"></script>
<script src="<?= base_url('/js/ui-alerts.js') ?>"></script>
<script src="<?= base_url('/js/advance-ui-modals.js') ?>"></script>
<script src="<?= base_url('/js/sprint.js') ?>"></script>
<script>
    $(".select2").select2({
        dropdownAutoWidth: true,
        width: '100%'
    });
</script>
<script>
    var stepper = document.querySelector('.stepper');
    var stepperInstace = new MStepper(stepper, {
        // options
        // firstActive: // this is the default
    })
</script>

<?= $this->endSection() ?>

