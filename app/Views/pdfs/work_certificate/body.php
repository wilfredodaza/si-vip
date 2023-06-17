<div class="col-sm-12 invoice-col" id="texto">
    <h4 class="text-center">CERTIFICA:</h4>
    <br>
    <?php setlocale(LC_TIME, 'spanish');?>
    <p style="font-size:12pt;text-align:justify ; font-family:Arial, Helvetica, sans-serif;"> Que el señor(a) <b><?= $customer->customer_name . ' ' . $customer->second_name . ' ' . $customer->surname . ' ' . $customer->second_surname ?></b> identificado(a) con No.
    <?= $customer->type_identification_name. ' ' . $customer->identification_number  ?>, 
    <?php if($customer->retirement_date != null && $customer->retirement_date != '0000-00-00'): ?>
        trabajó en esta compañia desde el <?= strftime(" %d de %B de %Y", strtotime($customer->admision_date)) ?> hasta el <?= strftime(" %d de %B del %Y", strtotime($customer->retirement_date)) ?>
    <?php else: ?>
        trabaja en esta compañia desde el <?= strftime(" %d de %B de %Y", strtotime($customer->admision_date)) ?>
    <?php endif ?>
    con un contrato <?php if($customer->type_contract_id == 1 || $customer->type_contract_id == 2): ?> a <?php  else: ?> de <?php endif; ?> <?= strtolower($customer->type_worker_name)  ?> bajo el cargo de
    <?=  mb_strtolower($customer->work, 'UTF-8') ?>, por el cual recibe un pago mensual, distribuido de la siguiente forma:
    </p>
</div>

<br><br>

<div class="col-sm-12 invoice-col">
    <table class="">
        <tbody>
            <tr>
                <td style="width:50%;  font-size:12pt"><b>Salario:</b></td>
                <td width="300px" style="font-size:12pt">$ <?= number_format($customer->salary, 0, '.', '.') ?></td>
            </tr>
  
            <?php if(isset($customer->transportation_assistance) && $customer->transportation_assistance != null && $customer->transportation_assistance != '0' && !empty($customer->transportation_assistance)): ?>
            <tr>
                <td style="width:50%;  font-size:12pt"><b>Auxilio de transporte:</b></td>
                <td width="300px" style="font-size:12pt">$ <?= number_format($customer->transportation_assistance, '0', '.', '.') ?></td>
            </tr>
            <?php endif; ?>
            <?php if(isset($customer->non_salary_payment) && $customer->non_salary_payment != null && $customer->non_salary_payment != 0 && !empty($customer->non_salary_payment)): ?>
            <tr>
                <td style="width:50%;  font-size:12pt"><b>Pagos no salariales:</b></td>
                <td width="300px" style="font-size:12pt">$ <?= number_format($customer->non_salary_payment, '0', '.', '.') ?></td>
            </tr>
            <?php endif; ?>
            <?php if(isset($customer->other_payments) && $customer->other_payments != null && $customer->other_payments != 0 && !empty($customer->other_payments)): ?>
            <tr>
                <td style="width:50%;  font-size:12pt"><b>Otros pagos salariales:</b></td>
                <td width="300px" style="font-size:12pt">$ <?= number_format($customer->other_payments, '0', '.', '.') ?></td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<br>
<div class="col-sm-12 invoice-col" id="texto" style="font-size:12pt"><br>
    Para mayor información y acreditación de la información aquí contenida, por favor
    comunicarse con nosotros al teléfono <?= $company->telephone ?> o al correo electrónico
    <?= $company->email ?>.
    <br><br>
    Se expide a solicitud del interesado, en la ciudad de  <?=  $company->municipality_name == 'Bogotá, D.c. ' ? 'Bogotá D.C.':  $company->municipality_name.'.' ?> a los <?= strftime(" %d días del mes de %B de %Y"); ?>.
    <br>
    <br>
    <br>
</div>

<div class="col-sm-12 invoice-col" style="width:100%; font-size:12pt;">
    <table>
        <tbody>
            <tr>
                <td style="width:50%; font-size:12pt;">
                    <div>
                    <?php if (!empty($company->firm)) : ?>
                        <div  class="text-center">
                        <img src="<?=base_url('assets/upload/images/'.$company->firm) ?>" width="150px" />
                        </div>
                    <?php endif; ?>
                    </p>
                    <b><?= $company->payroll_manager ?></b><br>
                    <?= ucwords(strtolower($company->payroll_work_manager)) ?><br>
                    <?=  $company->municipality_name == 'Bogotá, D.c. ' ? 'Bogotá D.C.':  $company->municipality_name.' ('.$company->department_name.')'  ?> - Colombia
                    </div>
                </td>
                <?php if (!empty( $company->stamp)) : ?>
                    <td> <img src="<?= base_url('assets/upload/images/'.$company->stamp) ?>" width="100px"></td>
                <?php endif; ?>
            </tr>

        </tbody>
    </table>
</div>

