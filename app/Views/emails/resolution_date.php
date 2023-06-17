<?= view('layouts/header_email') ?>

<section>
    <p>
        Hola, <?= $company->company ?><br><br>
        <?php foreach($this->resolutionOverdue as $item): ?>
           <?php $days  = differenceDays(date('Y-m-d'), $item->date_to);?>
            La resolución de facturación con numero  <?= $item->resolution ?> que va desde la  <?= $item->from ?> hasta  <?= $item->to ?> esta a <?= $days ?> dias de vencerse.<br>';
        <?php endforeach ?>
        Te recomendamos que saques una nueva resolución de facturación electrónica y nos la envíes por medio de correo electrónico <a href="mailto:soporte@mifacturalegal.com"> </a> por el chat para realizar la activación.
    </p>
</section>


<?= view('layouts/footer_email') ?>