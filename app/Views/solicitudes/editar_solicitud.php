<?= view('layouts/header') ?>
<?= view('layouts/navbar_horizontal') ?>
<?= view('layouts/navbar_vertical') ?>
<!-- vista -->
<style>
    .text-center {
        text-align: center;
    }

    td {
        padding: 3px 5px !important;
    }

    .container-sprint-email,
    .container-sprint-send {
        background: rgba(0, 0, 0, 0.51);
        z-index: 2000;
        position: absolute;
        width: 100%;
        top: 0px;
        height: 100vh;
        justify-content: center !important;
        align-content: center !important;
        flex-wrap: wrap;
        display: none;
    }
</style>
<!-- BEGIN: Page Main-->
<div id="main">
    <div class="row">
        <div class="col s12">
            <div class="container">
                <div class="section">
                    <?php if (session('success')): ?>
                        <div class="card-alert card green">
                            <div class="card-content white-text">
                                <?= session('success') ?>
                            </div>
                            <button type="button" class="close white-text" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                    <?php endif; ?>
                    <?php if (session('errors')): ?>
                        <div class="card-alert card red">
                            <div class="card-content white-text">
                                <?= session('errors') ?>
                            </div>
                            <button type="button" class="close white-text" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                    <?php endif; ?>
                    <div class="card">
                        <div class="card-content">
                            <div class="divider"></div>
                            <div class="divider"></div>
                            <div class="row">
                                <div class="col s12 m6" style="position: relative; ">
                                    <div class="card-title dark">
                                        <h4> Solicitud # <?= $solicitud[0]->idsolicitud ?></h4>
                                    </div>
                                    <!--<a href="/invoice/create" class="btn">Registrar</a>-->
                                </div>
                                <div class="col s12 m4 " style="position: relative; ">
                                    <div class="card-title blue-text text-darken-2">
                                        <h4> Estado: <?= $solicitud[0]->estado ?></h4>
                                    </div>
                                    <!--<a href="/invoice/create" class="btn">Registrar</a>-->
                                </div>
                                <div class="col s12 m2 " style="position: relative; ">
                                    <div class="card-title blue-text text-darken-2"><a style="margin-top:15px;"
                                                                                       href="<?= base_url() ?>/solicitudes"
                                                                                       class="btn btn-light-blue">Regresar</a>
                                    </div>
                                    <!--<a href="/invoice/create" class="btn">Registrar</a>-->
                                </div>
                            </div>
                            <div class="divider"></div>
                            <!-- datos empresa -->
                            <br>
                            <div class="table-response">
                                <div class="row">
                                    <form class="col s12"
                                          action="<?= base_url() ?>/solicitud/edit/<?= $solicitud[0]->idsolicitud ?>"
                                          method="post">
                                        <div class="row">
                                            <div class="input-field col s6">
                                                <input id="nempresa" name="nempresa" type="text"
                                                       value="<?= $solicitud[0]->company_name ?>" class="validate">
                                                <label for="nempresa">Nombre Empresa</label>
                                            </div>
                                            <div class="input-field col s6">
                                                <input id="nit" name="nit" value="<?= $solicitud[0]->nit ?>" type="text"
                                                       class="validate">
                                                <label for="nit">Nit</label>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="input-field col s12">
                                                <input id="direccion" name="direccion" type="text"
                                                       value="<?= $solicitud[0]->adress ?>" class="validate">
                                                <label for="direccion">Dirección</label>
                                            </div>
                                            <div class="input-field col s6">
                                                <input id="correo" name="correo" value="<?= $solicitud[0]->email ?>"
                                                       type="text" class="validate">
                                                <label for="correo">Correo Contacto</label>
                                            </div>
                                            <div class="input-field col s6">
                                                <input id="correo" name="correoem"
                                                       value="<?= $solicitud[0]->email_confirmation ?>"
                                                       type="text" class="validate">
                                                <label for="correo">Correo Empresa</label>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="input-field col s12">
                                                <input id="rl" name="rl"
                                                       value="<?= $solicitud[0]->legal_representative ?>" type="text"
                                                       class="validate">
                                                <label for="rl">Representante Legal</label>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="input-field col s6">
                                                <input id="direccion" name="tdocumento" type="text"
                                                       value="<?= $solicitud[0]->type_document ?>" class="validate">
                                                <label for="direccion">Tipo Documento</label>
                                            </div>
                                            <div class="input-field col s6">
                                                <input id="documento" name="documento"
                                                       value="<?= $solicitud[0]->num_documento ?>" type="text"
                                                       class="validate">
                                                <label for="documento">Número Documento</label>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="input-field col s6">
                                                <button type="submit" class="btn btn-light-blue">Editar Información
                                                </button>
                                                <a class="btn btn-light-blue ml-1 modals-trigger" href="#seguimiento">Seguimiento</a>
                                            </div>
                                            <div class="input-field col s6">
                                                <?php if ($solicitud[0]->estado == 'En espera' || $solicitud[0]->estado == 'Validacion' || $solicitud[0]->estado == 'Finalizado'): ?>
                                                    <div>
                                                        <a href="<?= base_url() ?>/solicitud/reenvio/<?= $solicitud[0]->idsolicitud ?>"
                                                           class="btn btn-light-light-blue float-right">Reenvio
                                                            Correo</a>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>


                                    </form>
                                </div>
                            </div>
                            <br>
                            <!-- end datos empresa-->
                            <!-- formularios autorizacion y contrato -->

                            <?php if ($solicitud[0]->estado == 'Solicitada'): ?>
                            <hr>
                            <div class="table-response">
                                <div class="row">
                                    <form class="col s12"
                                          action="<?= base_url() ?>/solicitud/archivos/<?= $solicitud[0]->idsolicitud ?>"
                                          method="post" enctype="multipart/form-data">
                                        <div class="row">
                                            <div class="col s12 " style="position: relative;">
                                                <div class="card-title dark">Subir Archivos contrato y autorización
                                                </div>
                                                <!--<a href="/invoice/create" class="btn">Registrar</a>-->
                                            </div>
                                        </div>
                                </div>
                                <div class="row">
                                    <div class="file-field input-field col s5">
                                        <?php if ($solicitud[0]->contract != ''): ?>
                                            <div class="btn">
                                                <span>Contrato</span>
                                                <input type="file" disabled name="contrato">
                                            </div>
                                            <div class="file-path-wrapper">
                                                <input class="file-path " disabled type="text"
                                                       value="<?= $solicitud[0]->contract ?>"
                                                       placeholder="Subir Contrato">
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($solicitud[0]->contract == ''): ?>
                                            <div class="btn">
                                                <span>Contrato</span>
                                                <input type="file" required name="contrato">
                                            </div>
                                            <div class="file-path-wrapper">
                                                <input class="file-path " required type="text"
                                                       placeholder="Subir Contrato">
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="file-field input-field col s5">
                                        <?php if ($solicitud[0]->autorizacion != ''): ?>
                                            <div class="btn">
                                                <span>Autorización</span>
                                                <input type="file" name="autorizacion" disabled>
                                            </div>
                                            <div class="file-path-wrapper">
                                                <input class="file-path validate" type="text" disabled
                                                       value="<?= $solicitud[0]->autorizacion ?>"
                                                       placeholder="Subir Autorización">
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($solicitud[0]->autorizacion == ''): ?>
                                            <div class="btn">
                                                <span>Autorización</span>
                                                <input type="file" required name="autorizacion">
                                            </div>
                                            <div class="file-path-wrapper">
                                                <input class="file-path validate" required type="text"
                                                       placeholder="Subir Autorización">
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="file-field input-field col s2">
                                        <?php if ($solicitud[0]->autorizacion != '' && $solicitud[0]->contract != '') {
                                            echo '<button type="submit" class="btn  btn-light-light-blue">Guardar</button>';
                                        } else {
                                            echo '<button type="submit" class="btn btn-light-light-blue">Guardar</button>';
                                        } ?>

                                    </div>

                                </div>

                                </form>
                            </div>
                        </div>
                        <?php endif; ?>
                        <!-- end formulario -->
                        <!-- formulario de validacion -->
                        <hr>
                        <div class="table-response">
                            <div class="row">
                                <div class="row">
                                    <div class="col s12 " style="position: relative;">
                                        <div class="card-title dark text-center">
                                            <h6>Documentos</h6>
                                        </div>
                                        <!--<a href="/invoice/create" class="btn">Registrar</a>-->
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <form action="<?= base_url() ?>/solicitud/validacion/<?= $solicitud[0]->idsolicitud ?>"
                                  method="post">
                                <div class="row">
                                    <div class="file-field input-field col s12">
                                        <table class="table-responsive">
                                            <thead>
                                            <tr>
                                                <th>Documentos</th>
                                                <th>Acciones</th>
                                                <th>Aprobado</th>
                                            </tr>
                                            </thead>

                                            <tbody>
                                            <?php if (isset($documentos) && $documentos != ''):
                                                $array = [];
                                                foreach ($documentos as $documento):

                                                    ?>
                                                    <tr>
                                                        <td><?= $documento->documento; ?></td>
                                                        <td><?php if ($documento->documento == 'Comprobante de pago' && $documento->archivo == '') { ?>
                                                                <p>Pagado por epayco</p>

                                                            <?php }elseif($documento->documento == 'Comprobante de pago' && $documento->archivo == 'pago proveedor'){ $provedor='si'?>
                                                                <p>Pago Proveedor</p>
                                                            <?php } else { ?>
                                                                <a class=" btn-flat"
                                                                   href="<?= $ruta_img, $documento->archivo ?>"
                                                                   download="<?= $documento->archivo ?>"
                                                                   target="_blank">Ver</a>
                                                                - <a data-id="<?= $documento->id ?>"
                                                                     class=" btn-flat modals-trigger"
                                                                     href="#modal<?= $documento->id ?>">Editar</a>
                                                            <?php } ?>
                                                        </td>
                                                        <td>
                                                            <div class="switch">
                                                                <label>
                                                                    No
                                                                    <input
                                                                            <?=($solicitud[0]->estado == 'Finalizado')?'disabled':'' ?>
                                                                            onclick="estado('<?= $documento->id ?>','<?= $documento->status ?>')"
                                                                            name='aprobado[<?= $documento->id ?>]'
                                                                            id='checked[<?= $documento->id ?>]'
                                                                            type="checkbox"
                                                                        <?= ($documento->status == 'Aprobado') ? 'checked' : ''; ?>>
                                                                    <span class="lever"></span>
                                                                    Si
                                                                </label>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <!-- modal -->

                                                    <!-- end modal -->
                                                    <?php
                                                    array_push($array, $documento->status);
                                                endforeach;
                                            endif; ?>
                                            </tbody>
                                        </table>
                                        <hr>

                                        <div class="section">
                                            <h5 class="text-center">Información de pago</h5>
                                            <?php if (isset($pago) && $pago != ''): ?>
                                                <div class="file-field input-field col s6">
                                                    <p><strong>Plan
                                                            Seleccionado:</strong> <?= $pago->name . "           " ?>
                                                    </p>
                                                </div>
                                                <div class="file-field input-field col s6">
                                                    <p><strong>Fecha de pago:</strong> <?= $pago->start_date ?></p>
                                                </div>
                                                <div class="file-field input-field col s6">
                                                    <p><strong>Número de comprobante:</strong>
                                                        <?= $pago->ref_epayco . "           " ?></p>
                                                </div>
                                                <div class="file-field input-field col s6">
                                                    <p><strong>Medio de
                                                            pago: </strong> <?= ($pago->ref_epayco != '') ? (isset($provedor))?'Proveedor':'Epayco' : 'Transaccion'; ?>
                                                    </p>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <hr>
                                        <?php $valor = 'Desaprobado';
                                        if(isset($array)):
                                        if (in_array($valor, $array) || $solicitud[0]->estado == 'Validacion' || $solicitud[0]->estado == 'Pruebas y credenciales' || $solicitud[0]->estado == 'Finalizado'): ?>
                                            <div class="file-field input-field col s6">
                                                <button type="submit" disabled class="btn btn-light-light-blue">Guardar
                                                    Validación
                                                </button>
                                            </div>
                                        <?php endif; endif; ?>
                                        <?php $valor = 'Desaprobado';
                                        if(isset($array)):
                                        if (!in_array($valor, $array) && $solicitud[0]->estado != 'Validacion' && $solicitud[0]->estado != 'Pruebas y credenciales' && $solicitud[0]->estado != 'Finalizado'): ?>
                                            <div class="file-field input-field col s6">
                                                <button type="submit" class="btn btn-light-light-blue">Guardar
                                                    Validación
                                                </button>
                                            </div>
                                        <?php endif;  endif;?>

                            </form>
                        </div>
                    </div>




                    <!-- end formulario de validacion  -->
                    <!-- prueba y credenciales -->
                    <?php if ($solicitud[0]->estado == 'Pruebas y credenciales' || $solicitud[0]->estado == 'Validacion'): ?>
                        <hr>
                        <div class="table-response">
                            <div class="row">
                                <div class="row">
                                    <div class="col s12 " style="position: relative;">
                                        <div class="card-title dark text-center">
                                            <?php if($solicitud[0]->process != 'renovacion'):?>
                                                <h5>Pruebas y Credenciales</h5>
                                            <?php else:?>
                                                <h5>renovación: Certificado digital </h5>
                                            <?php endif;?>

                                        </div>
                                        <!--<a href="/invoice/create" class="btn">Registrar</a>-->
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <form action="<?= base_url() ?>/solicitud/pruebaycredenciales/<?= $solicitud[0]->idsolicitud ?>"
                                  enctype="multipart/form-data" method="post">
                                <div class="row">
                                    <?php if($solicitud[0]->process != 'renovacion'): ?>
                                    <div>
                                        <div class="file-field input-field col s4">
                                            <div class="btn">
                                                <span>Prueba 1</span>
                                                <input type="file" required name="prueba">
                                            </div>
                                            <div class="file-path-wrapper">
                                                <input class="file-path validate" required type="text"
                                                       placeholder="Subir Imagen prueba 1">
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="file-field input-field col s4">
                                            <div class="btn">
                                                <span>Prueba 2</span>
                                                <input type="file" required name="pruebas">
                                            </div>
                                            <div class="file-path-wrapper">
                                                <input class="file-path validate" required type="text"
                                                       placeholder="Subir Imagen prueba 2">
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    <div>
                                        <div class="file-field input-field col <?= ($solicitud[0]->process != 'renovacion')?'s4':'s12' ?>">
                                            <div class="btn">
                                                <span>Certificado</span>
                                                <input type="file" required name="certificado">
                                            </div>
                                            <div class="file-path-wrapper">
                                                <input class="file-path validate" required type="text"
                                                       placeholder="Subir certificado">
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <?php if($solicitud[0]->process != 'renovacion'): ?>
                                <div class="row">
                                    <div class="input-field col s6">
                                        <input id="usuario" name="usuario" required type="text" class="validate">
                                        <label class="active" for="usuario">Usuario</label>
                                    </div>
                                    <div class="input-field col s6">
                                        <input id="clave" name="clave" required type="password" class="validate">
                                        <label class="active" for="clave">Contraseña</label>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <div class="row">
                                    <div class="file-field input-field col s6">
                                        <button type="submit" class="btn btn-light-light-blue">Enviar</button>
                                    </div>
                                </div>


                            </form>
                        </div>


                    <?php endif; ?>


                    <!-- end prueba y credenciales-->

                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
