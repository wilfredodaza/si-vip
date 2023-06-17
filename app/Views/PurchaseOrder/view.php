<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?> previsualización <?= $this->endSection() ?>
<?= $this->section('content') ?>
<div id="main">
    <div class="row">
        <div class="breadcrumbs-inline pt-3 pb-1" id="breadcrumbs-wrapper">
            <div class="container">
                <div class="row">
                    <div class="col s12">
                    </div>
                    <div class="col s10 m12 l12 breadcrumbs-left">
                        <h5 class="breadcrumbs-title mt-0 mb-0 display-inline hide-on-small-and-down ">
                            <span>
                               Previsualización
                            </span>
                        </h5>
                        <ol class="breadcrumbs mb-0">
                            <li class="breadcrumb-item"><a href="<?= base_url() ?>/home">Home</a></li>
                            <li class="breadcrumb-item active"><a href="#">previsualización </a></li>
                        </ol>
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
                                <div class="col s12 m12 l12">
                                    <a href="javascript: history.go(-1)"
                                       class=" btn btn-light-indigo left invoice-print">
                                        <i class="material-icons left">reply</i>
                                        <span>Retroceder</span>
                                    </a>
                                    <a href="<?= base_url() ?>/reports/view/<?= $id ?>" target="_blank"
                                       class=" btn btn-light-indigo right invoice-print">
                                        <i class="material-icons right">local_printshop</i>
                                        <span>Imprimir</span>
                                    </a>
                                </div>
                            </div>
                            <div class="card-content invoice-print-area" id="invoice">
                                <!-- header section -->
                                <div class="row invoice-date-number">
                                    <div class="col xl4 s12">
                                        <span class="invoice-number mr-1"><?= $document->nameDocument ?> #</span>
                                        <span><?= ($document->resolution ?? $document->id) ?></span>
                                    </div>
                                    <div class="col xl8 s12">
                                        <div class="invoice-date display-flex align-items-center flex-wrap">
                                            <div class="mr-3">
                                                <small>Fecha de emisión: </small>
                                                <span><?= ($document->issue_date ?? '') ?></span>
                                            </div>
                                            <div>
                                                <small>Fecha de vencimiento: </small>
                                                <span><?= ($document->payment_due_date ?? '') ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- logo and title -->
                                <div class="row mt-3 invoice-logo-title">
                                    <div class="col m6 s12 display-flex invoice-logo mt-1 push-m6">

                                    </div>
                                    <div class="col m6 s12 pull-m6">
                                        <h4 class="indigo-text"><?= $document->nameDocument ?></h4>
                                        <span></span>
                                    </div>
                                </div>
                                <div class="divider mb-3 mt-3"></div>
                                <!-- invoice address and contact -->
                                <div class="row invoice-info">
                                    <div class="col m6 s6">
                                        <h6 class="invoice-from">Cliente</h6>
                                        <div class="invoice-address">
                                            <span>Nombre : <?= $document->name ?></span>
                                        </div>
                                        <div class="invoice-address">
                                            <span>Tipo documento: <?= $document->typeDocumentIdentification ?></span>
                                        </div>
                                        <div class="invoice-address">
                                            <span>Identificación: <?= $document->identification ?></span>
                                        </div>
                                        <div class="invoice-address">
                                            <span>Teléfono: <?= ($document->phone ?? 'Sin teléfono') ?></span>
                                        </div>
                                    </div>
                                    <div class="col m6 s6">
                                        <div class="divider show-on-small hide-on-med-and-up mb-3"></div>
                                        <h6 class="invoice-to"><br></h6>
                                        <div class="invoice-address">
                                            <span>Correo: <?= ($document->email ?? 'Sin correo') ?></span>
                                        </div>
                                        <div class="invoice-address">
                                            <span>Dirección: <?= ($document->address) ?></span>
                                        </div>
                                        <div class="invoice-address">
                                            <span></span>
                                        </div>
                                        <div class="invoice-address">
                                            <span></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="divider mb-3 mt-3"></div>
                                <!-- product details table-->
                                <div class="invoice-product-details">
                                    <table class="striped responsive-table">
                                        <thead>
                                        <tr>
                                            <th>Producto</th>
                                            <th>Valor</th>
                                            <th>Cantidad</th>
                                            <th>Entregada</th>
                                            <th>Faltante</th>
                                            <th class="right-align">Precio</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        <?php
                                        $discount = 0;
                                        foreach ($lineDocuments as $item):
                                            $discount += $item->discount_amount;
                                            ?>
                                            <tr>
                                                <td><?= $item->name ?></td>
                                                <td>$ <?= number_format($item->price_amount, '2', ',', '.') ?></td>
                                                <td><?= $item->quantity ?></td>
                                                <td><?= ($quantities[$item->products_id] ?? 0) ?></td>
                                                <td><?= ($item->quantity - ($quantities[$item->products_id] ?? 0) )?></td>
                                                <td class="indigo-text right-align">
                                                    $ <?= number_format($item->line_extension_amount, '2', ',', '.') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- invoice subtotal -->
                                <div class="divider mt-3 mb-3"></div>
                                <div class="invoice-subtotal">
                                    <div class="row">
                                        <div class="col m5 s12">
                                            <p><?= $document->notes ?></p>
                                        </div>
                                        <div class="col xl4 m7 s12 offset-xl3">
                                            <ul>
                                                <li class="display-flex justify-content-between">
                                                    <span class="invoice-subtotal-title">Subtotal</span>
                                                    <h6 class="invoice-subtotal-value">
                                                        $ <?= number_format($document->line_extesion_amount, '2', ',', '.') ?></h6>
                                                </li>
                                                <li class="display-flex justify-content-between">
                                                    <span class="invoice-subtotal-title">Descuentos</span>
                                                    <h6 class="invoice-subtotal-value">-
                                                        $ <?= number_format($discount, '2', ',', '.') ?></h6>
                                                </li>
                                                <li class="display-flex justify-content-between">
                                                    <span class="invoice-subtotal-title">Iva</span>
                                                    <h6 class="invoice-subtotal-value">
                                                        $ <?= number_format(($document->tax_inclusive_amount - $document->tax_exclusive_amount), '2', ',', '.') ?></h6>
                                                </li>
                                                <li class="display-flex justify-content-between">
                                                    <span class="invoice-subtotal-title">Retenciones</span>
                                                    <h6 class="invoice-subtotal-value">- <?= number_format($taxTotal, '2', ',', '.') ?></h6>
                                                </li>
                                                <li class="divider mt-2 mb-2"></li>
                                                <li class="display-flex justify-content-between">
                                                    <span class="invoice-subtotal-title">Total</span>
                                                    <h6 class="invoice-subtotal-value">
                                                        $ <?= number_format(($document->payable_amount - $taxTotal), '2', ',', '.') ?></h6>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="divider"></div>
                            <div class="row center">
                                <h5>Seguimiento Remisiones creadas</h5>
                            </div>
                            <div class="card-content">
                                <table>
                                    <thead>
                                    <tr>
                                        <th class="center">#</th>
                                        <th class="center">Fecha</th>
                                        <th class="center">Cantidad</th>
                                        <th class="center">Sede</th>
                                        <th class="center">Valor</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($remissions as $key): ?>
                                    <tr>
                                        <td class="center"><?= $key->resolution ?></td>
                                        <td class="center"><?= $key->created_at ?></td>
                                        <td class="center"><?= $key->quantity ?></td>
                                        <td class="center"><?= $key->company ?></td>
                                        <td class="center">$ <?= number_format($key->payable_amount, '2', ',', '.') ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
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
<script src="<?= base_url('/assets/js/sweetalert.min.js') ?>"></script>
<script src="<?= base_url('/js/sprint.js') ?>"></script>

<script src="<?= base_url('/js/ui-alerts.js') ?>"></script>
<script src="<?= base_url('/js/views/invoice1.js') ?>"></script>
<script>
    function printDiv(nombreDiv) {
        var contenido= document.getElementById(nombreDiv).innerHTML;
        var contenidoOriginal= document.body.innerHTML;

        document.body.innerHTML = contenido;

        window.print();

        document.body.innerHTML = contenidoOriginal;
    }

</script>
<?= $this->endSection() ?>




