<?php
    $rut = '';
    $cc = '';
    $cr = '';
    $cf = '';
    $af = '';
    $rd = '';
if ($cantidad > 1) {
    foreach ($documentos as $documento) {
        if ($documento->documento == 'Rut') {
            if ($documento->status == 'Aprobado') {
                $rut = 'Si';
            } elseif ($documento->status == 'Pendiente') {
                $rut = 'Pendiente';
            } else {
                $rut = 'No';
            }
        } elseif ($documento->documento == 'Camara de comercio') {
            if ($documento->status == 'Aprobado') {
                $cc = 'Si';
            } elseif ($documento->status == 'Pendiente') {
                $cc = 'Pendiente';
            } else {
                $cc = 'No';
            }
        } elseif ($documento->documento == 'Cedula representante') {
            if ($documento->status == 'Aprobado') {
                $cr = 'Si';
            } elseif ($documento->status == 'Pendiente') {
                $cr = 'Pendiente';
            } else {
                $cr = 'No';
            }
        } elseif ($documento->documento == 'Contrato firma') {
            if ($documento->status == 'Aprobado') {
                $cf = 'Si';
            } elseif ($documento->status == 'Pendiente') {
                $cf = 'Pendiente';
            } else {
                $cf = 'No';
            }
        } elseif ($documento->documento == 'Autorizacion firma') {
            if ($documento->status == 'Aprobado') {
                $af = 'Si';
            } elseif ($documento->status == 'Pendiente') {
                $af = 'Pendiente';
            } else {
                $af = 'No';
            }
        } elseif ($documento->documento == 'Resolucion Dian') {
            if ($documento->status == 'Aprobado') {
                $rd = 'Si';
            } elseif ($documento->status == 'Pendiente') {
                $rd = 'Pendiente';
            } else {
                $rd = 'No';
            }
        } elseif ($documento->documento == 'Comprobante de pago') {
            if ($documento->status == 'Aprobado') {
                $cp = 'Si';
            } elseif ($documento->status == 'Pendiente') {
                $cp = 'Pendiente';
            } else {
                $cp = 'No';
            }
        }
    }
} else {
    $rut = '';
    $cc = '';
    $cr = '';
    $cf = '';
    $af = '';
    $rd = '';
    ($pago == 'no')?$cp = '': $cp = 'si';
}

foreach ( $ayudas as $key) {
    if ($key->documento == 'rut') {
        $rut_ayuda = $key->ayuda;
    } elseif ($key->documento == 'camara de comercio') {
        $cc_ayuda = $key->ayuda;
    } elseif ($key->documento == 'cedula de representante') {
        $cr_ayuda = $key->ayuda;
    } elseif ($key->documento == 'contrato firma digital') {
        $cf_ayuda = $key->ayuda;
    } elseif ($key->documento == 'autorizacion firma digital') {
        $af_ayuda = $key->ayuda;
    } elseif ($key->documento == 'resolucion dian') {
        $rd_ayuda = $key->ayuda;
    } elseif ($key->documento == 'comprobante de pago') {
        $cp_ayuda = $key->ayuda;
    }

}

?>
<?= view('layouts/header') ?>
<style>
*::-webkit-scrollbar {
    display: none;
}
    #sidebar::-webkit-scrollbar {
    /*display: none;*/
    overflow:hidden;
}
</style>
<nav>
    <div class="nav-wrapper white">

        <a href="https://www.mifacturalegal.com" target="_blank" class="brand-logo"><img
                    src="https://www.mifacturalegal.com/seo-agency/img/logo8.png" alt="" srcset=""
                    style="width:100px; height:64px;"><img
                    src="https://www.mifacturalegal.com/seo-agency/img/logo7.png" alt="" srcset=""
                    style="width:200px; height:70px;"></a>
        <ul id="nav-mobile" class="right hide-on-med-and-down">
        </ul>
    </div>
