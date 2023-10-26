<table style="font-size: 8px !important; width:100%">
    <tr>
        <td class="vertical-align-top" style="width: 33%;">
            <table style="font-size: 8px !important;">
                <tbody>
                    <?php if(
                        ($invoice->type_documents_id != 118 && $invoice->type_documents_id != 115) || ($invoice->type_documents_id == 118 && $invoice->name)
                    ): ?>
                    <tr>
                        <td><span style="font-size: 8pt;"> <b>Nombre: </span></b></td>
                        <td><span style="font-size: 8pt;"><?= $invoice->name ?></span></td>
                    </tr>
                    <tr>
                        <td><span style="font-size: 8pt;"><b>Telefono:</span></b></td>
                        <td><span style="font-size: 8pt;"><?= $invoice->phone ?></span></td>
                    </tr>
                    <?php endif ?>
                    <tr>
                        <td><span style="font-size: 8pt;"><b>Forma de pago:</span></b></td>
                        <td><span style="font-size: 8pt;"><?= $invoice->payment_forms_name ?></span></td>
                    </tr>
                    <tr>
                        <td><span style="font-size: 8pt;"> <b>Metodo de pago: </b></span></td>
                        <td><?= $invoice->accounting_account_name ?></td>
                    </tr>
                </tbody>
            </table>
        </td>
        <td class="vertical-align-top" style="width: 33%;">
            <table width="100%">
                <tbody>
                    <?php if(
                        ($invoice->type_documents_id != 118 && $invoice->type_documents_id != 115) || ($invoice->type_documents_id == 118 && $invoice->name)
                    ): ?>
                    <tr>
                        <td><span style="font-size: 8pt;"><b>Dirección:</b></span></td>
                        <td><span style="font-size: 8pt;"><?= $invoice->address ?></span></td>
                    </tr>
                    <tr>
                        <td><span style="font-size: 8pt;"><b>Ciudad:</span></b></td>
                        <td><span style="font-size: 8pt;"><?= $invoice->municipio ?></span></td>
                    </tr>
                    <?php else: ?>
                        <tr>
                            <td><span style="font-size: 8pt;"><b><?= $invoice->nameDocument ?>:</b></span></td>
                            <td><span style="font-size: 8pt;"> # <?= $invoice->resolution ?></span></td>
                        </tr>
                    <?php endif ?>
                    <tr>
                        <td><span style="font-size: 8pt;"><b>Responsable:</span></b></td>
                        <td><span style="font-size: 8pt;"><?= $invoice->responsable ?></span></td>
                    </tr>
                </tbody>
            </table>
        </td>
        <td class="vertical-align-top" style="width: 33%;">
            <table width="100%">
                <tbody>
                    <?php if(
                        ($invoice->type_documents_id != 118 && $invoice->type_documents_id != 115) || ($invoice->type_documents_id == 118 && $invoice->name)
                    ): ?>
                        <tr>
                            <td><span style="font-size: 8pt;"><b><?= $invoice->nameDocument ?>:</b></span></td>
                            <td><span style="font-size: 8pt;"> # <?= $invoice->resolution ?></span></td>
                        </tr>
                    <?php endif ?>
                    <tr>
                        <td><span style="font-size: 8pt;"><b>Fecha de operación:</span></b></td>
                        <td><span style="font-size: 8pt;"><?= substr($invoice->created_at ,0, 10)  ?></span></td>
                    </tr>
                    <tr>
                        <td><span style="font-size: 8pt;"><b>Hora de operación:</span></b></td>
                        <td><span style="font-size: 8pt;"> <?= substr($invoice->created_at ,10, 17) ?></span></td>
                    </tr>
                </tbody>
            </table>
        </td>
    </tr>
