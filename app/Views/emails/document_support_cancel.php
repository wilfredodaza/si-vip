<?= view('layouts/header_email') ?>
<?php  if($invoice->type_documents_id == '105'): ?>
    <section style="font-family:Verdana;">
    <p style="text-align:justify;">
            Hola <?= $invoice->customer_name ?>, <br><br>
            El documento soporte que has firmado para le empresa  <strong><?= $invoice->company ?> </strong> con NIT  <strong><?= $invoice->identification_number ?></strong>-<strong><?= $invoice->dv ?></strong>
            ha sido rechazado/requerido por lo siguiente:
            <ul>
                <?php foreach($trackings as $item):?>
                <li><?= $item->message ?></li>
                <?php endforeach ?>
            </ul><br>
     
        </p>     
        <p>Para realizar el ajuste requerido y proceder a la aceptación y firma del documento soporte da clic en el botón
         <strong>Validar y Firmar</strong>.</p>     
        <center>
            <a style="display:inline-block; padding:10px 20px; margin:10px; text-decoration:none;font-family: Verdana; background:#8021B5; color:white; font-weight:bold;" 
                href="<?= base_url('document_support/firm_document/'.$invoice->uuid) ?>" target="_bland">
                Validar y Firmar
            </a>       
        </center> 
    </section>    
<?php endif; ?>


<?php  if($invoice->type_documents_id == '106'): ?>
    <section style="font-family:Verdana;">
        <p>Hola <?= $invoice->customer_name ?>, <br><br>
            El documento soporte que has firmado para le empresa  <strong><?= $invoice->company ?> </strong> con NIT  <strong><?= $invoice->identification_number ?></strong>-<strong><?= $invoice->dv ?></strong>
            ha sido rechazado/requerido por lo siguiente:

            <ul>
                <?php foreach($trackings as $item):?>
                <li><?= $item->message ?></li>
                <?php endforeach ?>
            </ul>
     
        </p>     
        <p>Para realizar el ajuste requerido y proceder a la aceptación y firma del documento soporte da clic en el botón <strong>Iniciar Sesion</strong>.</p>   
        <center>
            <a style="display:inline-block; padding:10px 20px; margin:10px; text-decoration:none;font-family: Verdana; background:#8021B5; color:white; font-weight:bold;" 
                href="<?= base_url() ?>" target="_bland">
                Iniciar Sesion
            </a>       
        </center>  
    </section>    
<?php endif; ?>

<?= view('layouts/footer_email') ?>