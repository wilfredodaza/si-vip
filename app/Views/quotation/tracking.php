<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?> Cotizaciones <?= $this->endSection() ?>

<?= $this->section('styles')  ?>
    <script src="<?= base_url('/js/ckeditor/ckeditor.js') ?>"></script>
<?= $this->endSection() ?>
<?= $this->section('content') ?>

<div id="app">
    <div id="main">
        <div class="row">
            <div class="breadcrumbs-inline pt-3 pb-1" id="breadcrumbs-wrapper">
                <div class="container">
                    <div class="row">
                        <div class="col s12">
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
                            <?php if (session('warning')): ?>
                                <div class="card-alert card yellow darken-2">
                                    <div class="card-content white-text">
                                        <?= session('warning') ?>
                                    </div>
                                    <button type="button" class="close white-text" data-dismiss="alert"
                                            aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col s10 m6 l6 breadcrumbs-left">
                            <h5 class="breadcrumbs-title mt-0 mb-0 display-inline hide-on-small-and-down ">
                            <span>
                                Cotizaciones
                            </span>
                            </h5>
                            <ol class="breadcrumbs mb-0">
                                <li class="breadcrumb-item"><a href="<?php base_url() ?>/home">Home</a></li>
                                <li class="breadcrumb-item active"><a href="#">Cotizaciones</a></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col s12">
                <div class="container">
                    <div class="section">
                        <div class="card">
                            <div class="card-content">
                                <a href="<?= base_url('quotation') ?>" class="btn btn-light-indigo  right ml-1" style="padding-left:5px; padding-right:10px;">
                                    <i class="material-icons left">chevron_left</i> Regresar
                                </a>
                                <button class="btn indigo modal-trigger right" data-target="modal1">
                                    Registrar
                                </button>
                               

                              
                                <ul class="collapsible z-depth-0 mt-5">
                                    <?php foreach ($data as $item): ?>
                                        <li>
                                            <div class="collapsible-header"><i class="material-icons">book</i>
                                                <?= 'N° ' . $item->id . ' - ' . strtoupper($item->username) . '  ' . $item->created_at ?>
                                            </div>
                                            <div class="collapsible-body">
                                                <button class="btn btn-small yellow darken-2 right modal-trigger quotation_edit "
                                                        style="padding:0px 10px;"
                                                        data-target="modaledit" data-id="<?= $item->id ?>" data-quotation-id="<?= $id ?>">
                                                    <i class="material-icons">create</i>
                                                </button>
                                                <span><?= $item->message ?></span>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form action="<?= base_url('/tracking/create/'.$id.'/quotation') ?>" method="post" id="form">
            <div id="modal1" class="modal modal-fixed-footer" style="height: 500px !important;">
                <div class="modal-content" style="padding-top: 20px;">
                    <div class="row">
                        <div class="col s9" style="padding-left: 0px ">
                            <h5 style="margin-top: 0px; margin-bottom: 20px;">Registrar Seguimiento</h5>
                        </div>
                        <div class="col s3">
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
                    <div class="divider" style="margin-bottom: 10px;"></div>
                    <div class="row">
                        <div class="col s8"></div>
                        <div class="input-field col s4 offset-8" id="created_at" style="display:none;">
                            <input type="date" name="created_at">
                            <label>Notificar el día.</label>
                        </div>
                        <div class="col s12">
                            <label for="first_name">Observación</label>
                            <textarea id="editorCreate" class="editor" rows="20" name="message" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#!" class="modal-action modal-close waves-effect waves-green btn-flat ">Cerrar</a>
                    <button class="btn indigo">Registrar</button>
                </div>
            </div>
        </form>


        <form action="" method="post" id="formEdit">
            <div id="modaledit" class="modal modal-fixed-footer" style="height: 500px !important;">
                <div class="modal-content" style="padding-top: 20px;">
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
                            <textarea id="editorEdit" rows="20" name="message" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#!" class="modal-action modal-close waves-effect waves-green btn-flat ">Cerrar</a>
                    <button class="btn indigo">Actualizar</button>
                </div>
            </div>
        </form>

        <?= $this->endSection() ?>
        <?= $this->section('scripts') ?>
            <script src="<?= base_url('js/advance-ui-modals.js') ?>"></script>
            <script src="<?= base_url('js/views/tracking.js') ?>"></script>
        <?= $this->endSection() ?>