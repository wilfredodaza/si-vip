<style>
    tr td,
    tr th {
        padding: 5px 5px;
    }
</style>

<div id="main">
    <div class="row">
        <div class="breadcrumbs-inline pt-3 pb-1" id="breadcrumbs-wrapper">
            <div class="container">
                <div class="row">
                    <div class="col s10 m6 l6 breadcrumbs-left">
                        <h5 class="breadcrumbs-title mt-0 mb-0 display-inline hide-on-small-and-down ">
                            <span>
                                <i class="material-icons left">people</i>
                                <?= $customer->first_name . ' ' . $customer->second_name . ' ' . $customer->surname . ' ' . $customer->second_surname ?>
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
                                    <p style="font-size:20px;font-weight:bold;"><?= $customer->admision_date ?></p>
                                </div>
                                <div class="col s12 m6 l3 center" style="border-right: solid 1px #DCDDE1;">
                                    <p>Salario</p>
                                    <p class="orange-text darken-3" style="font-size:20px;font-weight:bold;">
                                        <strong>$ <?= number_format($customer->salary, 2, ',', '.') ?></strong>
                                    </p>
                                </div>
                                <div class="col s12 m6 l3 center" style="border-right: solid 1px #DCDDE1;">
                                    <p>Nominas Emitidas</p>
                                    <p class="light-blue-text darken-3" style="font-size:20px;font-weight:bold;">
                                        <?= $invoice->payroll_count ?></p>
                                </div>
                                <div class="col s12 m6 l3 center">
                                    <p>Cargo</p>
                                    <p style="font-size:20px;font-weight:bold;"><?= strtoupper($customer->work) ?></p>
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
                                            <label for="">Tipo de Identificación</label>
                                            <input type="text" readonly value="<?= $customer->type_document_identification_name  ?>">
                                        </div>
                                        <div class="col s12 m6 input-field">
                                            <label for="">Numero de Identificación</label>
                                            <input type="text" readonly value="<?= $customer->identification_number  ?>">
                                        </div>
                                        <div class="col s12 m6 input-field">
                                            <label for="">Municipio</label>
                                            <input type="text" readonly value="<?= $customer->municipality_name  ?>">
                                        </div>
                                        <div class="col s12 m6 input-field">
                                            <label for="">Dirección</label>
                                            <input type="text" readonly value="<?= $customer->address  ?>">
                                        </div>
                                        <div class="col s12 m6 input-field">
                                            <label for="">Teléfono</label>
                                            <input type="text" readonly value="<?= $customer->phone  ?>">
                                        </div>

                                        <div class="col s12 m6 input-field">
                                            <label for="">Correo Electrónico</label>
                                            <input type="text" readonly value="<?= $customer->email  ?>">
                                        </div>

                                        <div class="col s12">
                                            <p>Datos de pago</p><br>
                                        </div>
                                        <div class="col s12 m6 input-field">
                                            <label for="">Medio de Pago</label>
                                            <input type="text" readonly value="<?= $customer->payment_method_name  ?>">
                                        </div>
                                        <div class="col s12 m6 input-field">
                                            <label for="">Banco</label>
                                            <input type="text" readonly value="<?= $customer->bank_name  ?>">
                                        </div>
                                        <div class="col s12 m6 input-field">
                                            <label for="">Tipo de Cuenta</label>
                                            <input type="text" readonly value="<?= $customer->bank_account_type_name ?>">
                                        </div>
                                        <div class="col s12 m6 input-field">
                                            <label for="">Número de Cuenta</label>
                                            <input type="text" readonly value="<?= '**********' . substr($customer->account_number, -4)   ?>">
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
                                        <label for="">Salario Integral</label>
                                        <input type="text" readonly value="<?= $customer->integral_salary == 'false' ? 'No' : 'Si'  ?>">
                                    </div>
                                    <div class="col s12 input-field">
                                        <label for="">Tipo de identificación</label>
                                        <input type="text" readonly value="<?= $customer->type_document_identification_name  ?>">
                                    </div>
                                    <div class="col s12 input-field">
                                        <label for="">Tipo de contrato</label>
                                        <input type="text" readonly value="<?= $customer->type_contract_name ?>">
                                    </div>
                                    <div class="col s12  input-field">
                                        <label for="">Pensión de Alto Riesgo</label>
                                        <input type="text" readonly value="<?= $customer->high_risk_pension == 'false' ? 'No' : 'Si' ?>">
                                    </div>
                                    <div class="col s12 input-field">
                                        <label for="">Tipo de trabajador</label>
                                        <input type="text" readonly value="<?= $customer->type_worker_name  ?>">
                                    </div>
                                    <div class="col s12 input-field">
                                        <label for="">SubTipo de trabajador</label>
                                        <input type="text" readonly value="<?= $customer->sub_type_worker_name  ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>