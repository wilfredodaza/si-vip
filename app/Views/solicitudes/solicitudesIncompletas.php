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

        .container-sprint-email, .container-sprint-send {
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
                                <button type="button" class="close white-text" data-dismiss="alert"
                                        aria-label="Close">
                                    <span aria-hidden="true">脳</span>
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
                                    <span aria-hidden="true">脳</span>
                                </button>
                            </div>
                        <?php endif; ?>
                        <div class="card">
                            <div class="card-content">
                                <div class="divider"></div>
                                <div class="row">
                                    <div class="col s12 m6 " style="position: relative;">
                                        <div class="card-title">Solicitudes <small> Listado de Solicitudes. </small>
                                        </div>
                                    </div>
                                    <div class="col m6 s12">
                                        <form action="" method="get" class="hide-on-small-only">
                                            <div class="row">
                                                <div class="col m5 s12">
                                                    <div class="input-field  s12">
                                                        <input placeholder="Buscar" id="first_name" type="text"
                                                               name="value"
                                                               class="validate">
                                                    </div>
                                                </div>
                                                <div class="col m4 s12">
                                                    <div class="input-field  s12">
                                                        <select name="campo" id="">
                                                            <option value="solicitud">ID</option>
                                                            <option value="fecha">Fecha</option>
                                                            <option value="empresa">Empresa</option>
                                                            <option value="plan">Plan</option>
                                                            <option value="vendedor">Vendedor</option>
                                                            <option value="estado">Estado</option>
                                                            <option value="fechae">Fecha Estado</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col m2 s12">
                                                    <button class="btn" style="margin-top: 20px">
                                                        <i class="material-icons">search</i>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="col m12 s12 ">
                                        <p>En esta sección podrá encontrar la empresas que tienen un registro pero no se tiene un pago registrado en la base de datos</p>
                                    </div>
                                </div>
                                <br>
                                <div class="table-response" style="overflow-x:auto;">
                                    <div class="divider"></div>
                                    <table>
                                        <thead>
                                        <tr>
                                            <th class="text-center"># Solicitud</th>
                                            <th class="text-center">Fecha</th>
                                            <th class="text-center">Empresa</th>
                                            <th class="text-center">Vendedor</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($data as $item): ?>

                                            <tr>
                                                <td class="text-center"><?= $item->idsolicitud ?></td>
                                                <td class="text-center"><?= date('Y-m-d', strtotime($item->application_date)) ?></td>
                                                <td class="text-center"><?= $item->company_name ?></td>
                                                <td class="text-center"><?= $item->nameseller ?></td>
                                                <td width="220px" style="display: flex; justify-content: center;">
                                                    <a href="<?=base_url()?>/solicitudes/incompletas/info/<?= $item->idsolicitud ?>"
                                                       class="btn btn-small  light-green remove_red_eye tooltipped"
                                                       style="padding:0px 10px;" data-position="center"
                                                       data-tooltip="Ver Empresa"><i
                                                            class="material-icons">remove_red_eye</i></a>
                                                    <a href="<?=base_url()?>/table/subscriptions#/add"
                                                       class="btn btn-small  light-blue remove_red_eye tooltipped"
                                                       style="padding:0px 10px;" data-position="center"
                                                       data-tooltip="Ver Empresa por parte del cliente"><i
                                                            class="material-icons">send</i></a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>

                                        </tbody>
                                    </table>
                                </div>

                                <!-- aqui va la paginacion -->

                                <!-- end paginacion -->

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <style>

    </style>
    <div class="container-sprint-send" style="display:none;">
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
        <span style="width: 100%; text-align: center; color: white;  display: block; ">Validando documento y enviando a la DIAN</span>
    </div>


    <div class="container-sprint-email" style="display:none;">
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
        <span style="width: 100%; text-align: center; color: white;  display: block;">Enviando Email</span>
    </div>

    <!-- Modal Structure -->
    <div id="modal1" class="modal modal-fixed-footer" style="height: 200px !important;">
        <div class="modal-content">
            <h5 class="card-title">Opciones</h5>
            <div class="divider" style="margin: 20px 0px;"></div>
            <div style="display: flex; justify-content: space-between;">
                <a href="" class="btn" id="noteDebit">Nota debito</a>
                <a href="" class="btn" id="noteCredit">Nota Credito</a>
                <a href="" class="btn blue " id="csvOffice">Csv WordOffice</a>
                <a href="" class="btn green" id="csv">Descargar Csv</a>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-action modal-close waves-effect waves-green btn-flat ">Cerrar</a>
        </div>
    </div>

    <!--<div style="bottom: 50px; right: 19px;" class="fixed-action-btn direction-top"><a class="btn-floating btn-large gradient-45deg-light-blue-cyan gradient-shadow"><i class="material-icons">add</i></a>
            <ul>
                <li><a href="https://api.whatsapp.com/send?phone=+573013207088&text=Hola, tengo una duda en mifacturalegal." target="_blank" class="btn-floating light-green darken-3"><i class="fa fa-whatsapp"></i></a></li>
            </ul>
        </div>-->
    <!-- fin vista-->
<?= view('layouts/footer') ?>