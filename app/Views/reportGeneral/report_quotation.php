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
                                        <ul class="collapsible collapsible-filter" style="box-shadow: none;">
                                            <li class="active">
                                                <div class="collapsible-header"><i class="material-icons">search</i>Filtrar Cotizaciones</div>
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
                                                <select class="select browser-default" name="customer_id"
                                                        id="customer_id" v-model="customerId">
                                                    <option value="" disabled>Seleccione ...</option>
                                                    <option v-for="item of customers" v-bind:value="item._id">
                                                        {{ item.name }}
                                                    </option>
                                                </select>
                                                <label for="customer_id" class="active">Cliente</label>
                                            </div>
                                        </div>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="collapsible-header"><i class="material-icons">search</i>Busqueda Avanzada</div>
                                                <div class="collapsible-body">
                                                    <div class="row">
                                                        <div class="col s12 m4 input-field">
                                                            <select multiple name="status_wallet" v-model="statusInvoice">
                                                                <option value="" disabled>Seleccione ...</option>
                                                                <option value="5">Abierta</option>
                                                                <option value="6">Cerrada</option>
                                                            </select>
                                                            <label for="status_wallet">Estado de Cotizacion</label>
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

                                        <div class="row">
                                            <div class="col s12">
                                                <div class="col s12 input-field">
                                                    <button class="waves-effect waves-green btn indigo pull-right">
                                                        Consulta
                                                    </button>
                                                    <button class="waves-effect waves-red red btn pull-right"
                                                            @click="reset()" style="margin-right: 10px;">Limpiar
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
                                        <a href="<?= base_url() . '/report_quotation/excel' ?>"
                                           class="btn modals-trigger btn-small grey lighten-5 grey-text text-darken-4 pull-right"
                                           style="margin-top: 10px;"
                                           data-toggle="modalss">
                                            Descargas
                                            <i class="material-icons right">cloud_download</i>
                                        </a>

                                       Reporte general de cotizaciones<br>
                                    </span>
                                    <small style="margin-bottom: 10px; display: block;">En este Reporte se detallan las cotizaciones de forma general.</small>
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
                                                <td style="text-align: center;">Número de documento</td>
                                                <td style="text-align: center;">Fecha</td>
                                                <td style="text-align: center;">Usuario</td>
                                                <td style="text-align: center;">Valor cotizado antes de IVA</td>
                                                <td style="text-align: center;">IVA ($)</td>
                                                <td style="text-align: center;">Retención en la fuente</td>
                                                <td style="text-align: center;">Retención de ICA</td>
                                                <td style="text-align: center;">Retención de IVA</td>
                                                <td style="text-align: center;">Total Cotizado</td>
                                                <td style="text-align: center;">Estado de cotización</td>
                                                <td style="text-align: center;">Fecha de cierre</td>
                                                <td style="text-align: center;">Cotización facturada</td>
                                                <td style="text-align: center;">Cantidad de facturas hechas en base a esta cotización.</td>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            $i = 1;
                                            foreach ($invoices as $item):
                                            $taxes = taxes($item->id,100);
                                            $wallets = wallets($item->id);
                                            ?>
                                            <tr>
                                                <td style="text-align: center;"><?= $i++ ?></td>
                                                <td style="text-align: center;"><?= $item->customer_name ?></td>
                                                <td style="text-align: center;"><?= $item->type_document_identification ?></td>
                                                <td style="text-align: center;"><?= $item->identification_number ?></td>
                                                <td style="text-align: center;"><?= $item->type_documents ?></td>
                                                <td style="text-align: center;"><?= $item->resolution ?></td>
                                                <td style="text-align: center;"><?= $item->created_at ?></td>
                                                <td style="text-align: center;"><?= userName($item->user_id) ?></td>
                                                <td style="text-align: center;"><?= $item->line_extesion_amount ?></td>
                                                <td style="text-align: center;"><?= $taxes['iva'] ?></td>
                                                <td style="text-align: center;"><?= $taxes['reteFuente'] ?></td>
                                                <td style="text-align: center;"><?= $taxes['reteICA'] ?></td>
                                                <td style="text-align: center;"><?= $taxes['reteIVA'] ?></td>
                                                <td style="text-align: center;"><?= $item->payable_amount - ($taxes['reteFuente'] + $taxes['reteIVA'] + $taxes['reteICA']) ?></td>
                                                <td style="text-align: center;"><?= $item->invoice_status ?></td>
                                                <td style="text-align: center;"><?= is_null($item->issue_date) ? '' : $item->issue_date  ?></td>
                                                <td style="text-align: center;"><?= (count($taxes['quotation']) > 0 ? 'Si' : 'No')  ?></td>
                                                <td style="text-align: center;"><?= (count($taxes['quotation']) > 0 ? $taxes['quotation'][0]->quantity : '0') ?></td>
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
            if (localStorage.getItem('filters_quotation')) {
                const data          = JSON.parse(localStorage.getItem('filters_quotation'));
                this.userId         = data.userId;
                this.dateStart      = data.dataStart;
                this.dateEnd        = data.dataEnd;
                this.customerId     = data.customerId;
                this.statusInvoice  = data.statusInvoice;
                this.accountId      = data.accountId;
                this.sellerId       = data.sellerId;
                this.typeDocumentId = data.typeDocumentId;
                this.statusWallet   = data.statusWallet;
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
                localStorage.setItem('filters_quotation', JSON.stringify(data));
                axios.post(`${localStorage.getItem('url')}/report_quotation`, data).then((response) => {
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
                if (localStorage.getItem('filters_quotation')) {
                    localStorage.removeItem('filters_quotation');
                    const url = localStorage.getItem('url');
                    location.href = url + '/report_quotation/reset'
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