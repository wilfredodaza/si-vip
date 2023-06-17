<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?> Roles <?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div id="main">
        <div class="row">
            <div class="breadcrumbs-inline pt-3 pb-1" id="breadcrumbs-wrapper">
                <div class="container">
                    <div class="row">
                        <div class="col s12">
                            <?= $this->include('layouts/alerts') ?>
                        </div>
                        <div class="col s10 m6 l6 breadcrumbs-left">
                            <h5 class="breadcrumbs-title mt-0 mb-0 display-inline hide-on-small-and-down ">
                            <span>
                               Roles
                                <a class="btn btn-small  darken-1 step-1 help purple" style="padding-left: 10px ; padding-right: 10px;">Ayuda</a>
                            </span>
                            </h5>
                            <ol class="breadcrumbs mb-0">
                                <li class="breadcrumb-item"><a href="<?= base_url() ?>/roles">Roles</a></li>
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
                                <button data-target="filter"
                                        class="right btn  btn-light-indigo modal-trigger step-5 active-red">
                                    Filtrar <i class="material-icons right">filter_list</i>
                                </button>
                                <button data-target="create" class="btn right  modal-trigger indigo mr-1 step-2 active-red">Registrar</button>
                                <br> <br>
                                <table>
                                    <thead>
                                        <tr>
                                            <td class="center">#</td>
                                            <td class="center">Fecha de creación</td>
                                            <?php if(session('user')->role_id == 1): ?>
                                                <td class="center"> Compañia</td>
                                            <?php endif; ?>
                                            <td class="center">Rol</td>
                                            <td class="center">Descripción</td>
                                            <td class="center">Tipo</td>
                                            <td class="center">Acciones</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $i = 0;
                                    foreach ($roles as $role):
                                        $i++; ?>
                                        <tr>
                                            <td  class="center"><?= $i ?></td>
                                            <td  class="center">
                                                <?=  $role->created_at ?>
                                            </td>
                                            <?php if(session('user')->role_id == 1): ?>
                                                <td class="center">
                                                    <?= $role->company ?>
                                                </td>
                                            <?php endif; ?>
                                            <td  class="center"><?= $role->name ?></td>
                                            <td class="center"><?= empty($role->description) ? '<small class="purple-text text-darken-4">Ninguna</small>' :$role->description ?></td>
                                            <td class="center">
                                                <?php if($role->type == 'Personalizado'): ?>
                                                    <span class="new badge blue" data-badge-caption="<?= $role->type ?>"></span>
                                                 <?php else: ?>
                                                    <span class="new badge green" data-badge-caption="<?= $role->type ?>"></span>
                                                 <?php endif; ?>
                                            </td>
                                            <td class="center">
                                                <?php if($role->id >= 6):?>
                                                    <div class="btn-group">
                                                        <button data-target="update" class="btn btn-small modal-trigger indigo role_update" data-id="<?= $role->id ?>">
                                                            <i class="material-icons">create</i>
                                                        </button>
                                                        <a class="btn btn-small yellow darken-2 " href="<?= base_url('permissions/'.$role->id) ?>">
                                                            <i class="material-icons">vpn_key</i>
                                                        </a>
                                                    </div>
                                                <?php else: ?>
                                                    <small class="purple-text text-darken-4">Sin Acciones</small>
                                                <?php endif; ?>
                                            </td>
                                        </tr>

                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <?= $pager->links(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


<form action="<?=  base_url().'/roles' ?>" method="post" id="formValidate">
    <div id="create" class="modal" role="dialog" style="height:auto; width: 600px">
        <div class="modal-content">
            <h5 class="modal-title" style="padding-left: 10px">
                Registrar Rol
            </h5>
            <div class="row">
                <div class="input-field col s12">
                    <input placeholder="Role" id="name" type="text" class="validate" name="name">
                    <label for="name">Rol</label>
                </div>
                <div class="input-field col s12">
                    <input placeholder="Descripción" id="description" type="text" class="validate" name="description">
                    <label for="description">Descripción</label>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-action modal-close waves-effect  btn-flat btn-light-indigo ">Cerrar</a>
            <button class="modal-action waves-effect waves-green btn indigo">Guardar</button>
        </div>
    </div>
</form>

<form action="" method="post" id="formUpdateValidate">
    <div id="update" class="modal" role="dialog" style="height:auto; width: 600px">
        <div class="modal-content">
            <h5 class="modal-title" style="padding-left: 10px">
                Actualizar Rol
            </h5>
            <div class="row">
                <div class="input-field col s12">
                    <input placeholder="Role" id="name-update" type="text" class="validate" name="name">
                    <label for="name-update">Rol</label>
                </div>
                <div class="input-field col s12">
                    <input placeholder="Descripción" id="description-update" type="text" class="validate" name="description">
                    <label for="description-update">Descripción</label>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-action modal-close waves-effect  btn-flat btn-light-indigo ">Cerrar</a>
            <button class="modal-action waves-effect waves-green btn indigo">Guardar</button>
        </div>
    </div>
</form>

<?=  $this->endSection() ?>

<?= $this->section('scripts') ?>
    <script src="<?= base_url('/js/advance-ui-modals.js') ?>"></script>
    <script src="<?= base_url('/js/ui-alerts.js') ?>"></script>
    <script src="<?= base_url('/js/jquery.validate.js') ?>"></script>
    <script src="<?= base_url('/js/jquery.validate.js') ?>"></script>
<script>

    $(document).ready(function() {
        $('.role_update').click(function() {
            var URLactual = localStorage.getItem('url');
            const id = $(this).data('id');
            $('#formUpdateValidate').attr('action', URLactual + '/roles/' + id)
            fetch(URLactual + '/roles/' + id)
                .then(function(response) {
                    return response.json();
                })
                .then(function(myJson) {
                    var dates = myJson;
                    $('#name-update').val(dates.name);
                    $('#description-update').val(dates.description);

                });
        });
    });

    $("#formValidate").validate({
        rules: {
            name: {
                required: true,
                maxlength: 40
            },
            description: {
                maxlength: 255
            }
        },
        messages: {
            name:{
                required: "El campo rol es obligatorio.",
                maxlength: "El campo rol solo permite un máximo de 40 caracteres."
            },
            description: {
                maxlength: "El campo descripción solo permite un máximo de 255 caracteres.",
            }
        },
        errorElement : 'div',
        errorPlacement: function(error, element) {
            var placement = $(element).data('error');
            if (placement) {
                $(placement).append(error)
            } else {
                error.insertAfter(element);
            }
        }
    });


    $("#formUpdateValidate").validate({
        rules: {
            name: {
                required: true,
                maxlength: 40
            },
            description: {
                maxlength: 255
            }
        },
        messages: {
            name:{
                required: "El campo rol es obligatorio.",
                maxlength: "El campo rol solo permite un máximo de 40 caracteres."
            },
            description: {
                maxlength: "El campo descripción solo permite un máximo de 255 caracteres.",
            }
        },
        errorElement : 'div',
        errorPlacement: function(error, element) {
            var placement = $(element).data('error');
            if (placement) {
                $(placement).append(error)
            } else {
                error.insertAfter(element);
            }
        }
    });
</script>
<?=  $this->endSection() ?>
