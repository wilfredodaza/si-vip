<div>
    <div class="text-center">
        <h2 class="text-center"><?= configInfo()['name_app'] ?></h2>
        <p>Dirección: <?= $invoice->company_address ?><br>
        Email: <?= $invoice->company_email ?> <br>
        Sede: <?= $invoice->company_name ?></p>
    </div>
</div>
<!-- <table width="100%">
    <tr>
        <td style="width: 25%;" class="vertical-align-top">
            <div id="reference">
                <p style="font-weight: 700;"><strong><?= $invoice->nameDocument ?></strong> # <?= $invoice->resolution ?></p>
            </div>
        </td>
        <td style="width: 25%; text-align: right;" class="vertical-align-top">
            <p>   Fecha de operación: <?= substr($invoice->created_at ,0, 10)  ?>
                Hora de operación: <?= substr($invoice->created_at ,10, 17) ?></p>
        </td>
    </tr>
</table> -->

<table style="font-size: 8px !important; width:100%">
    <tr>
        <td class="vertical-align-top" style="width: 33%;">
            <table style="font-size: 8px !important;">
                <tbody>
                    <tr>
                        <td><span style="font-size: 8pt;"> <b>Nombre: </span></b></td>
                        <td><span style="font-size: 8pt;"><?= $invoice->name ?></span></td>
                    </tr>
                    <tr>
                        <td><span style="font-size: 8pt;"><b>Telefono:</span></b></td>
                        <td><span style="font-size: 8pt;"><?= $invoice->phone ?></span></td>
                    </tr>
                    <tr>
                        <td><span style="font-size: 8pt;"><b>Forma de pago:</span></b></td>
                        <td><span style="font-size: 8pt;"><?= $invoice->payment_forms_name ?></span></td>
                    </tr>
                    <tr>
                        <td><span style="font-size: 8pt;"> <b>Metodo de pago</b></span></td>
                        <td><?= $invoice->accounting_account_name ?></td>
                    </tr>
                </tbody>
            </table>
        </td>
        <td class="vertical-align-top" style="width: 33%;">
            <table width="100%">
                <tbody>
                    <tr>
                        <td><span style="font-size: 8pt;"><b>Dirección:</b></span></td>
                        <td><span style="font-size: 8pt;"><?= $invoice->address ?></span></td>
                    </tr>
                    <tr>
                        <td><span style="font-size: 8pt;"><b>Ciudad:</span></b></td>
                        <td><span style="font-size: 8pt;"><?= $invoice->municipio ?></span></td>
                    </tr>
                    <tr>
                        <td><span style="font-size: 8pt;"><b>Responsable:</span></b></td>
                        <td><span style="font-size: 8pt;"><?= $invoice->user_name ?></span></td>
                    </tr>
                </tbody>
            </table>
        </td>
        <td class="vertical-align-top" style="width: 33%;">
            <table width="100%">
                <tbody>
                    <tr>
                        <td><span style="font-size: 8pt;"><b><?= $invoice->nameDocument ?>:</b></span></td>
                        <td><span style="font-size: 8pt;"> # <?= $invoice->resolution ?></span></td>
                    </tr>
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
