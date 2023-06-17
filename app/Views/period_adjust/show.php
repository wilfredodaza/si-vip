<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?> Nóminas <?= $this->endSection() ?>
<?= $this->section('styles') ?>
    <link rel="stylesheet" href="<?= base_url('css/views/payrolls.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div id="main" style="overflow-y: auto;">
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
                                Emitir Nómina de Ajuste Electrónica
                                <a class="btn btn-small light-blue darken-1 step-1 help active-red" style="padding-left: 10px ; padding-right: 10px;">Ayuda</a>
                            </span>
                            <a href="<?= base_url('period_adjusts') ?>"  data-target="filter" class="btn btn-light-indigo right  btn-small" style="padding-left:5px; padding-right:10px;">
                                <i class="material-icons left">keyboard_arrow_left</i> Regresar
                            </a>
                        </h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="col s12 m6">
            <div class="container">
                <div class="section">
                    <div class="card">
                        <div class="card-content">
                            <div class="row">
                                <div class="col s8" style=" display: flex; align-items: center; height:90px;">
                                    <p><span style="font-size: 20px;">Progreso de emisión</span>   <br>
                                    Se han emitido <?= $periodData['emiter'] ?> de <?= $periodData['workers']?> empleados</p>
                                </div>
                                <div class="col s4 ">
                                    <div style="display:flex; justify-content:flex-end;">
                                        <div class="ldBar"  data-stroke="#A53394" id="myItem1" data-preset="circle" data-value="<?=  round( $periodData['emiter'] * 100 / ($periodData['workers'] == 0 ? 1 : $periodData['workers'])) ?>"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col s12 m6">
            <div class="container">
                <div class="section">
                    <div class="card">
                        <div class="card-content">
                            <div class="row">
                                <div class="col s6">
                                    <p>Mes</p> 
                                </div>
                                <div class="col s6 right">
                                    <p class="right"><?= $period[0]->month ?> - <?= $period[0]->year ?></p> 
                                </div>
                                <div class="col s12">
                                    <div class="divider" style="margin:10px 0px;"></div>
                                </div>
                                <div class="col s6">
                                    <p>Estado</p> 
                                </div>
                                <div class="col s6 right">
                                    <?php if($periodData['emiter'] <  $periodData['workers']): ?>
                                        <p class="right" style="font-weight:900;">
                                            En Proceso
                                        </p>
                                    <?php else: ?>  
                                        <p class="right green-text" style="font-weight:900;">
                                            Terminado
                                        </p>
                                    <?php endif; ?> 
                                </div>        
                            </div>
                        </div>
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
                                <div class="col s12 m6 l3 center" style="border-right: solid 1px #DCDDE1;">
                                    <p>Emisiones aceptadas</p>
                                    <p  style="font-size:20px;font-weight:bold;"><?=  $periodData['emiter'] ?></p>
                                </div>
                                <div class="col s12 m6 l3 center" style="border-right: solid 1px #DCDDE1;">
                                    <p>Emisiones rechazadas</p>
                                    <p class="orange-text darken-3" style="font-size:20px;font-weight:bold;">
                                        <strong><?= $periodData['errors'] ?></strong>
                                    </p>  
                                </div>
                                <div class="col s12 m6 l2 center" style="border-right: solid 1px #DCDDE1;">
                                    <p>Ingresos</p>   
                                    <p class="light-blue-text darken-3" style="font-size:20px;font-weight:bold;">
                                            $<?= number_format($periodData['accrueds'], '2', ',', '.') ?>
                                    </p>  
                                </div>
                                <div class="col s12 m6 l2 center" style="border-right: solid 1px #DCDDE1;">
                                    <p>Deducciones</p>
                                    <p  style="font-size:20px;font-weight:bold;" class="green-text">
                                        $<?= number_format($periodData['deductions'], '2', ',', '.') ?>
                                    </p>  
                                </div>
                                <div class="col s12 m6 l2 center">
                                    <p>Total Pago</p>
                                    <p  style="font-size:20px;font-weight:bold;">
                                        $<?= number_format(($periodData['accrueds'] - $periodData['deductions']), '2', ',', '.') ?>
                                    </p>  
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="col s12">
            <div class="container">
                <div class="section">
                    <div class="card">
                        <div class="card-content" style="margin-bottom: 100px;">
                            <?php if(count($searchShow) != 0): ?>
                                <a href="<?= base_url('period_adjusts/'.$id) ?>" class="btn right btn-light-red btn-small ml-1"   style="padding-left: 10px;padding-right: 10px; ">
                                    <i class="material-icons left">close</i>
                                    Quitar Filtro
                                </a>
                            <?php endif; ?>
                        <!--<button  class="btn right btn-small btn-light-indigo modal-trigger ml-1 tooltipped step-2"  data-position="top" data-tooltip="Añadir Empleados"   style="padding-left:5px; padding-right:10px;" data-target="worker">
                                <i class="material-icons left">people</i> Añadir
                            </button>-->
                            <button  class="btn right btn-small btn-light-indigo modal-trigger tooltipped step-7"  data-position="top" data-tooltip="Filtrar Empleados" style="padding-left:5px; padding-right:10px;" data-target="filter">
                                <i class="material-icons left">filter_list </i> Filtrar
                            </button>
                            <button type="button" class="btn indigo send_multiple modal-trigger resolution_multiple tooltipped step-6"  data-position="top" data-tooltip="Enviar Masivamente las Nominas"
                                data-send="<?= $id ?>" data-target="resolution_multiple" 
                                style="padding-left:10px; padding-right:10px;" disabled>Enviar Masivamente</button>
                            <div style="overflow-x: auto;">
                                <table class="mb-1 ">
                                    <thead>
                                        <tr>
                                            <th class="center indigo-text step-5">
                                                <p><label><input type="checkbox" class="checkbox-todo"/><span></span></label></p>
                                            </th>
                                            <th class="center indigo-text">Nombre</th>
                                            <th class="center indigo-text">Numero de documento</th>
                                            <th class="center indigo-text">Valor pago</th>
                                            <th class="center step-4  indigo-text">Estado de emision</th>
                                            <th class="center step-3 indigo-text">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php  $i = 1; foreach($workers as $item):
                                        $item = (Object) $item?>
                                        <tr>
                                            <td class="center">
                                                <?php switch ($item->invoice_status_id){
                                                    case '12':
                                                        echo '-';
                                                        break;
                                                    case '13':
                                                        echo '<p><label><input type="checkbox" class="checkbox-active" name="payrolls[]" value="'.$item->invoice_id.'"/><span></span></label></p>';
                                                        break;
                                                    case '14':
                                                        echo  $item->prefix .'-' . $item->resolution;
                                                        break; 
                                                    case '15':
                                                        echo '-';
                                                        break;  
                                                    case '16':
                                                        echo '-';
                                                        break; 
                                                } ?>
                                            </td>
                                            <td class="center"><?= strtoupper($item->name.' '. $item->second_name . ' '. $item->surname. ' ' .$item->second_surname) ?></td>
                                            <td class="center"><?= $item->identification_number ?></td>
                                            <td class="center">$ <?=  number_format(($item->accrued - $item->deduction), '2', ',', '.') ?></td>
                                            <td class="center">
                                                <?php 
                                                switch ($item->invoice_status_id) {
                                                case '12':
                                                    echo '<span class="badge new pink darken-1 " style="width:140px;" data-badge-caption="' . $item->invoice_status_name . '" ></span>';
                                                    break;
                                                case '13':
                                                    echo '<span  class="badge new yellow darken-2"  style="width:140px;" data-badge-caption="' .  $item->invoice_status_name . '"></span>';
                                                    break;
                                                case '14':
                                                    echo '<span  class="badge new light-blue" style="width:140px;"  data-badge-caption="' .  $item->invoice_status_name . '"></span>';
                                                    break;
                                                case '15':
                                                    echo '<span  class="badge new red" style="width:140px;"  data-badge-caption="' .  $item->invoice_status_name . '"></span>';
                                                    break;
                                                case '16':
                                                    echo '<span  class="badge new orange" style="width:140px;"  data-badge-caption="' .  $item->invoice_status_name . '"></span>';
                                                    break;
                                                }?>
                                            </td>
                                            <td class="center">
                                                <div class="btn-group">
                                                    <?php  if($item->invoice_status_id != '12' && $item->invoice_status_id != '13'  && $item->invoice_status_id != '15' && $item->invoice_status_id != '16'): ?>
                                                        <a href="<?= base_url('payroll/download/'.$item->invoice_id) ?>" class="btn btn-small pink darken-1 tooltipped" data-tooltip="Descargar PDF de la Nomina">
                                                            <i class="material-icons">insert_drive_file</i>
                                                        </a>
                                                    <?php  endif; ?>
                                                    <?php  if($item->invoice_status_id == '13'): ?>
                                                        <a href="<?= base_url('payroll/download_previsualization/'.$item->invoice_id) ?>" class="btn btn-small pink darken-1 tooltipped" data-tooltip="Descargar PDF de la Nomina"> 
                                                            <i class="material-icons">insert_drive_file</i>
                                                        </a>
                                                    <?php  endif; ?>
                                                    <?php  if($item->invoice_status_id != '14' && $item->invoice_status_id != '16'): ?>
                                                        <a href="<?= base_url('payroll_adjust/edit/'.$item->invoice_id) ?>" class="btn btn-small indigo tooltipped" data-tooltip="Editar Nomina">
                                                            <i class="material-icons">edit</i>
                                                        </a>
                                                    <?php  endif; ?>
                                                    <?php  if($item->invoice_status_id != '14' && $item->invoice_status_id != '12' && $item->invoice_status_id != '16'): ?>
                                                        <button  data-send="<?= $item->invoice_id ?>" data-target="resolutions" class="btn btn-small modal-trigger  light-blue send tooltipped" data-tooltip="Enviar Nomina">
                                                            <i class="material-icons">send</i>
                                                        </button>
                                                    <?php  endif; ?>
                                                    <?php  if($item->invoice_status_id != '13' && $item->invoice_status_id != '12' && $item->invoice_status_id != '15' && $item->invoice_status_id != '16'): ?>
                                                        <a href="<?= base_url('payroll/xml/'.$item->invoice_id) ?>"  class="btn btn-small tooltipped" data-tooltip="Descargar XML de la  Nomina">
                                                            <i class="material-icons">attach_file</i>
                                                        </a>
                                                    <?php  endif; ?>
                                                    <?php  if(($item->invoice_status_id == '14') && company()->type_environments_id == 2): ?>
                                                        <a href="<?= base_url('/periods/status_zip/'.$item->invoice_id) ?>"  class="btn btn-small tooltipped green"
                                                           data-tooltip="Validar errores de la DIAN">
                                                            <i class="material-icons">done</i>
                                                        </a>
                                                    <?php  endif; ?>
                                                    <?php  if(($item->invoice_status_id == '14')): ?>
                                                        <a href="<?= base_url('/periods/cune/'.$item->uuid) ?>" target="_blank"  class="btn btn-small tooltipped blue lighten-2" data-tooltip="Validar CUNE">
                                                            <i class="material-icons">done_all</i>
                                                        </a>
                                                    <?php  endif; ?>
                                                    <?php  if($item->invoice_status_id == '15'): ?>
                                                        <button
                                                                data-error="<?= esc($item->errors) ?>"
                                                                class="btn btn-small red modal-trigger tooltipped btn-error"
                                                                data-target="errors"
                                                                data-tooltip="Ver Errores"
                                                        >
                                                            <i class="material-icons">info_outline</i>
                                                        </button>
                                                    <?php  endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php if(count($workers) == 0): ?>
                                <p class="center purple-text pt-1">No hay ningún elemento registrado en la tabla.</p>
                            <?php endif; ?>
                            <?php // $pager->links(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--------------------------Modal multiples resoluciones-------------------------------->
