<table width="100%">
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
</table>
<br><br>
<table style="font-size: 8px !important;">
    <tr>
        <td class="vertical-align-top" style="width: 60%;">
            <table style="font-size: 8px !important;">
                <tbody>
                <tr>
                    <td><span style="font-size: 8pt;"> Nombre: </span></td>
                    <td><span style="font-size: 8pt;"><?= $invoice->name ?></span></td>
                </tr>
                <tr>
                    <td><span style="font-size: 8pt;">Telefono:</span></td>
                    <td><span style="font-size: 8pt;"><?= $invoice->phone ?></span></td>
                </tr>
                <tr>
                    <td><span style="font-size: 8pt;">Forma de pago:</span></td>
                    <td><span style="font-size: 8pt;"><?= $invoice->payment_forms_name ?></span></td>
                </tr>
                </tbody>
            </table>
        </td>
        <td class="vertical-align-top" style="width: 40%; padding-left: 1rem">
            <table width="100%">
                <tbody>
                <tr>
                    <td><span style="font-size: 8pt;">Dirección:</span>
                    </th>
                    <td><span style="font-size: 8pt;"><?= $invoice->address ?></span></td>
                </tr>
                <tr>
                    <td><span style="font-size: 8pt;">Ciudad:</span></td>
                    <td><span style="font-size: 8pt;"><?= $invoice->municipio ?></span></td>
                </tr>

                </tbody>
            </table>
        </td>
    </tr>
</table>
