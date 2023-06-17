<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?> <?= $title ?> <?= $this->endSection() ?>
<?= $this->section('styles') ?>
<link rel="stylesheet" type="text/css"
      href="<?= base_url('/app-assets/vendors/data-tables/css/jquery.dataTables.min.css') ?>">
<link rel="stylesheet" type="text/css"
      href="<?= base_url('/app-assets/vendors/data-tables/extensions/responsive/css/responsive.dataTables.min.css') ?>">
<link rel="stylesheet" type="text/css"
      href="<?= base_url('/app-assets/vendors/data-tables/css/select.dataTables.min.css') ?>">
<link rel="stylesheet" type="text/css" href="<?= base_url('/app-assets/css/pages/data-tables.css') ?>">
<style>
    .activeId{
        color: forestgreen;
    }
</style>
<?= $this->endSection() ?>
<?= $this->section('content') ?>

<div id="main">
    <?php
    $client = 0;
    if(isset($_GET['customer'])){
        $client= $_GET['customer'];
    }
    ?>
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
                                Reporte <?= $title ?>
                            </span>
                        </h5>
                        <ol class="breadcrumbs mb-0">
                            <li class="breadcrumb-item"><a href="<?= base_url() ?>/home">Home</a></li>
                            <li class="breadcrumb-item active"><a href="#"><?= $title ?></a></li>
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
                                <!-- -->
                                <button data-target="filter" class="btn btn-small btn-light-indigo modal-trigger  right"
                                        style="margin-right: 5px;">
                                    Filtrar <i class="tiny material-icons right">filter_list</i>
                                </button>
                                <?php if (isset($_GET['customer'])): ?>
                                    <a href="<?= base_url('reports/customerAges') ?>"
                                       class="btn right btn-light-red btn-small ml-1"
                                       style="padding-left: 10px;padding-right: 10px; margin-right: 10px; ">
                                        <i class="material-icons left">close</i>
                                        Quitar Filtro
                                    </a>
                                <?php endif; ?>
                                <br>
                            </div>
                            <div class="row">
                                <div class="col s12 m12 l12 center">
                                    <h5><?= $title ?></h5>
                                    <div class="divider"></div>
                                </div>

                                <div class="col s12 m12 l12">
                                    <table class="table table-responsive">
                                        <thead>
                                        <tr>
                                            <th class="center indigo-text">  < 30 dias </th>
                                            <th class="center indigo-text"> Entre 30 y 60 dias </th>
                                            <th class="center indigo-text"> Entre 60 y 90 dias </th>
                                            <th class="center indigo-text"> Mayor a 90 dias</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td class="center numberId reload" id="quantity1" style="cursor: pointer;" data-id="1" ><?= $invoices['quantity'] ?></td>
                                            <td class="center numberId reload" id="quantity2" style="cursor: pointer;" data-id="2" ><?= $invoices30['quantity'] ?></td>
                                            <td class="center numberId reload" id="quantity3" style="cursor: pointer;" data-id="3" ><?= $invoices60['quantity'] ?></td>
                                            <td class="center numberId reload" id="quantity4" style="cursor: pointer;" data-id="4" ><?= $invoices90['quantity'] ?></td>
                                        </tr>
                                        <tr>
                                            <td class="center numberId reload" id="total1" style="cursor: pointer;" data-id="1" >$ <?= number_format($invoices['total'], '2', ',', '.') ?></td>
                                            <td class="center numberId reload" id="total2" style="cursor: pointer;" data-id="2" >$ <?= number_format($invoices30['total'], '2', ',', '.') ?></td>
                                            <td class="center numberId reload" id="total3" style="cursor: pointer;" data-id="3" >$ <?= number_format($invoices60['total'], '2', ',', '.') ?></td>
                                            <td class="center numberId reload" id="total4" style="cursor: pointer;" data-id="4" >$ <?= number_format($invoices90['total'], '2', ',', '.') ?></td>
                                        </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col s12 m12 l12">
                                    <br>
                                    Información
                                    <div id="kardex" class="col s12 section-data-tables">
                                        <table class="display" id="table_kardex">
                                            <thead>
                                            <tr style="padding-bottom: 30px !important">
                                                <th class="center">Fecha</th>
                                                <th class="center">Cliente</th>
                                                <th class="center">Sede</th>
                                                <th class="center">Total</th>
                                                <th class="center"> Acciones </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                        <br><br>
                                    </div>
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
<form action="" method="get">
    <div id="filter" class="modal" role="dialog" style="height:auto; width: 600px">
        <div class="modal-content">
            <h4 class="modal-title">Filtrar</h4>
            <div class="row">
                <div class="col s12 m12 l12">
                    <label for="customer"><?= ($title == 'Edades Clientes')?'Cliente':'proveedor' ?></label>
                    <select class="browser-default" id="customer" name="customer">
                        <option value="">Seleccione ...</option>
                        <?php foreach ($customers as $customer) : ?>
                            <option value="<?= $customer->id ?>" <?= (isset($_GET['customer']) && $_GET['customer']  == $customer->id) ? 'selected' : '' ?>>
                                <?= $customer->name ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-action modal-close waves-effect waves-red btn-flat mb-5 ">Cerrar</a>
            <button class="modals-action waves-effect waves-green btn indigo mb-5">Guardar</button>
        </div>
    </div>
