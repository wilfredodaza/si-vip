<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?> Nomina <?= $this->endSection() ?>
<?= $this->section('content') ?>
<div id="main">
  <div class="row">
    <div class="breadcrumbs-inline pt-3 pb-1" id="breadcrumbs-wrapper">
      <div class="container">
        <div class="row">
          <div class="col s12">
            <?= view('layouts/alerts') ?>
          </div>
          <div class="col s10 m12 l12 breadcrumbs-left">
            <h5 class="breadcrumbs-title mt-0 mb-0 display-inline hide-on-small-and-down ">
              <span>
                Nomina
              </span>
            </h5>
            <ol class="breadcrumbs mb-0">
              <li class="breadcrumb-item"><a href="<?= base_url() ?>/home">Home</a></li>
              <li class="breadcrumb-item active"><a href="#">Nomina </a></li>
            </ol>
          </div>
        </div>
      </div>
    </div>
    <div class="col s12">
      <div class="container">
        <div class="section">
          <div class="card-panel">
            <div class="row">
              <div class="col s12 m12">
                <form action="" method="get">
                  <div class="row">
                    <div class="col s12 m4 ">
                      <label for="user">Usuario</label>
                      <select class="select browser-default" id="user" name="user" required>
                        <option value="">Seleccione ...</option>
                        <?php foreach ($users as $item) : ?>
                        <option value="<?= $item->id ?>"
                          <?= (isset($_GET['user']) && $_GET['user'] == $item->id) ? 'selected' : '' ?>>
                          <?= $item->name ?>
                        </option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="col s12 m2 ">
                      <label for="date">Mes</label>
                      <select class="select browser-default" id="date" name="date" required>
                        <option value="">Seleccione ...</option>
                        <?php foreach ($months as $month) : ?>
                        <option value="<?= $month->id ?>"
                          <?= (isset($_GET['date']) && $_GET['date'] == $month->id) ? 'selected' : '' ?>>
                          <?= $month->name ?>
                        </option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="col s12 m2 ">
                      <label for="date">Año</label>
                      <?php
                                            $cont = date('Y');
                                            ?>
                      <select class="select browser-default validate" id="year" name="year" required>
                        <option value="" disabled="" selected="">Seleccione una opción</option>
                        <?php while ($cont >= 2023) { ?>
                        <option <?= (isset($_GET['year']) && $_GET['year'] == $cont) ? 'selected' : '' ?>
                          value="<?php echo($cont); ?>"><?php echo($cont); ?></option>
                        <?php $cont = ($cont-1); } ?>
                      </select>
                    </div>
                    <div class="col s12 m4">
                      <button type="submit" class="modals-action btn indigo mt-5 right">Filtrar</button>
                      <?php if (isset($_GET['date']) || isset($_GET['user'])): ?>
                      <a href="<?= base_url('payrolls') ?>" class="btn right btn-light-red btn mr-1 mt-5"
                        style="padding-left: 10px;padding-right: 10px; margin-right: 10px; ">
                        <i class="material-icons left">close</i>
                        Quitar Filtro
                      </a>
                      <?php endif; ?>

                    </div>
                  </div>

                </form>
              </div>
            </div>
          </div>

          <?php if(isset($_GET['user'])): ?>
          <div class="row" style="padding-bottom: 60px">
            <?php if(!$nomina): ?>
              <div class="col s12">
                <ul class="tabs">
                  <li class="tab col m6"><a href="#data" onclick="changeValues()">Valores</a></li>
                  <li class="tab col m6"><a href="#gastos">Vales / Otros conceptos</a></li>
                </ul>
              </div>
            <?php endif ?>
            <div id="data" class="col s12">
              <div class="card">
                <div class="card-content">
                  <div class="row">
                    <div class="col s12 m12 l12">
                      <a href="javascript: history.go(-1)" class=" btn btn-light-indigo left invoice-print">
                        <i class="material-icons left">reply</i>
                        <span>Retroceder</span>
                      </a>

                      <a onclick="printDiv('invoice')" class=" btn btn-light-indigo right invoice-print">
                        <i class="material-icons right">local_printshop</i>
                        <span>Imprimir</span>
                      </a>
                      <a href="#viewCustomer" class="mr-1 btn btn-light-green right invoice-print  modal-trigger">
                        <i class="material-icons right">person_outline</i>
                        <span>Ver empleado</span>
                      </a>
                    </div>
                  </div>
                  <div class="card-content invoice-print-area" id="invoice">
                    <!-- header section -->
                    <div class="row">
                      <div class="col s12 m12">
                        <table class="responsive-table table-salary centered">
                          <thead>
                            <tr>
                              <th class="center">Dia</th>
                              <th class="center">Items</th>
                              <th class="center">Valor</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                              $salary = $nomina ? 0 : $user->salary;
                              $expenses = 0;
                            ?>
                            <?php if(!$nomina): ?>
                              <tr>
                                <td class="center"> <?= date('Y-m-d') ?></td>
                                <td class="center">Salario</td>
                                <td class="center">$ <?= number_format($user->salary, '2', ',', '.') ?></td>
                              </tr>
                            <?php else: ?>
                              <?php foreach($nomina->line_invoice as $line_invoice): ?>
                                <tr>
                                  <td class="center"> <?= date('Y-m-d') ?></td>
                                  <td class="center"><?= $line_invoice->description ?></td>
                                  <td class="center"><?= $line_invoice->payroll == 'true' ? '' : '-' ?>$ <?= number_format($line_invoice->price_amount, '2', ',', '.') ?></td>
                                </tr>
                                <?php
                                  $line_invoice->payroll == 'true' ? $salary += (double) $line_invoice->price_amount : $expenses += (double) $line_invoice->price_amount;
                                ?>
                              <?php endforeach ?>
                            <?php endif ?>
                            <?php
                                                      $payroll = true; ?>
                            <tr>
                              <th colspan="2" class="black-text right-align">Sub Total</th>
                              <th class="black-text center salaryParcial">$ <?= number_format($salary, '2', ',', '.') ?>
                              </th>
                            </tr>
                            <tr>
                              <th colspan="2" class="black-text right-align">Total Gastos</th>
                              <th class="black-text center expense">- $ <?= number_format($expenses, '2', ',', '.') ?></th>
                            </tr>
                            <tr>
                              <th colspan="2" class="black-text right-align">Total a pagar</th>
                              <th class="black-text center salary">$
                                <?= number_format(($nomina ? $nomina->payable_amount : $salary - $expenses), '2', ',', '.') ?></th>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col s12 m12 l12">
                      <form
                        action="<?= base_url('payrolls/payment/'.$_GET['user'].'/'.($user->salary - $expenses)) ?>"
                        method="post">
                        <a href="#pagoNomina" <?= (!$nomina && ($user->salary && $user->salary != 0))?'':'disabled' ?> class="btn btn-light-indigo right modal-trigger">Pagar</a>
                        <!-- <button type="submit" <?= ($payroll)?'':'disabled' ?> class="btn btn-light-indigo right">Pagar</button> -->
                      </form>
                    </div>
                  </div>


                </div>
              </div>
            </div>
            <?php if(!$nomina): ?>
              <div id="gastos" class="col s12">
                <div class="card">
                  <div class="card-content">
                    <div class="row">

                        <a onclick="bono()" class=" btn btn-light-indigo right invoice-print">
                          <i class="material-icons right">attach_money</i>
                          <span>Añadir concepto nómina</span>
                        </a>
                      </div>
                      <table class="centered striped">
                        <thead>
                          <tr>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Descripción</th>
                            <th>Valor total</th>
                            <th>Valor pagado</th>
                            <th>Acciones</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach($data as $item): ?>
                          <tr>
                            <td><?= $item->payment_due_date ?></td>
                            <td>Vale</td>
                            <td><?= $item->notes ?></td>
                            <td>$ <?= number_format($item->payable_amount, '2', ',', '.') ?></td>
                            <td>$ <?= number_format($item->valor_pagado, '2', ',', '.') ?></td>
                            <td><a href="javascript:void(0);" onclick="viewGastos(<?= $item->id ?>)"><i
                                  class="material-icons">attach_money</i></a></td>
                          </tr>
                          <?php endforeach; ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            <?php endif ?>
          </div>
        </div>
        <?php else: ?>
        <div class="card">
          <div class="card-content">
            <p>Por favor utilize los filtro que se encuentra en la parte superior para buscar la información de nomina
            </p>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
