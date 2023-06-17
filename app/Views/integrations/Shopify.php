<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?> Integración Shopify <?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div id="<?= (!isset($shop)) ? 'main' : '' ?>">
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
                               Integraciones
                            </span>
                            </h5>
                            <ol class="breadcrumbs mb-0">
                                <li class="breadcrumb-item"><a href="<?= base_url('integrations/') ?>">Aplicaciones</a>
                                </li>
                                <li class="breadcrumb-item"><a
                                            href="<?= base_url('integrations/shopify') ?>">Shopify</a>
                                </li>
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
                                <?php if ($active == false): ?>
                                <p class="">
                                <h5>Integra en segundos Mawii con Shopify</h5>
                                Conecta tu tienda Shopify y automatiza tu proceso de nómina eléctronica
                                </p>
                                <form action="<?= base_url('integrations/shopify/auth'); ?>" method="post">
                                    <ul class="stepper linear">
                                        <li class="step active">
                                            <div class="step-title waves-effect">Información General</div>
                                            <div class="step-content">
                                                <div class="row">
                                                    <div class="input-field col m6 s12">
                                                        <label class="active" for="name">Nombre de tu tienda: <span
                                                                    class="red-text">*</span></label>
                                                        <input type="text" id="name" name="name" class="validate"
                                                               placeholder="Mawii.myshopify.com" required="">
                                                    </div>
                                                    <div class="input-field col m6 s12">
                                                        <label class="active" for="status_invoice">Estado de las
                                                            facturas <span
                                                                    class="red-text">*</span></label>
                                                        <select id="status_invoice"
                                                                class="select2 browser-default validate"
                                                                name="status_invoice" required>
                                                            <option value="" disabled="" selected="">Seleccione estado
                                                            </option>
                                                            <option value="Borrador">Borrador</option>
                                                            <option value="Por pagar">Por pagar</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="step-actions">
                                                    <div class="row">
                                                        <div class="col m4 s12 mb-3">
                                                            <button id="btn_name"
                                                                    class=" dark btn btn-light-indigo next-step">
                                                                Continuar
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="step">
                                            <div class="step-title waves-effect">Dale permisos a Mawii</div>
                                            <div class="step-content">
                                                <div class="row">
                                                    <p>Ahora debes otorgarle permisos a la aplicación
                                                        <strong>Mawii</strong>,
                                                        por favor dar click en el botón "Permisos"</p>
                                                    <br>
                                                    <div class="col m12 s12 mr-5">

                                                    </div>
                                                </div>
                                            </div>
                            </div>
                            </li>
                            </ul>
                            </form>

                            <?php else: ?>
                                <a id="btncargue"
                                   href="<?= base_url() ?>/integrations/shopify/control_orders/<?= $idCompany ?>?idIntegrationShopify=<?= $idIntegrationShopify ?>"
                                   class="btn right btn-small btn-light-indigo modal-trigger tooltipped step-5"
                                   style="padding-left:2px; padding-right:10px; margin-left: 10px;" data-position="top"
                                   data-tooltip="Cargar pedidos">
                                    cargar <i class="material-icons left">autorenew</i>
                                </a>
                                <button class="btn right btn-small btn-light-indigo modal-trigger tooltipped step-5"
                                        data-position="top" data-tooltip="Filtrar pedidos"
                                        style="padding-left:2px; padding-right:10px; margin-left: 10px;"
                                        data-target="filter">
                                    <i class="material-icons left">filter_list </i> Filtrar
                                </button>
                                <p class="">
                                    <?php if (count($searchShow) != 0): ?>
                                        <a href="<?= base_url('integrations/shopify?shop=' . $shop) ?>"
                                           class="btn right btn-light-red btn-small ml-1"
                                           style="padding-left: 10px;padding-right: 10px; margin-right: 10px; ">
                                            <i class="material-icons left">close</i>
                                            Quitar Filtro
                                        </a>
                                    <?php endif; ?>
                                <h5>Shopify exclusivo <?= $nameCompany ?><span class="chip green lighten-5"><span
                                                class="card-title green-text  darken-1">
                                        <i class="material-icons no-padding" style="line-height: 0px !important;">notifications</i> Integración Activa</span></span>
                                </h5>
                                Puedes ver tu facturas enviadas y verificar que todas hayan sido recibidas por la DIAN.
                                <a class="btn btn-light-indigo btn-small ml-1 modal-trigger"
                                   style="padding-left: 10px;padding-right: 10px; margin-right: 10px; "
                                   href="#modal1">Generar Factura</a>
                                </p>

                                <div id="modal1" class="modal">
                                    <div class="modal-content">
                                        <form action="<?= base_url() ?>/integrations/shopify/control_orders/<?= $idCompany ?>"
                                              method="get">
                                            <div class="row">
                                                <div class="input-field col m6 s6">
                                                    <input placeholder="Número pedido" id="numbers" type="number"
                                                           class="validate" name="order" required>
                                                </div>
                                                <input type="number" hidden class="validate" name="idIntegrationShopify"
                                                       value="<?= $idIntegrationShopify ?>">
                                                <div class="col m6 s12">
                                                    <button type="submit" class="btn btn-light-indigo btn-small ml-1"
                                                            style="padding-left: 10px;padding-right: 10px; margin-right: 10px; margin-top: 20px; ">
                                                        Generar
                                                    </button>
                                                </div>
                                                <!--<div class="input-field col m3 s6">
                                                    <select name="type_order" class="select2 browser-default">
                                                        <option value="json">Json</option>
                                                        <option selected value="order">Factura</option>
                                                    </select>
                                                </div>-->
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <table class="table-responsive">
                                    <thead>
                                    <tr>
                                        <th class="center">Fecha</th>
                                        <th class="center">Número</th>
                                        <th class="center">Tipo documento</th>
                                        <th class="center">Pedido Shopify</th>
                                        <th class="center">Estado</th>
                                        <th class="center">Observaciones</th>
                                        <th class="center">Acciones</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($trafficLight as $item): ?>
                                        <tr>
                                            <td class="center"><?= $item['created_at'] ?></td>
                                            <td class="center"><?= $item['number_mfl'] ?></td>
                                            <td class="center"><?php switch ($item['type_document_id']) {
                                                    case '1':
                                                        echo 'Factura Venta Nacional';
                                                        break;
                                                    case '4':
                                                        echo 'Nota credito';
                                                        break;
                                                }
                                                ?></td>
                                            <td class="center"><?= $item['number_app'] ?></td>
                                            <td class="center state" width="150px">
                                                <?php switch ($item['status']) {
                                                    case 'aceptada':
                                                        echo '<span class="chip green lighten-5"><span class="green-text">' . ucfirst($item['status']) . '</span></span>';
                                                        break;
                                                    case 'rechazada':
                                                        echo '<span class="chip red lighten-5"><span class="red-text">' . ucfirst($item['status']) . '</span></span>';
                                                        break;
                                                    case 'devuelto':
                                                        echo '<span class="chip lighten-5 blue"><span class="blue-text">' . ucfirst($item['status']) . '</span></span>';
                                                        break;
                                                    case 'devuelto_prod':
                                                        echo '<span class="chip lighten-5 orange"><span class="blue-text">Devoluciòn Prod</span></span>';
                                                        break;
                                                }
                                                ?>
                                            </td>
                                            <td class="center"><?= $item['observations'] ?></td>
                                            <td class="center">
                                                <div class="btn-group" role="group">

                                                    <a href="<?= getenv('API') ?>/download/<?= $nit ?>/<?= ($item['type_document_id'] != 4) ? 'FES' : 'NCS' ?>-<?= ($item['type_document_id'] != 4) ? $prefix : 'NC' ?><?= $item['number_mfl'] ?>.pdf" <?= ($item['status'] == 'rechazada') ? 'disabled' : '' ?>
                                                       class="btn btn-small  green darken-1  tooltipped"
                                                       data-position="top" data-tooltip="Descargar factura">
                                                        <i class="material-icons">file_download</i>
                                                    </a>
                                                    <?php if ($item['status'] == 'aceptada' && $item['check_return'] != 1 && $item['type_document_id'] == 1): ?>
                                                        <button
                                                                class=" check_return btn btn-small pink darken-1  tooltipped"
                                                                data-position="top"
                                                                data-numberMfl="<?= $item['number_mfl'] ?>"
                                                                data-tooltip="Nota Credito">
                                                            <i class="material-icons">remove_circle</i>
                                                        </button>
                                                        <button
                                                                class=" verify_return_by_product btn btn-small purple darken-1  tooltipped"
                                                                data-position="top"
                                                                data-numberMfl="<?= $item['number_mfl'] ?>"
                                                                data-tooltip="Nota Credito por producto">
                                                            <i class="material-icons">remove</i>
                                                        </button>
                                                    <?php elseif ($item['status'] == 'devuelto'): ?>
                                                        <button <?= ($item['check_return'] == 0) ? 'disabled' : '' ?>
                                                                class="regenerar_pedido btn btn-small blue darken-1  tooltipped"
                                                                data-position="top"
                                                                data-numberApp="<?= $item['number_app'] ?>"
                                                                data-tooltip="Regenerar pedido">
                                                            <i class="material-icons">replay</i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>

                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <?php if (count($trafficLight) == 0): ?>
                                    <p class="center red-text pt-1">No hay ningún elemento enviado.</p>
                                <?php endif ?>
                                <?= $pager->links(); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <style>
        .container-sprint-cargando {
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

    <div class="container-sprint-cargando" style="display:none;">
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
        <span style="width: 100%; text-align: center; color: white;  display: block; ">Cargando Facturas</span>
    </div>
    <div class="container-sprint-nc" style="display:none;">
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
        <span style="width: 100%; text-align: center; color: white;  display: block; ">Cargando Nota Crédito</span>
    </div>
    <!--------------------------- modal de busqueda ------------------------------->
    <form action="" method="GET" autocomplete="off">
        <div id="filter" class="modal" role="dialog" style="height:auto; width: 600px">
            <div class="modal-content">
                <h5>Filtrar Pedido</h5>
                <div class="row">
                    <input type="text" value="<?= $shop ?>" name="shop" hidden>
                    <div class="col s12 m6 input-field">
                        <label for="number_app">Nùmero pedido Shopify</label>
                        <input type="text" id="number_app" name="number_app" value="<?= $_GET['number_app'] ?? '' ?>">
                    </div>
                    <div class="col s12 m6 input-field">
                        <label for="number_mfl">Nùmero MFL</label>
                        <input type="text" id="number_mfl" name="number_mfl" value="<?= $_GET['number_mfl'] ?? '' ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col s12 m6 input-field">
                        <select name="status" id="status">
                            <option selected value="">Seleccione una opciòn</option>
                            <option value="aceptada">Aceptados</option>
                            <option value="rechazada">Rechazados</option>
                        </select>
                        <label for="status">Estado</label>
                    </div>
                    <!--<div class="col s12 m6 input-field">
                        <label for="second_surname">Segundo Apellido</label>
                        <input type="text" id="second_surname" name="second_surname" value="<?= $_GET['second_surname'] ?? '' ?>">
                    </div>-->
                </div>
            </div>
            <div class="modal-footer">
                <a href="#!" class="modal-action modal-close waves-effect  btn-flat btn-light-indigo ">Cerrar</a>
                <button class="btn indigo">Buscar</button>

            </div>
        </div>
    </form>
    <!--------------------------- modal para veficicar productos para devolucion ------>
    <div id="productReturn" class="modal" style="height: 300px; width: 600px;">
        <div class="modal-content">
            <h4 class="modal-title">Productos para devolucion</h4>
            <div class="row">
                <div class="col m12 l12 s12">
                    <ul id="respReturn">

                    </ul>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button id="check_return_by_product" disabled class="btn btn-small purple darken-1"
                    data-numberMfl="">
                Seguir
            </button>
            <button id="calcelProductReturn"
                    class="modal-action modal-close waves-effect waves-green btn-flat btn-light-indigo">Cerrar</button>
        </div>
    </div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
    <script src="<?= base_url('/js/advance-ui-modals.js') ?>"></script>
    <script src="<?= base_url('/js/ui-alerts.js') ?>"></script>
    <script src="<?= base_url('/js/jquery.validate.js') ?>"></script>
    <script src="<?= base_url('/js/jquery.validate.js') ?>"></script>
    <script src="<?= base_url('/assets/js/sweetalert.min.js') ?>"></script>
    <script>
        var stepper = document.querySelector('.stepper');
        var stepperInstace = new MStepper(stepper, {
            // options
            firstActive: 0 // this is the default
        })
        $(document).ready(function () {
            $(".select2").select2({
                placeholder: 'Seleccione una opcion ...'
            });
            $('#btn_name').click(function () {
                var client = '<?= $client_id ?>';
                var scope = 'read_orders,write_orders,' +
                    'read_assigned_fulfillment_orders,write_assigned_fulfillment_orders,' +
                    'read_customers,write_customers,' +
                    'read_draft_orders,write_draft_orders';
                var url = 'https://' + $('#name').val() + '/admin/oauth/authorize?client_id=' + client + '&scope=' + scope + '&redirect_uri=https://planetalab.xyz/integrations/shopify/token_access&state=Mawii_facturador_prueba';
                $('#url_permissions').attr('href', url);
                console.log($('#url_permissions').val());
                $.post('<?= base_url() ?>/integrations/shopify/save_name', JSON.stringify({
                    'nombre': $('#name').val(),
                    'status': $('#status_invoice').val()
                }), function (data) {
                    console.log(data);
                });
            });
        });

    </script>
    <script>
        var url = "<?= base_url()?>/integrations/shopify/credit_note";
        var urlByProduct = "<?= base_url()?>/integrations/shopify/credit_note_by_product";
        var urlByProductValidation = "<?= base_url()?>/integrations/shopify/product_for_note_credit";
        var urlRegenerate = "<?= base_url() ?>/integrations/shopify/regenerate_order";
        $(document).ready(function () {
            $('#btncargue').click(function () {
                $('.container-sprint-cargando').show();
                $('.container-sprint-cargando').css('display', 'flex');
                $('html, body').css({
                    overflow: 'hidden',
                    height: '100%'
                });
            });
            $(".check_return").click(function () {
                swal({
                    title: "¿Esa seguro de realizar una Nota crédito a esta factura?",
                    text: "Recuerde al momento de generar la nota crédito, no se podrá deshacer esta acción.",
                    icon: "info",
                    buttons: ["cancelar", " Aceptar "],
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        $.post(url,
                            {
                                number: $(this).attr("data-numberMfl"),
                                shop: `<?= $shop ?>`,
                                idCompany: `<?= $idCompany ?>`
                            },
                            function (data, status) {
                                var resp = JSON.parse(data);
                                console.log(data.data);
                                if (resp.status == 'aceptada') {
                                    swal({
                                        title: "Procesado con éxito!",
                                        //text: "You clicked the button!",
                                        icon: "success",
                                    });
                                    setTimeout(() => {
                                        location.reload();
                                    }, 5000);
                                } else {
                                    swal({
                                        title: "No se realizó el proceso",
                                        text: resp.observation,
                                        icon: "warning",
                                    });
                                }
                            });

                    } else {
                        swal({
                            title: "Cancelado!",
                            //text: "You clicked the button!",
                            icon: "error",
                        });
                    }
                });
            });
            $("#check_return_by_product").click(function () {
                swal({
                    title: "¿Esa seguro de realizar una Nota crédito a esta factura?",
                    text: "Recuerde al momento de generar la nota crédito, no se podrá deshacer esta acción.",
                    icon: "info",
                    buttons: ["cancelar", " Aceptar "],
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        $.post(urlByProduct,
                            {
                                number: $(this).attr("data-numberMfl"),
                                shop: `<?= $shop ?>`,
                                idCompany: `<?= $idCompany ?>`
                            },
                            function (data, status) {
                                var resp = JSON.parse(data);
                                console.log(data.data);
                                if (resp.status == 'aceptada') {
                                    swal({
                                        title: "Procesado con éxito!",
                                        //text: "You clicked the button!",
                                        icon: "success",
                                    });
                                    setTimeout(() => {
                                        location.reload();
                                    }, 5000);
                                } else {
                                    swal({
                                        title: "No se realizó el proceso",
                                        text: resp.observation,
                                        icon: "warning",
                                    });
                                }
                            });

                    } else {
                        swal({
                            title: "Cancelado!",
                            //text: "You clicked the button!",
                            icon: "error",
                        });
                    }
                });
            });
            $(".verify_return_by_product").click(function () {
                document.getElementById('respReturn').innerHTML = '';
                $('#check_return_by_product').attr('data-numberMfl', '');
                var number = $(this).attr("data-numberMfl");
                $.post(urlByProductValidation,
                    {
                        number: number,
                        shop: `<?= $shop ?>`,
                        idCompany: `<?= $idCompany ?>`
                    },
                    function (data, status) {
                        var resp = JSON.parse(data);
                        console.log(data);
                        if (resp.status == 'aceptada') {
                            resp.data.forEach(function (valor, indice) {
                                const listViewItem = document.createElement('li');
                                listViewItem.appendChild(document.createTextNode(`producto : ${valor.nameProduct} - Cantidad : ${valor.quantity}`));
                                document.querySelector("#respReturn").appendChild(listViewItem)
                            });
                            $('#check_return_by_product').attr('data-numberMfl', number);
                            if(resp.data.length > 0){
                                $('#check_return_by_product').prop('disabled', false);
                            }
                            $('#productReturn').modal({backdrop: 'static', keyboard: false})
                            $('#productReturn').modal('open');
                        } else {

                        }
                    });
            });
            $(".regenerar_pedido").click(function () {
                swal({
                    title: "¿Desea volver a crear la factura?",
                    //text: "Recuerde al momento de generar la nota crédito, no se podrá deshacer esta acción.",
                    icon: "info",
                    buttons: ["cancelar", " Aceptar "],
                    dangerMode: true,
                })
                    .then((willDelete) => {
                        if (willDelete) {
                            $.post(urlRegenerate,
                                {
                                    shop: `<?= $shop ?>`,
                                    idCompany: `<?= $idCompany ?>`,
                                    order: $(this).attr("data-numberApp")
                                },
                                function (data, status) {
                                    var resp = JSON.parse(data);
                                    if (resp.status == 'aceptada') {
                                        swal({
                                            title: "Procesado con éxito!",
                                            //text: "You clicked the button!",
                                            icon: "success",
                                        });
                                        setTimeout(() => {
                                            window.location.href = "<?= base_url() ?>/integrations/shopify?shop=<?= $shop ?>";
                                        }, 5000);
                                    } else {
                                        swal({
                                            title: "No se realizó el proceso",
                                            text: resp.observation,
                                            icon: "warning",
                                        });
                                    }
                                });

                        } else {
                            swal({
                                title: "Cancelado!",
                                //text: "You clicked the button!",
                                icon: "error",
                            });
                        }
                    });
            });
        });
    </script>
<?= $this->endSection() ?>