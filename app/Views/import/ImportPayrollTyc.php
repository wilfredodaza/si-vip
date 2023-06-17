<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?> Importar Nomina<?= $this->endSection() ?>
<?= $this->section('styles') ?>
    <link rel="stylesheet" href="<?= base_url('css/views/periods.css') ?>">
    <link rel="stylesheet" href="<?= base_url('/dropify/css/dropify.min.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <!-- BEGIN: Page Main-->
    <style>
        .dropzone {
            border: #a53394 dashed 2px;
            width: 100%;
            height: 100px !important;
            margin-bottom: 3px !important;
        }
    </style>
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
                                    <span aria-hidden="true">x</span>
                                </button>
                            </div>
                        <?php endif; ?>
                        <?php if (session('vacio')): ?>
                            <div class="card-alert card yellow">
                                <div class="card-content black-text">
                                    <?= session('vacio') ?>
                                </div>
                                <button type="button" class="close white-text" data-dismiss="alert"
                                        aria-label="Close">
                                    <span aria-hidden="true">x</span>
                                </button>
                            </div>
                        <?php endif; ?>
                        <?php if (session('error')):
                            $cantidaderror = 0;
                            ?>
                            <div class="card-alert card red">
                                <div class="card-content white-text">
                                    <?php foreach (session('error') as $data) {
                                        foreach ($data['errores'] as $error) {
                                            $cantidaderror++;
                                        }
                                    } ?>
                                    <p><strong>Error</strong>, no se pudo validar el archivo ya que se
                                        encontraron <?= $cantidaderror ?> errores.<br><a href="#modal1"
                                                                                         class=" btn  btn-flat white-text modal-trigger">Ver
                                            Errores ..</a><br>
                                        Los archivos han sido eliminados, se debe realizar el cargue con las correcciones
                                        correspondientes.
                                    </p>
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
                        <div class="card">
                            <div class="card-content">
                                <div class="divider"></div>
                                <div class="row">
                                    <div class="col s12 m12 text-center" style="position: relative;">
                                        <div class="card-title"><h5>Importar Archivos de nómina <?= company()->company ?>
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="divider"></div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="row">
                                <div class="col s12">
                                    <ul class="tabs">
                                        <li class="tab col m6 "><a class="active" href="#load">Cargue</a></li>
                                        <li class="tab col m6"><a href="#validation">Validar o Eliminar </a></li>
                                    </ul>
                                </div>
                                <div id="load" class="col s12">
                                    <form action="<?= base_url() ?>/import/tyc/load" method="post"
                                          enctype="multipart/form-data">
                                        <?php
                                        if (company()->identification_number == 900782726) {
                                            echo view('import/Import_tyc');
                                        } elseif (company()->identification_number == 901030030) {
                                            echo view('import/Simetrik_import');
                                        } elseif (company()->identification_number == 901400629) {
                                            echo view('import/Commure_import');
                                        } elseif (company()->identification_number == 901441683) {
                                            echo view('import/Sumer_import');
                                        } elseif (company()->identification_number == 901233605) {
                                            echo view('import/Mubler_import');
                                        } elseif (company()->identification_number == 901515179) {
                                            echo view('import/Fjm_import');
                                        } elseif (company()->identification_number == 901427659) {
                                            echo view('import/Melonn_import');
                                        } elseif (company()->identification_number == 901433542) {
                                            echo view('import/Heal_room_import');
                                        } elseif (company()->identification_number == 901465526) {
                                            echo view('import/Onza_import');
                                        } elseif (company()->identification_number == 901005608) {
                                            echo view('import/Tyc_contadores_import');
                                        } elseif (company()->identification_number == 901112882) {
                                            echo view('import/Biotech_import');
                                        }elseif (company()->identification_number == 901525415){
                                            echo view('import/Gelt_import');
                                        }
                                        ?>
                                    </form>
                                </div>
                                <div id="validation" class="col s12">
                                    <form id="form"
                                          action="<?= base_url() ?>/validation/<?= isset(company()->identification_number) ? company()->identification_number : '' ?>"
                                          method="post"
                                          enctype="multipart/form-data">
                                        <div class="card-content">
                                            <div class="input-field col l12 m12 s12">
                                                <h6>
                                                    Mes de liquidación
                                                </h6>
                                                <select id="monthValidation" class="select2 browser-default" name="month">
                                                    <option value="" disabled="" selected="">Seleccione una opción</option>
                                                    <?php foreach ($periods_active as $active):?>
                                                        <option value="<?= $active['month'] ?>" >
                                                            <?= ($active['document'] == 9)?$active['name_month'].' - Nómina Individual - '.$active['year']:($active['document'] == 110)?$active['name_month'].' - Desprendible empleado - '.$active['year']:$active['name_month'].' - Nómina de Ajuste - '.$active['year'] ?>
                                                        </option>
                                                    <?php endforeach;?>
                                                </select>
                                            </div>
                                            <div class="col s12 m12 l12 padding-3">
                                                <button style="" type="button" id="delete"
                                                        class="btn btn-small float-left purple"><i
                                                            class="material-icons right">delete</i>Eliminar
                                                </button>
                                                <button style="margin-right: 5px !important;" type="submit" id="validator"
                                                        class=" waves-effect waves-light btn-small float-right purple"><i
                                                            class="material-icons right">send</i>Validar
                                                </button>
                                            </div>
                                        </div>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br><br>
    </div>
    <style>
        .container-sprint-validando {
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

        .container-sprint-cargando {
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
        .container-sprint-mese {
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

    <div class="container-sprint-validando" style="display:none;">
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
        <span style="width: 100%; text-align: center; color: white;  display: block; ">Validando Información</span>
    </div>


    <div class="container-sprint-cargando" style="display:none;">
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
        <span style="width: 100%; text-align: center; color: white;  display: block;">Cargando información</span>
    </div>

    <div class="container-sprint-mese" style="display:none;">
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
        <span style="width: 100%; text-align: center; color: white;  display: block;">Validando meses</span>
    </div>
    <!-- modal -->
<?php if (session('error')): ?>
    <div id="modal1" class="modal modal-fixed-footer">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Errores encontrados</h3>
            </div>
            <div class="row">
                <?php foreach (session('error') as $data) {
                    foreach ($data['errores'] as $error) {
                        echo '<p>* El empleado con # de identificación ' . $data['Empleado'] . ' presenta el siguiente error: ' . $error . '<p>';
                    }
                } ?>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#" class="modal-action modal-close btn-flat ">Cancelar</a>
        </div>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
    <script src="<?= base_url('js/advance-ui-modals.js') ?>"></script>
    <script src="<?= base_url('/js/shepherd.min.js') ?>"></script>
    <script src="<?= base_url('js/views/dates.js') ?>"></script>
    <script src="<?= base_url('/dropify/js/dropify.min.js') ?>"></script>
    <script src="<?= base_url('/assets/js/sweetalert.min.js') ?>"></script>
    <script>
        $(document).ready(function () {
            $('.dropify').dropify({
                messages: {
                    'default': 'Arrastra y suelta un archivo aquí o haz clic',
                    'replace': 'Arrastra y suelta un archivo aquí o haz clic para reemplazar',
                    'remove': 'Eliminar',
                    'error': 'Ooops, A ocurrido un error'
                }
            });
            $(".select2").select2({
                placeholder: 'Seleccione una opcion ...'
            });

            $('.datepicker').datepicker({
                format: 'yyyy/mm/dd',
                language: 'es',
                multidate: true
            });

            $('.chips-placeholder').chips({
                placeholder: 'Enter a tag',
                secondaryPlaceholder: '+Tag',
            });
            $("#delete").click(function () {
                swal({
                    title: "¿Esta seguro de eliminar los datos?",
                    text: "Recuerde, los datos se eliminaran permanentemente ",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                    .then((willDelete) => {
                        if (willDelete) {
                            $("#form").attr("action", '<?= base_url() ?>/delete/<?= isset(company()->identification_number) ? company()->identification_number : "" ?>');
                            $('#form').submit();
                        } else {
                            swal("Los datos no serán eliminados");
                        }
                    });
            });
            var month = false;
            var period_cargue = false;
            var file = false;
            var fi = false;
            var ff = false;
            var year = false;
            var document = false;
            $('#files').change(function () {
                file = true;
            });
            $('#period_cargue').change(function () {
                period_cargue = true;
            });
            $('#month_cargue').change(function () {
                month = true;
            });
            $('#year').change(function () {
                year = true;
            });
            $('#document_cargue').change(function () {
                document = true;
            });
            $('#btncargue').click(function () {
                if($('#fi_cargue').val() != ''){
                    fi = true;
                }
                if($('#fi_cargue').val() != ''){
                    ff =  true;
                }
                if(month != false && period_cargue != false && file != false && ff != false && fi != false && year != false && document != false){
                    $('.container-sprint-cargando').show();
                    $('.container-sprint-cargando').css('display', 'flex');
                    $('html, body').css({
                        overflow: 'hidden',
                        height: '100%'
                    });
                }
            });
            $('#validator').click(function () {
                $('.container-sprint-validando').show();
                $('.container-sprint-validando').css('display', 'flex');
                $('html, body').css({
                    overflow: 'hidden',
                    height: '100%'
                });
            });
            var monthValidation = false;
            $('#document_cargue').change(function () {
                $('.container-sprint-mese').show();
                $('.container-sprint-mese').css('display', 'flex');
                $('html, body').css({
                    overflow: 'hidden',
                    height: '100%'
                });
                monthValidation = true
                $('#month_cargue').empty();
                var url = "<?= base_url()?>/monthvalidation";
                $.post(url,
                    {
                        document: $(this).val(),
                        year: $('#year').val()
                    },
                    function (data, status) {
                        $('.container-sprint-mese').hide();
                        $('.container-sprint-mese').css('display', 'none');
                        $('html, body').css({
                            overflow: 'visible',
                            height: '100%'
                        });
                        valor = JSON.parse(data);
                        $('#month_cargue').prepend($('<option>', {
                            value: '',
                            text: 'seleccione una opción',
                            selected: true,
                            disabled: true
                        }));
                        $.each(valor, function (ind, elem) {
                            $('#month_cargue').prepend($('<option>', {
                                value: elem.id,
                                text: elem.name
                            }));
                        });

                        //alert("Data: " + data + "\nStatus: " + status);
                    });
            });
            $('#year').change(function () {
                year = true;
                $('#document_cargue').empty();
                $('#document_cargue').prepend($('<option>', {
                    value: '',
                    text: 'seleccione una opción',
                    selected: true,
                    disabled: true
                }));
                $('#document_cargue').prepend($('<option>', {
                    value: 10,
                    text: 'Nómina de Ajuste'

                }));
                $('#document_cargue').prepend($('<option>', {
                    value: 9,
                    text: 'Nómina Individual'
                }));
                $('#document_cargue').prepend($('<option>', {
                    value: 110,
                    text: 'Desprendible empleado'
                }));
            });
        });
    </script>
<?= $this->endSection() ?>