</div>

<?php if(isset($_GET['user'])): ?>
<div id="pagoNomina" class="modal" role="dialog" style="height:auto; width: 600px">
  <div class="modal-content">
    <h4 class="modal-title">Registrar pago</small>
    </h4>
    <form action="" id="formPagoNomina">
      <input type="hidden" id="salary" value="<?= $user->salary ?>">
      <input type="hidden" id="expense" value="0">
      <input type="hidden" id="file">
      <input type="hidden" id="filename">
      <input type="hidden" id="customes" value="<?= $_GET['user'] ?>">
      <input type="hidden" id="year" value="<?= $_GET['year'] ?>">
      <input type="hidden" id="date" value="<?= $_GET['date'] ?>">
      <div class="row">
        <div class="input-field col s12">
          <select class="browser-default" id="payment_method_id" name="payment_method_id">
            <option value="" disabled>Seleccione ...</option>
            <?php foreach ($paymentMethod as $item): ?>
            <option value="<?= $item->id ?>">[<?= $item->code ?>]
              - <?= ucfirst($item->name) ?> </option>
            <?php endforeach; ?>
          </select>
          <label for="payment_method_id" class="active">Medio de pago <span
              class="text-red red-text darken-1">*</span></label>
        </div>
        <!-- <div class="input-field col s12 m6">
          <select class="browser-default" id="sede_id" name="sede_id">
            <option value="">Seleccione ...</option>
            <?php foreach ($sedes as $item): ?>
            <option value="<?= $item->id ?>"><?= ucfirst($item->company) ?> </option>
            <?php endforeach; ?>
          </select>
          <label for="sede_id" class="active">Sede de pago <span
              class="text-red red-text darken-1">*</span></label>
        </div> -->
      </div>
      <div class="row">
        <div class="col s12 input-field">
          <textarea id="description" name="description" placeholder="Descripción" class="materialize-textarea validate"
            required></textarea>
          <label for="description">Descripción <span class="text-red red-text darken-1">*</span></label>
        </div>
      </div>
      <div class="row">
        <div class="file-field input-field col s12">
          <div class="btn indigo">
            <span>Soporte</span>
            <input type="file" onchange="changeFile(event);">
          </div>
          <div class="file-path-wrapper">
            <input class="file-path validate" type="text" placeholder="Subir soporte">
          </div>
        </div>
        <!-- <div class="col s12">
          <div class="file-field input-field">
            <div class="btn indigo">
              <span>Soporte</span>
              <input type="file" name="soport">
            </div>
            <div class="file-path-wrapper">
              <input class="file-path validate" type="text" name="nameFile" placeholder="Subir soporte" id="soporte">
            </div>
          </div>
        </div> -->
      </div>
    </form>
  </div>
  <div class="modal-footer">
    <a href="#!" class="modal-action modal-close waves-effect waves-red btn-flat mb-5 btn-light-indigo ">Cerrar</a>
    <button class="modal-action waves-effect waves-green btn indigo mb-5" onclick="pagoNomina()">
      Guardar
    </button>
  </div>
