<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?> Empleado <?= $this->endSection() ?>

<?php $this->section('styles') ?>
    <link rel="stylesheet" href="<?= base_url('/css/select2.min.css') ?>"  type="text/css">
    <link rel="stylesheet" href="<?= base_url('/css/select2-materialize.css') ?>" type="text/css">

<?php $this->endSection() ?>

<?= $this->section('content') ?>
<div id="main">
    <div class="row">
        <div class="breadcrumbs-inline pt-3 pb-1" id="breadcrumbs-wrapper">
            <div class="container">
                <div class="row">
                    <div class="col s12">
                        
                    </div>
                    <div class="col s10 m6 l6 breadcrumbs-left">
                        <h5 class="breadcrumbs-title mt-0 mb-0 display-inline hide-on-small-and-down ">
                            <span>
                                Crear Empleado
                            </span>
                        </h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col s12">
            <div class="container">
                <div class="section">
                    <form action="<?= base_url('workers') ?>" autocomplete="off" method="post">
                        <div class="card">
                            <div class="card-content">
                                <p class="card-title">
                                    INFORMACIÓN DEL EMPLEADO
                                    <a href="<?= base_url('workers') ?>"  data-target="filter" class="btn btn-light-indigo right  btn-small" style="padding-left:5px; padding-right:10px;">
                                        <i class="material-icons left">keyboard_arrow_left</i> Regresar
                                    </a>
                                </p>
                               
                                <div class="row">
                                    <div class="col m6 s12 input-field">
                                        <input type="text" placeholder="Primer Nombre" class="<?= session('errors.first_name') ? 'invalid' : '' ?>" name="first_name" id="first_name" value="<?= old('first_name') ?>">
                                        <label for="first_name">Primer Nombre</label>
                                        <?php if(session('errors.first_name')): ?>
                                            <span class="helper-text" data-error="<?= session('errors.first_name') ?>"></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col m6 s12 input-field">
                                        <input type="text" placeholder="Segundo Nombre" class="<?= session('errors.second_name') ? 'invalid' : '' ?>" name="second_name" id="second_name" value="<?= old('second_name') ?>">
                                        <label for="second_name">Segundo Nombre</label>
                                        <?php if(session('errors.second_name')): ?>
                                            <span class="helper-text" data-error="<?= session('errors.second_name') ?>"></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col m6 s12 input-field">
                                        <input type="text" placeholder="Primer Apellido" class="<?= session('errors.surname') ? 'invalid' : '' ?>" name="surname" id="surname" value="<?= old('surname')?>">
                                        <label for="surname">Primer Apellido</label>
                                        <?php if(session('errors.surname')): ?>
                                            <span class="helper-text" data-error="<?= session('errors.surname') ?>"></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col m6 s12 input-field">
                                        <input type="text" placeholder="Segundo Apellido"  class="<?= session('errors.second_surname') ? 'invalid' : '' ?>" name="second_surname" id="second_surname" value="<?= old('second_surname')?>">
                                        <label for="second_surname">Segundo Apellido</label>
                                        <?php if(session('errors.second_surname')): ?>
                                            <span class="helper-text" data-error="<?= session('errors.second_surname') ?>"></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col m6 s12 input-field ">
                                        <label class="active">Tipo de identificación</label>
                                        <select name="type_document_identification_id"   data-placeholder="Tipo de identificación"  id="type_document_identification_id" class="<?= session('errors.type_document_identification_id') ? 'invalid' : '' ?> select2 browser-default"  value="<?= old('type_document_identification_id')?>">
                                            <?php foreach($typeDocumentIdentifications as $item): ?>
                                                <option <?=  old('type_document_identification_id') == $item->id ? 'selected' : '' ?>  value="<?= $item->id ?>"><?=  $item->name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php if(session('errors.type_document_identifications_id')): ?>
                                            <span class="helper-text" data-error="<?= session('errors.type_document_identification_id') ?>"></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col m6 s12 input-field">
                                        <input type="number" placeholder="Número de documento" name="identification_number" id="identification_number" class="<?= session('errors.identification_number') ? 'invalid' : '' ?> " value="<?= old('identification_number')?>">
                                        <label for="identification_number">Número de documento</label>
                                        <?php if(session('errors.identification_number')): ?>
                                            <span class="helper-text" data-error="<?= session('errors.identification_number') ?>"></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col m6 s12 input-field">
                                        <label class="active">Municipio</label>
                                        <select name="municipality_id" id="municipality_id" placeholder="Municipio" class="<?= session('errors.municipality_id') ? 'invalid' : '' ?> select2 browser-default" value="<?= old('municipality_id')?>">
                                            <?php foreach($municipalities as $item): ?>
                                                <option <?=  old('municipality_id') == $item->id ? 'selected' : '' ?>  value="<?= $item->id ?>"><?=  $item->name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                     
                                        <?php if(session('errors.municipality_id')): ?>
                                            <span class="helper-text" data-error="<?= session('errors.municipality_id') ?>"></span>
                                        <?php endif; ?>
                                        
                                    </div>
                                    <div class="col m6 s12 input-field">
                                        <input type="text" placeholder="Dirección" name="address" id="address" class="<?= session('errors.address') ? 'invalid' : '' ?>" value="<?= old('address')?>">
                                        <label for="address">Dirección</label>
                                        <?php if(session('errors.address')): ?>
                                            <span class="helper-text" data-error="<?= session('errors.address') ?>"></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col m6 s12 input-field">
                                        <input type="email" placeholder="Correo Electrónico" name="email" id="email" class="<?= session('errors.email') ? 'invalid' : '' ?>" value="<?= old('email')?>">
                                        <label for="email">Correo Electrónico</label>
                                        <?php if(session('errors.email')): ?>
                                            <span class="helper-text" data-error="<?= session('errors.email') ?>"></span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="col m6 s12 input-field">
                                        <input type="number" placeholder="Teléfono" name="phone" id="phone" class="<?= session('errors.phone') ? 'invalid' : '' ?>" value="<?= old('phone')?>">
                                        <label for="phone">Teléfono</label>
                                        <?php if(session('errors.phone')): ?>
                                            <span class="helper-text" data-error="<?= session('errors.phone') ?>"></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-content">
                                <p class="card-title">
                                    INFORMACIÓN LABORAL    
                                </p>
                                <div class="row">
                                    <div class="col m6 s12 input-field">
                                        <label class="active">Tipo de contrato</label>
                                        <select name="type_contract_id" id="type_contract_id" placeholder="Tipo de contrato" class=" <?= session('errors.type_contract_id') ? 'invalid' : '' ?> select2 browser-default" value="<?= old('type_contract_id')?>">
                                            <?php foreach($typeContracts as $item): ?>
                                                <option <?=  old('type_contract_id') == $item->id ? 'selected' : '' ?> value="<?= $item->id ?>"><?=  $item->name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php if(session('errors.type_contract_id')): ?>
                                            <span class="helper-text" data-error="<?= session('errors.type_contract_id') ?>"></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col m6 s12 input-field">
                                        <label class="active">Salrio Integral</label>
                                        <select name="integral_salary" placeholder="Salrio Integral" id="integral_salary" class="<?= session('errors.integral_salary') ? 'invalid' : '' ?> select2 browser-default" value="<?= old('integral_salary')?>">
                                            <option value="No" <?=  old('integral_salary') == $item->id ? 'selected' : '' ?>>No</option>
                                            <option value="Si" <?=  old('integral_salary') == $item->id ? 'selected' : '' ?>>Si</option>
                                        </select>
                                        <?php if(session('errors.integral_salary')): ?>
                                            <span class="helper-text" data-error="<?= session('errors.integral_salary') ?>"></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="row ">
                                    <div class="col m6 s12 input-field">
                                        <input type="date" placeholder="Fecha de contratación" name="admision_date" id="admision_date" class="<?= session('errors.admision_date') ? 'invalid' : '' ?>" value="<?= old('admision_date')?>">
                                        <label for="admision_date">Fecha de contratación</label>
                                        <?php if(session('errors.admision_date')): ?>
                                            <span class="helper-text" data-error="<?= session('errors.admision_date') ?>"></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col m6 s12 input-field">
                                        <input type="date" placeholder="Fecha de contratación" name="retirement_date" id="admision_date" class="<?= session('errors.admision_date') ? 'invalid' : '' ?>" value="<?= old('admision_date')?>">
                                        <label for="admision_date">Fecha de retiro</label>

                                        <?php if(session('errors.retirement_date')): ?>
                                            <span class="helper-text" data-error="<?= session('errors.retirement_date') ?>"></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col s12 input-field">
                                        <input type="text" placeholder="Salario" name="salary" id="salary" class="<?= session('errors.salary') ? 'invalid' : '' ?>" value="<?= old('salary')?>">
                                        <label for="salary">Salario</label>
                                        <?php if(session('errors.salary')): ?>
                                            <span class="helper-text" data-error="<?= session('errors.salary') ?>"></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col m6 s12 input-field">
                                        <label class="active">Tipo de trabajado</label>
                                        <select name="type_worker_id" id="type_worker_id" placeholder="Tipo de trabajador" class=" <?= session('errors.type_worker_id') ? 'invalid' : '' ?> select2 browser-default" value="<?= old('type_worker_id')?>">
                                            <?php foreach($typeWorkers as $item): ?>
                                                <option <?= old('type_worker_id') == $item->id ? 'selected' : '' ?> value="<?= $item->id ?>"><?=  $item->name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php if(session('errors.type_worker_id')): ?>
                                            <span class="helper-text" data-error="<?= session('errors.type_worker_id') ?>"></span>
                                        <?php endif; ?>
                                    </div>
                                
                                    <div class="col m6 s12 input-field">
                                        <label class="active">Pensión de Alto riesgo</label>
                                        <select name="high_risk_pension" placeholder="Pensión de Alto riesgo" id="high_risk_pension" class="<?= session('errors.high_risk_pension') ? 'invalid' : '' ?> select2 browser-default" >
                                            <option value="No" <?= old('high_risk_pension') == $item->id ? 'selected' : '' ?>>No</option>
                                            <option value="Si" <?= old('high_risk_pension') == $item->id ? 'selected' : '' ?>>Si</option>
                                        </select>
                                        <?php if(session('errors.high_risk_pension')): ?>
                                            <span class="helper-text" data-error="<?= session('errors.high_risk_pension') ?>"></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col m6 s12 input-field">
                                        <label class="active">Frecuencia de pago</label>
                                        <select name="payroll_period_id" placeholder="Frecuencia de pago" id="payroll_period_id" class="<?= session('errors.payroll_period_id') ? 'invalid' : '' ?> select2 browser-default" value="<?= old('payroll_period_id')?>">
                                            <?php foreach($payrollPeriods as $item): ?>
                                                <option <?= old('payroll_period_id') == $item->id ? 'selected' : '' ?> value="<?= $item->id ?>"><?=  $item->name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php if(session('errors.payroll_period_id')): ?>
                                            <span class="helper-text" data-error="<?= session('errors.payroll_period_id') ?>"></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col m6 s12 input-field">
                                        <label class="active">Subtipo de trabajado</label>
                                        <select name="sub_type_worker_id" placeholder="Subtipo de trabajado" id="sub_type_worker_id" class="<?= session('errors.sub_type_worker_id') ? 'invalid' : '' ?> select2 browser-default" value="<?= old('sub_type_worker_id')?>">
                                            <?php foreach($subTypeWorkers as $item): ?>
                                                <option <?= old('sub_type_worker_id') == $item->id ? 'selected' : '' ?>  value="<?= $item->id ?>"><?=  $item->name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php if(session('errors.sub_type_worker_id')): ?>
                                            <span class="helper-text" data-error="<?= session('errors.sub_type_worker_id') ?>"></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col m6 s12 input-field">
                                        <input type="text" placeholder="Cargo" name="work" id="work" class="<?= session('errors.work') ? 'invalid' : '' ?>" value="<?= old('work')?>">
                                        <label for="work">Cargo</label>
                                        <?php if(session('errors.work')): ?>
                                            <span class="helper-text" data-error="<?= session('errors.work') ?>"></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col m6 s12 input-field">
                                        <input type="text" placeholder="Codigo del empleado" name="worker_code" id="worker_code" class="<?= session('errors.worker_code') ? 'invalid' : '' ?>" value="<?= old('worker_code')?>">
                                        <label for="worker_code">Codigo del empleado</label>
                                        <?php if(session('errors.worker_code')): ?>
                                            <span class="helper-text" data-error="<?= session('errors.worker_code') ?>"></span>
                                        <?php endif; ?>
                                    
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-content">
                                <p class="card-title">
                                    DATOS PAGO
                                </p>
                                <div class="row">
                                    <div class="col m6 s12 input-field">
                                        <label class="active">Método de pago</label>
                                        <select name="payment_method_id" placeholder="Método de pago" id="payment_method_id" class="<?= session('errors.payment_method_id') ? 'invalid' : '' ?> select2 browser-default"  value="<?= old('payment_method_id')?>">
                                            <?php foreach($paymentMethods as $item): ?>
                                                <option  <?= old('payment_method_id') == $item->id ? 'selected' : '' ?> value="<?= $item->id ?>"><?=  $item->name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php if(session('errors.payment_method_id')): ?>
                                            <span class="helper-text" data-error="<?= session('errors.payment_method_id') ?>"></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col m6 s12 input-field">
                                        <label class="active">Banco</label>
                                        <select name="bank_id" placeholder="Banco" id="bank_id" class="<?= session('errors.bank_id') ? 'invalid' : '' ?> select2 browser-default" value="<?= old('bank_id')?>">
                                            <?php foreach($banks as $item): ?>
                                                <option  <?= old('bank_id') == $item->id ? 'selected' : '' ?> value="<?= $item->id ?>"><?=  $item->name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        
                                        <?php if(session('errors.bank_id')): ?>
                                            <span class="helper-text" data-error="<?= session('errors.bank_id') ?>"></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col m6 s12 input-field">
                                        <label class="active">Tipo de cuenta</label>
                                        <select name="bank_account_type_id" placeholder="Tipo de cuenta"  id="bank_account_type_id" class="<?= session('errors.bank_account_type_id') ? 'invalid' : '' ?> select2 browser-default" value="<?= old('bank_account_type_id')?>">
                                            <?php foreach($typeAccountBanks  as $item): ?>
                                                <option <?= old('bank_account_type_id') == $item->id ? 'selected' : '' ?> value="<?= $item->id ?>"><?=  $item->name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php if(session('errors.bank_account_type_id')): ?>
                                            <span class="helper-text" data-error="<?= session('errors.bank_account_type_id') ?>"></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col m6 s12 input-field">
                                        <input type="number" placeholder="Número de cuenta" name="account_number" id="account_number" class=" <?= session('errors.account_number') ? 'invalid' : '' ?> " value="<?= old('account_number')?>">
                                        <label for="account_number">Número de cuenta</label>
                                        <?php if(session('errors.account_number')): ?>
                                            <span class="helper-text" data-error="<?= session('errors.account_number') ?>"></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-content">
                                <p class="card-title">
                                    INFORMACIÓN DEL CERTIFICADO LABORAL
                                </p>
                                <div class="row">
                                    <div class="col m12 s12 input-field">
                                        <input type="number" placeholder="Auxilio de transporte" name="transportation_assistance" id="transportation_assistance" class=" <?= session('errors.transportation_assistance') ? 'invalid' : '' ?> " value="<?= old('transportation_assistance')?>">
                                        <label for="transportation_assistance">Auxilio de transporte</label>
                                        <?php if(session('errors.transportation_assistance')): ?>
                                            <span class="helper-text" data-error="<?= session('errors.transportation_assistance') ?>"></span>
                                        <?php endif; ?>
                                    </div>
                                   
                                </div>
                                <div class="row">
                                    <div class="col m6 s12 input-field">
                                        <input type="number" placeholder="Pagos no salariales" name="non_salary_payment" id="non_salary_payment" class=" <?= session('errors.non_salary_payment') ? 'invalid' : '' ?> " value="<?= old('non_salary_payment')?>">
                                        <label for="non_salary_payment">Pagos no salariales</label>
                                        <?php if(session('errors.non_salary_payment')): ?>
                                            <span class="helper-text" data-error="<?= session('errors.non_salary_payment') ?>"></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col m6 s12 input-field">
                                        <input type="number" placeholder="Otros pagos salariales" name="other_payments" id="other_payments" class=" <?= session('errors.other_payments') ? 'invalid' : '' ?> " value="<?= old('account_number')?>">
                                        <label for="other_payments">Otros pagos salariales</label>
                                        <?php if(session('errors.other_payments')): ?>
                                            <span class="helper-text" data-error="<?= session('errors.other_payments') ?>"></span>
                                        <?php endif; ?>
                                    </div>
                                    

                                    <div class="row">
                                        <div class="col s12">
                                            <button class="btn indigo right">Guardar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?=  $this->endSection() ?>
<?= $this->section('scripts') ?>
    <script src="<?= base_url('js/select2.full.min.js') ?>"></script>
    <script src="<?= base_url('js/form-select2.js') ?>"></script>
<?= $this->endSection() ?>