<form action="<?= base_url('payroll/send_multiple/'.$id) ?>" method="post" id="form-resolution-multiple" >
    <div id="resolution_multiple" class="modal" role="dialog" style="height:auto; width: 600px">
        <div class="modal-content">
            <h4 class="modal-title">Seleccione la resolucion</h4>
            <div class="row">
                <div class="col s12">
                    <p style="text-align:justify;">
                        Al enviar masivamente las nominas debes esperar un momento mientras el sistema termina de cargar las nóminas, 
                        pero puedes seguir trabajando en los otros módulos de MiFacturaLegal.com sin ningún problema. <br><br>
                        Puedes recargar la página para validar el cargue de la nómina.
                    </p>
                </div>
                <div class="col s12">
                    <label for="">Resolución</label>
                    <select type="text" name="resolution" class="browser-default" id="multiple-resolution">
                        <?php foreach($resolutions as $item): ?>
                            <option value="<?= $item->id ?>"><?=  $item->prefix.' '.$item->from.'-'.$item->to ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
                <input type="hidden" name="payrolls[]" id="payrolls">
            </div>
        </div>
        <div class="modal-footer" >
            <a href="#!" class="modal-action modal-close waves-effect  btn-flat btn-light-indigo ">Cerrar</a>
            <button class="modal-action waves-effect waves-green btn indigo resolution-multiple-save">Guardar</button>
        </div>
    </div>
