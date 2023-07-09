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
                            <!-- <form action="" method="get"> -->
                                <div class="row">
                                    <div class="col s12 l12">
                                        <div class="input-field">
                                            <select class="select2 browser-default" id="customer" name="customer"  multiple="multiple">
                                                <!-- <option value="">Seleccione ...</option> -->
                                                <?php foreach ($customers as $customer) : ?>
                                                    <option value="<?= $customer->id ?>" <?= (isset($_GET['customer']) && $_GET['customer']  == $customer->id) ? 'selected' : '' ?>>
                                                        <?= $customer->name ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <label for="customer">Cliente</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="input-field col s12 l5">
                                        <input name="date_init" id="date_init" type="date" value="<?= date('Y-m-d', strtotime('-1 month', strtotime(date('Y-m-d')))) ?>">
                                        <label class="active" for="date_init">Fecha de inicio</label>
                                    </div>
                                    <div class="input-field col s12 l5">
                                        <input name="date_end" id="date_end" type="date" value="<?= date('Y-m-d') ?>">
                                        <label class="active" for="date_end">Fecha de fin</label>
                                    </div>
                                    <div class="col s12 l2 center">
                                        <button class="modals-action waves-effect waves-green btn indigo" onclick="reload()">Buscar</button>
                                    </div>
                                </div>
                            <!-- </form> -->
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-content">
                            <!-- <div class="card-title">
                                <?php if (isset($_GET['customer'])): ?>
                                    <a href="<?= base_url('reports/customerAges') ?>"
                                       class="btn right btn-light-red btn-small ml-1"
                                       style="padding-left: 10px;padding-right: 10px; margin-right: 10px; ">
                                        <i class="material-icons left">close</i>
                                        Quitar Filtro
                                    </a>
                                <?php endif; ?>
                                <br>
                            </div> -->
                            <div class="row">
                                    <h5 class="center-align"><?= $title ?></h5>
                                    <h6 class="center-align" id="total_title"></h6>
                                    <div class="divider"></div>

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
                                        <!-- </tr>
                                        <tr>
                                            <td class="center numberId reload" id="total1" style="cursor: pointer;" data-id="1" >$ <number_format($invoices['total'], '2', ',', '.') ?></td>
                                            <td class="center numberId reload" id="total2" style="cursor: pointer;" data-id="2" >$ <number_format($invoices30['total'], '2', ',', '.') ?></td>
                                            <td class="center numberId reload" id="total3" style="cursor: pointer;" data-id="3" >$ <number_format($invoices60['total'], '2', ',', '.') ?></td>
                                            <td class="center numberId reload" id="total4" style="cursor: pointer;" data-id="4" >$ <number_format($invoices90['total'], '2', ',', '.') ?></td>
                                        </tr> -->

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
                                            <!-- <thead>
                                            <tr style="padding-bottom: 30px !important">
                                                <th class="center">Fecha</th>
                                                <th class="center">Cliente</th>
                                                <th class="center">Sede</th>
                                                <th class="center">Total</th>
                                                <th class="center"> Acciones </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            </tbody> -->
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
                <div class="col s12">
                    <label for="customer"><?= ($title == 'Edades Clientes')?'Cliente':'proveedor' ?></label>
                    <select class="select2 browser-default" name="customer">
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
        escapeMarkup: function (es) { return es; }
    });
</script>
<script src="<?= base_url('/app-assets/vendors/data-tables/js/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('/app-assets/vendors/data-tables/extensions/responsive/js/dataTables.responsive.min.js') ?>"></script>
<script src="<?= base_url('/app-assets/vendors/data-tables/js/dataTables.select.min.js') ?>"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="<?= base_url('/js/shepherd.min.js') ?>"></script>
<script src="<?= base_url('/js/ui-alerts.js') ?>"></script>
<script src="<?= base_url('/js/advance-ui-modals.js') ?>"></script>
<script src="<?= base_url('/js/sprint.js') ?>"></script>

