<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?> Empleados <?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div id="main">
        <div class="row">
            <div class="breadcrumbs-inline pt-3 pb-1" id="breadcrumbs-wrapper">
                <div class="container">
                    <div class="row">
                        <div class="col s12">
                            <?= view('layouts/alerts') ?>
                        </div>
                        <div class="col s10 m6 l6 breadcrumbs-left">
                            <h5 class="breadcrumbs-title mt-0 mb-0 display-inline hide-on-small-and-down ">
                            <span>
                                Emplados
                                <a class="btn btn-small light-blue darken-1 step-1 help active-red" style="padding-left: 10px ; padding-right: 10px;">Ayuda</a>
                            </span>
                            </h5>
                        </div>
     <?php if( session('user')->role_id != 10): ?>
                        <div class="col s2 m6 l6">
                            <a class="btn right btn-small indigo ml-2 step-2" style="padding-left: 10px;padding-right: 10px;"
                               href="<?= base_url('workers/create') ?>">
                                <i class="material-icons left">add</i>
                                Añadir
                            </a>
                            <a class="btn right btn-small btn-light-indigo  modal-trigger step-4"
                               style="padding-left: 10px;padding-right: 10px;"
                               href="#importWorkers">
                                <i class="material-icons left step-4">file_upload</i>
                                Importar
                            </a>
                            <a class="btn  right btn-small btn-light-indigo mr-2 step-5"
                               style="padding-left: 10px;padding-right: 10px;"
                               href="<?= base_url('workers/export') ?>">
                                <i class="material-icons left">file_download</i>
                                Exportar
                            </a>

                        </div>
