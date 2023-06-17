<?= view('layouts/header') ?>
<?php if(isset($valor)): ?>
<form class="hidden">
    <script src='https://checkout.epayco.co/checkout.js'
            data-epayco-key='<?= $epayco; ?>'
            class='epayco-button'
            data-epayco-extra1 = '<?= $company_name; ?>'
            data-epayco-extra2 = '<?= $email;; ?>'
            data-epayco-extra3 = '<?= $nit; ?>'
            data-epayco-extra4 = '<?= $phone; ?>'
            data-epayco-extra5 = 'si'
            data-epayco-amount='<?= $valor; ?>'
            data-epayco-tax='0'
            data-epayco-tax-base='<?= $valor; ?>'
            data-epayco-name='<?= $plan; ?>'
            data-epayco-description='<?= $plan; ?>'
            data-epayco-currency='COP'
            data-epayco-country='CO'
            data-epayco-test='false'
            data-epayco-external='true'
            data-epayco-autoclick = 'true'
            data-epayco-response='https://mifacturalegal.com/respuesta.php'
            data-epayco-confirmation='https://mifacturalegal.com/confirmar.php'
            data-epayco-button='https://369969691f476073508a-60bf0867add971908d4f26a64519c2aa.ssl.cf5.rackcdn.com/btns/boton_carro_de_compras_epayco2.png'>
    </script>
</form>
<?php endif; ?>
<style>

    @media only screen and (max-width: 600px) {
        .botones{
            width: 100%;
            margin-top: 3px !important;
        }
    }

</style>
<nav>
    <div class="nav-wrapper white">
        <a href="https://www.mifacturalegal.com" target="_blank" class="brand-logo"><img
                    src="https://mifacturalegal.com/seo-agency/img/logo8.png" alt="" srcset=""
                    style="width:80px; height:64px;"><img
                    src="https://mifacturalegal.com/seo-agency/img/logo7.png" alt="" srcset=""
                    style="width:180px; height:70px;"></a>
        <ul id="nav-mobile" class="right hide-on-med-and-down">
        </ul>
    </div>
