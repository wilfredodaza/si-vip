<?= $this->extend('layouts/main') ?>


<?= $this->section('title') ?> Facturación <?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    td {
        padding-top: 3px;
        padding-bottom: 3px;
        font-size: 12px;
    }
</style>


<div id="main">
    <div class="row" id="filters">
        <div class="col s12">
            <div class="container">
                <div class="section">
                    <div class="card">
                        <div class="card-content">
                            <div class="row">
                                <div class="col s12">
                                    <?= $this->include('layouts/alerts') ?>
                                </div>
                                <div class="col s12">
                                    <div class="divider"></div>
                                    <span class="card-title" style="margin-top: 20px;margin-bottom: 0px;">
                                         <!--<a href="<?= base_url() . '/report_general/excel' ?>"
                                            class="btn modals-trigger btn-small grey lighten-5 grey-text text-darken-4 pull-right "
                                            style="margin-top: 10px; float: right;"
                                            data-toggle="modalss">
                                            Descargas
                                            <i class="material-icons right">cloud_download</i>
                                        </a>-->

                                        Conciliaciòn  Shopify<br>

                                    </span>
                                    <small style="margin-bottom: 10px; display: block;">En este Reporte se validan los
                                        pedidos de shopify de acuerdo a la fecha actual, si desea puede filtrar por
                                        fecha pero únicamente permite traer los pedidos de un mes</small>
                                    <small style="margin-bottom: 10px; display: block;">
                                        <p>
                                            <span style="vertical-align: center"><i
                                                        class="material-icons green-text tiny">fiber_manual_record</i>Pedidos sin diferencias</span>,
                                            <span style="vertical-align: center"><i
                                                        class="material-icons yellow-text tiny">fiber_manual_record</i>Pedidos sin enviar</span>,
                                            <span style="vertical-align: center"><i
                                                        class="material-icons red-text tiny">fiber_manual_record</i>Pedidos con diferencias</span>
                                            <span style="vertical-align: center"><i
                                                        class="material-icons blue-text tiny">fiber_manual_record</i>Pedidos con diferencias pero consolidados</span>
                                        </p></small>
                                    <form action="" method="get">
                                        <div class="row">
                                            <ul class="collapsible collapsible-filter" style="box-shadow: none;">
                                                <li class="active">
                                                    <div class="collapsible-header"><i class="material-icons">equalizer</i>Estadisticas
                                                    </div>
                                                    <div class="collapsible-body ">
                                                        <div class="row">
                                                            <div class="col s3 m3 l3">
                                                                <div class="card padding-4 animate fadeLeft">
                                                                    <div class="row">
                                                                        <div class="col s5 m5">
                                                                            <h5 class="mb-0"><?= $totals['quantityPSD'] ?></h5>
                                                                            <p class="no-margin">Cantidad</p>
                                                                        </div>
                                                                        <div class="col s7 m7 right-align">
                                                                            <i class="material-icons background-round mt-5 mb-5 green white-text">done</i>
                                                                            <p class="mb-0">Pedidos sin diferencia</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col s3 m3 l3">
                                                                <div class="card padding-4 animate fadeLeft">
                                                                    <div class="row">
                                                                        <div class="col s5 m5">
                                                                            <h5 class="mb-0"><?= $totals['quantityPSE'] ?></h5>
                                                                            <p class="no-margin">Cantidad</p>
                                                                        </div>
                                                                        <div class="col s7 m7 right-align">
                                                                            <i class="material-icons background-round mt-5 mb-5 yellow white-text">slow_motion_video</i>
                                                                            <p class="mb-0">Pedidos sin enviar</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col s3 m3 l3">
                                                                <div class="card padding-4 animate fadeLeft">
                                                                    <div class="row">
                                                                        <div class="col s5 m5">
                                                                            <h5 class="mb-0"><?= $totals['quantityPCD'] ?></h5>
                                                                            <p class="no-margin">Cantidad</p>
                                                                        </div>
                                                                        <div class="col s7 m7 right-align">
                                                                            <i class="material-icons background-round mt-5 mb-5 red white-text">error_outline</i>
                                                                            <p class="mb-0">Pedidos con diferencias</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col s3 m3 l3">
                                                                <div class="card padding-4 animate fadeLeft">
                                                                    <div class="row">
                                                                        <div class="col s5 m5">
                                                                            <h5 class="mb-0"><?= $totals['quantityPCDC'] ?></h5>
                                                                            <p class="no-margin">Cantidad</p>
                                                                        </div>
                                                                        <div class="col s7 m7 right-align">
                                                                            <i class="material-icons background-round mt-5 mb-5 blue white-text">offline_pin</i>
                                                                            <p class="mb-0">Pedidos consolidados</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li class="active">
                                                    <div class="collapsible-header"><i class="material-icons">search</i>Filtrar
                                                        por fecha
                                                    </div>
                                                    <div class="collapsible-body ">
                                                        <div class="row">
                                                            <div class="col  s12 m4 input-field">
                                                                <input type="date" name="date_start" onchange="fechas()"
                                                                       id="date_start">
                                                                <label for="date_start">Fecha de inicio</label>
                                                            </div>
                                                            <div class="col s12 m4 input-field">
                                                                <input type="date" name="date_end" id="date_end">
                                                                <label for="date_end">Fecha fin</label>
                                                            </div>
                                                            <div class="col s12 m4">
                                                                <div class="col s12 input-field">
                                                                    <button class="waves-effect waves-green btn indigo pull-right">
                                                                        Consulta
                                                                    </button>
                                                                    <a href="<?= base_url('integrations/shopify/report_conciliation') ?>" class="waves-effect waves-red red btn pull-right"
                                                                            style="margin-right: 10px;">Limpiar
                                                                    </a>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </form>
                                    <div class="divider"></div>
                                    <div class="table-response" style="overflow-x:auto;">
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <td style="text-align: center;">Número de pedido</td>
                                                <td style="text-align: center;">Estado Shopify</td>
                                                <td style="text-align: center;">Dia de creaciòn</td>
                                                <td style="text-align: center;">Nùmero MFL</td>
                                                <td style="text-align: center;">Dia de envío</td>
                                                <td style="text-align: center;">Estado</td>
                                                <td style="text-align: center;">Valor Shopify</td>
                                                <td style="text-align: center;">Valor Facturado</td>
                                                <td style="text-align: center;">Diferencia</td>
                                                <td style="text-align: center;">Acciones</td>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            foreach ($invoices as $invoice):
                                                ?>
                                                <tr>
                                                    <td style="text-align: center;"><?= $invoice['orderNumber'] ?></td>
                                                    <td style="text-align: center;"><?= $invoice['statusShopify'] ?></td>
                                                    <td style="text-align: center;"><?= $invoice['dateCreate'] ?></td>
                                                    <td style="text-align: center;"><?= $invoice['number_mfl'] ?></td>
                                                    <td style="text-align: center;"><?= $invoice['dataSend'] ?></td>
                                                    <td style="text-align: center;"><?= $invoice['status'] ?></td>
                                                    <td style="text-align: center;"><?= $invoice['valueShopify'] ?></td>
                                                    <td style="text-align: center;"><?= $invoice['value'] ?></td>
                                                    <td style="text-align: center;"><span
                                                                style="vertical-align: center">
                                                            <?php if ($invoice['diferencia'] < 1 && $invoice['status'] == 'aceptada'): $consolidation = false; ?>
                                                                <i class="material-icons green-text tiny"
                                                                   style="text-align: start;">fiber_manual_record</i>
                                                            <?php elseif ($invoice['valueShopify'] == $invoice['diferencia']): $consolidation = false; ?>
                                                                <i class="material-icons yellow-text tiny">fiber_manual_record</i>
                                                            <?php elseif($invoice['consolidations'] > 0): $consolidation = true;?>
                                                                <i class="material-icons blue-text tiny">fiber_manual_record</i>
                                                            <?php else: $consolidation = true;?>
                                                                <i class="material-icons red-text tiny">fiber_manual_record</i>
                                                            <?php endif; ?>
                                                            <?= '$ ' . $invoice['diferencia'] ?>
                                                        </span>
                                                    </td>

                                                    <td style="">
                                                        <div class="btn-group" role="group">
                                                            <button style=" padding-left: 5px;padding-right: 5px;" <?= ($consolidation)?'':'disabled' ?>
                                                                    class="btn btn-small blue lighten-3 modal-trigger consolidate tooltipped"
                                                                    data-target="consolidar" type="button"
                                                                    data-position="top"
                                                                    data-tooltip="consolidar"
                                                                    data-idTraffic="<?= $invoice['idTraffic'] ?>"
                                                                    data-consolidations="<?= $invoice['consolidations'] ?>"
                                                                    data-idIntegrationShopify="<?= $invoice['idShop'] ?>">
                                                                <i class="material-icons">assignment</i>
                                                            </button>
                                                            <button style=" padding-left: 5px;padding-right: 5px;"
                                                                    class="btn btn-small grey lighten-3 modal-trigger options-consolidate tooltipped"
                                                                    data-target="modal1" type="button"
                                                                    data-position="top"
                                                                    data-tooltip="Mas opciones"
                                                                    data-numberMfl="<?= $invoice['number_mfl'] ?>"
                                                                    data-shop="<?= $invoice['shop'] ?>"
                                                                    data-idShopify="<?= $invoice['idShopify'] ?>"
                                                                    data-typeDocument="<?= $invoice['typeDocument'] ?>"
                                                                    data-prefix="<?= $invoice['prefix'] ?>">
                                                                <i class="material-icons grey-text text-darken-4">add</i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php
                                            endforeach;
                                            ?>
                                            </tbody>
                                        </table>
                                        <?php if(count($invoices) == 0): ?>
                                            <p class="center red-text pt-1" >No hay ningún elemento.</p>
                                        <?php endif ?>
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
<!-- Modal acciones -->
<div id="modal1" class="modal" style="height: auto; width: 400px;">
    <div class="modal-content">
        <h4 class="modal-title">Opciones</h4>
        <a class="btn btn-block yellow darken-2" id="desDocumento">Descargar Documento</a><br>
        <a href="" class="btn btn-block green darken-2" target="_blank" id="verPedido">Ver pédido</a><br>
    </div>
    <div class="modal-footer">
        <a href="#!" class="modal-action modal-close waves-effect waves-green btn-flat btn-light-indigo">Cerrar</a>
    </div>
