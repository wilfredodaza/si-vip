<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?> Proveedor <?= $this->endSection() ?>
<?= $this->section('styles') ?>
<link rel="stylesheet" type="text/css"
      href="<?= base_url('/app-assets/vendors/data-tables/css/jquery.dataTables.min.css') ?>">
<link rel="stylesheet" type="text/css"
      href="<?= base_url('/app-assets/vendors/data-tables/extensions/responsive/css/responsive.dataTables.min.css') ?>">
<link rel="stylesheet" type="text/css"
      href="<?= base_url('/app-assets/vendors/data-tables/css/select.dataTables.min.css') ?>">
<link rel="stylesheet" type="text/css"
      href="<?= base_url('/app-assets/vendors/data-tables/css/dataTables.uikit.min.css') ?>">
<link rel="stylesheet" type="text/css"
      href="<?= base_url('/app-assets/vendors/data-tables/css/uikit.min.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= base_url('/app-assets/css/pages/data-tables.css') ?>">
<style>

</style>
<?= $this->endSection() ?>
<?= $this->section('content') ?>

<div id="main">
    <div class="row">
        <div class="breadcrumbs-inline pt-3 pb-1" id="breadcrumbs-wrapper">
            <div class="container">
                <div class="row">
                    <div class="col s12">
                        <?= view('layouts/alerts') ?>
                    </div>
                    <div class="col s12 m12 l12 breadcrumbs-left">
                        <h5 class="breadcrumbs-title mt-0 mb-0 display-inline hide-on-small-and-down">
                            <span>
                                Perfil proveedor
                            </span>
                        </h5>
                        <ol class="breadcrumbs mb-0">
                            <li class="breadcrumb-item"><a href="<?= base_url() ?>/home">Home</a></li>
                            <li class="breadcrumb-item active"><a href="<?= base_url() ?>/table/providersC">proveedores</a></li>
                            <li class="breadcrumb-item active"><a href="#">Perfil</a></li>
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
                            <div class="card-title">
                                <div class="row">
                                    <div class="col s12 m6 l6">
                                        Perfil
                                    </div>
                                    <div class="col s12 m6 l6">
                                        <a href="javascript: history.go(-1)"
                                           class=" btn btn-light-indigo right invoice-print">
                                            <i class="material-icons left">reply</i>
                                            <span>Retroceder</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="divider"></div>
                            <br>
                            <div class="row">
                                <div class="col s12 m12 l12" >
                                    <ul class="stepper horizontal" style="min-height: 600px;">
                                        <li class="step">
                                            <div class="step-title waves-effect">Información Personal</div>
                                            <div class="step-content" style="height: auto;">
                                                <form action="<?= base_url() . route_to('providers.updatePayment', $customer->id) ?>"
                                                      method="post">
                                                    <div class="row">
                                                        <div class="col s12 m12 l12">
                                                            <div class="row">
                                                                <div class="col s12 m12 l12">
                                                                    <h6>Información Personal</h6>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col m4 s12 input-field">
                                                                    <input type="text" name="name" id="name"
                                                                           value="<?= $customer->name ?>">
                                                                    <label class="active" for="name">Nombre</label>
                                                                </div>
                                                                <div class="col m4 s12 input-field">
                                                                    <input type="text" name="identification"
                                                                           id="identification"
                                                                           value="<?= $customer->identification ?>">
                                                                    <label class="active" for="identification">Identificación</label>
                                                                </div>
                                                                <div class="col m4 s12 input-field">
                                                                    <input type="text" disabled
                                                                           name="type_identification"
                                                                           id="type_identification"
                                                                           value="<?= $customer->type_identification ?>">
                                                                    <label class="active" for="type_identification">Tipo
                                                                        de identificación</label>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col m4 s12 input-field">
                                                                    <input type="text" name="address"
                                                                           id="address"
                                                                           value="<?= $customer->address ?>">
                                                                    <label class="active"
                                                                           for="address">Dirección</label>

                                                                </div>
                                                                <div class="col m4 s12 input-field">
                                                                    <input type="email" name="email" id="email"
                                                                           value="<?= $customer->email ?>">
                                                                    <label class="active" for="email">Correo</label>
                                                                </div>
                                                                <div class="col m4 s12 input-field">
                                                                    <input type="text" name="phone"
                                                                           id="phone"
                                                                           value="<?= $customer->phone ?>">
                                                                    <label class="active"
                                                                           for="phone">Teléfono</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="step-actions">
                                                        <!-- Here goes your actions buttons -->
                                                        <!--<button type="button" class="btn next-step right btn-light-red">Siguiente</button>-->
                                                        <button type="submit" class="btn btn-light-indigo left">
                                                            Actualizar
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </li>
                                        <li class="step">
                                            <div class="step-title waves-effect">Compras</div>
                                            <div class="step-content">
                                                <div class="row">
                                                    <div class="col s12 m12 l12">
                                                        <div class="row">
                                                            <div class="col s12 m12 l12">
                                                                Compras : $ <?= number_format($lastShopping, '2', ',', '.') ?>
                                                                <button data-target="filter" class="btn btn-small btn-light-indigo modal-trigger  right"
                                                                        style="margin-right: 5px;">
                                                                    Filtrar <i class="tiny material-icons right">filter_list</i>
                                                                </button>
                                                                <?php if (isset($_GET['start_date']) || isset($_GET['end_date']) ):
                                                                    if($_GET['option'] == 'c'):
                                                                    ?>
                                                                    <a href="<?= base_url('/providers/profile/'.$customer->id) ?>"
                                                                       class="btn right btn-light-red btn-small ml-1"
                                                                       style="padding-left: 10px;padding-right: 10px; margin-right: 10px; ">
                                                                        <i class="material-icons left">close</i>
                                                                        Quitar Filtro
                                                                    </a>
                                                                <?php
                                                                    endif;
                                                                    endif; ?>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div id="compras" class="col s12 section-data-tables">
                                                                <table class="uk-table uk-table-hover uk-table-striped" id="table_compras">
                                                                    <thead>
                                                                    <tr>
                                                                        <th class="center">Fecha</th>
                                                                        <th class="center">Documento</th>
                                                                        <th class="center">Total</th>
                                                                        <th class="center">Acciones</th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                                <div class="step-actions">
                                                    <!-- Here goes your actions buttons -->
                                                </div>
                                            </div>
                                        </li>
                                        <li class="step">
                                            <div class="step-title waves-effect">Productos</div>
                                            <div class="step-content">
                                                <div class="row">
                                                    <div class="col s12 m12 l12">
                                                        <div class="row">
                                                            <div class="col s12 m12 l12">
                                                                Productos: $ <?= number_format($lastProductsShopping, '2', ',', '.') ?>
                                                                <button data-target="filterProduct" class="btn btn-small btn-light-indigo modal-trigger  right"
                                                                        style="margin-right: 5px;">
                                                                    Filtrar <i class="tiny material-icons right">filter_list</i>
                                                                </button>

                                                                <?php if (isset($_GET['start_date']) || isset($_GET['end_date']) ):
                                                                    if($_GET['option'] == 'p'):
                                                                        ?>
                                                                        <a href="<?= base_url('/providers/profile/'.$customer->id) ?>"
                                                                           class="btn right btn-light-red btn-small ml-1"
                                                                           style="padding-left: 10px;padding-right: 10px; margin-right: 10px; ">
                                                                            <i class="material-icons left">close</i>
                                                                            Quitar Filtro
                                                                        </a>
                                                                    <?php
                                                                    endif;
                                                                endif; ?>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div id="product" class="col s12 section-data-tables">
                                                                <table class="uk-table uk-table-hover uk-table-striped" id="table_product">
                                                                    <thead>
                                                                    <tr>
                                                                        <th class="center">Fecha</th>
                                                                        <th class="center">Producto</th>
                                                                        <th class="center">Cantidad</th>
                                                                        <th class="center">Valor</th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="step-actions">
                                                    <!-- Here goes your actions buttons -->
                                                    <!--<button type="button" class="btn btn-light-indigo previous-step left"> Anterior</button>-->
                                                </div>
                                            </div>
                                        </li>
                                        <li class="step">
                                            <div class="step-title waves-effect">Top productos</div>
                                            <div class="step-content">
                                                <div class="row">
                                                    <div class="col s12 m12 l12">
                                                        <div class="row">
                                                            <div class="col s12 m12 l12">
                                                                <h6>Top productos</h6>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col m12 s12 ">
                                                                <table>
                                                                    <thead>
                                                                    <tr>
                                                                        <th class="center indigo-text">Código</th>
                                                                        <th class="center indigo-text">Producto</th>
                                                                        <th class="center indigo-text">Cantidad</th>
                                                                        <th class="center indigo-text">Valor</th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    <?php
                                                                    foreach ($productsShopping as $item) : ?>
                                                                        <tr>
                                                                            <td class="center"><?= $item->code ?></td>
                                                                            <td class="center"><?= "{$item->nameProduct} - {$item->reference}" ?></td>
                                                                            <td class="center"><?= $item->tQuantity ?></td>
                                                                            <td class="center"><?= number_format($item->total, '2', ',', '.') ?></td>
                                                                        </tr>
                                                                    <?php
                                                                    endforeach; ?>
                                                                    </tbody>
                                                                </table>
                                                                <?php if (count($productsShopping) == 0): ?>
                                                                    <p class="center red-text" style="padding: 10px;">No
                                                                        hay ningún elemento.</p>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="step-actions">
                                                    <!-- Here goes your actions buttons -->
                                                    <!--<button type="button" class="btn next-step right btn-light-red">Siguiente</button>
                                                    <button type="button" class="btn previous-step btn-light-indigo left"> Anterior</button>-->
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--modal de filtro de busqeuda-->
<form action="?c=c" method="get">
    <div id="filter" class="modal" role="dialog" style="height:auto; width: 600px">
        <div class="modal-content">
            <h4 class="modal-title">Filtrar</h4>
            <div class="row">
                <div class="col s12 m6 input-field">
                    <input type="date" id="start_date" name="start_date"
                           value="<?= isset($_GET['start_date']) ? $_GET['start_date'] : '' ?>">
                    <label for="start_date">Fecha de inicio</label>
                </div>
                <div class="col s12 m6 input-field">
                    <input id="end_date" type="date" name="end_date"
                           value="<?= isset($_GET['end_date']) ? $_GET['end_date'] : '' ?>">
                    <label for="end_date">Fecha fin</label>
                </div>
                <input type="text" value="c" name="option" class="hide">
            </div>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-action modal-close waves-effect waves-red btn-flat mb-5 ">Cerrar</a>
            <button class="modals-action waves-effect waves-green btn indigo mb-5">Filtrar</button>
        </div>
    </div>
