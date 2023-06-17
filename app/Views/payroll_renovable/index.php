<?= $this->extend('layouts/main') ?>


<?= $this->section('title') ?> Desprendibles de Pago  <?= $this->endSection() ?>

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
				<?php if(session('user')->role_id != 10): ?>
                            <a  href="#modal1" class="btn btn-light-indigo right  btn-small modal-trigger"  data-target="modal1" style="padding-left:5px; padding-right:10px;">
                                <i class="material-icons left">layers</i> Consolidar
                            </a>
				<?php endif; ?>

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
                                    <th>Periodo</th>
                                    <th>Documento</th>
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
                                      <td><?= $item->month.'-'.$item->year ?></td>
                                      <td><?= $item->name ?></td>
                                      <td class="center">$ <?= number_format($item->accrueds, '2', ',', '.') ?></td>
                                      <td class="center">$ <?= number_format($item->deductions, '2', ',', '.')  ?></td>
                                      <td class="center">$ <?= number_format($item->accrueds - $item->deductions, '2', ',', '.')  ?></td>
                                      <td class="center">
                                          <?php if($item->total == $item->emitir and $item->total != $item->consolidado): ?>
                                            <span class="badge new  pink darken-1 " style="width:140px;" data-badge-caption="En espera" ></span><br>
                                          <?php elseif($item->total == $item->consolidado): ?>
                                            <span class="badge new  yellow darken-2 " style="width:140px;" data-badge-caption="Consolidado" ></span><br>
                                          <?php elseif($item->total == $item->por_emitir_DIAN): ?>
                                            <span class="badge new green " style="width:140px;" data-badge-caption="Enviado a la DIAN" ></span>
                                          <?php endif; ?>
                                      </td>
                                      <td class="center">
                                          <div class="btn-group">
                                              <a href="<?= base_url('payroll_removable/'.$item->id ); ?>" class="btn btn-small yellow darken-2">
                                                  <i class="material-icons">remove_red_eye</i>
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
    <form action="<?= base_url('payroll_removable/consolidate') ?>" method="post">
        <div id="modal1" class="modal">
            <div class="modal-content">
                <h6>Periodo de Nómina</h6>
    
                    <div class="row ">
                        <div class="col s12">
                            <label>Periodo</label>
                            <select class="browser-default" name="period_id">
                                <option value="" disabled selected>Selecciona la opción</option>
                                <?php foreach($periods as $period): ?>
                                    <option value="<?= $period->id ?>"><?= $period->month.' - '.$period->year?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

            </div>
            <div class="modal-footer">
                <a href="#!" class="modal-close waves-effect waves-red btn-flat">Cerrar</a>
                <button class=" waves-effect btn indigo  waves-green ">Consolidar</button>
            </div>
        </div>
    </form>
</div>


<?= $this->endSection() ?>

<?php $this->section('scripts') ?>
<script>
    $(function() {
        $('.modal').modal();
        $('#modal1').modal('open');
        $('#modal1').modal('close');
    });
</script>
<?php $this->endSection() ?>
