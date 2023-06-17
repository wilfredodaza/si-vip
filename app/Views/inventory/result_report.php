<?= $this->extend('layouts/main') ?>


<?= $this->section('title') ?> Facturación <?= $this->endSection() ?>

<?= $this->section('content') ?>
<div id="main">
    <div class="row">
        <div class="breadcrumbs-inline pt-3 pb-1" id="breadcrumbs-wrapper">
            <div class="container">
                <div class="row">
                    <div class="col s12">
                        <?= $this->include('layouts/alerts') ?>
                        <?= $this->include('layouts/notification') ?>
                    </div>
                    <div class="col s12 m12 l12 breadcrumbs-left">
                        <h5 class="breadcrumbs-title mt-0 mb-0 display-inline hide-on-small-and-down ">
                            <span>
                               Reporte de Operaciones
                            </span>
                        </h5>
                        <!--<ol class="breadcrumbs mb-0">
                            <li class="breadcrumb-item"><a href="<?= base_url() ?>/home">Home</a></li>
                            <li class="breadcrumb-item active"><a href="#">Inventary</a></li>
                        </ol>-->
                        <a href="<?= base_url() ?>/inventory/report"
                           class="btn btn-small purple right">Regresar
                            <i class="material-icons left">arrow_back</i>
                        </a>

                    </div>
                </div>
            </div>
        </div>
        <div class="col s12">
            <div class="container">
                <div class="section">
                    <?php if ($vista == 'result'): ?>
                        <div class="card">
                            <div class="card-content">
                                <table class="table-responsive">
                                    <thead>
                                    <tr>
                                        <th class="center">Fecha</th>
                                        <th class="center">Estatus</th>
                                        <th class="center">Proveedor</th>
                                        <th class="center">Tipo Operación</th>
                                        <th class="center">Cantidad</th>
                                        <th class="center step-4">Total</th>
                                        <th class="center step-3 ">Acciones</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $i = 0;
                                    foreach ($data as $item): ?>
                                        <tr>
                                            <td class="center"><?= $item['issue_date'] ?>
                                            <td class="center"><?= $item['status_wallet'] ?></td>
                                            <td class="center"><?= $item['customer_name'] ?></td>
                                            <td class="center"><?= $item['type_documents_name'] ?></td>
                                            <td class="center"><?= $quantity[$i] ?></td>
                                            <td class="center" width="100px">
                                                $ <?= number_format($item['payable_amount'], '0', '.', '.') ?>
                                            </td>
                                            <td>
                                                <a href="<?= base_url() ?>/inventory/details/<?= $item['invoices_id'] ?>"
                                                   class="btn btn-small  light-blue send tooltipped" data-position="top"
                                                   data-tooltip="Ver detalles">
                                                    <i class="material-icons">remove_red_eye</i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php $i++;
                                    endforeach; ?>
                                    </tbody>
                                </table>
                                <?php if (count($data) == 0): ?>
                                    <p class="center red-text pt-1">No hay ningún elemento.</p>
                                <?php endif ?>
                                <?= $pager->links(); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php
                    $rete = 0;
                    $iva = 0;
                    $discount = 0;
                    $total = 0;
                    $subtotal = 0;
                    if ($vista == 'details'): ?>
                    <div class="card">
                        <div class="card-content">
                            <table class="table-responsive">
                                <thead>
                                <tr>
                                    <th class="center">Producto</th>
                                    <th class="center">Código</th>
                                    <th class="center">Cantidad</th>
                                    <th class="center">Valor Unidad</th>
                                    <th class="center">Descuento</th>
                                    <th class="center step-4">Iva</th>
                                    <th class="center step-3 ">Total</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($data as $item): ?>
                                    <tr>
                                        <td class="center"><?= $item->name ?>
                                        <td class="center"><?= $item->code ?></td>
                                        <td class="center"><?= $item->quantity ?></td>
                                        <td class="center">$ <?= number_format($item->valor, '0', '.', '.') ?></td>
                                        <td class="center">$ <?= ($item->discount_amount != null || $item->discount_amount != '')?number_format($item->discount_amount, '0', '.', '.'):0 ?></td>
                                        <td class="center">$ <?= number_format($item->iva_product, '0', '.', '.') ?></td>
                                        <td class="center" width="100px">
                                            $ <?= number_format($item->line_extension_amount, '0', '.', '.') ?>
                                        </td>
                                    </tr>
                                <?php
                                $rete += $item->retenciones;
                                $iva += $item->iva_product;
                                $discount += (int)$item->discount_amount;
                                $subtotal += $item->line_extension_amount;
                                $total += $item->price_amount;
                                endforeach; ?>
                                </tbody>
                            </table>
                            <br>
                            <div class="row">
                                <div class="col s12 m6 l6">
                                    <h6>Descripcion</h6>
                                    <p><?= (empty($invoice[0]->notes))?$invoice[0]->notes:'No se encuentran notas agregadas'; ?></p>
                                </div>
                                <div class="col s12 m6 l6">
                                    <h6>Totales</h6>
                                    <table class="table-responsive">
                                        <tbody>
                                        <tr>
                                            <th>Subtotal:</th>
                                            <td>$ <?= number_format($subtotal) ?></td>
                                        </tr>
                                        <tr>
                                            <th>- Descuento:</th>
                                            <td>$ <?= number_format($discount) ?></td>
                                        </tr>
                                        <tr>
                                            <th>BASE GRAVABLE:</th>
                                            <td>$ <?php $bgravable =$subtotal-$discount; echo  number_format($bgravable) ?></td>
                                        </tr>
                                        <tr>
                                            <th>+ Impuesto</th>
                                            <td>$ <?= number_format($iva) ?></td>
                                        </tr>
                                        <tr>
                                            <th>- Retenciones</th>
                                            <td>$ <?= number_format($rete) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Total:</th>
                                            <td>$ <?= number_format($bgravable+$iva-$rete) ?></td>
                                        </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</div>


<?= $this->endSection() ?>



<?= $this->section('scripts') ?>
<script src="<?= base_url('/js/advance-ui-modals.js') ?>"></script>
<script src="<?= base_url('/js/shepherd.min.js') ?>"></script>
<script src="<?= base_url('/js/vue.js') ?>"></script>
<script src="<?= base_url('/js/views/invoice.js') ?>"></script>
<script src="<?= base_url('/js/ui-alerts.js') ?>"></script>
<?= $this->endSection() ?>
