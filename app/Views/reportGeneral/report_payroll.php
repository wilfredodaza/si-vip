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
                                                <div class="collapsible-header"><i class="material-icons">search</i>Filtrar Nómina Electrónica</div>
                                                <div class="collapsible-body ">
                                                    <div class="row">
                                                        <div class="col s12 m4 input-field">
                                                            <input type="date" name="date_start" id="date_start"
                                                                   v-model="dateStart">
                                                            <label for="date_start">Fecha de inicio</label>
                                                        </div>
                                                        <div class="col s12 m4 input-field">
                                                            <select name="typeDocumentID" id="typeDocumentID" v-model="typeDocumentID">
                                                                <option value="">Selecciona una opcion</option>
                                                                <option value="9">Nomina Electronica</option>
                                                                <option value="10">Nomina Electronica de Ajuste</option>
                                                            </select>
                                                            <label for="typeDocumentID">Tipo de documento</label>
                                                        </div>
                                                        <div class="col s12 m4 input-field">
                                                            <input type="date" name="date_end" id="date_end" v-model="dateEnd">
                                                            <label for="date_end">Fecha fin</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>

                                    <div class="col s12">
                                        <div class="row">
                                            <div class="col s12">
                                                <div class="col s12 input-field">
                                                    <a href="<?= base_url() . '/report_payroll/excel' ?>"
                                                       class="btn modals-trigger  light-green darken-2  pull-right" >
                                                        Descargas
                                                        <i class="material-icons right" >cloud_download</i>
                                                    </a>
                                                    <button class="waves-effect waves-green btn  pull-right purple" style="margin-right: 10px;">
                                                        Consulta
                                                    </button>
                                                    <button class="waves-effect waves-red grey lighten-1 btn pull-right"
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

                                    </span>
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
            this.url = localStorage.getItem('url');
            if (localStorage.getItem('filters_payroll')) {
                const data              = JSON.parse(localStorage.getItem('filters_payroll'));
                this.dateStart          = data.dataStart;
                this.dateEnd            = data.dataEnd;
            }else{
                const date = new Date();
                const dateInit  = new Date(date.getFullYear(), date.getMonth(), 1);
                const dateEnd   = new Date(date.getFullYear(), date.getMonth() + 1, 0);
                this.dateStart = `${dateInit.getFullYear() + '-' + (dateInit.getMonth() + 1 < 10 ? '0' + (dateInit.getMonth() + 1): dateInit.getMonth() + 1)}-01`;
                this.dateEnd =  `${dateEnd.getFullYear() + '-' + (dateEnd.getMonth() + 1 < 10 ? '0' + (dateEnd.getMonth() + 1): dateEnd.getMonth() + 1)}-${dateEnd.getDate()}`;
            }

        },
        data: {
            dateStart: '',
            dateEnd: '',
            typeDocumentID: ''
        },
        methods: {
            send() {
                const data = {
                    dataStart: this.dateStart,
                    dataEnd: this.dateEnd,
                    typeDocumentId: this.typeDocumentID
                }
                localStorage.setItem('filters_payroll', JSON.stringify(data));
                axios.post(`${localStorage.getItem('url')}/report_payroll`, data).then((response) => {
                    console.log(response);
                });
            },
            reset() {
                if (localStorage.getItem('filters_payroll')) {
                    localStorage.removeItem('filters_payroll');
                    const url = localStorage.getItem('url');
                    location.href = url + '/report_payroll/reset'
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