</form>
<!---------------------------end multiples resoluciones----------------------------------------->

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
                        <option value="">Elige tu opción</option>
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

<!------------------------------Modal de resoluciones-------------------------->
<form action="<?= base_url('send/') ?>" method="post" id="form-resolution">
    <div id="resolutions" class="modal" role="dialog" style="height:auto; width: 600px">
        <div class="modal-content">
            <h4 class="modal-title">Seleccione la resolucion</h4>
            <div class="row">
                <div class="col s12">
                    <label for="">Resolución</label>
                    <select type="text" name="resolution" class="browser-default" id="resolution">
                        <?php foreach($resolutions as $item): ?>
                            <option value="<?= $item->id ?>"><?=  $item->prefix.' '.$item->from.'-'.$item->to ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="modal-footer" >
            <a href="#!" class="modal-action modal-close waves-effect  btn-flat btn-light-indigo ">Cerrar</a>
            <button type="submit" id="btn-send" class="modal-action waves-effect waves-green btn indigo resolution-save">Guardar</button>
        </div>
    </div>
</form>
<!-------------------End modal de resoluciones --------------------------->

<form action="<?= base_url('payroll/add/'.$id) ?>" method="post" id="add-worker">
    <div id="worker" class="modal" role="dialog" style="height:auto; width: 600px">
        <div class="modal-content">
            <h4 class="modal-title">Añadir Empleado a la Nomina</h4>
            <div class="row">
                <div class="col s12">
                    <label for="">Empleados</label>
                    <select type="text" name="customer_id" class="browser-default" id="resolution">
                            <option value="" disabled selected>Seleccione un empleado</option>
                        <?php foreach($customers as $item): ?>
                            <option value="<?= $item->customer_id ?>"><?=  $item->name.' '.$item->second_name.' '.$item->surname.' '.$item->second_surname ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="modal-footer" >
            <a href="#!" class="modal-action modal-close waves-effect  btn-flat btn-light-indigo ">Cerrar</a>
            <button class="modal-action waves-effect waves-green btn indigo">Guardar</button>
        </div>
    </div>