<!-- end vista -->
<?php if (isset($documentos) && $documentos != ''):

    foreach ($documentos as $documento):

        ?>
        <form action="<?= base_url() ?>/solicitud/documento/edit/<?= $solicitud[0]->idsolicitud ?>/<?= $documento->id ?>"
              enctype="multipart/form-data" method="post">
            <div id="modal<?= $documento->id ?>" class="modals modals-fixed-footer" style="height: 200px !important;">
                <div class="modals-content">


                    <h5 class="card-title">Editar archivo</h5>
                    <div class="divider" style="margin: 20px 0px;"></div>
                    <div>
                        <div class="file-field input-field col s12">
                            <input type="hidden" name="documento" value="<?= $documento->documento ?>">
                            <div class="btn">
                                <span><?= $documento->documento ?></span>
                                <input type="file" name="edicion">
                            </div>
                            <div class="file-path-wrapper">
                                <input class="file-path validate" type="text" value="<?= $documento->archivo ?>"
                                       placeholder="Subir Autorización">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modals-footer">
                    <button class="btn-flat" type="submit">Guardar</button>
                    <a href="#!" class="modals-action modals-close waves-effect waves-green btn-flat ">Cerrar</a>
                </div>

            </div>
            </div>
        </form>
    <?php
    endforeach;