<script>
    const table= [];
    $(document).ready(function () {
        var number = 1;
        // if(localStorage.getItem('customerAge')){
        //     restablecerColores();
        //     number = localStorage.getItem('customerAge');
            asignarColor(number);
        // }else{
        //     localStorage.setItem('customerAge', number);
        //     asignarColor(number);
        // }
        $('.numberId').click(function() {
            number = $(this).data('id');
            switch (number) {
                case 2:
                    var fecha1 = moment("<?= date('Y-m-d') ?>").subtract(2, 'month');
                    $('#date_init').val(fecha1.format("YYYY-MM-DD"));
                    var fecha2 = moment("<?= date('Y-m-d') ?>").subtract(1, 'month');
                    $('#date_end').val(fecha2.format("YYYY-MM-DD"));
                    break;
                case 3:
                    var fecha1 = moment("<?= date('Y-m-d') ?>").subtract(3, 'month');
                    $('#date_init').val(fecha1.format("YYYY-MM-DD"));
                    var fecha2 = moment("<?= date('Y-m-d') ?>").subtract(2, 'month');
                    $('#date_end').val(fecha2.format("YYYY-MM-DD"));
                    break;
                case 4:
                    $('#date_init').val('');
                    var fecha2 = moment("<?= date('Y-m-d') ?>").subtract(3, 'month');
                    $('#date_end').val(fecha2.format("YYYY-MM-DD"));
                    break;
            
                default:
                    var fecha1 = moment("<?= date('Y-m-d') ?>").subtract(1, 'month');
                    $('#date_init').val(fecha1.format("YYYY-MM-DD"));
                    var fecha2 = moment("<?= date('Y-m-d') ?>").subtract(0, 'month');
                    $('#date_end').val(fecha2.format("YYYY-MM-DD"));
                    break;
            }
        })
        var url = '';
        if('<?= $title ?>' === 'Edades Clientes'){
            url = `<?= base_url() ?>/reports/customersAges/kardex/`;
        }else{
            url = `<?= base_url() ?>/reports/providersAges/kardex/`;
        }

        table['kardex'] = $(`#table_kardex`).DataTable({
            "ajax": {
                "url": `${url}${number}`,
                data: function(d) {
                    d.customer = $('#customer').val(),
                    d.date_init = $('#date_init').val(),
                    d.date_end = $('#date_end').val()
                },
                // "data" : { 'customer' : <?= $client ?> },
                "dataSrc": 'table'
            },
            processing: true,
            serverSide: true,
            "order": [[ 0, 'desc' ]],
            "columns": columns(),
            "responsive": false,
            "scrollX": true,
            "ordering": false,
            language: {url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"},
            initComplete: (data) => {}
        });
        table['kardex'].on('draw', function(e, data){
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
            var total = 0;
            data.json.table.forEach(detail => {
                total += parseInt(detail.total)
            });
            const formatter = new Intl.NumberFormat('es-CO', {
                style: 'currency',
                currency: 'COP',
                minimumFractionDigits: 2
            })
            $('#total_title').html(`<b>Total: </b>${formatter.format(total)}`);
            $(`#total${number}`).html(`${formatter.format(total)}`);
        });

        $('.reload').click(function() {
            restablecerColores();
            number = $(this).data('id');
            table['kardex'].ajax.url( url + number ).load();
            localStorage.setItem('customerAge', number);
            asignarColor(number);
        })

    });

    function columns(){
        const formatter = new Intl.NumberFormat('es-CO', {
            style: 'currency',
            currency: 'COP',
            minimumFractionDigits: 2
        })
                // 
        if('<?= $title ?>' === 'Edades Clientes'){
            return [
                {'title': 'Cliente', data: 'name'},
                {'title': 'Total N° facturas', data:'total_invoices'},
                {'title': 'Total Valor facturas', data:'total', render: (value) => formatter.format(value)},
                {'title': 'Acciones', data:'total', render: (value, e, data) => {
                    return `
                    <div class="btn-group" role="group">
                        <a href="<?= base_url() ?>/reports/client/new/${data.customer_id}/1?date_init=${$('#date_init').val()}&date_end=${$('#date_end').val()}" target="_blank"
                            class="btn btn-small green darken-1  tooltipped" data-position="top" data-tooltip="ver detalle">
                            <i class="material-icons">insert_drive_file</i>
                        </a>
                    </div>`;
                }},
            ];
        }else{
            return [
                // {data: 'date'},
                {title: 'Proveedor', data: 'name'},
                {title: 'Total N° facturas', data:'total_invoices'},
                {title: 'Total Valor facturas', data:'total', render: (value) => formatter.format(value)},
                // {data: 'total'},
                {title: 'Acciones', data: 'action', render: (value, e, data) => {
                    return `
                    <div class="btn-group" role="group">
                        <a href="<?= base_url() ?>/reports/client/new/${data.customer_id}/2?date_init=${$('#date_init').val()}&date_end=${$('#date_end').val()}" target="_blank"
                            class="btn btn-small green darken-1  tooltipped" data-position="top" data-tooltip="ver detalle">
                            <i class="material-icons">insert_drive_file</i>
                        </a>
                    </div>`;
                }},
            ]
        }
    }
    function reload(number = 1){
        if('<?= $title ?>' === 'Edades Clientes')url = `<?= base_url() ?>/reports/customersAges/kardex/`;
        else url = `<?= base_url() ?>/reports/providersAges/kardex/`;
        restablecerColores();
        table['kardex'].ajax.url( url + number ).load((data) => {
            console.log(data);
            $(`#quantity${number}`).html(data.recordsTotal)
        });
        localStorage.setItem('customerAge', number);
        asignarColor(number);
    }
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

