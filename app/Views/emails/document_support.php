<?= view('layouts/header_email') ?>
<?php  if($invoice->type_documents_id == '105'): ?>
    <section  style="font-family:Verdana;">
        <h1 style="font-family:Verdana;">Bienvenido a <strong> MiFacturaLegal.com</strong></h1>
            <p>
            La empresa <strong><?=$company->company ?></strong> con NIT <strong><?=$company->identification_number ?>-<?=$company->dv ?> </strong>
            te invita a que ingreses a nuestro m&oacute;dulo de validaci&oacute;n y firma de documentos de soporte generado por la prestación de tu servicios. 
            <br><br>
            Para lo anterior, deberás seguir los siguientes pasos:

            </p>
            <ul style="font-size:14px;">
                <ol style="padding-left:0px;">1. Ingresar al módulo dando Click en el boton <strong>Validar y Firmar</strong> que esté más abajo.</ol>
                <ol style="padding-left:0px;">2. Verificar y/o actualizar tus datos.</ol>
                <ol style="padding-left:0px;">3. Anexar los siguientes documentos:
                    <ul>
                        <li>Certificación Bancaria actualizada no mayor a 30 diás.</li>
                        <li>RUT actualizado.</li>
                        <li>Firma.</li>
                        <li>Otros soportes necesarios. Por ejemplo, pago a seguridad social, orden de compra, etc.</li>
                    </ul>
                </ol>
                <ol style="padding-left:0px;">4. Revisar el documento generado y si estás de acuerdo dar Click en el botón <strong>Firma de documento soporte.</strong></ol>
            </ul>
        <?php 
            if(count($trackings) > 0): 
                foreach($trackings as $item):
        ?>
        <p><?= $item->message ?></p>
        <?php 
                endforeach;
            endif; 
        ?>
        <center>
    	<a style="display:inline-block; padding:10px 20px; margin:10px; text-decoration:none;font-family: Verdana; background:#8021B5; color:white; font-weight:bold;"  class="button"
                href="<?= base_url('document_support/firm_document/'.$invoice->uuid) ?>">
                Validar y Firmar 
            </a>       
        </center>  
    </section>            
<?php endif; ?>
<?= view('layouts/footer_email') ?>
