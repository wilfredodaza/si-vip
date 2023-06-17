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
                                                    <div class="collapsible-header"><i class="material-icons">search</i>Filtrar Reporte Helisa</div>
                                                    <div class="collapsible-body ">
                                                        <div class="row">
                                                            <div class="col  s12 m6 input-field">
                                                                <input type="date" name="date_start" id="date_start"
                                                                       v-model="dateStart">
                                                                <label for="date_start">Fecha de inicio</label>
                                                            </div>
                                                            <div class="col s12 m6 input-field">
                                                                <input type="date" name="date_end" id="date_end" v-model="dateEnd">
                                                                <label for="date_end">Fecha fin</label>
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
                                         <a href="<?= base_url() . '/report/helisa/customers/download' ?>"
                                            class="btn modals-trigger btn-small grey lighten-5 grey-text text-darken-4 pull-right"
                                            style="margin-top: 10px;"
                                            data-toggle="modalss">
                                            Descargas
                                            <i class="material-icons right">cloud_download</i>
                                        </a>
                                    </span>
                                    <div clas="divider"></div>
                                    <div class="table-response" style="overflow-x:auto;">

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
                this.dateStart          = data.dataStart;
                this.dateEnd            = data.dataEnd;
                this.typeDocumentId     = data.typeDocumentId;
            }
        },
        data: {
            dateStart: '',
            dateEnd: '',
            customerId: '',
            url: '',
            typeDocumentId: [],
        },
        methods: {
            send() {
                const data = {
                    dataStart: this.dateStart,
                    dataEnd: this.dateEnd,
                    typeDocumentId: this.typeDocumentId
                }
                localStorage.setItem('filters_general', JSON.stringify(data));
                axios.post(`${localStorage.getItem('url')}/report/helisa/customer`, data).then((response) => {
                    console.log(response.data)
                });
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