</table>
<hr>
<div class="row">
    <div class="col s12 m12 l12">
        <table   style="width:100%;">
            <thead>
            <tr>
                <th class="text-center">Código</th>
                <th class="text-center">Producto</th>
                <th class="text-center">Descripción</th>
                <th class="text-center">Valor</th>
                <th class="text-center">Cantidad</th>
                <th class="text-center">Total</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $discount = 0;
            foreach ($withholding as $item):
                $discount += $item->discount_amount;
                ?>
                <tr>
                    <td class="text-center"><?= $item->code ?></td>
                    <td class="text-center"><?= $item->name ?></td>
                    <td class="text-center"><?= $item->description ?></td>
                    <td class="text-center">$ <?= number_format($item->price_amount, '2', ',', '.') ?></td>
                    <td class="text-center"><?= $item->quantity ?></td>
                    <td class="indigo-text center-align text-center">
                        $ <?= number_format($item->line_extension_amount, '2', ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<br><br>

<div class="row">
    <div class="col s12">
        <table style="width:100%;">
            <tbody>
                <?php if($newVersion): ?>
                    <tr>
                        <td style="width:100%;">
                            <table>
                                <tr>
                                    <td style=" text-align:justify;">
                                        <span style="font-size: 9pt;"><?= $invoice->invoice_status_id == 28 ? 'Motivo de la anulacion: ' : ''?> <?= $invoice->notes ?></span><br>
                                        <?php if($invoice->invoice_status_id == 28): ?>
                                            <span style="font-size: 9pt;">Remisión anulada por: <?= $user->name ?></span>
                                        <?php endif ?>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:100%;">
                            <table style="padding-bottom: 0px; margin-bottom: 0px; width:100%;">
                                <thead>
                                    <tr>
                                        <th class="text-right">Concepto</th>
                                        <th class="text-right">Valor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($invoice->type_documents_id != 118 && $invoice->type_documents_id != 120 && $invoice->type_documents_id != 115): ?>
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
                                    <?php elseif($invoice->type_documents_id == 120): ?>
                                        <tr>
                                            <td class="text-right">Salario:</td>
                                            <td class="text-right">$ <?= number_format($invoice->line_extesion_amount, '2', ',', '.') ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-right">Deducciones:</td>
                                            <td class="text-right">$ <?= number_format($invoice->tax_exclusive_amount, '2', ',', '.') ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-right">Devengado:</td>
                                            <td class="text-right">$ <?= number_format($invoice->tax_inclusive_amount, '2', ',', '.') ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-right">Total:</td>
                                            <td class="text-right">
                                                $ <?= number_format((($invoice->line_extesion_amount + $invoice->tax_inclusive_amount) - $invoice->tax_exclusive_amount), '2', ',', '.') ?></td>
                                        </tr>
                                    <?php else: ?>
                                        <tr>
                                            <td class="text-right">Total:</td>
                                            <td class="text-right">
                                                $ <?= number_format(($invoice->payable_amount), '2', ',', '.') ?></td>
                                        </tr>
                                    <?php endif ?>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                <?php else: ?>
                    <tr>
                        <td style="width:70%;">
                            <table>
                                <tr>
                                    <td style=" text-align:justify;">
                                        <span style="font-size: 9pt;"><?= $invoice->invoice_status_id == 28 ? 'Motivo de la anulacion: ' : ''?> <?= $invoice->notes ?></span><br>
                                        <?php if($invoice->invoice_status_id == 28): ?>
                                            <span style="font-size: 9pt;">Remisión anulada por: <?= $user->name ?></span>
                                        <?php endif ?>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td style="width:30%;">
                            <table style="padding-bottom: 0px; margin-bottom: 0px; width:100%;">
                                <thead>
                                    <tr>
                                        <th class="text-right">Concepto</th>
                                        <th class="text-right">Valor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($invoice->type_documents_id != 118 && $invoice->type_documents_id != 120 && $invoice->type_documents_id != 115): ?>
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
                                    <?php elseif($invoice->type_documents_id == 120): ?>
                                        <tr>
                                            <td class="text-right">Salario:</td>
                                            <td class="text-right">$ <?= number_format($invoice->line_extesion_amount, '2', ',', '.') ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-right">Deducciones:</td>
                                            <td class="text-right">$ <?= number_format($invoice->tax_exclusive_amount, '2', ',', '.') ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-right">Devengado:</td>
                                            <td class="text-right">$ <?= number_format($invoice->tax_inclusive_amount, '2', ',', '.') ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-right">Total:</td>
                                            <td class="text-right">
                                                $ <?= number_format((($invoice->line_extesion_amount + $invoice->tax_inclusive_amount) - $invoice->tax_exclusive_amount), '2', ',', '.') ?></td>
                                        </tr>
                                    <?php else: ?>
                                        <tr>
                                            <td class="text-right">Total:</td>
                                            <td class="text-right">
                                                $ <?= number_format(($invoice->payable_amount), '2', ',', '.') ?></td>
                                        </tr>
                                    <?php endif ?>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                <?php endif ?>
            </tbody>
        </table>
        <!-- <div>
            <table style="width:70%;">
                <tr>
                    <td style=" text-align:justify;">
                        <span style="font-size: 9pt;"><?= $invoice->invoice_status_id == 28 ? 'Motivo de la anulacion: ' : ''?> <?= $invoice->notes ?></span><br>
                        <?php if($invoice->invoice_status_id == 28): ?>
                            <span style="font-size: 9pt;">Remisión anulada por: <?= $user->name ?></span>
                        <?php endif ?>
                    </td>
                </tr>
            </table>
        </div>
        <div> -->
            <!-- </div> -->
    </div>
</div>
    <!-- <table
           style="padding-bottom: 0px; margin-bottom: 0px; width:100%;margin-left:70%">
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
    </table> -->


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