</form>
<!--modal de filtro de busqeuda-->
<form action="" method="get">
    <div id="filterProduct" class="modal" role="dialog" style="height:auto; width: 600px">
        <div class="modal-content">
            <h4 class="modal-title">Filtrar</h4>
            <div class="row">
                <div class="col s12 m6 input-field">
                    <input type="date" id="start_date" name="start_date"
                           value="<?= isset($_GET['start_date']) ? $_GET['start_date'] : '' ?>">
                    <label for="start_date">Fecha de inicio</label>
                </div>
                <div class="col s12 m6 input-field">
                    <input id="end_date" type="date" name="end_date"
                           value="<?= isset($_GET['end_date']) ? $_GET['end_date'] : '' ?>">
                    <label for="end_date">Fecha fin</label>
                </div>
                <input type="text" value="p" name="option" class="hide">
            </div>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-action modal-close waves-effect waves-red btn-flat mb-5 ">Cerrar</a>
            <button class="modals-action waves-effect waves-green btn indigo mb-5">Filtrar</button>
        </div>
    </div>
</form>
<?php
$start_date = ($_GET['start_date'] ?? 0);
$end_date = ($_GET['end_date'] ?? 0);
$option = ($_GET['option'] ?? 0);
$title = 0;
if(isset($_GET['option'])){
    switch ($_GET['option']){
        case 'c':
            $title = 1;
            break;
        case 'p':
            $title = 2;
            break;
    }
}
?>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script src="<?= base_url('/app-assets/vendors/data-tables/js/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('/app-assets/vendors/data-tables/extensions/responsive/js/dataTables.responsive.min.js') ?>"></script>
<script src="<?= base_url('/app-assets/vendors/data-tables/js/dataTables.select.min.js') ?>"></script>
<script src="<?= base_url('/js/shepherd.min.js') ?>"></script>
<script src="<?= base_url('/js/ui-alerts.js') ?>"></script>
<script src="<?= base_url('/js/advance-ui-modals.js') ?>"></script>
<script src="<?= base_url('/js/sprint.js') ?>"></script>
<script>
    $(document).ready(function () {
        $('#table_compras').DataTable({
            "ajax": {
                "url": `<?= base_url() ?>/providers/shopping/<?= $customer->id ?>`,
                "data": {'start_date': '<?= $start_date ?>', 'end_date': '<?= $end_date ?>','option': '<?= $option ?>'},
                "dataSrc": ''
            },
            "order": [[0, 'desc']],
            "columns": [
                {data: 'date'},
                {data: 'document'},
                {data: 'total'},
                {data: 'action'}
            ],
            "responsive": false,
            "scrollX": true,
            "ordering": false,

            language: {url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"},
            initComplete: (data) => {
                console.log(data)
            }
        });
        $('#table_product').DataTable({
            "ajax": {
                "url": `<?= base_url() ?>/providers/products/<?= $customer->id ?>`,
                "data": {'start_date': '<?= $start_date ?>', 'end_date': '<?= $end_date ?>','option': '<?= $option ?>'},
                "dataSrc": ''
            },
            "order": [[0, 'desc']],
            "columns": [
                {data: 'date'},
                {data: 'name'},
                {data: 'tQuantity'},
                {data: 'total'}
            ],
            "responsive": false,
            "scrollX": true,
            "ordering": false,

            language: {url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"},
            initComplete: (data) => {
                console.log(data)
            }
        });
    });
</script>
<script>
    $(".select2").select2({
        dropdownAutoWidth: true,
        width: '100%'
    });
</script>
<script>
    var stepper = document.querySelector('.stepper');
    var stepperInstace = new MStepper(stepper, {
        // options
        firstActive: <?= $title ?> // this is the default
    })
</script>

<?= $this->endSection() ?>
