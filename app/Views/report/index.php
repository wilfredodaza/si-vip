<?= view('layouts/header') ?>
<?= view('layouts/navbar_horizontal') ?>
<?= view('layouts/navbar_vertical') ?>


<style>
    td {
        padding-top: 3px;
        padding-bottom: 3px;
    }
</style>


<div id="main">
    <div class="row" id="filters">
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
                                <div class="col s12 m3 " style="position: relative;">
                                    <div class="card-title">
                                        <h4>Reportes</h4>
                                    </div>
                                </div>
                                <div class="col m9 s9">
                                    <div class="row">
                                        <div class="col m6 s12  push-l6">
                                            <a href="<?= base_url().'/report/reset'?>"
                                               class="btn btn-small modals-trigger red tooltipped" data-tooltip="Eliminar Filtro"
                                               style="padding:0px 10px;margin-top: 10px; float: right; ">Filtro<i
                                                        class="material-icons right">close</i></a>
                                            <a href="#format"
                                               class="btn modals-trigger btn-small grey lighten-5 grey-text text-darken-4 "
                                               style="padding:0px 10px;margin-top: 10px; float: right; margin-right: 10px;"
                                               data-toggle="modalss">
                                                Descargas
                                                <i class="material-icons right">cloud_download</i>
                                            </a>
                                            <a href="#filter"
                                               class="btn btn-small modals-trigger grey lighten-5 grey-text text-darken-4 "
                                               data-toggle="modals"
                                               style="padding:0px 10px;margin-top: 10px;float: right;margin-right: 10px;">
                                                Filtrar <i class="material-icons right">filter_list</i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="divider"></div>
                            <div>
                                <div class="table-response" style="overflow-x:auto;">
                                    <table>
                                        <thead>
                                        <tr>
                                            <td style="text-align: center;">Nro</td>
                                            <td style="text-align: center;">Fecha</td>
                                            <td style="text-align: center;">Documento</td>
                                            <td style="text-align: center;">Cliente</td>
                                            <td style="text-align: center;">Estado</td>
                                            <td style="text-align: center;">Cartera</td>
                                            <td style="text-align: center;">Valor Base</td>
                                            <td style="text-align: center;">Total</td>
                                            <td style="text-align: center;">Acciones</td>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($invoices as $item): ?>
                                            <tr>
                                                <td style="text-align: center;"><?= $item->resolution ?></td>
                                                <td style="text-align: center;"><?= $item->created_at ?></td>
                                                <td style="text-align: center;"><?= $item->type_document ?></td>
                                                <td style="text-align: center;"
                                                    width="300px"><?= $item->customer_name ?></td>
                                                <td style="text-align: center;"><?= $item->invoice_status_name ?></td>
                                                <td style="text-align: center;"><?= $item->status_wallet ?></td>
                                                <td style="text-align: center;">
                                                    $<?= number_format(($item->tax_exclusive_amount), '2', ',', '.') ?></td>
                                                <td style="text-align: center;">
                                                    $<?= number_format(($item->payable_amount) - retentions($item->invoices_id), '2', ',', '.') ?></td>
                                                <td style="text-align: center;">
                                                    <div class="btn-group" role="group">
                                                        <a href="<?= base_url('report/tax/' . $item->id) ?>"
                                                           class="btn tooltipped yellow darken-2  btn-small"
                                                           style="padding:0px 10px;"
                                                           data-tooltip="Informe de Impuestos">
                                                            <i class="material-icons small">assignment</i>

                                                        </a>
                                                        <a href="<?= base_url('report/sale/' . $item->id) ?>"
                                                           class="btn green tooltipped btn-small"
                                                           style="padding:0px 10px;"
                                                           data-tooltip="Informe de Venta">
                                                            <i class="material-icons small">attach_money</i>
                                                        </a>
                                                    </div>

                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                    <?= $pager->makeLinks($page = isset($_GET['page']) ? $_GET['page'] : 1, $perPage = 10, $total = $count, 'default_full') ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="format" class="modals" role="dialog" style="height:auto; width: 530px">
        <div class="modals-content">
            <h4>Descargar</h4>
            <div class="divider" style="margin:10px 0px; "></div>
            <a href="<?= base_url() . '/report/csv' ?>" class="btn green" style="width: 100%; margin-top: 10px;">Descargar
                CSV</a>
            <a href="<?= base_url() . '/report/csvExportReportTax' ?>" class="btn yellow darken-2"
               style="width: 100%; margin-top: 10px;">Informe de Impuesto</a>
            <a href="<?= base_url() . '/report/csvExportReportSale' ?>" class="btn pink"
               style="width: 100%; margin-top: 10px;">Informe de Venta</a>
           <!-- <a href="<?= base_url() . '/report/csvWordOffice' ?>" class="btn blue"
               style="width: 100%; margin-top: 10px;">WordOffice</a>-->
            <a href="<?= base_url() . '/report/csvExportHelisa' ?>" class="btn blue"
               style="width: 100%; margin-top: 10px;">Helisa</a>



        </div>
        <div class="modals-footer" style=" border-top: #E0E0E0 1px solid; display: block; clear: both;">
            <a href="#!" class="modals-action modals-close waves-effect waves-red btn-flat mb-5 ">Cerrar</a>
        </div>
    </div>

    <div id="filter" class="modals" role="dialog" style="height:auto; width: 1000px">
        <form action="" method="get" id="form" v-on:submit.prevent="send()">
            <div class="modals-content">
                <h4>Filtro </h4>
                <div class="divider" style="margin:10px 0px; "></div>
                <div class="row">
                    <div class="col s12 m6">
                        <div class="row">
                            <div class="col s4">
                                <span for="value" style=" display:block;margin-top: 20px;">Fecha de inicio</span>
                            </div>
                            <div class="col s8">
                                <input type="date" name="date_start" v-model="dateStart">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col s4">
                                <span for="value" style=" display:block;margin-top: 20px;">Cliente</span>
                            </div>
                            <div class="col s8">
                                <div class="input-field">
                                    <select class="select browser-default" name="customer_id" v-model="customerId">
                                        <option value="" disabled>Seleccione ...</option>
                                        <option v-for="item of customers" v-bind:value="item._id">{{ item.name }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col s12 m4">
                                <span for="value" style=" display:block;margin-top: 20px;">Estado de Factura</span>
                            </div>
                            <div class="col s8">
                                <div class="input-field">
                                    <select multiple name="status_invoice" v-model="statusInvoice">
                                        <option value="" disabled>Seleccione ...</option>
                                        <option value="1">Guardada</option>
                                        <option value="2">Enviada a la DIAN</option>
                                        <option value="3">Email Enviado</option>
                                        <option value="4">Recibido por el cliente</option>
                                    </select>

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col s4">
                                <span for="value" style=" display:block;margin-top: 20px;">Cuenta Contable</span>
                            </div>
                            <div class="col s8">
                                <div class="input-field">
                                    <select multiple name="account" v-model="accountId">
                                        <option disabled value="">Seleccione ...</option>
                                        <?php foreach ($accoutings as $item): ?>
                                            <option value="<?= $item->id ?>"><?= $item->name . ' - [' . $item->percent . ']' ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col s4">
                                <span for="value" style=" display:block;margin-top: 20px;">Usuarios</span>
                            </div>
                            <div class="col s8">
                                <div class="input-field">
                                    <select  class="select browser-default"  name="user" v-model="userId">
                                        <option disabled value="">Seleccione ...</option>
                                        <option v-for="item of users" v-bind:value="item.id">{{ item.name }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col s12 m6">
                        <div class="row">
                            <div class="col s4">
                                <span for="value" style=" display:block;margin-top: 20px;">Fecha fin</span>
                            </div>
                            <div class="col s8">
                                <input id="value" type="date" name="date_end" v-model="dateEnd">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col s4">
                                <span for="value" style=" display:block;margin-top: 20px;">Vendedor</span>
                            </div>
                            <div class="col s8">
                                <div class="input-field">
                                    <select class="select browser-default" name="seller_id" v-model="sellerId">
                                        <option value="" disabled>Seleccione ...</option>
                                        <option v-for="item of sellers" v-bind:value="item.id">{{ item.name }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col s4">
                                <span for="value" style=" display:block;margin-top: 20px;">Estado de cartera</span>
                            </div>
                            <div class="col s8">
                                <div class="input-field">
                                    <select multiple name="status_wallet" v-model="statusWallet">
                                        <option value="" disabled>Seleccione ...</option>
                                        <option value="Paga">Paga</option>
                                        <option value="Pendiente">Pendiente</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col s4">
                                <span for="value" style=" display:block;margin-top: 20px;">Tipo de Documento</span>
                            </div>
                            <div class="col s8">
                                <div class="input-field">
                                    <select multiple name="account" v-model="typeDocumentId">
                                        <option disabled value="">Seleccione ...</option>
                                        <?php foreach ($typeDocuments as $item): ?>
                                            <option value="<?= $item->id ?>"><?= $item->name ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modals-footer" style=" border-top: #E0E0E0 1px solid; display: block; clear: both;">
                    <a href="#!" class="modals-action modals-close waves-effect waves-red btn-flat mb-5 ">Cerrar</a>
                    <button class="modals-action     waves-effect waves-green btn indigo mb-5">Guardar</button>
                </div>
        </form>
    </div>
</div>


</div>


<script src="<?= base_url() ?>/assets/js/vendors.min.js"></script>
<script src="<?= base_url() ?>/assets/js/select2.full.min.js"></script>
<script src="<?= base_url() ?>/assets/js/plugins.min.js"></script>
<script src="<?= base_url() ?>/assets/js/search.js"></script>
<script src="<?= base_url() ?>/assets/js/custom-script.min.js"></script>
<script src="<?= base_url() ?>/assets/js/form-select2.js"></script>
<script src="<?= base_url() ?>/assets/js/advance-ui-modals.js"></script>
<script src="<?= base_url() ?>/dropify/js/dropify.min.js"></script>
<script src="<?= base_url() ?>/assets/js/ui-alerts.js"></script>


<script src="https://cdn.jsdelivr.net/npm/vue@2/dist/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script type="text/javascript">
    const vue = new Vue({
        el: '#main',
        mounted() {
            this.customer();
            this.url = localStorage.getItem('url');
            this.seller();
            this.user();
        },
        data: {
            dateStart: '',
            dateEnd: '',
            customerId: '',
            statusInvoice: [],
            statusWallet: [],
            accountId: [],
            reports: [],
            url: '',
            customers: [],
            typeDocumentId: [],
            sellers: [],
            sellerId: '',
            userId: '',
            users: []
        },
        methods: {
            send() {
                axios.post(`${localStorage.getItem('url')}/report`,
                    {
                        dataStart: this.dateStart,
                        dataEnd: this.dateEnd,
                        customerId: this.customerId,
                        statusInvoice: this.statusInvoice,
                        statusWallet: this.statusWallet,
                        accountId: this.accountId,
                        typeDocumentId: this.typeDocumentId,
                        sellerId: this.sellerId,
                        userId: this.userId
                    }).then((response) => {
                    location.reload();
                });
            },
            customer() {
                axios.get(`${localStorage.getItem('url')}/api/v1/customers`).then((response) => {
                    this.customers = response.data.data;
                }).catch((error) => {
                    console.log(error);
                })
            },
            seller()  {
                axios.get(`${localStorage.getItem('url')}/api/v1/sellers`).then((response) => {
                    this.sellers = response.data.data;
                }).catch((error) => {
                    console.log(error);
                })
            },
            user() {
                axios.get(`${localStorage.getItem('url')}/api/v1/users`).then((response) => {
                    this.users = response.data.data;
                }).catch((error) => {
                    console.log(error);
                })
            }
        }

    });
</script>


</body>
</html>