</div>

<div id="viewGastos" class="modal" role="dialog">
  <div class="modal-content">
    <h4 class="modal-title">Registrar pago</small>
    </h4>
    <div class="row">
      <table class="centered striped">
      </table>
    </div>
  </div>
  <div class="modal-footer">
    <a href="#!" class="modal-action modal-close waves-effect waves-red btn-flat mb-5 btn-light-indigo ">Cerrar</a>
    <button class="modal-action waves-effect waves-green btn indigo mb-5" id="btnSavePayroll" onclick="">
      Guardar
    </button>
  </div>
</div>
<div id="viewCustomer" class="modal" role="dialog" style="height:auto; width: 600px">
  <div class="modal-content">
    <h4 class="modal-title">Información del empleado</small>
    </h4>
    <div class="row">
      <div class="col s12 m12">
        <table class="striped centered">
          <tbody>
            <tr>
              <td>Nombre:</td>
              <td><?= $user->name ?></td>
            </tr>
            <tr>
              <td>Salario:</td>
              <td class="users-view-latest-activity">$
                <?= number_format($user->salary ? $user->salary : 0, '2', ',', '.') ?></td>
            </tr>
            <tr>
              <td>teléfono :</td>
              <td class="users-view-verified"><?= $user->phone ?></td>
            </tr>
            <tr>
              <td>Dirección:</td>
              <td class="users-view-role"><?= $user->address ?></td>
            </tr>
            <tr>
              <td>Estado:</td>
              <td><span class=" users-view-status chip green lighten-5 green-text"><?= $user->status ?></span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="modal-footer">
    <a href="#!" class="modal-action modal-close waves-effect waves-red btn-flat mb-5 btn-light-indigo ">Cerrar</a>
  </div>
</div>
<?php endif ?>

<?= $this->endSection() ?>



<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?= base_url('/js/advance-ui-modals.js') ?>"></script>
<script src="<?= base_url('/js/shepherd.min.js') ?>"></script>
<script src="<?= base_url('/js/vue.js') ?>"></script>
<script src="<?= base_url('/assets/js/sweetalert.min.js') ?>"></script>
<script src="<?= base_url('/js/sprint.js') ?>"></script>

<script src="<?= base_url('/js/ui-alerts.js') ?>"></script>
<script src="<?= base_url('/js/views/invoice1.js') ?>"></script>
<script src="<?= base_url('/assets/js/new_scripts/funciones.js') ?>"></script>
<script src="<?= base_url('/js/nomina/payrolls.js') ?>"></script>
<script>
const gastos = <?= json_encode($data) ?>;
const user = <?= json_encode($user) ?>;
const productOtros = <?= json_encode($productOtros) ?>;
$(document).ready(function() {
  $('.datepicker').datepicker();
  $(".select2").select2({
    dropdownAutoWidth: true,
    width: '100%'
  });
});

function printDiv(nombreDiv) {
  var contenido = document.getElementById(nombreDiv).innerHTML;
  var contenidoOriginal = document.body.innerHTML;

  document.body.innerHTML = contenido;

  window.print();

  document.body.innerHTML = contenidoOriginal;
}
</script>
<?= $this->endSection() ?>