</nav>
<div class="main">
    <div class="row">
        <?php if (session('errors')): ?>
                <div class="card-alert card red">
                    <div class="card-content white-text">
                        <?= session('errors') ?>
                    </div>
                    <button type="button" class="close white-text" data-dismiss="alert"
                            aria-label="Close">
                        <span aria-hidden="true">x</span>
                    </button>
                </div>
            <?php endif; ?>
        <div class="section width-90 ml-5">
            <div class="card">
                <div class="card-content p-0">
                    <div class="row">
                        <div class="col m1 pt-1"><a class="btn-floating pt-2" href="https://www.mifacturalegal.com/"><i
                                        class="material-icons">arrow_back</i></a></div>
                        <div class="col m11 white ">
                            <h4><?= (!isset($solicitante)) ? 'Pre-registro a MiFacturaLegal' : 'Completar Registro a MiFacturaLegal'; ?></h4>
                        </div>
                    </div>
                </div>
                <!-- contenedor de form -->
                <div class="divider"></div>
                <div class="card-content">
                    <form class="cmxform" id="formulario" method="post" enctype="multipart/form-data"
                          action="<?= (!isset($solicitante))? base_url().'/solicitudes/epayco' : base_url().'/solicitudes/actualicese/'.$solicitante[0]->applicant_id ;?>">
                        <div class="row" id="datos">
                            <div class="input-field col s12 m4">
                                <input id="nempresa" type="text" name="nempresa" class="validate" required
                                       value="<?= (isset($solicitante)) ? $solicitante[0]->company_name : ($applicant[0]->company_name ?? ''); ?>">
                                <label for="nempresa" id="nempresal" class="">Nombre Empresa o Persona Natural*</label>
                            </div>
                            <div class="input-field col s12 m4">
                                <select name="tdocumentoc" id="tdocumentoc" required class="validate">
                                    <option value="" <?= (!isset($solicitante))?'selected':''; ?>>Seleccione tipo documento...</option>
                                    <option value="6">NIT</option>
                                    <option value="3" <?= (!isset($solicitante))?'':'selected'; ?>>Cédula de Ciudadanía</option>
                                    <?php foreach ($tdocumentos as $tdocumento): ?>
                                        <?php if($tdocumento->id != '3' && $tdocumento->id != '6'): ?>
                                            <option value="<?= $tdocumento->id ?>"><?= $tdocumento->name ?></option>
                                        <?php endif; ?>
                                    <?php endforeach;?>
                                </select>
                                <label id="tdocumentocl">Tipo De Documento Empresa*</label>
                            </div>
                            <div class="input-field col s12 m4">
                                <input id="nit" type="text" name="nit" class="validate"
                                       value="<?= (isset($solicitante)) ? $solicitante[0]->nit : ($applicant[0]->nit ?? ''); ?>"
                                       required>
                                <label for="nit" id="nitl">Número de identificación de la Empresa*</label>
                            </div>
                            <div class="input-field col s12 m6">
                                <input id="email" type="text" name="email"
                                       value="<?= (isset($solicitante)) ? $solicitante[0]->email : ($applicant[0]->email ?? ''); ?>"
                                       class="validate" required>
                                <label for="email" id="emaill">Correo de Contacto*</label>
                            </div>
                            <div class="input-field col s12 m6">
                                <input id="emailem" type="text" name="emailem"
                                       value="<?= ($applicant[0]->email_confirmation ?? '') ?>" class="validate"
                                       required>
                                <label for="emailem">Correo de la Empresa</label>
                            </div>
                            <div class="input-field col s12 m12">
                                <input id="direccion" type="text" name="direccion" class="validate" required
                                       value="<?= (isset($solicitante)) ? $solicitante[0]->adress : ($applicant[0]->adress ?? ''); ?>">
                                <label for="direccion" id="direccionl">Dirección*</label>
                            </div>
                            <div class="input-field col s12 m4">
                                <input id="rl" type="text" name="rl" class="validate" required
                                       value="<?= (isset($solicitante)) ? $solicitante[0]->legal_representative : ($applicant[0]->legal_representative ?? ''); ?>">
                                <label for="rl" id="rll">Representante Legal*</label>
                            </div>
                            <div class="input-field col s12 m4">
                                <select name="tdocumento" id="tdocumento" required class="validate">
                                    <option value="" <?= (!isset($solicitante))?'selected':''; ?> >Seleccione tipo documento...</option>
                                    <option value="3" <?= (!isset($solicitante))?'':'selected';?> >Cédula de Ciudadanía</option>
                                    <option value="6">NIT</option>
                                    <?php foreach ($tdocumentos as $tdocumento): ?>
                                        <?php if($tdocumento->id != '3' && $tdocumento->id != '6'): ?>
                                                <option value="<?= $tdocumento->id ?>"><?= $tdocumento->name ?></option>
                                        <?php endif; ?>
                                    <?php endforeach;?>
                                </select>
                                <label id="tdocumentol">Tipo De Documento*</label>
                            </div>
                            <div class="input-field col s12 m4">
                                <input id="documento" type="text" name="documento" class="validate" required
                                       value="<?= (isset($solicitante)) ? $solicitante[0]->num_documento : ($applicant[0]->num_documento ?? ''); ?>">
                                <label for="documento" id="documentol">Número Documento*</label>
                            </div>
                            <input id="vendedor" type="hidden" name="vendedor"
                                   value="<?= (!isset($_GET['v'])) ? '222222222' : $_GET['v']; ?>">
                            <div class="input-field col s12 m12">
                                <textarea id="icon_prefix2" class="materialize-textarea" name="observaciones"
                                          id="observaciones"></textarea>
                                <label for="icon_prefix2">Observaciones</label>
                            </div>
                            <div style="z-index: 0 !important;" class="input-field col s12 m6">
                                <select name="plan" id="plan" required class="validate">
                                    <?php if(!isset($solicitante)): ?>
                                    <option value="" selected>Selecione su Plan</option>
                                    <?php foreach ($paquetes as $paquete): ?>
                                            <option class="rsi" value="<?= $paquete->id ?>"><?= $paquete->name ?> - $<?= (!isset($_GET['v'])) ?  number_format($paquete -> price) : number_format($paquete ->price - 30000);
                                            ?>  <?= (!isset($_GET['v'])) ? '' : (!isset($_GET['renv']))?'- Descuento de $30000':'';
                                            ?></option>
                                    <?php endforeach;?>
                                    <?php else: ?>
                                        <?php foreach ($paquetes as $paquete): ?>
                                            <?php if($paquete->id == $solicitante[0]->packages_id): ?>
                                                <option class="rsi" value="<?= $paquete->id ?>"><?= $paquete->name ?> - $<?= (!isset($_GET['v'])) ?  number_format($paquete -> price) : number_format($paquete ->price - 30000);
                                                    ?>  <?= (!isset($_GET['v'])) ? '' : (!isset($_GET['renv']))?'- Descuento de $30000':'';
                                                    ?></option>
                                            <?php endif; ?>
                                        <?php endforeach;?>
                                    <?php endif;?>
                                </select>
                                <label id="lplan">Seleccione su plan*</label>
                                <a href="https://www.mifacturalegal.com/#pricing-sec" target="_blank">Ver detalle de los
                                    planes</a>
                            </div>
                            <div style="z-index: 0 !important;" class="input-field col s12 m6">
                                <select name="process" id="process" required class="validate">
                                    <option value="enuevo" <?= (!isset($applicant[0]->company_name)) ? '' : 'selected' ?>>Empresa Nueva</option>
                                    <option value="renovacion" <?= (isset($applicant[0]->company_name)) ? 'selected' : '' ?> >Renovación</option>
                                </select>
                                <label id="process">Seleccione Proceso</label>
                            </div>
                            <div class="input-field col s12 m12">
                                <div class="col s12 m6">
                                    <label for="tyc" id="tycl">Autorizaciones *</label>
                                    <p>
                                        <label>
                                            <input type="checkbox" required name="tyc" id="tyc">
                                            <span>Terminos y condiciones</span>
                                        </label>
                                        <a href="https://www.mifacturalegal.com/tyc/" target="_blank"><span>clic para leer</span></a>
                                        <br>
                                        <label>
                                            <input type="checkbox" required name="tycemail" id="tycemail">
                                            <span>Acepto notificacion por email</span>
                                        </label>
                                    </p>
                                </div>
                                <div class="col s12 m6">
                                    <?php if (!isset($solicitante)): ?>
                                        <a style="z-index: 0 !important;"
                                           class="botones waves-effect waves-light btn-large" id="linea"><i
                                            class="material-icons left">local_atm</i>Pagar en Linea</a>
                                        <button style="z-index: 0 !important;"
                                                class=" botones  waves-light btn-large left mr-1" id="validar"
                                                type="button"><i
                                            class="material-icons left">cloud_upload</i>Cargar Consignación
                                </button>
                                    <?php else: ?>
                                        <button style="z-index: 0 !important;"
                                                class="botones waves-effect waves-light btn-large " type="submit"><i
                                                    class="material-icons left">send</i>Guardar
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <!--
                         @user        : john vergara
                         @fecha       : 06-12-2020
                         @modificacion: se crea modal para realizar la explicación de pago en linea
                        -->
                        <div id="modal1" class="modals modals-fixed-footer">
                            <div class="modals-content">
                                <h4>Pasos pago en línea</h4>
                                <p> 1.	Será enviado a la página de “Epayco”<br>
                                    2.  Recordar que las renovaciones no cuentan con descuentos, al momento de empezar el proceso de pago encontrara el valor total sin descuento.<br>
                                    3.	Llenar los datos correspondientes que pide la pagina de “Epayco” para realizar el pago<br>
                                    4.	Una vez realizado el pago “Epayco” le dará la opción para devolverse o después 30 segundo la página lo retornará a una factura emitida por nosotros.<br>
                                    5.	Al final la página la retornara a la página principal de “MiFacturaLegal” donde terminara todo el proceso del pago en línea.<br>
                                </p>
                            </div>
                            <div class="modals-footer">
                                <button style="z-index: 0 !important;"
                                        class="botones waves-effect waves-light btn-large" type="submit"><i
                                            class="material-icons left">done</i>Aceptar
                                </button>
                                <a href="#!" class="modals-action modals-close waves-effect waves-green btn-flat ">Cancelar</a>
                            </div>
                        </div>
                        <div class="row" id="confirmacion">
                            <div class="col m12 s12">
                                <h4>Soporte de Consignación</h4>
                            </div>
                            <div class="col m12 s12">
                                <span>Le recordamos que las opciones para consignación bancaria a nombre IPLANET COLOMBIA S.A.S.- NIT 900.444.608-8. son las siguientes:</span>
                            </div>
                            <dic class="col m12 s12">
                                <i class="material-icons pr-1">account_balance</i><span>cuenta de ahorros No. 476100052432</span><span> banco Davivienda</span>
                            </dic>
                            <dic class="col m12 s12">
                                 <label for="dato1" id="dato1">Documento Invalido</label>
                                <div class="file-field input-field">
                                    <div style="width:250px !important;">
                                        <input type="file" name="dato" id="dato"
                                               class="dropify" data-height="120">
                                    </div>
                                </div>
                            </dic>
                            <div class="col m12">
                                <span>Recuerde:</span>
                                <p>
                                    <li>Las extenciones permitidas son: png, jpg, jpeg y pdf.
                                </li>
                                <li>Si usted es agente retenedor aplicar la retención en la fuente del 4% por
                                    servicios
                                </li>
                                <li>Si está en Bogotá D.C. aplicar ReteICA del 9.66 * mil. Agradecemos enviar
                                    certificados de
                                    retención.
                                </li>
                                </p>
                            </div>
                            <div class="input-field col s12 m6">
                                <button style="z-index: 0 !important;"
                                        class="botones waves-effect waves-light btn-large " id="volver" type="button"><i
                                            class="material-icons left">clear</i>Cancelar
                                </button>
                                <button style="z-index: 0 !important;"
                                        class="botones waves-effect waves-light btn-large " type="submit"><i
                                            class="material-icons left">send</i>enviar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="row" id="informacion_pago_pago">
                            <div class="col m12 s12">
                                <h4>Soporte de Consignación</h4>
                            </div>
                            <div class="col m12 s12">
                                <span>Le recordamos que las opciones para consignación bancaria a nombre IPLANET COLOMBIA S.A.S.- NIT 900.444.608-8. son las siguientes:</span>
                            </div>
                            <dic class="col m12 s12">
                                <i class="material-icons pr-1">account_balance</i><span>cuenta de ahorros No. 476100052432</span><span> banco Davivienda</span>
                            </dic>
                            <div class="col m12">
                                <span>Recuerde:</span>
                                <p>
                                <li>Si usted es agente retenedor aplicar la retención en la fuente del 4% por
                                    servicios
                                </li>
                                <li>Si está en Bogotá D.C. aplicar ReteICA del 9.66 * mil. Agradecemos enviar
                                    certificados de
                                    retención.
                                </li>
                                <li id="info_renovacion">Las renovaciones no cuentan con descuentos, al momento de realizar el pago encontrara el valor sin descuento.
                                </li>
                                </p>
                            </div>

                        </di
                        </div>
        </div><br><br><br>
                </div>
            </div>

