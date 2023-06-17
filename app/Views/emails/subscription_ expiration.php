<?= view('layouts/header_email') ?>

<section>
    <p>
        Hola, <?= $company->company ?><br><br>
        La subcripción esta  a <?= $days ?> dias de vencer te recomendamos que te comuniques con nosotros para adquirir una nueva suscripción. 
        los puedes realizar por medio de nuestro chat o por correo electrónico  <a href="mailto:soporte@mifacturalegal.com">soporte@mifacturalegal.com</a>.
    </p>

</section>


<?= view('layouts/footer_email') ?>