<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?> Periodos <?= $this->endSection() ?>
<?= $this->section('styles') ?>
    <link rel="stylesheet" href="<?= base_url('css/views/periods.css') ?>">
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
                    <div class="col s10 m6 l6 breadcrumbs-left">
                        <h5 class="breadcrumbs-title mt-0 mb-0 display-inline hide-on-small-and-down ">
                            <span>
                                Periodos de Nomina Ajuste Electrónica
                                <a class="btn btn-small light-blue darken-1 step-1 help active-red" style="padding-left: 10px ; padding-right: 10px;">Ayuda</a>
                            </span>
                        </h5>
                    </div>
                    <div class="col s12 m6 l6">
                        <!--<button  data-target="create" class="btn indigo right modal-trigger mr-2 btn-small step-2" style="padding-left:5px; padding-right:10px;">
                            <i class="material-icons left">add </i> Añadir
                        </button>-->
    
                        
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
                                <div class="col s12">
                                <?php if(count($search) != 0): ?>
                                    <a href="<?= base_url('periods') ?>" class="btn right btn-light-red btn-small ml-1"   style="padding-left: 10px;padding-right: 10px; ">
                                        <i class="material-icons left">close</i>
                                        Quitar Filtro
                                    </a>
                                <?php endif; ?>
                                    <button data-target="filter"  class="btn right btn-small modal-trigger btn-light-indigo step-5" style="padding-left:5px; padding-right:10px;">
                                        <i class="material-icons left">filter_list </i> Filtrar
                                    </button>
                                </div>
                                <div class="col s12">
                                    <div style="overflow-x: auto;">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>Mes</th>
                                                    <th class="center">Empleados</th>
                                                    <th class="center">Emitidos</th>
                                                    <th class="center">Por Emitir</th>
                                                    <th class="center">Rechazado</th>
                                                    <th class="center step-4" width="150px">Estado</th>
                                                    <th class="center step-3">Acciones<td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php  foreach($periods as $item): ?>
                                                <tr>
                                                    <td class="indigo-text"><?= $item->month.' '.$item->year ?></td>
                                                    <td class="center">
                                                        <?= $item->workers ?>
                                                    </td>
                                                    <td class="center">
                                                        <?= $item->emiter ?>
                                                    </td>
                                                    <td class="center">
                                                        <?= $item->errors + $item->for_emiter ?>
                                                    </td>
                                                    <td class="center">
                                                        <?= $item->errors ?>
                                                    </td>
                                                    <td class="center">
                                                        <?php if($item->emiter <  $item->workers): ?>
                                                            <span class="new badge orange" data-badge-caption="En Proceso"></span>
                                                        <?php else: ?>
                                                            <span class="new badge green" data-badge-caption="Terminado"></span>
                                                        <?php endif; ?> 
                                                    </td>
                                                    <td class="center">
                                                        <div class="btn-group">
                                                            <a href="<?= base_url('period_adjusts/'. $item->id) ?>" class="btn btn-small yellow darken-2 tooltipped" data-tooltip="Ver Nomina"><i class="material-icons">remove_red_eye</i> </a>
                                                            <?php if($item->emiter == 0): ?>
                                                                <form action="<?= base_url('period_adjusts/delete/'. $item->id) ?>" method="post" style="">
                                                                    <button  class="btn btn-small red  tooltipped" data-tooltip="Eliminar Nomina"><i class="material-icons">delete</i> </button>
                                                                </form>
                                                            <?php endif ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php if(count($periods) == 0): ?>
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
    </div>
</div>


<form action="" method="GET">
    <div id="filter" class="modal" role="dialog" style="height:auto; width: 600px">
        <div class="modal-content">
            <h5>Filtrar Empleado</h5>
            <div class="row">
                <div class="col s12 input-field">
                    <label for="month" class="active">Periodo</label>
                    <select type="text" name="period_id"  id="month" class="browser-default validate select2" required>
                        <?php  foreach($select as $item): ?>
                            <option value="<?=  $item->id ?>"  ><?= $item->month ?> - <?=  $item->year ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-action modal-close waves-effect  btn-flat btn-light-indigo ">Cerrar</a>
            <button class="btn indigo">Buscar</button>

        </div>
    </div>
</form>


<form action="<?= base_url('periods') ?>" method="post">
    <div id="create" class="modal" role="dialog" style="height:auto; width: 600px">
        <div class="modal-content">
            <h4 class="modal-title">Nuevo Periodo de Nomina</h4>
            <div class="row">
                <div class="col s12 mb-2 input-field">
                    <select type="text" name="period_id"  id="month" class="browser-default validate select2" required>
                        <?php  foreach($select as $item): ?>
                        <option value="<?=  $item->id ?>"   <?=  in_array($item->id, $periodDue) ?  'disabled': ''?>><?= $item->month ?> - <?=  $item->year ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label for="period_id" class="active">Periodo<span class="purple-text">*</span></label>
                </div>

                <div class="col s12 ">
                <div class="button">
                        <button type="button" class="btn btn-samll indigo addDate right" style="padding-left: 10px;padding-right:10px; margin-top:20px;"> + </button>
                    </div>
                    <div class="input input-field">
                        <input type="date" id="payment_dates" class="validate" name="payments" placeholder="Fechas de pago">
                        <label for="payment_dates" class="active">Fechas de pago</label>
                        <input type="hidden" name="payment_dates[]" class="payment_dates">
                    </div>
              
                </div>
                <div class="col s12">
                    <ul class="collection addDates">
                    </ul>
                </div>
            </div>
        </div>
        <div class="modal-footer" >
            <a href="#!" class="modal-action modal-close waves-effect  btn-flat btn-light-purple ">Cerrar</a>
            <button class="modal-action waves-effect waves-green btn purple send" disabled>Guardar</button>
        </div>
    </div>
</form>



</div>


<?= $this->endSection() ?>

<?= $this->section('scripts')?>
    <script src="<?= base_url('js/advance-ui-modals.js') ?>"></script>
    <script src="<?= base_url('/js/shepherd.min.js') ?>"></script>
    <script src="<?= base_url('js/views/periods.js') ?>"></script>
<?= $this->endSection() ?>
