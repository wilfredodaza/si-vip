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
<style>

</style>
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
                                <a href="<?= $urlReturn ?>" class="btn indigo right"
                                   style="padding-right: 10px; padding-left: 10px;">
                                    <i class="material-icons left">keyboard_arrow_left</i>
                                    Regresar
                                </a>
                                <h5>Kardex</h5>
                                <span><strong>Codigo:</strong>  <?= $product->code ?></span> <br>
                                <span><strong>Producto:</strong> <?= $product->name ?> - <?= $product->tax_iva ?></span>
                                <div id="kardex" class="col s12 section-data-tables">
                                    <table class="display" id="table_kardex">
                                        <thead>
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
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
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
<script src="<?= base_url() ?>/app-assets/vendors/data-tables/js/jquery.dataTables.min.js"></script>
<script src="<?= base_url() ?>/app-assets/vendors/data-tables/extensions/responsive/js/dataTables.responsive.min.js"></script>
<script src="<?= base_url() ?>/app-assets/vendors/data-tables/js/dataTables.select.min.js"></script>
<script>
    $(document).ready(function () {
        const table= [];
        var producto = <?= $product->id ?>;
        console.log(producto);
        table['kardex'] = $(`#table_kardex`).DataTable({
            "ajax": {
                "url": `<?= base_url() ?>/inventory/kardexTable/${producto}`,
                "data" : { 'headquarter' : <?= $headquarter ?> },
                "dataSrc": ''
            },
            "order": [[ 0, 'desc' ]],
            "columns": [
                {data: 'created_at'},
                {data: 'type_document_name'},
                {data: 'resolution'},
                {data: 'source'},
                {data: 'destination'},
                {data: 'input'},
                {data: 'out'},
                {data: 'balance'},
                {data: 'customerOrProvider'},
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
    });
</script>
<?= $this->endSection() ?>
