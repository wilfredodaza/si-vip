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
                                                                                       href="<?= base_url() ?>/solicitudes/incompletas"
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
                                          action="<?= base_url() ?>/solicitud/incompletas/edit/<?= $solicitud[0]->idsolicitud ?>"
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
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <br>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
<!-- modal de seguimiento -->
<div id="seguimiento" class="modals modal-lg" style="height: 800px;" >
    <div class="modals-content" >
        <div>
            <form action="<?= base_url()?>/solicitudes/incompletas/seguimiento/<?= $solicitud[0]->idsolicitud ?>" method="post">
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

