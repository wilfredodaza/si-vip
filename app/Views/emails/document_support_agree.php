<?= view('layouts/header_email') ?>

    <section>
        <p>
        Hola <?= $invoice->customer_name ?>,<br><br>
        El documento soporte enviado a la empresa  <strong><?= $invoice->company ?></strong> con NIT  <strong> <?=  number_format($invoice->identification_number, '0', '.', '.')?>-<?= $invoice->dv ?></strong> 
        ha sido aceptado y aprobado.
        <br><br>
        Para descargarlo da clic en el siguiente botón.
      </p>
        <center>
            <a style="display:inline-block; padding:10px 20px; margin:10px; text-decoration:none;font-family: Verdana; background:#8021B5; color:white; font-weight:bold;" 
                href="<?= base_url('document_support/create_pdf/'.$invoice->id) ?>">
                Descargar 
            </a>       
        </center>  
    </section>



<?= view('layouts/footer_email') ?>
