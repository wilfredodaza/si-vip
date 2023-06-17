<?= view('layouts/header') ?>
<?= view('layouts/navbar_horizontal') ?>
<?= view('layouts/navbar_vertical') ?>
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
                                <span aria-hidden="true">×</span>
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
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                    <?php endif; ?>
                    <div class="card">
                        <div class="card-content">
                            <div class="divider"></div>
                            <div class="row">
                                <div class="col s12 m6 " style="position: relative;">
                                    <div class="card-title">Cotizaciones <small> Listado de Cotizaciones </small></div>

                                    <a href="<?= base_url('/quotation/create') ?>" class="btn">Registrar</a>
                                </div>
                                <div class="col m6 s12">
                                    <form action="" method="get" class="hide-on-small-only">
                                        <div class="row">
                                            <div class="col m5 s12">
                                                <div class="input-field  s12">
                                                    <input placeholder="Buscar" id="first_name" type="text" name="value"
                                                           class="validate">
                                                </div>
                                            </div>
                                            <div class="col m4 s12">
                                                <div class="input-field  s12">
                                                    <select name="campo" id="">
                                                        <option value="resolution">#</option>
                                                        <option value="Tipo de factura">Tipo de documento</option>
                                                        <option value="Cliente">Cliente</option>
                                                        <option value="Estado">Estado</option>
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
                            </div>
                            <div class="divider"></div>
                            <div class="table-response" style="overflow-x:auto;">
                                <table>
                                    <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">Fecha</th>
                                        <th class="text-center">Cliente</th>
                                        <th class="text-center">Total factura</th>
                                        <th class="text-center">Estado</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                    <tbody>


                                    <?php foreach ($invoices as $item): ?>
                                        <tr>
                                            <td class="text-center"><?= $item['resolution'] ?>
                                            <td class="text-center"><?= $item['created_at'] ?></td>
                                            <td class="text-center"><?= ucwords($item['customer']) ?></td>
                                            <td class="text-center" width="100px">
                                                $ <?= number_format($item['payable_amount'], '0', '.', '.') ?>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                    switch ($item['invoice_status_id']){
                                                        case 5:
                                                            echo '<span class="badge new green darken-1 "  data-badge-caption="' . $item['status'] . '" ></span>';
                                                            break;
                                                        case 6:
                                                            echo '<span class="badge new red darken-1 "  data-badge-caption="' . $item['status'] . '" ></span>';
                                                            break;
                                                    }
                                                ?>
                                            </td>
                                            <td width="100px" >
                                                <div class="btn-group" role="group">
                                                    <a href="<?= base_url() ?>/invoice/pdf/<?= $item['companies_id'] ?>/<?= $item['id_invoice'] ?>"
                                                       class="btn btn-small  pink darken-1  tooltipped"
                                                       style="padding:0px 10px;" data-position="top"
                                                       data-tooltip="Descargar Cotizacion"
                                                    ><i class="material-icons">insert_drive_file</i></a>
                                                    <a href="<?= base_url() ?>/quotation/edit/<?= $item['companies_id'] ?>/<?= $item['id_invoice'] ?>"
                                                       class="btn btn-small yellow darken-2 tooltipped"
                                                       style="padding:0px 10px;" data-position="top"
                                                       data-tooltip="Editar Factura"><i
                                                                class="material-icons">create</i></a>
                                                    <a href="<?= base_url() ?>/quotation/email/<?= $item['id_invoice'] ?>/<?= $item['companies_id'] ?>"
                                                       class="btn btn-small tooltipped email <?=  $item['invoice_status_id'] ==  6 ? 'disabled': ''?>" style="padding:0px 10px;"
                                                       data-position="top" data-tooltip="Enviar Email">
                                                        <i class="material-icons">email</i>
                                                    </a>

                                                    <a href="<?= base_url() ?>/tracking/quotation/<?= $item['id_invoice'] ?>"
                                                       class="btn btn-small green tooltipped " style="padding:0px 10px;"
                                                       data-position="top" data-tooltip="Seguimiento de la Cotización">
                                                        <i class="material-icons">assignment</i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </thead>
                                    </tbody>
                                </table>
                                <?php if(count($invoices) == 0):  ?>
                                <p class="red-text text-center" style="padding: 10px 0px;">No hay documentos registrados.</p>
                                <?php endif; ?>
                            </div>

                            <?= $pager->only(['value', 'campo'])->makeLinks($page = isset($_GET['page']) ? $_GET['page'] : 1, $perPage = 10, $total = $count, 'default_full') ?>
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




<div style="bottom: 50px; right: 19px;" class="fixed-action-btn direction-top"><a
            class="btn-floating btn-large gradient-45deg-light-blue-cyan gradient-shadow"><i
                class="material-icons">add</i></a>
    <ul>
        <li><a href="https://api.whatsapp.com/send?phone=+573013207088&text=Hola, tengo una duda en mifacturalegal."
               target="_blank" class="btn-floating light-green darken-3"><i class="fa fa-whatsapp"></i></a></li>
    </ul>
</div>

<?= view('layouts/footer') ?>
<script>
    $(function () {
        $('.modal').modals();
        $('#modal1').modals('open');
        $('#modal1').modals('close');
    });

    $(document).ready(function () {
        $('.tooltipped').tooltip();

        $('.send').click(function () {
            $('.container-sprint-send').show();
            $('.container-sprint-send').css('display', 'flex');
            $('html, body').css({
                overflow: 'hidden',
                height: '100%'
            });

        });
        $('.email').click(function () {
            $('.container-sprint-email').show();
            $('.container-sprint-email').css('display', 'flex');
            $('html, body').css({
                overflow: 'hidden',
                height: '100%'
            });

        });
    });


    $(".card-alert .close").click(function () {
        $(this)
            .closest(".card-alert")
            .fadeOut("slow");
    });


    $(document).ready(function () {
        $('.otros').click(function () {
            const id = $(this).data('id');
            var URLactual = window.location;
            fetch(URLactual.origin + '/api/invoices/cufe/' + id)
                .then(function (response) {
                    return response.json();
                })
                .then(function (myJson) {
                    var dates = myJson;
                    $('#DIAN').attr('href', dates.url);
                });

            $('#noteCredit').attr('href', `/noteCredit/${id}`);
            $('#noteDebit').attr('href', `/noteDebit/${id}`);
            $('#csv').attr('href', `/document/csv/${id}`);
            $('#csvOffice').attr('href', `/document/worldOffice/${id}`);


            if ($(this).data('type') == 'Nota Débito' || $(this).data('type') == 'Nota Crédito') {
                $('#noteCredit').hide();
                $('#noteDebit').hide();

            } else {
                $('#noteCredit').show();
                $('#noteDebit').show();
            }


        });
    });


</script>
