<body>
<table style="width:100%;">
    <tr>
        <td style=" text-align:justify;">
            <span style="font-size: 9pt;"><?= $invoice->notes ?></span>
        </td>
    </tr>
</table>

<br><br>
<hr>
<div class="row">
    <div class="col s12 m12 l12">
        <table   style="width:100%;">
            <thead>
            <tr>
                <th class="text-right">Código</th>
                <th class="text-right">Producto</th>
                <th class="text-right">Valor</th>
                <th class="text-right">Cantidad</th>
                <th class="text-right">Total</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $discount = 0;
            foreach ($withholding as $item):
                $discount += $item->discount_amount;
                ?>
                <tr>
                    <td class="text-right"><?= $item->code ?></td>
                    <td class="text-right"><?= $item->description ?></td>
                    <td class="text-right">$ <?= number_format($item->price_amount, '2', ',', '.') ?></td>
                    <td class="text-right"><?= $item->quantity ?></td>
                    <td class="indigo-text right-align text-right">
                        $ <?= number_format($item->line_extension_amount, '2', ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="row">
    <div class="col s12 m12 l12">
        <br><br>
        <table
               style="padding-bottom: 0px; margin-bottom: 0px; width:100%; margin-left: 70%">
            <thead>
            <tr>
                <th class="text-right">Concepto</th>
                <th class="text-right">Valor</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td class="text-right">Base:</td>
                <td class="text-right">$ <?= number_format($invoice->line_extesion_amount, '2', ',', '.') ?></td>
            </tr>
            <tr>
                <td class="text-right">Descuento:</td>
                <td class="text-right">$ <?= number_format($discount, '2', ',', '.') ?></td>
            </tr>
            <?php if($invoice->type_documents_id != 108 && $invoice->type_documents_id != 107): ?>
            <tr>
                <td class="text-right">Iva:</td>
                <td class="text-right">
                    $ <?= number_format(($invoice->tax_inclusive_amount - $invoice->tax_exclusive_amount), '2', ',', '.') ?></td>
            </tr>
            <tr>
                <td class="text-right">Retenciones:</td>
                <td class="text-right">$ <?= number_format($taxTotal, '2', ',', '.') ?></td>
            </tr>
            <?php endif; ?>
            <tr>
                <td class="text-right">Total:</td>
                <td class="text-right">
                    $ <?= number_format(($invoice->payable_amount - $taxTotal), '2', ',', '.') ?></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>


<!--<div class="summarys">
    <div id="note">
        <p style="font-size: 12px;"><br>
            <strong>SON: </strong> <?= convertir($invoice->payable_amount - $taxTotal) ?>
        </p>
    </div>
</div>-->
<hr>
<br><br>
<br><br>
<?php if($invoice->type_documents_id != 108): ?>
    <div style="text-align:center;">
        <hr style="width: 30%">
        <span style="font-weight:bold;">Firma: </span>
    </div>
<?php endif; ?>


<?php if ($invoice->resolution != null): ?>
    <!--<div id="footer">
        <p id='mi-texto'>
            Resolución de Documento de soporte No.
            de , Rango - Vigencia
            Desde: Hasta: <br>
            <span>Elaborado  y enviado electrónicamente por MiFacturaLegal.com.</span>
        </p>
    </div>-->
<?php endif; ?>

</body>