endif; ?>
<!-- modal de seguimiento -->
<div id="seguimiento" class="modals modal-lg" style="height: 800px;" >
    <div class="modals-content" >
        <div>
            <form action="<?= base_url()?>/solicitudes/seguimiento/<?= $solicitud[0]->idsolicitud ?>" method="post">
                <div class="row">
                    <div class="input-field col s8">
                        <i class="material-icons prefix">mode_edit</i>
                        <textarea id="icon_prefix2" required name="log" class="materialize-textarea"></textarea>
                        <label for="icon_prefix2">Comentario</label>
                    </div>
                    <input type="hidden" name="user" value="<?= session('user')->id ?>">
                    <div class="input-field col s4">
                        <button type="submit" class="modals-action modal-close waves-effect waves-green btn ">Comentar</button>
                    </div>
                </div>
            </form>
        </div>
        <div id="work-collections" class="seaction">
            <div class="row">
                <div class="col s12 m12 xl12">
                    <ul id="projects-collection" class="collection z-depth-1">
                        <li class="collection-item avatar">
                            <i class="material-icons cyan circle">card_travel</i>
                            <h6 class="collection-header m-0">Seguimiento de Solicitud</h6>
                        </li>
                        <div class="" style ="overflow-y: scroll; height: 150px;">
                        <?php foreach($tracings as $tracing): ?>
                            <li class="collection-item">
                                <div class="row">
                                    <div class="col s3">
                                        <p class="collections-title font-weight-600"><?= $tracing->date ?></p>
                                        <p class="collections-content"><?= $tracing->name ?></p>
                                    </div>
                                    <div class="col s9">
                                        <p><?= $tracing->log ?></p>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                        </div>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<?= view('layouts/footer') ?>
<script>
    $(function () {
        var id = $(this).data('id');
        $('.modals').modals();
    });
    $(document).ready(function () {
        $("#checked").click(function () {
            console.log('aqui!');
        });
    });

    function estado(id, estado) {
        if (estado == '') {
            var nuevoEstado = 'Aprobado';
        }
        if (estado == 'Pendiente') {
            var nuevoEstado = 'Aprobado';
        }
        if (estado == 'Aprobado') {
            var nuevoEstado = 'Desaprobado';
        }
        if (estado == 'Desaprobado') {
            var nuevoEstado = 'Aprobado';
        }

        $.post('<?= base_url() ?>/solicitud/documento/estado', {
            'id': id,
            'estado': nuevoEstado
        }, function (data) {
            location.reload();
        });

    }
</script>