</form>
<!--end modal de filtro de busqeuda-->

<!--sprint loader-->
<div class="container-sprint-send">
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
    <span class="text-insert"></span>
</div>
<!--end sprint loader -->
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
    $(".select2").select2({
        dropdownAutoWidth: true,
        width: '100%'
    });
</script>
<script src="<?= base_url('/app-assets/vendors/data-tables/js/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('/app-assets/vendors/data-tables/extensions/responsive/js/dataTables.responsive.min.js') ?>"></script>
<script src="<?= base_url('/app-assets/vendors/data-tables/js/dataTables.select.min.js') ?>"></script>
<script src="<?= base_url('/js/shepherd.min.js') ?>"></script>
<script src="<?= base_url('/js/ui-alerts.js') ?>"></script>
<script src="<?= base_url('/js/advance-ui-modals.js') ?>"></script>
<script src="<?= base_url('/js/sprint.js') ?>"></script>

<script>
    $(document).ready(function () {
        const table= [];
        var number = 1;
        if(localStorage.getItem('customerAge')){
            restablecerColores();
            number = localStorage.getItem('customerAge');
            asignarColor(number);
        }else{
            localStorage.setItem('customerAge', number);
            asignarColor(number);
        }
        $('.numberId').click(function() {
            console.log($(this).data('id'));
            number = $(this).data('id');
        })
        var url = '';
        if('<?= $title ?>' === 'Edades Clientes'){
            url = `<?= base_url() ?>/reports/customersAges/kardex/`;
        }else{
            url = `<?= base_url() ?>/reports/providersAges/kardex/`;
        }

        table['kardex'] = $(`#table_kardex`).DataTable({
            "ajax": {
                "url": url+number,
                "data" : { 'customer' : <?= $client ?> },
                "dataSrc": ''
            },
            "order": [[ 0, 'desc' ]],
            "columns": [
                {data: 'date'},
                {data: 'name'},
                {data: 'company'},
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
        table['kardex'].on('draw', function(){
            $('.material-tooltip').remove();
            $('.tooltipped').tooltip();
            $('.dropdown-trigger').dropdown({
                inDuration: 300,
                outDuration: 225,
                constrainWidth: false, // Does not change width of dropdown to that of the activator
                hover: false, // Activate on hover
                gutter: 0, // Spacing from edge
                coverTrigger: false, // Displays dropdown below the button
                alignment: 'left', // Displays dropdown with edge aligned to the left of button
                stopPropagation: false // Stops event propagation
            });
        })
        $('.reload').click(function() {
            restablecerColores();
            number = $(this).data('id');
            table['kardex'].ajax.url( url + number ).load();
            localStorage.setItem('customerAge', number);
            asignarColor(number);
        })

    });
    function restablecerColores() {
        for (var i = 0; i <= 5; i++) {
            $('#quantity'+i).removeClass('activeId');
            $('#total'+i).removeClass('activeId');
        }
    }

    function asignarColor(id) {
        $('#quantity'+id).addClass('activeId');
        $('#total'+id).addClass('activeId');
    }
</script>
<?= $this->endSection() ?>

