<?= view('layouts/header_email') ?>

<section>
    <p>
        Hola, <?= $company->company ?> <br><br>
        El paquete <?= $package->name ?> de <?= $package->quantity_document ?> documentos esta pronto a vencerse, solo te quedan  
        <?= $available ?> documentos disponibles. Te aconsejamos comunicarte con nosotros vía chat o correo electrónico 
        <a href="mailto:soporte@mifacturalegal.com"> soporte@mifacturalegal.com </a> iniciar con el proceso compra de un nuevo paquete de documentos.
    </p>

</section>


<?= view('layouts/footer_email') ?>