<?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    
        <div class="col s12">
            <div class="container">
                <div class="section">
                    <div class="card">
                        <div class="card-content">
                            <?php if(count($search) != 0): ?>
                                <a href="<?= base_url('workers') ?>" class="btn right btn-light-red"   style="padding-left: 10px;padding-right: 10px; ">
                                    <i class="material-icons left">close</i>
                                    Quitar Filtro
                                </a>
                            <?php endif; ?>
                            <button class="btn right btn-light-indigo modal-trigger  step-6 mr-1"  data-target="filter" style="padding-left: 10px;padding-right: 10px; ">
                                <i class="material-icons left">filter_list</i>
                                Filtrar
                            </button> 
                            <table>
                                <thead>
                                    <tr>
                                        <th class="center indigo-text"></th>
                                        <th class="center indigo-text">Nombre</th>
                                        <th class="center indigo-text">Tipo de documento</th>
                                        <th class="center indigo-text">Número de documento</th>
                                        <th class="center indigo-text">Valor</th>
                                        <th class="center indigo-text step-3">Acciones</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $i = 1;
                                    foreach ($workers as $item): ?>
                                        <tr>
                                            <td class="center"><?= $i++ ?></td>
                                            <td class="center"><?= strtoupper($item->name . ' ' . $item->second_name . ' ' . $item->surname . ' ' . $item->second_surname) ?></td>
                                            <td class="center"><?= $item->type_document_identification_name ?></td>
                                            <td class="center"><?= $item->identification_number ?></td>
                                            <td class="center">$ <?= number_format($item->salary, '2', ',', '.') ?></td>
                                            <td class="center">
                                                <div class="btn-group">
                                                    <a href="<?= base_url('workers/'.$item->customer_id) ?>" class="btn btn-small yellow darken-2 tooltipped" data-tooltip="Ver Empleado"><i class="material-icons">remove_red_eye</i></a>
                                                   	<?php if(session('user')->role_id != 10): ?>
							 <a href="<?= base_url('workers/edit/'.$item->customer_id) ?>" class="btn btn-small green darken-1 tooltipped" data-tooltip="Editar Empleado"><i class="material-icons">edit</i></a>
                                                    
							<?php if($item->status == 'Activo'): ?>
                                                        <a href="<?= base_url('workers/change_status/'.$item->customer_id)?>" class="btn btn-small tooltipped  blue darken-1" tooltipped data-tooltip="Inactivar Empleado">
                                                            <i class="material-icons">check</i>
                                                        </a>
                                                    <?php else: ?>
                                                        <a href="<?= base_url('workers/change_status/'.$item->customer_id)?>"  class="btn btn-small tooltipped  blue darken-1" data-tooltip="Activar Empleado">
                                                            <i class="material-icons">close</i>
                                                        </a>
                                                    <?php endif ?>
                                                    <a href="<?= base_url('workers/delete/'.$item->customer_id) ?>" class="btn btn-small red darken-2 tooltipped" data-tooltip="Eliminar Empleado"><i class="material-icons">delete</i></a>
                                                <?php endif ?>

						 </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <?php if (count($workers) == 0): ?>
                                    <p class="center purple-text pt-1">Noy ningun elemento registrado en la table.</p>
                                <?php endif; ?>
                                <?= $pager->links(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Structure -->
    <div id="importWorkers" class="modal" style="width: 50% !important;">
        <form action="<?= base_url() ?>/workers/import" method="post" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="row">
                    <div class="col s12">
                        <div class="modal-title" style="margin-bottom: 20px;">Importar Empleados</div>
                    </div>
                    <div class="col s12">
                        <div class="file-field input-field">
                            <div class="btn indigo btn-small">
                                <span>Archivo</span>
                                <input type="file" name="file">
                            </div>
                            <div class="file-path-wrapper">
                                <input class="file-path validate" type="text" required placeholder="Cargar Archivo">
                            </div>
                        </div>
                    </div>
                
                </div>
            </div>
            <div class="modal-footer">
                
      
                <a href="<?= base_url('assets/upload/documents/Empleados.xlsx') ?>" class="btn green darken-2">Plantilla Excel</a>
                <a href="#!" class="modal-action modal-close btn-light-indigo btn-flat">Cerrar</a>
                <button type="submit" class="btn btn-light-indigo">Cargar</button>
            </div>
        </form>
    </div>

<form action="" method="GET">
    <div id="filter" class="modal" role="dialog" style="height:auto; width: 600px">
        <div class="modal-content">
            <h5>Filtrar Empleado</h5>
            <div class="row">
                <div class="col s12 m6 input-field">

                    <input type="text" id="first_name" name="first_name" placeholder="Primer Nombre" value="<?= isset($_GET['first_name']) ? $_GET['first_name'] : '' ?>">
                    <label for="first_name">Primer Nombre</label>
                </div>
                <div class="col s12 m6 input-field">
                    <input type="text" id="second_name" name="second_name" placeholder="Segundo Nombre" value="<?= isset($_GET['second_name']) ? $_GET['second_name'] : '' ?>">
                    <label for="second_name">Segundo Nombre</label>
                </div>
            </div>
            <div class="row">
                <div class="col s12 m6 input-field">
                    <input type="text" id="second_name" name="surname" placeholder="Primer Apellido" value="<?= isset($_GET['surname']) ? $_GET['surname'] : '' ?>">
                    <label for="surname">Primer Apellido</label>
                </div>
                <div class="col s12 m6 input-field">
                    <input type="text" id="second_surname" name="second_surname" placeholder="Segundo Apellido" value="<?= isset($_GET['second_surname']) ? $_GET['second_surname'] : '' ?>">
                    <label for="second_surname">Segundo Apellido</label>
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
                    <label class="active" class="active">Tipo de documento</label>
                </div>
            
           
                <div class="col s12 m6 input-field">
                    <input type="text" id="identification_number" name="identification_number" placeholder="Numero de documento" value="<?= isset($_GET['identification_number']) ? $_GET['identification_number'] : '' ?>"> 
                    <label for="identification_number">Numero de documento</label>
                </div>
                <div class="col s12 m6  input-field">
                    <select class="browser-default" name="status">
                        <option value="" disabled selected>Elige tu opción</option>
                        <option value="Activo"  <?= isset($_GET['status']) ? ($_GET['status']  == 'Activo' ? 'selected': ''): '' ?>>Activo</option>
                        <option value="Inactivo"  <?= isset($_GET['status']) ? ($_GET['status']  == 'Inactivo' ? 'selected': ''): '' ?>>Inactivo</option>
                    </select>
                    <label class="active">Estado</label>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-action modal-close waves-effect  btn-flat btn-light-indigo ">Cerrar</a>
            <button class="btn indigo">Buscar</button>

        </div>
    </div>
</form>
<?=  $this->endSection() ?>
<?= $this->section('scripts') ?>
    <script src="<?= base_url('/js/advance-ui-modals.js') ?>"></script>
    <script src="<?= base_url('/js/shepherd.min.js') ?>"></script>
    <script src="<?= base_url('/js/view/worker.js')?>"></script>
<?= $this->endSection() ?>