</nav>
<div class="section">
    <?php if (session('success')): ?>
        <div class="card-alert card green">
            <div class="card-content white-text">
                <?= session('success') ?>
            </div>
            <button type="button" class="close white-text" data-dismiss="alert"
                    aria-label="Close">
                <span aria-hidden="true">x</span>
            </button>
        </div>
    <?php endif; ?>
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
    <div class="container">
        <div class="card">
            <div class="card-content">
                <div class="row">
                    <div class="col s12 m12 ">
                        <h4><?= ($datos->process != 'renovacion')?'Información de registro de empresas.':'Información de renovaciòn de empresas.' ?></h4>
                        <div class="card-content dark">


                            Esta es la información registrada de su empresa en nuestros sistema;
                            Si encuentra alguna inconsistencia por favor enviar correo a
                            soporte@mifacturalegal.com


                        </div>
                        <div class="col s12 m6 ">
                            <div class="card-content dark p-0">
                                <p><strong class="black-text">Nombre Empresa
                                        : </strong><?= $datos->company_name; ?></p>
                            </div>
                        </div>
                        <div class="col s12 m6 ">
                            <div class="card-content dark p-0">
                                <p><strong class="black-text">NIT : </strong><?= $datos->nit; ?></p>
                            </div>
                        </div>
                        <div class="col s12 m6 ">
                            <div class="card-content dark p-0">
                                <p><strong class="black-text">Correo de
                                        Contacto: </strong><?= $datos->email; ?></p>
                            </div>
                        </div>
                        <div class="col s12 m6 ">
                            <div class="card-content dark p-0">
                                <p><strong class="black-text">Correo de la Empresa
                                        : </strong><?= $datos->email_confirmation; ?></p>
                            </div>
                        </div>
                        <div class="col s12 m6 ">
                            <div class="card-content dark p-0">
                                <p><strong class="black-text">Dirección
                                        : </strong><?= $datos->adress; ?></p>
                            </div>
                        </div>
                        <div class="col s12 m6 ">
                            <div class="card-content dark p-0">
                                <p><strong class="black-text">Representante
                                        Legal: </strong><?= $datos->legal_representative; ?></p>
                            </div>
                        </div>
                        <div class="col s12 m6 ">
                            <div class="card-content dark p-0">
                                <p><strong class="black-text">Número de
                                        Documento: </strong><?= $datos->num_documento; ?></p>
                            </div>
                        </div>
                        <div class="col s12 m6 ">
                            <div class="card-content dark p-0">
                                <p><strong class="black-text">Plan
                                        Adquirido: </strong><?= $datos->paquete; ?></p>
                            </div>
                        </div>
                    </div>

                </div><!--fin row-->
            </div><!--card-content-->
        </div><!--card-->

        <div class="card">
            <div class="card-content">
                <div class="row">
                    <div class="col s12">
                        <!-- -->
                <ul class="stepper horizontal" style="height:900px !important;">
                            <!--
                    <li class="step active<?= ($datos->vestado >= 1) ? ' done' : ''; ?>  ">
                        <div class="step-title waves-effect <?= ($datos->vestado >= 1) ? ' green-text text-darken-4' : ''; ?> ">
                            Solicitada
                        </div>
                        <div class="step-content">
                            <!-- Your step content goes here (like inputs or so) ->
                            <div class="card">
                                <div class="card-content">
                                    <div class="row">
                                        <div class="col s12 m12 ">
                                            <h5>Solicitud</h5>
                                            <div class="card-content dark">

                                                <p>Inicio del proceso de configuración y registro del Facturador de <b>MiFacturaLegal.COM</b> con validación previa de la DIAN. </p>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </li>
                    <li class="step <?= ($datos->vestado >= 2) ? 'done' : ''; ?> ">
                        <div class="step-title waves-effect <?= ($datos->vestado >= 2) ? ' green-text text-darken-4 d' : ''; ?> ">
                            En espera
                        </div>
                        <div class="step-content">
                            <!-- Your step content goes here (like inputs or so) ->
                            <div class="card">
                                <div class="card-content">
                                    <div class="row">
                                        <div class="col s12 m12 ">
                                            <div class="card-content dark">
                                                <p>Mucha gracias por escogernos como su aliado de facturación
                                                    electrónica con validación previa de la DIAN; Quiero comentarles
                                                    como es el proceso, este consta de dos partes, la primera es el
                                                    registro de la firma digital y la segunda del registro y pruebas con
                                                    la DIAN.

                                                </p><br>
                                                <p>Para primer proceso necesitamos que nos envíe la siguiente
                                                    documentación ( Este es un proceso que toma cinco días hábiles
                                                    aproximadamente con la empresa certificadora ):</p>
                                                <br>

                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                    -->
                            <li class="step  <?= ($datos->vestado <= 3) ? ($datos->vestado == 3) ? 'done active' : 'done' : ''; ?> ">
                                <div class="step-title waves-effect <?= ($datos->vestado <= 3) ? ' green-text text-darken-4' : ''; ?> ">
                                    Documentación
                                </div>
                                <div class="step-content" id="sidebar"  >
                                    <!-- Your step content goes here (like inputs or so) -->
                                    <div class="card">
                                        <div class="card-content">
                                            <!--<div class="row">
                                                <div class="col s12 m12 ">
                                                    <div class="card-title dark">
                                                        <h4>Documentos</h4>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="divider"></div>

                                            <div class="row">
                                                <div class="col  s12 m12 ">
                                                    <div class="card-content dark">
                                                        Lista de documentos requeridos para la solicitud de la firma digital
                                                            y la configuración del sistema de facturación electronica.
                                                    </div>
                                                </div>
                                            </div>     -->
                                            <form action="<?= base_url() ?>/solicitud/guardarDocumentos/<?= $id; ?>"
                                                  method="post" enctype="multipart/form-data">
                                                <div class="row">
                                                    Lista de documentos requeridos para la solicitud de la firma digital
                                                    y la configuración del sistema de facturación electrónica.
                                                    <p>Solamente se permiten archivos con extensiones pdf, png y jpg</p>
                                                    <div class="col s12 m12 ">
                                                        <div class="card-content dark">
                                                            <table class="table-responsive">
                                                                <thead class="green lighten-3 black-text">
                                                                <th>Documento</th>
                                                                <th>?</th>
                                                                <th>Cargar</th>
                                                                <th>Recibido</th>
                                                                <th>Validado</th>
                                                                </thead>
                                                                <tbody>
                                                                <tr>
                                                                    <td>
                                                                        <p id="vrut"><strong>Rut.</strong></p>
                                                                        <p>(Original con todas las hojas)</p>
                                                                        <p id="nrut" class="red-text">Documento Invalido</p>
                                                                    </td>
                                                                    <td>
                                                                        <a class="btn-floating tooltipped"
                                                                           style="z-index:0 !important;" data-position="left"
                                                                           data-tooltip="<?= $rut_ayuda; ?>"><i
                                                                                    style="padding-top:0px;"
                                                                                    class="material-icons">help</i></a>
                                                                    </td>
                                                                    <td class="">
                                                                        <?php if ($rut == '' || $rut == 'No'): ?>
                                                                            <div style="width:200px !important;">
                                                                                <input type="file" id="rut" required name="rut"
                                                                                       class="dropify" data-height="120">
                                                                            </div>
                                                                        <?php elseif ($rut == 'Pendiente'): ?>
                                                                            En Validación
                                                                        <?php else: ?>
                                                                            Aprobado
                                                                        <?php endif; ?>

                                                                    </td>
                                                                    <td><?= ($rut != '' && $rut == 'Si' || $rut == 'Pendiente') ? 'Si' : 'No'; ?></td>
                                                                    <td class=""><?= ($rut == '') ? 'No' : $rut; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <p id="vcc"><strong>Camara de Comercio.</strong></p>
                                                                        <p>(Fotocopia legible)</p>
                                                                        <p id="ncc" class="red-text">Documento Invalido</p>
                                                                    </td>
                                                                    <td>
                                                                        <a class="btn-floating tooltipped"
                                                                           style="z-index:0 !important;" data-position="left"
                                                                           data-tooltip="<?= $cc_ayuda; ?>"><i
                                                                                    style="padding-top:0px;"
                                                                                    class="material-icons">help</i></a>
                                                                    </td>
                                                                    <td class="">
                                                                        <?php if ($cc == '' || $cc == 'No'): ?>
                                                                            <div style="width:200px !important;">
                                                                                <input type="file"  name="cc" id="cc"
                                                                                       class="dropify" data-height="120">
                                                                            </div>
                                                                        <?php elseif ($cc == 'Pendiente'): ?>
                                                                            En Validación
                                                                        <?php else: ?>
                                                                            Aprobado
                                                                        <?php endif; ?>

                                                                    </td>
                                                                    <td><?= ($cc != '' && $cc == 'Si' || $cc == 'Pendiente') ? 'Si' : 'No'; ?></td>
                                                                    <td class=""><?= ($cc == '') ? 'No' : $cc; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <p id="vcr"><strong>Cedula de Representante.</strong></p>
                                                                        <p>(Original con todas las hojas)</p>
                                                                        <p id="ncr" class="red-text">Documento Invalido</p>
                                                                    </td>
                                                                    <td>
                                                                        <a class="btn-floating  tooltipped "
                                                                           data-position="left" style="z-index:0 !important;"
                                                                           data-tooltip="<?= $cr_ayuda; ?>"><i
                                                                                    style="padding-top:0px;"
                                                                                    class="material-icons">help</i></a>
                                                                    </td>
                                                                    <td class="">
                                                                        <?php if ($cr == '' || $cr == 'No'): ?>
                                                                            <div style="width:200px !important;">
                                                                                <input type="file" class="dropify"
                                                                                       data-height="120" required name="cr"
                                                                                       id="cr">
                                                                            </div>
                                                                        <?php elseif ($cr == 'Pendiente'): ?>
                                                                            En Validación
                                                                        <?php else: ?>
                                                                            Aprobado
                                                                        <?php endif; ?>

                                                                    </td>
                                                                    <td><?= ($cr != '' && $cr == 'Si' || $cr == 'Pendiente') ? 'Si' : 'No'; ?></td>
                                                                    <td class=""><?= ($cr == '') ? 'No' : $cr; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <p id="vcfdi"><strong>Contrato Firma Digital.<a class=" btn-flat"
                                                                                                             href="<?= base_url() . '/upload/conyauto/' . $datos->nit . '/' . $datos->contract ?>"
                                                                                                             download="<?= $datos->contract ?>"
                                                                                                             target="_blank">Descargar</a></strong>
                                                                        </p>
                                                                        <p>(Firmado por representante legal)</p>
                                                                        <p id="ncfdi" class="red-text">Documento Invalido</p>
                                                                    </td>
                                                                    <td>
                                                                        <a class="btn-floating  tooltipped" data-position="left"
                                                                           style="z-index:0 !important;"
                                                                           data-tooltip="<?= $cf_ayuda; ?>"><i
                                                                                    style="padding-top:0px;"
                                                                                    class="material-icons">help</i></a>
                                                                    </td>
                                                                    <td class="">
                                                                        <?php if ($cf == '' || $cf == 'No'): ?>
                                                                        <div style="width:200px !important;">
                                                                            <input type="file" required name="cfirmad" id="cfdi"
                                                                                   class="dropify" data-height="120">
                                                                        </div>
                                                                    </td>
                                                                    <?php elseif ($cf == 'Pendiente'): ?>
                                                                        En Validación
                                                                    <?php else: ?>
                                                                        Aprobado
                                                                    <?php endif; ?>

                                                                    <td><?= ($cf != '' && $cf == 'Si' || $cf == 'Pendiente') ? 'Si' : 'No'; ?></td>
                                                                    <td class=""><?= ($cf == '') ? 'No' : $cf; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <p id="vafirmad"><strong>Autorización Firma Digital.<a
                                                                                        class=" btn-flat"
                                                                                        href="<?= base_url() . '/upload/conyauto/' . $datos->nit . '/' . $datos->autorizacion ?>"
                                                                                        download="<?= $datos->autorizacion ?>"
                                                                                        target="_blank">Descargar</a></strong>
                                                                        </p>
                                                                        <p>(Firmado por el representante legal)</p>
                                                                        <p id="nafirmad" class="red-text">Documento Invalido</p>
                                                                    </td>
                                                                    <td>
                                                                        <a class="btn-floating  tooltipped "
                                                                           data-position="left" style="z-index:0 !important;"
                                                                           data-tooltip="<?= $af_ayuda; ?>"><i
                                                                                    style="padding-top:0px;"
                                                                                    class="material-icons">help</i></a>
                                                                    </td>
                                                                    <td class="">
                                                                        <?php if ($af == '' || $af == 'No'): ?>
                                                                            <div style="width:200px !important;">
                                                                                <input type="file" required name="afirmad"
                                                                                       id="afd" class="dropify"
                                                                                       data-height="120">
                                                                            </div>
                                                                        <?php elseif ($af == 'Pendiente'): ?>
                                                                            En Validación
                                                                        <?php else: ?>
                                                                            Aprobado
                                                                        <?php endif; ?>

                                                                    </td>
                                                                    <td><?= ($af != '' && $af == 'Si' || $af == 'Pendiente') ? 'Si' : 'No'; ?></td>
                                                                    <td class=""><?= ($af == '') ? 'No' : $af; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <p id="vrd"><strong>Resolución DIAN.</strong></p>
                                                                        <p>(De autorizacion de facturador o nómina electrónica)</p>
                                                                        <p id="nrd" class="red-text">Documento Invalido</p>
                                                                    </td>
                                                                    <td>
                                                                        <a class="btn-floating  tooltipped "
                                                                           data-position="left" style="z-index:0 !important;"
                                                                           data-tooltip="<?= $rd_ayuda; ?>"><i
                                                                                    style="padding-top:0px;"
                                                                                    class="material-icons">help</i></a>
                                                                    </td>
                                                                    <td class="">
                                                                        <?php if ($rd == '' || $rd == 'No'): ?>
                                                                            <div style="width:200px !important;">
                                                                                <input type="file" class="dropify"
                                                                                       data-height="120" id="rd"
                                                                                       name="rd">
                                                                            </div>
                                                                        <?php elseif ($rd == 'Pendiente'): ?>
                                                                            En Validación
                                                                        <?php else: ?>
                                                                            Aprobado
                                                                        <?php endif; ?>

                                                                    </td>
                                                                    <td><?= ($rd != '' && $rd == 'Si' || $rd == 'Pendiente') ? 'Si' : 'No'; ?></td>
                                                                    <td class=""><?= ($rd == '') ? 'No' : $rd; ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <p id="vcp"><strong>Comprobante de Pago</strong></p>
                                                                        <p id="ncp" class="red-text">Documento Invalido</p>
                                                                    </td>
                                                                    <td>
                                                                        <a class="btn-floating  tooltipped "
                                                                           data-position="left" style="z-index:0 !important;"
                                                                           data-tooltip="<?= $cp_ayuda; ?>"><i
                                                                                    style="padding-top:0px;"
                                                                                    class="material-icons">help</i></a>
                                                                    </td>
                                                                    <td class="">
                                                                        <?php if ($cp == '' || $cp == 'No'): ?>
                                                                            <div style="width:200px !important;">
                                                                                <input type="file" class="dropify"
                                                                                       data-height="120" required id="cp"
                                                                                       name="cp">
                                                                            </div>
                                                                        <?php elseif ($cp == 'Pendiente'): ?>
                                                                            En Validación
                                                                        <?php else: ?>
                                                                            Aprobado
                                                                        <?php endif; ?>

                                                                    </td>
                                                                    <td><?= ($cp != '' && $cp == 'Si' || $cp == 'Pendiente') ? 'Si' : 'No'; ?></td>
                                                                    <td class=""><?= ($cp == '') ? 'No' : $cp; ?></td>
                                                                </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                <br>
                                                <div class="row <?= ($rut == 'Si' && $rd == 'Si' && $af == 'Si' && $cf == 'Si' && $cr == 'Si' && $cc == 'Si') ? 'hidden' : ''; ?> ">
                                                    <div class="col s12 m12">
                                                        <div class="card-content  dark">
                                                            <button type="submit" class="btn btn-large btn-light-light-blue">
                                                                Enviar Documentos
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <br>
                                                <hr>
                                            </form>
                                        </div>
                                    </div>
                                    <!-- -->
                                </div>
                            </li>
                            <!--
                    <li class="step ">
                        <div class="step-title waves-effect <?= ($datos->vestado >= 4) ? ' green-text text-darken-4' : ''; ?> ">
                            Validación
                        </div>
                        <div class="step-content">
                            <!-- Your step content goes here (like inputs or so) ->
                            <p>Gracias por su confianza en nosotros, le confirmo el recibido de la documentaci&oacute;n;
                                Y que sigue?, este proceso consta de dos partes, la primera es el registro de la firma
                                digital y la segunda del registro y pruebas con la DIAN.</p>

                            <p>
                            <p><strong>Expedici&oacute;n de la firma digital</strong>, la empresa certificadora
                                &quot;THOMAS SIGNE SAS&quot;,<strong> se pondr&aacute; en contacto con el
                                    representante legal</strong> para validar los datos registrados. ( en algunas
                                ocasiones solo la documentaci&oacute;n es suficiente para que la expidan ), esto
                                depende de THOMAS y puede demorar hasta 5 d&iacute;as h&aacute;biles, nosotros
                                estaremos muy pendientes.
                            </p>
                            <p><strong>El segundo</strong> es la configuraci&oacute;n del sistema de MiFacturaLegal
                                <strong>y pruebas con la DIAN</strong> ( El proceso toma m&aacute;ximo un d&iacute;a
                                h&aacute;bil ) y le notificaremos una vez finalice. &nbsp;&nbsp;
                            </p>
                            </p>

                        </div>
                    </li>
                    -->
                            <li class="step  <?= ($datos->vestado >= 4) ?($datos->vestado == 4 ) ? 'done active' : 'done' : '';  ?> ">
                                <div class="step-title waves-effect <?= ($datos->vestado >= 4) ? ' green-text text-darken-4' : ''; ?> ">
                                    Validaciones
                                </div>
                                <div class="step-content">
                                    <!-- Your step content goes here (like inputs or so) -->
                                    <p>Este proceso consta de dos partes, la primera es el registro de la firma digital y la segunda del registro y pruebas con la DIAN.:<br><br>
                                    <ul>
                                        <li><b>Expedición de la firma digital</b>, la empresa certificadora "THOMAS SIGNE SAS",
                                            se pondrá en contacto con el representante legal para validar los datos registrados. ( en algunas ocasiones solo la documentación es suficiente para que la expidan ), esto depende de THOMAS, nosotros estaremos muy pendientes.</li>
                                        <li><b>El segundo</b> es la configuración del sistema de MiFacturaLegal y pruebas con la DIAN y le notificaremos una vez finalice</li>
                                    </ul>
                                    Si tienen cualquier inquietud estaremos pendientes.
                                    <br><br>

                                    <b>Equipo de Soporte</b><br>
                                    MiFacturaLegal.com
                                    </p>

                                </div>
                            </li>
                            <li class="step <?= ($datos->vestado >= 6)? ($datos->vestado == 6) ? 'done active' : 'done' : ''; ?> ">
                                <div class="step-title waves-effect <?= ($datos->vestado >= 6) ? ' green-text text-darken-4' : ''; ?> ">
                                    Envio credenciales
                                </div>
                                <div class="step-content">
                                    <!-- Your step content goes here (like inputs or so) -->
                                    Le notificaremos una vez finalice el proceso al correo elecrtonico registrado, si tiene alguna duda por favor contactenos a soporte@mifacturalegal.com.
                                    <br><br>
                                    <b>Equipo de Soporte</b><br>
                                    MiFacturaLegal.com
                                </div>
                            </li>
                        </ul>
                    </div>
                    <!-- info -->

                    <!-- end info -->

                    <!-- end datos -->

                </div>
                <!-- end documentacion -->

            </div><!--card-content-->
        </div><!--card-->

    </div>
