<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?> Eventos <?= $this->endSection() ?>
<?= $this->section('content') ?>
    <div id="main">
        <div class="row">
            <div class="col s12">
                <?= $this->include('layouts/alerts') ?>
                <div class="container">
                    <div class="section">
                        <div class="card">
                            <div class="card-content">
                                <div class="row">
                                    <!--Botonera de eventos RADIAN-->
                                    <div class="col s12">
                                        <a href="<?=  base_url().route_to('documents-event', $id, 1, 0, 0) ?>" class="btn btn-small indigo btn-sm sprint-load" data-sprint-text="Emitiendo Evento">
                                            Acuse de recibido <i class="material-icons right" >file_download</i>
                                        </a>
                                        <a href="<?=  base_url().route_to('documents-event', $id, 3, 0, 0) ?>" class="btn btn-small purple btn-sm sprint-load"  data-sprint-text="Emitiendo Evento">
                                            Recepción de bienes <i class="material-icons right" >inbox</i>
                                        </a>
                                        <a href="<?=  base_url().route_to('documents-event', $id, 4, 0, 0) ?>" class="btn btn-small green btn-sm sprint-load" data-sprint-text="Emitiendo Evento">
                                            Aceptación<i class="material-icons right" >check</i>
                                        </a>
                                        <button data-target="modal1" class="btn modal-trigger btn btn-small red btn-sm">
                                            Rechazar<i class="material-icons right" >close</i>
                                        </button>
                                        <a href="<?= base_url().route_to('document-index') ?>" class="btn btn-small btn-light-indigo right btn-sm" >
                                            Regresar <i class="material-icons right">keyboard_return</i>
                                        </a>
                                    </div>
                                    <!-- end Botonera de eventos RADIAN--->

                                    <!--iframe encargado de mostrar las facturación-->
                                    <div class="col s12 m8">
                                        <br>
                                        <iframe src="<?=  base64file('document_reception/'.$documents->identification_number.'/pdf', $documents->pdf) ?>" type="application/pdf"  name="<?= $documents->name_pdf ?>" class="p-2 border-none"  width="100%" height="500px"></iframe>
                                    </div>
                                    <!--- end iframe encargado de mostrar las facturación -->

                                    <!--Tabla de documentos externos subidos al sistema-->
                                    <div class="col s12 m4">
                                        <h5>Eventos Emitidos</h5>
                                        <a href="<?= base_url().route_to('documents-download', $documents->zip) ?>" download="<?= $documents->zip_name ?>" class="btn btn-small indigo right mr-1 btn-sm" >Adjunto
                                            <i class="material-icons right">attach_file</i>
                                        </a>
                                        <table class="table center">
                                            <thead>
                                            <tr>
                                                <th>Id</th>
                                                <th>Fecha</th>
                                                <th>Evento</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach ($events as $event):?>
                                                <tr>
                                                    <td><?= $event->document_event_id ?></td>
                                                    <td><?= $event->created_at ?></td>
                                                    <td><?= $event->event_name ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                            <?php if(count($events) == 0): ?>
                                                <tr>
                                                    <td colspan="3">
                                                        <p class="center red-text">No hay ningún elemento registrado.</p>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <!--End  Tabla de documentos externos subidos al sistema-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--modal formulario de motivos de rechazo de factura externa RADIAN  -->
    <form action="<?= base_url(['documents', 'event', $id, 2]) ?>" id="form-event" method="get"></form>
        <div id="modal1" class="modal">
            <div class="modal-content">
                <h4>Tipo de Rechazo</h4>
                <div class="row">

                    <div class="input-field col s12 m12">
                        <select class="browser-default" id="type_rejection_id" name="type_rejection_id">
                            <option value="" disabled>Seleccione ...</option>
                            <?php foreach ($typeRejections as $item): ?>
                                <option value="<?= $item->id ?>">[<?= $item->code ?>] - <?= ucfirst($item->name) ?> </option>
                            <?php endforeach; ?>
                        </select>
                        <label for="type_rejection_id" class="active">Medio de pago <span class="text-red red-text darken-1">*</span></label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#!" class="modal-close waves-effect waves-green btn-flat">Cerrar</a>
                <button  class="btn modal-close btn-light-indigo waves-effect waves-green sprint-load" onclick="enviar_evento()" data-sprint-text="Emitiendo Evento">Guardar</button>
            </div>
        </div>
    <!-- end modal formulario de motivos de rechazo de factura externa RADIAN -->

    <!--sprint loader-->
    <div class="container-sprint-send" >
        <div class="preloader-wrapper big active">
            <div class="spinner-layer spinner-blue-only">
                <div class="circle-clipper left">
                    <div class="circle"></div>
                </div>
                <div class="gap-patch">
                    <div class="circle"></div>
                </div>
                <div class="circle-clipper right">
                    <div class="circle"></div>
                </div>
            </div>
        </div>
        <span>Emitiendo Evento</span>
    </div>
    <!--end sprint loader -->

<?= $this->endSection('content') ?>
<?= $this->section('scripts') ?>
    <script src="<?= base_url('/js/advance-ui-modals.js') ?>"></script>
    <script src="<?= base_url('/js/ui-alerts.js') ?>"></script>
    <script src="<?= base_url('/js/sprint.js') ?>"></script>
    <script>
        function enviar_evento(){
            var url = $('#form-event').attr('action');
            var type = $('#type_rejection_id').val();
            window.location.assign(`${url}/${type}/0`);
            // console.log(`${url}/${type}`);
        }
    </script>
<?= $this->endSection('scripts') ?>