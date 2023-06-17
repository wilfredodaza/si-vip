<?= view('layouts/header') ?>
<?= view('layouts/navbar_horizontal') ?>
<?= view('layouts/navbar_vertical') ?>


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
                            <form id="form" v-on:submit.prevent="send()">
                                <div class="row">
                                    <div class="col s12 ">

                                        <div class="row">
                                            <ul class="collapsible collapsible-filter" style="box-shadow: none;">
                                                <li class="active">
                                                    <div class="collapsible-header"><i class="material-icons">search</i>Filtrar Reporte General</div>
                                                    <div class="collapsible-body ">
                                                        <div class="row">
                                                            <div class="col  s12 m4 input-field">
                                                                <input type="date" name="date_start" id="date_start"
                                                                       v-model="dateStart">
                                                                <label for="date_start">Fecha de inicio</label>
                                                            </div>
                                                            <div class="col s12 m4 input-field">
                                                                <input type="date" name="date_end" id="date_end" v-model="dateEnd">
                                                                <label for="date_end">Fecha fin</label>
                                                            </div>
                                                            <div class="col s12 m4 input-field">

                                                                <select multiple name="type_document" id="type_document"
                                                                        v-model="typeDocumentId">
                                                                    <option disabled value="">Seleccione ...</option>
                                                                    <?php foreach ($typeDocuments as $item): ?>
                                                                        <option value="<?= $item->id ?>"><?= $item->name ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                                <label for="type_document">Tipo de Documento</label>
                                                            </div>

                                                            <!-- -->
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="collapsible-header"><i class="material-icons">search</i>Busqueda Avanzada</div>
                                                    <div class="collapsible-body">
                                                        <div class="row">
                                                            <div class="col s12 m4 input-field">
                                                                <select class="select browser-default" name="customer_id"
                                                                        id="customer_id" v-model="customerId">
                                                                    <option value="" disabled>Seleccione ...</option>
                                                                    <option v-for="item of customers" v-bind:value="item._id">
                                                                        {{ item.name }}
                                                                    </option>
                                                                </select>
                                                                <label for="customer_id" class="active">Cliente</label>
                                                            </div>
                                                            <div class="col s12 m4 input-field">
                                                                <select multiple name="status_invoice" id="status_invoice"
                                                                        v-model="statusInvoice">
                                                                    <option value="" disabled>Seleccione ...</option>
                                                                    <option value="1">Guardada</option>
                                                                    <option value="2">Enviada a la DIAN</option>
                                                                    <option value="3">Email Enviado</option>
                                                                    <option value="4">Recibido por el cliente</option>
                                                                </select>
                                                                <label for="status_invoice">Estado de Factura</label>
                                                            </div>
                                                            <div class="col s12 m4 input-field">
                                                                <select multiple name="status_wallet" v-model="statusWallet">
                                                                    <option value="" disabled>Seleccione ...</option>
                                                                    <option value="Paga">Paga</option>
                                                                    <option value="Pendiente">Pendiente</option>
                                                                </select>
                                                                <label for="status_wallet">Estado de cartera</label>
                                                            </div>
                                                            <div class="col s12 m4 input-field">
                                                                <select multiple name="account_id" id="account_id" v-model="accountId">
                                                                    <option disabled value="">Seleccione ...</option>
                                                                    <?php foreach ($accoutings as $item): ?>
                                                                        <option value="<?= $item->id ?>"><?= $item->name . ' - [' . $item->percent . ']' ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                                <label for="account_id">Cuenta Contable</label>
                                                            </div>
                                                            <div class="col s12 m4 input-field">
                                                                <select id="userId" class="select browser-default" v-model="userId">
                                                                    <option disabled value="">Seleccione ...</option>
                                                                    <option v-for="item of users" v-bind:value="item.id">{{ item.name
                                                                        }}
                                                                    </option>
                                                                </select>
                                                                <label for="user_id" class="active">Usuario</label>
                                                            </div>
                                                            <div class="col s12 m4 input-field">
                                                                <select class="select browser-default" name="seller_id" id="seller_id"
                                                                        v-model="sellerId">
                                                                    <option value="" disabled>Seleccione ...</option>
                                                                    <option v-for="item of sellers" v-bind:value="item.id">{{ item.name
                                                                        }}
                                                                    </option>
                                                                </select>
                                                                <label for="seller_id" class="active">Vendedor</label>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="row">
                                            <div class="col s12">
                                                <div class="col s12 input-field">
                                                    <button class="waves-effect waves-green btn indigo pull-right">
                                                        Consulta
                                                    </button>
                                                    <button class="waves-effect waves-red red btn pull-right"
                                                            @click.prevent="reset()" style="margin-right: 10px;">Limpiar
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="row">
                                <div class="col s12">
                                    <div class="divider"></div>
                                    <span class="card-title" style="margin-top: 20px;margin-bottom: 0px;">
                                         <a href="<?= base_url() . '/report_general/excel' ?>"
                                            class="btn modals-trigger btn-small grey lighten-5 grey-text text-darken-4 pull-right"
                                            style="margin-top: 10px;"
                                            data-toggle="modalss">
                                            Descargas
                                            <i class="material-icons right">cloud_download</i>
                                        </a>

                                        Reporte general de facturación<br>

                                    </span>
                                    <small style="margin-bottom: 10px; display: block;">En este Reporte no se detallan productos facturados. Va a nivel de factura.</small>
                                    <div class="divider"></div>
                                    <div class="table-response" style="overflow-x:auto;">
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <td style="text-align: center;">#</td>
                                                <td style="text-align: center;">Tercero</td>
                                                <td style="text-align: center;">Tipo de identificación</td>
                                                <td style="text-align: center;">Número de identificación</td>
                                                <td style="text-align: center;">Tipo de documento</td>
                                                <td style="text-align: center;">Usuario</td>
                                                <td style="text-align: center;">Vendedor</td>
                                                <td style="text-align: center;">Número de documento</td>
                                                <td style="text-align: center;">Estado de factura</td>
                                                <td style="text-align: center;">Fecha de factura</td>
                                                <td style="text-align: center;">Valor facturado antes de IVA</td>
                                                <td style="text-align: center;">IVA ($)</td>
                                                <td style="text-align: center;">Retención en la fuente</td>
                                                <td style="text-align: center;">Retención de ICA</td>
                                                <td style="text-align: center;">Retención de IVA</td>
                                                <td style="text-align: center;">Total Factura</td>
                                                <td style="text-align: center;">Estado de cartera</td>
                                                <td style="text-align: center;">Saldo por cobrar</td>

                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                                $i = 1;
                                                foreach ($invoices as $item):
                                                $taxes = taxes($item->id, $item->type_documents_id);
                                                $wallets = wallets($item->id);
                                                ?>
                                                <tr>
                                                    <td style="text-align: center;"><?= $i++ ?></td>
                                                    <td style="text-align: center;"><?= $item->customer_name ?></td>
                                                    <td style="text-align: center;"><?= $item->type_document_identification ?></td>
                                                    <td style="text-align: center;"><?= $item->identification_number ?></td>
                                                    <td style="text-align: center;"><?= $item->type_documents ?></td>
                                                    <td style="text-align: center;"><?= userName($item->user_id) ?></td>
                                                    <td style="text-align: center;"><?= userName($item->seller_id) ?></td>
                                                    <td style="text-align: center;"><?= $item->resolution ?></td>
                                                    <td style="text-align: center;"><?= $item->invoice_status ?></td>
                                                    <td style="text-align: center;"><?= $item->created_at ?></td>
                                                    <td style="text-align: center;">
                                                        $ <?= number_format($item->line_extesion_amount - $taxes['free'], '2', ',', '.') ?></td>
                                                    <td style="text-align: center;">
                                                        $ <?= number_format($taxes['iva'], '2', ',', '.') ?></td>
                                                    <td style="text-align: center;">
                                                        $ <?= number_format($taxes['reteFuente'], '2', ',', '.') ?></td>
                                                    <td style="text-align: center;">
                                                        $ <?= number_format($taxes['reteICA'], '2', ',', '.') ?></td>
                                                    <td style="text-align: center;">
                                                        $ <?= number_format($taxes['reteICA'], '2', ',', '.') ?></td>
                                                    <td style="text-align: center;">
                                                        $ <?= number_format($item->payable_amount + $taxes['iva'] - ($taxes['reteICA'] + $taxes['reteIVA'] + $taxes['reteFuente']), '2', ',', '.') ?></td>
                                                    <td style="text-align: center;"><?= ($item->type_documents_id == 1 || $item->type_documents_id == 5) ? $item->status_wallet : 'Paga' ?></td>
                                                    <?php if ($item->type_documents_id == 1 || $item->type_documents_id == 5): ?>
                                                        <?php if ($item->status_wallet == 'Pendiente'): ?>
                                                            <td style="text-align: center;">
                                                                $ <?= count($wallets) > 0 ?
                                                                    number_format(($item->payable_amount - ($wallets[0]->value + $taxes['reteIVA'] + $taxes['reteICA'] + $taxes['reteFuente'] + $taxes['free'])), '2', ',', '.') :
                                                                    number_format(($item->payable_amount - ($taxes['reteIVA'] + $taxes['reteICA'] + $taxes['reteFuente'] + $taxes['free'])), '2', ',', '.') ?>
                                                            </td>
                                                        <?php else: ?>
                                                            <td style="text-align: center;">$ 0</td>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <td style="text-align: center;">$ 0</td>
                                                    <?php endif; ?>
                                                </tr>
                                            <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                        <?php if(count($invoices) == 0): ?>
                                <p class="center red-text pt-1" >No hay ningún elemento.</p>
                            <?php endif ?>
                            <?= $pager->links(); ?>
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
            if (localStorage.getItem('filters_general')) {
                const data              = JSON.parse(localStorage.getItem('filters_general'));
                this.userId             = data.userId;
                this.dateStart          = data.dataStart;
                this.dateEnd            = data.dataEnd;
                this.customerId         = data.customerId;
                this.statusInvoice      = data.statusInvoice;
                this.accountId          = data.accountId;
                this.sellerId           = data.sellerId;
                this.typeDocumentId     = data.typeDocumentId;
                this.statusWallet       = data.statusWallet

            }
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
                const data = {
                    dataStart: this.dateStart,
                    dataEnd: this.dateEnd,
                    customerId: this.customerId,
                    statusInvoice: this.statusInvoice,
                    statusWallet: this.statusWallet,
                    accountId: this.accountId,
                    typeDocumentId: this.typeDocumentId,
                    sellerId: this.sellerId,
                    userId: this.userId
                }
                localStorage.setItem('filters_general', JSON.stringify(data));
                axios.post(`${localStorage.getItem('url')}/report_general`, data).then((response) => {
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
            seller() {
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
            },
            reset() {
                if (localStorage.getItem('filters_general')) {
                    localStorage.removeItem('filters_general');
                    const url = localStorage.getItem('url');
                    location.href = url + '/report_general/reset'
                }

            }
        }

    });
    $(document).ready(function() {
        $('.collapsible-filter').collapsible({
            accordion: false
        })
    });

</script>


</body>
</html>