</div>
<!-- modal de pago -->
<div id="pagar" class="modals modals-fixed-footer" style="height: 400px !important;">
    <div class="modals-content">
        <h5 class="card-title">Realizar pago</h5>
        <div class="divider" style="margin: 20px 0px;"></div>
        <div>
            <form action="" method="get">
                <div class="input-field col s12">
                    <select name="plan" id="">
                        <option value="" selected>Selecione su Plan</option>
                        <option value="basico">Basico $180.000</option>
                        <option value="emprendedor">Emprendedor $342.000</option>
                        <option value="empresarial">Empresarial $474.000</option>
                        <option value="premium">Premium $618.000</option>
                        <option value="gold">Gold $1.350.000</option>
                    </select>
                    <label>Seleccione su plan</label>
                </div>
        </div>
    </div>
    <div class="modals-footer">
        <button type="submit" class=" waves-effect waves-green btn-flat">Pagar</button>
        <a href="#!" class="modals-action modals-close waves-effect waves-green btn-flat ">Cerrar</a>
    </div>
    </form>
</div>
</div>

<?= view('layouts/footer') ?>
<script>

    $(function () {
        $('.modals').modals();
        $('#rut').modals('open');
        $('#rut').modals('close');
    });

    var stepper = document.querySelector('.stepper');
    var stepperInstace = new MStepper(stepper, {
        // options
        firstActive: <?php if($datos->vestado == 6){
            echo 2;
        }elseif($datos->vestado == 4){
            echo 1;
        }else{
            echo 0;
        } ?>
    });
    $(document).ready(function () {
        $('.dropify').dropify();
        $('#nrut').hide();
        $('#ncc').hide();
        $('#ncr').hide();
        $('#ncfdi').hide();
        $('#nafirmad').hide();
        $('#nrd').hide();
        $('#ncp').hide();
        $("#rut").on("change", (e) => {
          const archivo = $(e.target)[0].files[0];
          let nombArchivo = archivo.name;
          var extension = nombArchivo.split(".").slice(-1);
              extension = extension[0];
          let extensiones = ["pdf", "png", "jpg"];
              if(extensiones.indexOf(extension) === -1){
                $("#vrut").css("color", "red");
                $('#nrut').show();
              }else{
                $("#vrut").css("color", "black");
                $('#nrut').hide();
              }
    });
         $("#cc").on("change", (e) => {
          const archivo = $(e.target)[0].files[0];
          let nombArchivo = archivo.name;
          var extension = nombArchivo.split(".").slice(-1);
              extension = extension[0];
          let extensiones = ["pdf", "png", "jpg"];
              if(extensiones.indexOf(extension) === -1){
                $("#vcc").css("color", "red");
                $('#ncc').show();
              }else{
                $("#vcc").css("color", "black");
                $('#ncc').hide();
              }
        });
         $("#cr").on("change", (e) => {
          const archivo = $(e.target)[0].files[0];
          let nombArchivo = archivo.name;
          var extension = nombArchivo.split(".").slice(-1);
              extension = extension[0];
          let extensiones = ["pdf", "png", "jpg"];
              if(extensiones.indexOf(extension) === -1){
                $("#vcr").css("color", "red");
                $('#ncr').show();
              }else{
                $("#vcr").css("color", "black");
                $('#ncr').hide();
              }
        });
         $("#cfdi").on("change", (e) => {
          const archivo = $(e.target)[0].files[0];
          let nombArchivo = archivo.name;
          var extension = nombArchivo.split(".").slice(-1);
              extension = extension[0];
          let extensiones = ["pdf", "png", "jpg"];
              if(extensiones.indexOf(extension) === -1){
                $("#vcdfi").css("color", "red");
                $('#ncfdi').show();
              }else{
                $("#vcdfi").css("color", "black");
                $('#ncfdi').hide();
              }
        });
         $("#afirmad").on("change", (e) => {
          const archivo = $(e.target)[0].files[0];
          let nombArchivo = archivo.name;
          var extension = nombArchivo.split(".").slice(-1);
              extension = extension[0];
          let extensiones = ["pdf", "png", "jpg"];
              if(extensiones.indexOf(extension) === -1){
                $("#vafirmad").css("color", "red");
                $('#nafirmad').show();
              }else{
                $("#vafirmad").css("color", "black");
                $('#nafirmad').hide();
              }
        });
         $("#rd").on("change", (e) => {
          const archivo = $(e.target)[0].files[0];
          let nombArchivo = archivo.name;
          var extension = nombArchivo.split(".").slice(-1);
              extension = extension[0];
          let extensiones = ["pdf", "png", "jpg"];
              if(extensiones.indexOf(extension) === -1){
                $("#vrd").css("color", "red");
                $('#nrd').show();
              }else{
                $("#vrd").css("color", "black");
                $('#nrd').hide();
              }
        });
         $("#cp").on("change", (e) => {
          const archivo = $(e.target)[0].files[0];
          let nombArchivo = archivo.name;
          var extension = nombArchivo.split(".").slice(-1);
              extension = extension[0];
          let extensiones = ["pdf", "png", "jpg"];
              if(extensiones.indexOf(extension) === -1){
                $("#vcp").css("color", "red");
                $('#ncp').show();
              }else{
                $("#vcp").css("color", "black");
                $('#ncp').hide();
              }
        });
    });


</script>