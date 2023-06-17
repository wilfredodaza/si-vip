<?= view('layouts/header_email') ?>
    <section>
        <h1 style="font-family:Verdana;">Bienvenido a MiFacturaLegal.com</h1>
        <p style="font-family:Verdana; font-size:14px;">Hola <?= $customer->name ?>, <br> <br>
            La empresa <strong><?=$company->company ?></strong> con NIT <?=$company->identification_number ?>-<?=$company->dv ?> 
            te invita a que ingreses a nuestro m&oacute;dulo de validaci&oacute;n y firma de documentos de soporte. <br><br>
            Da clic en el bot&oacute;n <strong>Iniciar sesion</strong>  documento para iniciar.
        </p>   
        <?php if(!empty($password)): ?>
            <section style="font-family:Verdana;font-size:14px;">
                <table>
                    <tr>
                        <td><strong>Nombre de usuario:</strong> </td>
                        <td> <?= $customer->email ?></td>
                    </tr>
                    <tr>
                        <td> <strong>Contraseña:</strong></td>
                        <td><?= $password ?></td>
                    </tr>
                </table>
            </section>
        <?php endif; ?>
        <center>
            <a href="<?= base_url() ?>" style="font-family:Verdana;text-decoration:none;color:white;background:#8021B5;padding:10px 20px;display:inline-block;margin-top:20px;" target="_bland">
            Iniciar sesion
            </a> 
        </center>      
    </section>

<?= view('layouts/footer_email') ?>
          