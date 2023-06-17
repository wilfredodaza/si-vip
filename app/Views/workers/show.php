<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?> Empleado <?= $this->endSection() ?>
<?= $this->section('content') ?>
<div id="main">
    <div class="row">
        <div class="breadcrumbs-inline pt-3 pb-1" id="breadcrumbs-wrapper">
            <div class="container">
                <div class="row">
                    <div class="col s12">
                        <?= $this->include('layouts/alerts') ?>
                    </div>
                        <div class="col s10 m6 l6 breadcrumbs-left">
                            <h5 class="breadcrumbs-title mt-0 mb-0 display-inline hide-on-small-and-down ">
                                <span>
                                    <i class="material-icons left">people</i> <?= $customer->first_name. ' '.$customer->second_name. ' '. $customer->surname.' '.$customer->second_surname ?> 
                                </span>
                            </h5>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
        <div class="container">
            <div class="section">
                    <div class="row">
                        <div class="col s12">
                            <div class="card">
                                <div class="card-content">
                                    <div class="row">
                                        <div class="col s12 m6 l3 center" style="border-right: solid 1px #DCDDE1;">
                                            <p>Fecha de contratación</p>
                                            <p  style="font-size:20px;font-weight:bold;"><?= $customer->admision_date ?></p>  
                                        </div>
                                        <div class="col s12 m6 l3 center" style="border-right: solid 1px #DCDDE1;">
                                            <p>Salario</p>
                                            <p class="orange-text darken-3" style="font-size:20px;font-weight:bold;">
                                                <strong>$ <?= number_format($customer->salary, 2, ',', '.')?></strong>
                                            </p>  
                                        </div>
                                        <div class="col s12 m6 l3 center" style="border-right: solid 1px #DCDDE1;">
                                            <p>Nominas Emitidas</p>   
                                            <p class="light-blue-text darken-3" style="font-size:20px;font-weight:bold;"><?= $invoice->payroll_count?></p>  
                                        </div>
                                        <div class="col s12 m6 l3 center">
                                            <p>Cargo</p>
                                            <p  style="font-size:20px;font-weight:bold;"><?= strtoupper($customer->work) ?></p>  
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="section">
                    <div class="row">
                        <div class="col s12 m8">
                            <div class="card">
                                <div class="card-content">
                                    <div class="row">
                                        <div class="col s12">
                                            <div class="row">
                                                <div class="col s12">
                                                    <p>Información del Empleado</p><br>
                                                </div>
                                                <div class="col s12 m6 input-field">
                                                    <input type="text" id="type_document_identification"  readonly value="<?= $customer-> type_document_identification_name  ?>" placeholder="Tipo de Identificación">
                                                    <label for="type_document_identification">Tipo de Identificación</label>
                                                </div>
                                                <div class="col s12 m6 input-field">
                                                    <input type="text" id="identification_number" readonly value="<?= $customer->identification_number  ?>" placeholder="Numero de Identificación">
                                                    <label for="identification_number"">Numero de Identificación</label>
                                                </div>
                                                <div class="col s12 m6 input-field">
                                                    <input type="text" id="municipality" readonly value="<?= $customer->municipality_name  ?>" placeholder="Municipio">
                                                    <label for="municipality">Municipio</label>
                                                </div>
                                                <div class="col s12 m6 input-field">
                                                    <input type="text" placeholder="address" readonly value="<?= $customer->address  ?>" placeholder="Dirección<">
                                                    <label for="address">Dirección</label>
                                                </div>
                                                <div class="col s12 m6 input-field">
                                                    <input type="text" placeholder="Teléfono" id="phone" readonly value="<?= $customer->phone  ?>">
                                                    <label for="phone">Teléfono</label>
                                                </div>

                                                <div class="col s12 m6 input-field">
                                                    <input type="text" id="email" placeholder="Correo Electrónico" readonly value="<?= $customer->email  ?>">
                                                    <label for="email">Correo Electrónico</label>
                                                </div>

                                                <div class="col s12">
                                                    <p>Datos de pago</p><br>
                                                </div>
                                                <div class="col s12 m6 input-field">
                                                    <input type="text" id="payment_method_id" placeholder="Medio de Pago" readonly value="<?= $customer->payment_method_name  ?>">
                                                    <label for="payment_method_id">Medio de Pago</label>
                                                </div>
                                                <div class="col s12 m6 input-field">
                                                    <input type="text" id="bank" readonly value="<?= $customer->bank_name  ?>" placeholder="Banco">
                                                    <label for="bank">Banco</label>
                                                </div>
                                                <div class="col s12 m6 input-field">
                                                    <input type="text" id="type_account" placeholder="Tipo de Cuenta" readonly value="<?= $customer->bank_account_type_name ?>">
                                                    <label for="type_account">Tipo de Cuenta</label>
                                                </div>
                                                <div class="col s12 m6 input-field">
                                                    <input type="text" id="acount_number" placeholder="Número de Cuenta" readonly value="<?= $customer->account_number  ?>">
                                                    <label for="acount_number">Número de Cuenta</label>
                                                </div>
						<div class="col s12 m6 input-field">
                                                    <input type="text" id="acount_number" placeholder="Fecha de corte para días disponible de vacaciones" readonly value="<?= $customer->court_date  ?>">
                                                    <label for="acount_number">Fecha de corte para días disponible de vacaciones</label>
                                                </div>
						<div class="col s12 m6 input-field">
                                                    <input type="text" id="acount_number" placeholder="Días de vacaciones disponibles a fecha de corte" readonly value="<?= $customer->holidays  ?>">
                                                    <label for="acount_number">Días de vacaciones disponibles a fecha de corte</label>
                                                </div>


                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col s12 m4">
                            <div class="card">
                                <div class="card-content">
                                    <div class="row">
                                        <div class="col s12" style="padding:20px ;">
                                            <div class="col s12">
                                                <p>Información Laboral</p><br>
                                            </div>
                                            <div class="col s12 input-field">
                                                <input type="text" id="salary_integral" placeholder="Salario Integral" readonly value="<?= $customer->integral_salary == 'false' ? 'No' : 'Si'  ?>">
                                                <label for="salary_integral">Salario Integral</label>
                                            </div>
                                            <div class="col s12 input-field">
                                                <input type="text" placeholder="Frecuencia de pago" id="type_document_identification_name " readonly value="<?= $customer->type_document_identification_name  ?>">
                                                <label for="type_document_identification_name">Frecuencia de pago </label>
                                            </div>

                                            <div class="col s12 input-field">
                                                <input type="text" placeholder="Tipo de contrato" id="type_contract_name" readonly value="<?= $customer->type_contract_name ?>">
                                                <label for="type_contract_name">Tipo de contrato</label>
                                            </div>
                                            <div class="col s12  input-field">
                                                <input type="text" id="high_risk_pension" placeholder="Pensión de Alto Riesgo" readonly value="<?= $customer->high_risk_pension == 'false' ? 'No' : 'Si' ?>">
                                                <label for="high_risk_pension">Pensión de Alto Riesgo</label>
                                            </div>
                                            <div class="col s12 input-field">
                                                <input type="text" id="type_worker_name" placeholder="type_worker_name" readonly value="<?= $customer->type_worker_name  ?>">
                                                <label for="type_worker_name">Tipo de trabajador</label>
                                            </div>
                                            <div class="col s12 input-field">
                                                <input type="text" id="sub_type_worker_name" placeholder="SubTipo de trabajador" readonly value="<?= $customer->sub_type_worker_name  ?>">
                                                <label for="sub_type_worker_name">SubTipo de trabajador</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="section">
                    <div class="row">
                        <div class="col s12">
                            <div class="card mb-5">
                                <div class="card-content">
                                    <?php if($validation != 0): ?>
                                        <a href="<?= base_url('work_certificate/pdf/'.$customer->customer_id) ?>" target="_blank" class="btn indigo <?php if($validation != 0): ?> center-align <?php else: ?> right   <?php endif; ?>">
                                            Certificado Laboral
                                        </a>
					<div class="crearfix"></div>
                                    <?php endif; ?>
     <?php if( session('user')->role_id != 10): ?>
                                    <table>
                                        <thead>
                                            <tr>
                                                <td>Periodo de Nomina</td>
                                                <td class="center">Tipo</td>
                                                <td class="center">N° Referencia</td>
                                                <td class="center">Ingreso</td>
                                                <td class="center">Deducidos</td>
                                                <td class="center">Pago total</td>
                                                <td class="center">Estado</td>
                                                <td class="center">Acciones</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($invoices as $item):?>
                                            <tr>
                                                <td class="indigo-text"><?=  $item->month.' '. $item->year?></td>
                                                <td class="center"><?=  $item->type_document_name ?> <?php  if($item->type_payroll_adjust_note_id == '1'){ echo '- Remplazar'; } elseif($item->type_payroll_adjust_note_id == '2') { echo '- Eliminar'; }?></td>
                                                <td class="center"><?= $item->resolution_credit ?></td>
                                                <td class="center">$<?=  number_format($item->accrued, '2', ',', '.') ?></td>
                                                <td class="center">$<?=  number_format($item->deduction, '2', ',', '.') ?></td>
                                                <td class="center">$<?=  number_format($item->accrued - $item->deduction, '2', ',', '.') ?></td>
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
                                                    }
                                                ?>
                                                </td>
                                                <td class="center">
                                                <div class="btn-group ">
                                                        <?php  if($item->invoice_status_id == '13' && $item->invoice_status_id != '14'): ?>
                                                            <a href="<?= base_url('payroll/download_previsualization/'.$item->invoice_id) ?>" class="btn btn-small pink darken-1 tooltipped" data-tooltip="Descargar PDF de la Nomina">
                                                                <i class="material-icons">insert_drive_file</i>
                                                            </a>
                                                        <?php  endif; ?>
                                                        <?php  if($item->invoice_status_id != '12' && $item->invoice_status_id == '14' && $item->invoice_status_id != '15'): ?>
                                                            <a href="<?= base_url('payrolls/download/'.$item->invoice_id) ?>" class="btn btn-small pink darken-1">
                                                                <i class="material-icons">insert_drive_file</i>
                                                            </a>
                                                        <?php  endif; ?>
                                                        <?php  if($item->invoice_status_id != '14' && $item->type_payroll_adjust_note_id == Null ): ?>
                                                            <a href="<?= base_url('payroll/edit/'.$item->invoice_id) ?>" class="btn btn-small indigo">
                                                                <i class="material-icons">edit</i>
                                                            </a>
                                                        <?php  endif; ?>
                                                    <?php  if($item->invoice_status_id != '14' && $item->type_documents_id == 10 && $item->type_payroll_adjust_note_id != Null): ?>
                                                        <a href="<?= base_url('payroll_adjust/edit/'.$item->invoice_id) ?>" class="btn btn-small indigo">
                                                            <i class="material-icons">edit</i>
                                                        </a>
                                                    <?php  endif; ?>
                                                        <?php  if($item->invoice_status_id != '13' && $item->invoice_status_id != '12' && $item->invoice_status_id != '15'): ?>
                                                            <a href="<?= base_url('payrolls/xml/'.$item->invoice_id) ?>"  class="btn btn-small">
                                                                <i class="material-icons">attach_file</i>
                                                            </a>
                                                        <?php  endif; ?>

                                                        <?php  if($item->invoice_status_id != '13' && $item->invoice_status_id != '12' && $item->invoice_status_id != '15' && $item->type_payroll_adjust_note_id == null): ?>
                                                            <a href="<?= base_url('payroll_adjust/'.$item->invoice_id) ?>"  class="btn btn-small purple">
                                                                <i class="material-icons">settings</i>
                                                            </a>
                                                        <?php  endif; ?>

                                                    <?php  if($item->invoice_status_id == '13' && $item->type_documents_id == 10): ?>
                                                        <button  data-send="<?= $item->invoice_id ?>" data-target="resolutions" class="btn btn-small send modal-trigger blue darken-1">
                                                            <i class="material-icons">send</i>
                                                        </button>
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
                                                    <?php  if(($item->invoice_status_id == '14')): ?>
                                                        <a href="<?= base_url('/periods/cune/'.$item->uuid) ?>" target="_blank"  class="btn btn-small tooltipped blue lighten-2" data-tooltip="Validar CUNE">
                                                            <i class="material-icons">done_all</i>
                                                        </a>
                                                    <?php  endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach ?>
                                        </tbody>
                                    </table>
                                    <?php if(count($invoices) == 0): ?>
                                        <p class="center purple-text pt-1">No hay ningún elemento registrado en la tabla.</p>
                                    <?php endif; ?>
                                    <?= $pager->links(); ?>
<?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!------------------------------Modal de resoluciones-------------------------->
<form action="" method="post" id="form-resolution">
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
            <button class="modal-action waves-effect waves-green btn indigo resolution-save">Guardar</button>
        </div>
    </div>
</form>
<!-------------------End modal de resoluciones --------------------------->
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
<script src="<?= base_url('js/views/worker_show.js') ?>"></script>
<script>
    $(document).ready(function() {
        localStorage.setItem('environment', 'worker');

        $('.send').click(function () {
		console.log(${localStorage.getItem('url')}/payrolls/send/${$(this).data('send'));
            $('#form-resolution').attr('action', `${localStorage.getItem('url')}/payrolls/send/${$(this).data('send')}`)
        });

        $('.btn-error').click(function () {
            var data = $(this).data('error');
            $('.errors-code').html(data);
        });

        $('.resolution-save').click(function (e){
            $(this).attr("disabled", true);
            $('#resolution').attr("readonly", true);
            $('#form-resolution').submit();
        });

    });
</script>
<?php $this->endSection() ?>
