<body>
    <table style="font-size: 8px !important;">
        <tr>
            <td class="vertical-align-top" style="width: 60%;">
                <table style="font-size: 8px !important;">
                    <tbody>
                        <tr>
                            <td><span style="font-size: 8pt;"> Empresa: </span></td>
                            <td><span style="font-size: 8pt;"><?= $invoice->company_name ?></span></td>
                        </tr>
                        <tr>
                            <td><span style="font-size: 8pt;">Nit:</span></td>
                            <td><span style="font-size: 8pt;"><?= $invoice->company_identification_number ?></span></td>
                        </tr>
                        <tr>
                            <td><span style="font-size: 8pt;">Dirección:</span></td>
                            <td><span style="font-size: 8pt;"><?= $invoice->company_address ?></span></td>
                        </tr>
                        <tr>
                            <td><span style="font-size: 8pt;">Ciudad:</span></td>
                            <td><span style="font-size: 8pt;"><?= $invoice->municipality_company_name ?></span></td>
                        </tr>
                        <tr>
                            <td><span style="font-size: 8pt;">Email:</span></td>
                            <td><span style="font-size: 8pt;"><?= $invoice->company_email?></span></td>
                        </tr>
                        <tr>
                            <td><span style="font-size: 8pt;">Telefono:</span></td>
                            <td><span style="font-size: 8pt;"><?= $invoice->company_phone ?></span></td>
                        </tr>
                    </tbody>
                </table>
            </td>
            <td class="vertical-align-top" style="width: 40%; padding-left: 1rem">
                <table width="100%">
                    <tbody>
                        <tr>
                            <td width="80px"><span style="font-size: 8pt;">Proveedor: </span></td>
                            <td><span style="font-size: 8pt;"><?= $invoice->customer_name ?></span></td>
                        </tr>
                        <tr>
                            <td><span style="font-size: 8pt;">Nit:</span></td>
                            <td><span style="font-size: 8pt;"><?= $invoice->customer_identification_number ?></span></td>
                        </tr>
                        <tr>
                            <td><span style="font-size: 8pt;">Dirección:</span></th>
                            <td><span style="font-size: 8pt;"><?= $invoice->customer_address ?></span></td>
                        </tr>
                        <tr>
                            <td><span style="font-size: 8pt;">Ciudad:</span></td>
                            <td><span style="font-size: 8pt;"><?= $invoice->municipality_customer_name ?></span></td>
                        </tr>
                        <tr>
                            <td><span style="font-size: 8pt;">Email:</span></td>
                            <td><span style="font-size: 8pt;"><?= $invoice->customer_email?></span></td>
                        </tr>
                        <tr>
                            <td><span style="font-size: 8pt;">Telefono:</span></td>
                            <td><span style="font-size: 8pt;"><?= $invoice->customer_phone ?></span></td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>
    <br>
    <br>

    <table style="width:100%;">
        <tr>
            <td  style=" text-align:justify;">
                <span style="font-size: 9pt;"><?= $invoice->notes ?></span>
            </td>
        </tr>
    </table>

    <br><br>
    
                <table class="table table-bordered table-condensed table-striped table-responsive" style="width: 100%"> 
                    <thead>
                    <tr>
                        <th class="text-center" width="60%">
                            <span>Retenciones</span>
                        </th>
                        <th class="text-center">Totales</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            <table class="table table-bordered table-condensed table-striped table-responsive" style="width:100%;">
                                <thead>
                                <tr>
                                    <th class="text-center">Tipo</th>
                                    <th class="text-center">Base</th>
                                    <th class="text-center">Porcentaje</th>
                                    <th class="text-center">Valor</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <?php $retention = 0; ?>
                                    <?php foreach($withholding as $item): ?>
                                        <tr>
                                            <td><?= $item->name ?></td>
                                            <td class="text-center">$<?= number_format($invoice->payable_amount, '2', ',', '.')?></td>
                                            <td class="text-center"><?= $item->percent ?> %</td>
                                            <?php $retention += ($invoice->payable_amount * $item->percent) / 100; ?>
                                            <td class="text-right">$<?= number_format($invoice->payable_amount * $item->percent / 100, '2', ',', '.')?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </td>
                        <td>
                            <table class="table table-bordered table-condensed table-striped table-responsive"
                                   style="padding-bottom: 0px; margin-bottom: 0px; width:100%;" >
                                <thead>
                                <tr>
                                    <th class="text-center">Concepto</th>
                                    <th class="text-center">Valor</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>Base:</td>
                                    <td class="text-right">$<?= number_format($invoice->payable_amount, '2', ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <td>Retenciones:</td>
                                    <td class="text-right">$<?=  number_format($retention, '2', ',', '.') ?></td>
                                </tr>
                                <tr>
                                    <td>Total:</td>
                                    <td class="text-right">$<?=  number_format($invoice->payable_amount - $retention, '2', ',', '.') ?></td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
     

    <div class="summarys">
        <div id="note">
            <p style="font-size: 12px;"><br>
                <strong>SON: </strong>  <?= convertir($invoice->payable_amount - $retention ) ?>
            </p>
        </div>
    </div>
    <br><br>
    <br><br>

        <div  style="text-align:center;">
            <?php if($firm):  ?>
                <img src="<?= base_url('upload/firm/'.$firm->file) ?>" alt="" width="150px"><br>
            <?php endif;?>
            <!--_____________________________________--><br>
            <br>
            <?= $invoice->customer_name ?><br>
            <span style="font-weight:bold;">Proveedor</span>
        </div>
   

    <?php if($invoice->resolution_id != null): ?>
        <div id="footer">
            <p id='mi-texto'>
                Resolución de Documento de soporte No. <?=  $invoice->resolution_id ?>
                de <?= $resolution->date_from ?>, Rango <?= $resolution->from ?> Al <?= $resolution->to ?> - Vigencia
                Desde: <?= $resolution->date_from ?>  Hasta:  <?= $resolution->date_to ?> <br>
                <span>Elaborado  y enviado electrónicamente por MiFacturaLegal.com.</span>
            </p>
        </div>
    <?php endif;  ?>
   
</body>