<footer class="page-footer footer footer-static footer-light navbar-border navbar-shadow" style="position:fixed; bottom: 0px; width: 100%;">
    <div class="footer-copyright">
        <div class="container"><span><?= isset(configInfo()['footer']) ? configInfo()['footer'] : '' ?></span></div>
    </div>
</footer>
    <script>localStorage.setItem('url', '<?= base_url() ?>')</script>


    <script src="<?= base_url() ?>/assets/js/vendors.min.js"></script>
    <script src="<?= base_url() ?>/assets/js/jquery.formatter.min.js" ></script>
<script src="<?= base_url() ?>/assets/js/jquery.validate.js"></script>
<script src="https://unpkg.com/materialize-stepper@3.1.0/dist/js/mstepper.min.js"></script>
     <script src="<?= base_url() ?>/assets/js/shepherd.min.js"></script>
<script src="<?= base_url() ?>/assets/js/plugins.min.js"></script>
<script src="<?= base_url() ?>/assets/js/search.min.js"></script>
    <script src="<?= base_url() ?>/assets/js/select2.full.min.js"></script>
<script src="<?= base_url() ?>/assets/js/select2.full.min.js"></script>
<script src="<?= base_url() ?>/assets/js/chart.min.js"></script>
<script src="<?= base_url() ?>/assets/js/custom-script.min.js"></script>
<script src="<?= base_url() ?>/assets/js/ui-alerts.js"></script>
    <script src="<?= base_url() ?>/assets/js/advance-ui-modals.js"></script>
