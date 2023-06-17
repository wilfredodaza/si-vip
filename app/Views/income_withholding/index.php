<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?> Retenciones e ingresos <?= $this->endSection() ?>
<?= $this->section('content') ?>
<div id="main">
    <div class="row">
        <div class="breadcrumbs-inline pt-3 pb-1" id="breadcrumbs-wrapper">
            <div class="container">
                <div class="row">
                    <div class="col s12">
                        <?= $this->include('layouts/alerts') ?>
                        <?= $this->include('layouts/notification') ?>
                    </div>
                    <div class="col s10 m6 l6 breadcrumbs-left">
                        <h5 class="breadcrumbs-title mt-0 mb-0 display-inline hide-on-small-and-down ">
                            <span>Retenciones e ingresos</span>
                        </h5>
                        <ol class="breadcrumbs mb-0">
                            <li class="breadcrumb-item"><a href="<?= base_url() ?>/home">Home</a></li>
                            <li class="breadcrumb-item active"><a href="#">Retenciones e ingresos</a></li>
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
                            <a href="#import_data_excel" class="btn  btn-small green darken-2 right step-2 modal-trigger " >
                                <span class="left">Importar</span>
                                <svg style="width:15px; display: block; margin-top:5px; margin-left:20px;" class="right" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="file-excel" class="svg-inline--fa fa-file-excel fa-w-12" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512">
                                    <path fill="currentColor" d="M224 136V0H24C10.7 0 0 10.7 0 24v464c0 13.3 10.7 24 24 24h336c13.3 0 24-10.7 24-24V160H248c-13.2 0-24-10.8-24-24zm60.1 106.5L224 336l60.1 93.5c5.1 8-.6 18.5-10.1 18.5h-34.9c-4.4 0-8.5-2.4-10.6-6.3C208.9 405.5 192 373 192 373c-6.4 14.8-10 20-36.6 68.8-2.1 3.9-6.1 6.3-10.5 6.3H110c-9.5 0-15.2-10.5-10.1-18.5l60.3-93.5-60.3-93.5c-5.2-8 .6-18.5 10.1-18.5h34.8c4.4 0 8.5 2.4 10.6 6.3 26.1 48.8 20 33.6 36.6 68.5 0 0 6.1-11.7 36.6-68.5 2.1-3.9 6.2-6.3 10.6-6.3H274c9.5-.1 15.2 10.4 10.1 18.4zM384 121.9v6.1H256V0h6.1c6.4 0 12.5 2.5 17 7l97.9 98c4.5 4.5 7 10.6 7 16.9z"></path>
                                </svg>
                            </a>
                            <?php if(count($searchShow) != 0): ?>
                                <a href="<?= base_url('income_withholding') ?>" class="btn right btn-light-red btn-small mr-1"   style="padding-left: 10px;padding-right: 10px; ">
                                    <i class="material-icons left">close</i>
                                    Quitar Filtro
                                </a>
                            <?php endif; ?>
                            <button  class="btn right btn-small btn-light-indigo modal-trigger tooltipped step-7 mr-1 ml-1"  data-position="top" data-tooltip="Filtrar Empleados" style="padding-left:5px; padding-right:10px;" data-target="filter">
                                <i class="material-icons left">filter_list </i> Filtrar
                            </button>
                            <table class="table-responsive">
                                <thead>
                                <tr>
                                    <th class="center">ID</th>
                                    <th class="center">Fecha</th>
                                    <th class="center">Tipo de Documento</th>
                                    <th class="center">N° Identificación</th>
                                    <th class="center">Cliente</th>
                                    <th class="center step-3 ">Acciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($documents as $item): ?>
                                    <tr>
                                        <td class="center"><?= $item->resolution ?>
                                        <td class="center"><?= $item->issue_date ?></td>
                                        <td class="center"><?= $item->type_document_name ?></td>
                                        <td class="center"><?= $item->identification_number ?></td>
                                        <td class="center"><?= $item->name.' '.$item->second_name.' '.$item->surname.' '.$item->second_surname ?></td>
                                        <td class="center" >
                                            <div class="btn-group" role="group">
                                                    <a href="<?= base_url('income_withholding/'.$item->id) ?>"
                                                       class="btn btn-small  pink darken-1  tooltipped" target="_blank" data-position="top" data-tooltip="Descargar PDF">
                                                        <i class="material-icons">insert_drive_file</i>
                                                    </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                            <?php if(count($documents) == 0): ?>
                                <p class="center red-text pt-1" >No hay ningún elemento en el facturador.</p>
                            <?php endif ?>
                            <?= $pager->links(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!---Modal De Cargue de archivos --->
<form action="<?= base_url() ?>/income_withholding/import" method="POST" id="form-upload_document" enctype="multipart/form-data">
    <!-- Modal Structure -->
    <div id="import_data_excel" class="modal">
        <div class="modal-content">
            <h6>Cargar Archivo</h6>
            <div class="row">
                <div class="col s12">
                    <div class="file-field input-field">
                        <div class="btn indigo">
                            <span>Cargar Excel</span>
                            <input type="file" name="file">
                        </div>
                        <div class="file-path-wrapper">
                            <input class="file-path validate" type="text">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <a href="#!" class="modal-action  modal-close waves-effect waves-green btn-flat btn-light-indigo">Cerrar</a>
            <button class="btn indigo">Guardar</button>
        </div>
    </div>
</form>
<!-- End Modal de Cargue de archivos -->


<!--------------------------- modal de busqueda ------------------------------->
<form action="" method="GET" autocomplete="off">
    <div id="filter" class="modal" role="dialog" style="height:auto; width: 600px">
        <div class="modal-content">
            <h5>Filtrar Empleado</h5>
            <div class="row">
                <div class="col s12 m6 input-field">
                    <label for="first_name">Primer Nombre</label>
                    <input type="text" id="first_name" name="first_name" value="<?= isset($_GET['first_name']) ? $_GET['first_name'] : '' ?>">
                </div>
                <div class="col s12 m6 input-field">
                    <label for="second_name">Segundo Nombre</label>
                    <input type="text" id="second_name" name="second_name" value="<?= isset($_GET['second_name']) ? $_GET['second_name'] : '' ?>">
                </div>
            </div>
            <div class="row">
                <div class="col s12 m6 input-field">
                    <label for="surname">Primer Apellido</label>
                    <input type="text" id="second_name" name="surname" value="<?= isset($_GET['surname']) ? $_GET['surname'] : '' ?>">
                </div>
                <div class="col s12 m6 input-field">
                    <label for="second_surname">Segundo Apellido</label>
                    <input type="text" id="second_surname" name="second_surname" value="<?= isset($_GET['second_surname']) ? $_GET['second_surname'] : '' ?>">
                </div>
            </div>
            <div class="row">
                <div class="col s12 m6 input-field">
                    <select name="type_document_id" class="browser-default">
                        <option value=""  >Elige tu opción</option>
                        <?php foreach($typeDocumentIdentifications as $item):  ?>
                            <option value="<?= $item->id ?>"    <?= isset($_GET['type_document_id']) ? ($_GET['type_document_id']  == $item->id ? 'selected': ''): '' ?> ><?= $item->name ?></option>
                        <?php endforeach ?>
                    </select>
                    <label class="active">Tipo de documento</label>
                </div>


                <div class="col s12 m6 input-field">
                    <input type="text" id="identification_number" name="identification_number" value="<?= isset($_GET['identification_number']) ? $_GET['identification_number'] : '' ?>">
                    <label for="identification_number">Numero de documento</label>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-action modal-close waves-effect  btn-flat btn-light-indigo ">Cerrar</a>
            <button class="btn indigo">Buscar</button>

        </div>
    </div>
</form>
<!-------------------------- end modal de busqueda ---------------------------->

<?= $this->section('scripts') ?>
    <script src="<?= base_url('/js/advance-ui-modals.js') ?>"></script>
<?= $this->endSection('scripts') ?>
<?= $this->endSection() ?>

