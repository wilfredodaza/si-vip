<?= $this->extend('layouts/main') ?>


<?= $this->section('title') ?> Kardex <?= $this->endSection() ?>
<?= $this->section('styles') ?>
<link rel="stylesheet" type="text/css"
      href="<?= base_url() ?>/app-assets/vendors/data-tables/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css"
      href="<?= base_url() ?>/app-assets/vendors/data-tables/extensions/responsive/css/responsive.dataTables.min.css">
<link rel="stylesheet" type="text/css"
      href="<?= base_url() ?>/app-assets/vendors/data-tables/css/select.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="<?= base_url() ?>/app-assets/css/pages/data-tables.css">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
    $headquarter = 0;
    $urlReturn = base_url('/inventory/availability');
    if(isset($_GET['headquarter'])){
        $headquarter = $_GET['headquarter'];
        $urlReturn = base_url('/inventory/availability?headquarter='.$headquarter);
    }
?>
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
                               Kardex
                            </span>
                        </h5>
                        <ol class="breadcrumbs mb-0">
                            <li class="breadcrumb-item"><a href="<?= base_url() ?>/home">Disponibilidad</a></li>
                            <li class="breadcrumb-item active"><a href="#"></a>Kardex</li>
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
                            <div class="row">
                                <!-- <a href="<?= $urlReturn ?>" class="btn indigo right"
                                   style="padding-right: 10px; padding-left: 10px;">
                                    <i class="material-icons left">keyboard_arrow_left</i>
                                    Regresar
                                </a> -->
                                <h5>Kardex</h5>
                                <span><strong>Codigo:</strong>  <?= $product->code ?></span> <br>
                                <span><strong>Producto:</strong> <?= $product->name ?> - <?= $product->tax_iva ?></span>
                                <div id="kardex" class="col s12 section-data-tables kardex">
                                    <table class="display" id="table_kardex">
                                        <!-- <thead>
                                            <tr style="padding-bottom: 30px !important">
                                                <th>Fecha</th>
                                                <th class="center">Tipo de Movimiento</th>
                                                <th class="center">Resolución</th>
                                                <th class="center">Origen</th>
                                                <th class="center">Destino</th>
                                                <th class="center">Entrada</th>
                                                <th class="center">Salida</th>
                                                <th class="center">Saldo</th>
                                                <th class="center">Cliente Proveedor</th>
                                                <th class="center">Seriales</th>
                                            </tr>
                                        </thead> -->
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br><br><br>
</div>


<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?= base_url() ?>/app-assets/vendors/data-tables/js/jquery.dataTables.min.js"></script>
<script src="<?= base_url() ?>/app-assets/vendors/data-tables/extensions/responsive/js/dataTables.responsive.min.js"></script>
<script src="<?= base_url() ?>/app-assets/vendors/data-tables/js/dataTables.select.min.js"></script>

<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.3.3/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.3.3/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.3.3/js/buttons.print.min.js"></script>
<script>
    let invoices;
    $(document).ready(function () {
        const table= [];
        var producto = <?= $product->id ?>;
        table['kardex'] = $(`#table_kardex`).DataTable({
            "dom": 'lrtip B',
            "ajax": {
                "url": `<?= base_url() ?>/inventory/kardexTable/${producto}`,
                "data" : { 'headquarter' : <?= $headquarter ?> },
                "dataSrc": 'data'
            },
            "order": [[ 0, 'desc' ]],
            buttons: [
                {
                    text: `<i class="material-icons left">keyboard_arrow_left</i>
                                    Regresar`,
                    className: 'btn btn-sm blue darken-3 rigth',
                    header: true,
                    action: (e, dt, node, config) => {
                        window.location.href = `<?= $urlReturn ?>`
                    }
                },
                {
                    text: 'Ver Seriales',
                    className: 'btn btn-sm indigo darken-3 rigth mr-1',
                    header: true,
                    action: (e, dt, node, config) => {
                        var data = dt.context[0].json.serials
                        var table = `<div class="kardex-serials" style="max-height: 200px; overflow-y: auto;"><table class="centered"><thead><tr><th>Serial</th><th>Tipo</th><th>Estado</th></tr></thead><tbody>`
                        data.forEach(serial => {
                            table += `<tr>
                                <td>${serial.serial}</td>
                                <td>${serial.serial_type_name}</td>
                                <td>${serial.status == 1 ? 'Activo' : 'Inactivo'}</td>
                            </tr>`
                        })
                        table += `</tbody></table></div>`
                        Swal.fire({
                            html: table,
                            scrollbarPadding: false
                        });
                    }
                },
            ],
            "columns": [
                // {data: 'invoice_id', title: 'id'},
                {data: 'serials', title:'Seriales', render: (data, e, row) => {
                    return `
                    <a href="javascript:void(0)" onclick="displaySerials(${row.invoice_id})" class="blue-text tooltipped" data-position="bottom" data-tooltip="Ver Seriales"><i class="material-icons">remove_red_eye</i></a>`;
                }},
                {data: 'created_at', title: 'Fecha', render: (data) => {
                    var aux_date = data.split(" ");
                    return `${aux_date[0]}<br>${aux_date[1]}`
                }},
                {data: 'type_document_name', title:'Tipo de Movimiento'},
                {data: 'resolution', title:'Resolución'},
                {data: 'source', title:'Origen'},
                {data: 'destination', title:'Destino'},
                {data: 'input', title:'Entrada'},
                {data: 'out', title:'Salida'},
                {data: 'balance', title:'Saldo'},
                {data: 'customerOrProvider', title:'Cliente Proveedor'},
            ],
            "responsive": false,
            "scrollX": true,
            "ordering": false,

            language: {url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"},
            initComplete: (data) => {
                invoices = data.json.data;
            }
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
        })
    });

    function displaySerials(id){
        var invoice = invoices.find(invoice => invoice.invoice_id == id);
        var table = `<div class="kardex-serials" style="max-height: 200px; overflow-y: auto;"><table class="centered"><thead><tr><th>Serial</th><th>Tipo</th></tr></thead><tbody>`
        invoice.serials.forEach(serial => {
            table += `<tr>
                <td>${serial.serial}</td>
                <td>${serial.serial_type_name}</td>
            </tr>`
        })
        table += `</tbody></table></div>`
        Swal.fire({
            html: table,
            scrollbarPadding: false
        });
        var list = "<ul>"
        invoice.serials.forEach(serial => {
            list += `<li>${serial.serial} - ${serial.serial_type_name}</li>`
        })
        list += "</ul>"
        Swal.fire({
            title: "Seriales",
            html: table,
        });
        console.log(invoice);
    }
</script>
<?= $this->endSection() ?>