</div>
<!-- Consolidar -->
<div id="consolidar" class="modal" style="height: 300px; width: 600px;">
    <form method="post" action="<?= base_url('integrations/shopify/upload_consolidation')?>" >
        <div class="modal-content">
            <h4 class="modal-title"><a id="seeConsolidation" href="">
                    <span id="consolidations" class="chip red lighten-5 right red-text">2 consolidados</span></a>Consolidados</h4>
            <div class="row">
                <div class="input-field col s12 m12 l12">
                    <i class="material-icons prefix">mode_edit</i>
                    <textarea id="textarea2" name="note" class="materialize-textarea"></textarea>
                    <label for="textarea2">Consolidar</label>
                </div>
                <input type="text" name="integrationShopifyid" id="integrationShopifyId" hidden>
                <input type="text" name="integrationTrafficid" id="integrationTrafficId" hidden>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="waves-effect waves-green btn indigo pull-right">
                Consolidar
            </button>
            <a href="#!" class="modal-action modal-close waves-effect waves-green btn-flat btn-light-indigo">Cerrar</a>
        </div>
    </form>
</div>


<?= $this->endSection() ?>



<?= $this->section('scripts') ?>
<script src="<?= base_url('/js/advance-ui-modals.js') ?>"></script>
<script src="<?= base_url('/js/shepherd.min.js') ?>"></script>
<script src="<?= base_url('/js/vue.js') ?>"></script>
<script src="<?= base_url('/js/views/invoice.js') ?>"></script>
<script src="<?= base_url('/js/ui-alerts.js') ?>"></script>
<script>
    $(document).ready(function () {
        M.textareaAutoResize($('#textarea2'));
        $('.options-consolidate').click(function () {
            var document = 'FES';
            var numberMfl = $(this).attr("data-numberMfl");
            var shop = $(this).attr("data-shop");
            var idShopify = $(this).attr("data-idShopify");
            var typeDocument = $(this).attr("data-typeDocument");
            var prefix = $(this).attr("data-prefix");
            if (typeDocument == 4) {
                document = 'NCS';
                prefix = 'NC';
            }
            //console.log(`${numberMfl},${shop},${idShopify}, ${typeDocument}, ${prefix}`);
            $('#desDocumento').attr('href', `<?= getenv('API') ?>/download/<?= company()->identification_number ?>/${document}-${prefix}${numberMfl}.pdf`);
            $('#verPedido').attr('href', `https://${shop}/admin/orders/${idShopify}`);
        });
        $('.consolidate').click(function () {
            var shopifyId = $(this).attr("data-idIntegrationShopify");
            var trafficId = $(this).attr("data-idTraffic");
            var consolidations = $(this).attr("data-consolidations")
            $('#integrationTrafficId').val(trafficId);
            $('#integrationShopifyId').val(shopifyId);
            $('#seeConsolidation').attr('href', `<?= base_url()?>/integrations/shopify/see_consolidation/${shopifyId}/${trafficId}`);
            document.getElementById("consolidations").textContent=`Ver ${consolidations} consolidados`;
        });
    });
</script>
<script>
    const fechas = () => {
        const dateStart = document.getElementById("date_start").value;
        document.getElementById("date_end").min = dateStart;
        let date = new Date(dateStart);
        let monthMilliseconds = 1000 * 60 * 60 * 24 * 30;
        let value = date.getTime() + monthMilliseconds; //getTime devuelve milisegundos de esa fecha
        let dateEnd = new Date(value);
        document.getElementById("date_end").max = dateEnd.toISOString().split('T')[0];
    }
</script>
<?= $this->endSection() ?>
