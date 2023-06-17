<?= view('layouts/header') ?>
<?= view('layouts/navbar_horizontal') ?>
<?= view('layouts/navbar_vertical') ?>

    <div id="app">
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
                                    <button type="button" class="close white-text" data-dismiss="alert"
                                            aria-label="Close">
                                        <span aria-hidden="true">×</span>
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
                                        <span aria-hidden="true">×</span>
                                    </button>
                                </div>
                            <?php endif; ?>
                            <div class="card">
                                <div class="card-content">
                                    <div class="card-title">
                                        Seguimiento
                                        <small> Listado de Cotizaciones</small>
                                        <button class="btn green float-right modals-trigger <?= $tracking[0]->invoice_status_id == 6 ? 'disabled' : '' ?>" data-target="modal1">
                                            Registrar
                                        </button>
                                    </div>
                                    <div class="divider"></div>

                                    <ul class="collapsible z-depth-0">
                                        <?php foreach ($data as $item): ?>
                                        <li>
                                            <div class="collapsible-header"><i class="material-icons">book</i>
                                                <?='N° '.$item->id.' - '. strtoupper($item->username).'  '.$item->created_at ?>

                                            </div>
                                            <div class="collapsible-body">
                                                <?php if( $tracking[0]->invoice_status_id != 6): ?>
                                                <button class="btn btn-small float-right modals-trigger quotation_edit" data-target="modaledit" data-id="<?=  $item->id  ?>">
                                                    <i class="material-icons">create</i>
                                                </button>
                                                <?php endif; ?>
                                                <span><?=  $item->message  ?></span>
                                            </div>
                                        </li>
                                        <?php  endforeach; ?>
                                    </ul>
                                    <?php  if(count($data) == 0): ?>
                                        <p class="text-red red-text text-center">No se ha encontrado registros.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <form action="<?php base_url() ?>/tracking/create/<?= $id ?>/quotation" method="post" id="form">
            <div id="modal1" class="modals modals-fixed-footer" style="height: 620px !important;">
                <div class="modals-content" style="padding-top: 20px;">
                    <div class="row">
                        <div class="col s12" style="padding-left: 0px ">
                            <h5 style="margin-top: 0px; margin-bottom: 20px;">Registrar Seguimiento</h5>
                        </div>
                    </div>
                    <div class="divider" style="margin-bottom: 10px;"></div>
                    <div class="row">
                        <div class="col s8"></div>
                        <div class="input-field col s4 offset-8" id="created_at" style="display:none;">
                            <input type="date" name="created_at" >
                            <label>Notificar el día.</label>
                        </div>
                        <div class="col s12">
                            <label for="first_name">Observación</label>
                            <textarea id="editorCreate" class="editor"  rows="20" name="message" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modals-footer">
                    <div class="float-right">
                        <a href="#!" class="modals-action modals-close waves-effect waves-green btn-flat ">Cerrar</a>
                        <button class="btn ">Registrar</button>
                    </div>
                    <div class="float-right " style="margin-top: 5px;">
                        <div class="switch"
                             style="margin-top: 0px; display: flex; justify-content: flex-end; margin-top: 10px; ">
                            <label>
                                Notificar
                                <input type="checkbox" id="notification" name="notification">
                                <span class="lever"></span>
                            </label>
                        </div>
                    </div>

                </div>
            </div>
            </form>



            <form action="" method="post" id="formEdit">
                <div id="modaledit" class="modals modals-fixed-footer" style="height: 500px !important;">
                    <div class="modals-content" style="padding-top: 20px;">
                        <div class="row">
                            <div class="col s9" style="padding-left: 0px ">
                                <h5 style="margin-top: 0px; margin-bottom: 20px;">Editar Seguimiento</h5>
                            </div>
                        </div>
                        <div class="divider" style="margin-bottom: 10px;"></div>
                        <div class="row">
                            <div class="col s8"></div>
                            <div class="col s12">
                                <label for="first_name">Observación</label>
                                <textarea id="editorEdit"  rows="20" name="message" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modals-footer">
                        <a href="#!" class="modals-action modals-close waves-effect waves-green btn-flat ">Cerrar</a>
                        <button class="btn ">Actualizar</button>
                    </div>
                </div>
            </form>


            <?= view('layouts/footer'); ?>

            <script src="https://cdn.ckeditor.com/ckeditor5/23.1.0/classic/ckeditor.js"></script>

            <script>
                $(document).ready(function(){


                    const created = CKEDITOR.replace( 'editorCreate' , {
                        extraPlugins: 'notification'
                    });

                    created.on( 'required', function( evt ) {
                        edit.showNotification( 'El campo Observación es obligatorio.', 'warning' );
                        evt.cancel();
                    });

                    const edit = CKEDITOR.replace( 'editorEdit' ,  {
                        extraPlugins: 'notification'
                    });

                    edit.on( 'required', function( evt ) {
                        edit.showNotification( 'El campo Observación es obligatorio.', 'warning' );
                        evt.cancel();
                    });

                    var url = window.location.origin;
                    $("#notification").change(function() {
                        if($('#notification').prop('checked')) {
                            $('#created_at').show();
                        } else {
                            $('#created_at').hide();

                        }
                    });

                    $('.quotation_edit').click(function () {
                        const id = $(this).data('id');
                        $('#formEdit').attr('action',`${url}/tracking/update/<?= $id ?>/quotation/${id}`)
                        $.get(`${url}/tracking/edit/${id}`, function (data) {
                            const info = JSON.parse(data);
                            edit.setData(info.message);
                        });
                    });
                });
            </script>