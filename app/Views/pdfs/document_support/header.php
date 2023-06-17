<table width="100%">
    <tr>
        <td style="width: 25%;" class="text-center vertical-align-top">
            <div id="reference">
                <p style="font-weight: 700;"><strong>DOCUMENTO SOPORTE DE PAGO DE NOMINA ELECTRONICA No</strong></p>
                <br>
                <p style="color: red;
                    font-weight: bold;
                    font-size: 14px;
                    margin-bottom:red solid 8px;
                    border: 1px solid #000;
                    padding: 5px 8px !important;
                    line-height: 1;
                    border-radius: 6px;">
                        <?php if($invoice->resolution_id !=  null): ?>
                            <?= $invoice->prefix.$invoice->resolution ?>
                        <?php else: ?>
                            0
                        <?php endif; ?>
                    </p>
                    <br>
                <p>   Fecha de operación: <?= substr($invoice->created_at ,0, 10)  ?> <br>
                        Hora de operación: <?= substr($invoice->created_at ,10, 17) ?></p>
            </div>
        </td>
        <td style="width: 50%; padding: 0 1rem;" class="text-center vertical-align-top">
            <div id="empresa-header1">
            <?php if($invoice->resolution_id ==  null): ?>
                <span style="color:red;"><strong>PRELIMINAR</strong></span> <br>
            <?php endif; ?>
            <span><strong>DOCUMENTO DE SOPORTE EN ADQUISICIONES  <br>
                EFECTUADAS A NO OBLIGADO A FACTURAR</strong><br>
                (Articulo 1.6.1.14.12 Decreto 1625 de 2016 Unico Regalemento en Materia Tributaria)
                </span>
            </div>
        </td>
        <td style="width: 25%; text-align: right;" class="vertical-align-top">
            <img  style="width: 136px; height: auto;" src="<?= base_url('assets/upload/imgs/'.$invoice->logo) ?>" alt="logo">
        </td>
    </tr>
</table>