<script src="<?= base_url() ?>/assets/js/additional-methods.js"></script>
<script src="<?= base_url() ?>/assets/js/form-wizard.js"></script>
    <script src="<?= base_url() ?>/assets/js/dropzone.js"></script>
<script src="<?= base_url() ?>/dropify/js/dropify.min.js"></script>
<script id = "sbinit" src = "https://mischats.com/supportboard/js/main.js?lang=es&mode=2" > </script> 
<script>
    $(function () {
        $('.modals').modals();
        $('#pago').modals('close');
    });
    $(document).ready(function () {
        $('.dropify').dropify();
        $('#dato1').hide();
        $('#info_renovacion').hide();
        $("#dato").on("change", (e) => {
          const archivo = $(e.target)[0].files[0];
          let nombArchivo = archivo.name;
          var extension = nombArchivo.split(".").slice(-1);
              extension = extension[0];
          let extensiones = ["pdf", "png", "jpg", "jpeg"];
              if(extensiones.indexOf(extension) === -1){
                  $('#dato1').show();
                $("#dato1").css("color", "red");
              }else{
                  $('#dato1').hide();
                $("#dato1").css("color", "black");
              }
        });
        $('#confirmacion').hide();
        $('#validar').click(function () {
            var validado = 'si';
            if ($('#nempresa').val().length <= 0) {
                $("#nempresal").css("color", "red");
                validado = 'no';
            }
            if ($('#nit').val().length <= 0) {
                $("#nitl").css("color", "red");
                validado = 'no';
            }
            if ($('#email').val().length <= 0) {
                $("#emaill").css("color", "red");
                validado = 'no';
            }
            if ($('#emailem').val().length <= 0) {
                $("#emaileml").css("color", "red");
                validado = 'no';
            }
            if ($('#direccion').val().length <= 0) {
                $("#direccionl").css("color", "red");
                validado = 'no';
            }
            if ($('#rl').val().length <= 0) {
                $("#rll").css("color", "red");
                validado = 'no';
            }
            if ($('#tdocumento').val().length <= 0) {
                $("#tdocumentol").css("color", "red");
                validado = 'no';
            }
            if ($('#documento').val().length <= 0) {
                $("#documentol").css("color", "red");
                validado = 'no';
            }
            if ($('#plan').val().length <= 0) {
                $("#lplan").css("color", "red");
                validado = 'no';
            }
            console.log($('#tyc').prop('checked'));
            if ($('#tyc').prop('checked') === false) {
                $("#tycl").css("color", "red");
                validado = 'no';
            }
            if ($('#tycemail').prop('checked') === false) {
                $("#tycl").css("color", "red");
                validado = 'no';
            }
            if (validado === 'si') {
                $('#formulario').attr('action','<?= base_url()?>/solicitud/guardarsolicitante');
                $('#confirmacion').show();
                $('#informacion_pago_pago').hide();
                $('#datos').hide();
            }if ($('#tdocumentoc').val().length <= 0) {
                $("#tdocumentocl").css("color", "red");
                validado = 'no';
            }
        });
        $('#linea').click(function () {
            var validado = 'si';
            if ($('#nempresa').val().length <= 0) {
                $("#nempresal").css("color", "red");
                validado = 'no';
            }
            if ($('#nit').val().length <= 0) {
                $("#nitl").css("color", "red");
                validado = 'no';
            }
            if ($('#email').val().length <= 0) {
                $("#emaill").css("color", "red");
                validado = 'no';
            }
            if ($('#emailem').val().length <= 0) {
                $("#emaileml").css("color", "red");
                validado = 'no';
            }
            if ($('#direccion').val().length <= 0) {
                $("#direccionl").css("color", "red");
                validado = 'no';
            }
            if ($('#rl').val().length <= 0) {
                $("#rll").css("color", "red");
                validado = 'no';
            }
            if ($('#tdocumento').val().length <= 0) {
                $("#tdocumentol").css("color", "red");
                validado = 'no';
            }
            if ($('#documento').val().length <= 0) {
                $("#documentol").css("color", "red");
                validado = 'no';
            }
            if ($('#plan').val().length <= 0) {
                $("#lplan").css("color", "red");
                validado = 'no';
            }
            console.log($('#tyc').prop('checked'));
            if ($('#tyc').prop('checked') === false) {
                $("#tycl").css("color", "red");
                validado = 'no';
            }
            if (validado === 'si') {
                $('#modal1').modals('open');
            }if ($('#tdocumentoc').val().length <= 0) {
                $("#tdocumentocl").css("color", "red");
                validado = 'no';
            }
        });
        $('#volver').click(function () {
            $('#formulario').attr('action','<?= base_url()?>/solicitudes/epayco');
            $('#confirmacion').hide();
            $('#datos').show();
            $('#informacion_pago_pago').show();
        });
        $('select[required]').css({
            position: 'absolute',
            display: 'inline',
            height: 0,
            padding: 0,
            border: '1px solid rgba(255,255,255,0)',
            width: 0
        });
        if($('#process').val() == "renovacion"){
            $('#info_renovacion').show();
            $("#info_renovacion").css("color", "red");
        }
        $('#process').change(function(){
            if($('#process').val() == "renovacion"){
                $('#info_renovacion').show();
                $("#info_renovacion").css("color", "red");
            }else{
                $('#info_renovacion').hide();
            }
        });
    });


</script>
<script>
    $(document).ready(function(){
        $('.notification-active').click(function(){
            var URLactual = window.location;
            var id = $(this).data('id');
            $(this).hide();
            fetch(URLactual.origin + '/notification/view/' + id)
                .then(function (response) {
                    return response.json();
                })
                .then(function (myJson) {
                    var dates = myJson;
                   location.href =  URLactual.origin+'/notification/index?nota='+ id;
                });
        });
    });
</script>
</body>
</html>