</form>

<!------------------------Modal añadir empleados ----------------------------->
<div class="container-sprint-send" style="display:none;">
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
    <span style="width: 100%; text-align: center; color: white;  display: block; ">Cargando</span>
</div>
<!-----------------------End modal añadir empleados-------------------------->

<!--------------------modal de errores----------------------->
<div class="modal" id="errors">
    <div class="modal-content">
        <h4 class="modal-title">Errores</h4>
        <div class="row">
            <div class="col s12">
                <div class="card-alert card red">
                    <div class="card-content white-text errors-code">

                    </div>
                    <button type="button" class="close white-text" data-dismiss="alert"
                            aria-label="Close">
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer" >
        <a href="#!" class="modal-action modal-close waves-effect  btn-flat btn-light-indigo ">Cerrar</a>
    </div>
</div>
<!---------------End modal de errores--------------------------->

<?= $this->endSection() ?>


<?= $this->section('scripts')?>
    <script src="<?= base_url('js/advance-ui-modals.js') ?>"></script>
    <script src="<?= base_url('/js/shepherd.min.js') ?>"></script>
    <script src="<?= base_url('js/loading-bar.min.js') ?>"></script>
    <script src="<?= base_url('js/views/payrolls.js') ?>"></script>
    <script>

        $(document).ready(function() {
            localStorage.setItem('environment', 'sub_period');
            $('.btn-error').click(function () {
                var data = $(this).data('error');
                $('.errors-code').html(data);
            });

            $('.resolution-save').click(function (e){
                $(this).attr("disabled", true);
                $('#resolution').attr("readonly", true);
                $('#form-resolution').submit();
            });


           /* $('.resolution-multiple-save').click(function (e){
                $(this).attr("disabled", true);
                $('#multiple-resolution').attr("readonly", true);
                $('#form-resolution-multiple').submit();
            });*/
        })
    </script>
<?= $this->endSection() ?>
