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
                        </h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col s12">
            <div class="container">
                <div class="section">
                    <div class="card">
                        <?php if($payrollCount > 0): ?>
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
                                            <td><?= $item->name ?></td>
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
                        <?php else: ?>
                            <div class="card-content">
                            <table>
                                <thead>
                                    <tr>
                                        <td>Periodo de Nomina</td>
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
                                        <td class="center">$<?=  number_format($item->accrued, '2', ',', '.') ?></td>
                                        <td class="center">$<?=  number_format($item->deduction, '2', ',', '.') ?></td>
                                        <td class="center">
                                            $<?=  number_format($item->accrued - $item->deduction, '2', ',', '.') ?>
                                        </td>
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
                                                <?php  if($item->invoice_status_id != '12' && $item->invoice_status_id != '15'): ?>
                                                <a href="<?= base_url('payroll/download/'. previsualizationPdf($item->invoice_id)) ?>"
                                                    class="btn btn-small pink darken-1">
                                                    <i class="material-icons">insert_drive_file</i>
                                                </a>
                                                <?php  endif; ?>
                                                <?php  if($item->invoice_status_id != '13' && $item->invoice_status_id != '12' && $item->invoice_status_id != '15'): ?>
                                                <a href="<?= base_url('payroll/xml/'. previsualizationPdf($item->invoice_id)) ?>"
                                                    class="btn btn-small">
                                                    <i class="material-icons">attach_file</i>
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
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
