<?= view('layouts/header') ?>
<?= view('layouts/navbar_horizontal') ?>
<?= view('layouts/navbar_vertical') ?>
<!-- vista -->
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
        <div class="breadcrumbs-inline pt-3 pb-1" id="breadcrumbs-wrapper">
                <div class="container">
                    <div class="row">
                        <div class="col s12">
                            <?= $this->include('layouts/alerts') ?>
                            <?= $this->include('layouts/notification') ?>
                        </div>
                        <div class="col s10 m6 l6 breadcrumbs-left">
                            <h5 class="breadcrumbs-title mt-0 mb-0 display-inline hide-on-small-and-down ">
                                <span>Importar Archivo</span>
                            </h5>
                            <ol class="breadcrumbs mb-0">
                                <li class="breadcrumb-item"><a href="<?= base_url() ?>/home">Home</a></li>
                                <li class="breadcrumb-item active"><a href="#">Importar Archivo</a></li>
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
                            <h5 class="card-title">Importar Archivo</h5>
                           
                            <form action="<?= base_url() ?>/import/upload" method="post" enctype="multipart/form-data">
                            <div class="row mt-2">
                                <div class="input-field col m6 s12">
                                    <select name="tipoD" id="tipoD">
                                        <option value="" disabled selected>Seleccione ...</option>
                                        <option value="1">Productos</option>
                                        <option value="2">Clientes</option>
                                        <option value="3">Cuentas Contables</option>
                                        <option value="5">Inventario</option>
                                        <!-- <option value="6">Nomina Electronica</option> -->
                                        <option value="7">Proveedor</option>
                                    </select>
                                    <label>Archivo a cargar</label>
                                </div>
                                <div class="col s12 m6">
                                    <div class="file-field input-field">
                                        <div class="btn indigo">
                                            <span>Archivo</span>
                                            <input type="file" name="file">
                                        </div>
                                        <div class="file-path-wrapper">
                                            <input class="file-path validate"  type="text">
                                         </div>
                                    </div>
                                </div>
                                <div class="input-field col m6 s12"  id="resolution">
                                    <select name="resolution">
                                        <?php foreach($resolutions as $resolution): ?>
                                            <option value="<?= $resolution->resolution ?>"><?= $resolution->prefix.''.$resolution->resolution.'-('.$resolution->from.'-'.$resolution->to.')'?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label>Resolución</label>
                                </div>
                                <div class="input-field col m6 s12"  id="sede">
                                    <select name="sede">
                                        <?php foreach($sedes as $sede): ?>
                                            <option value="<?= $sede->id ?>"><?= $sede->company?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label>Sedes</label>
                                </div>

                                <div class="row" id="payroll">
                                    <div class="col col m3 s12">
                                        <label>Periodo</label>
                                        <select name="period_id" class="browser-default">
                                            <option value="" disabled selected>Elige tu opción</option>
                                            <?php  foreach($periods as $period): ?>
                                                <option value="<?= $period->id ?>"><?= $period->month.' - '.$period->year ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col m3 s12">
                                        <label>Mes</label>
                                        <select name="period" class="browser-default">
                                            <option value="" disabled selected>Elige tu opción</option>
                                            <?php  foreach( $payrollPeriods as $payrollPeriod): ?>
                                                <option value="<?= $payrollPeriod->name ?>"><?= $payrollPeriod->name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="input-field col m3 s12">
                                        <input value="date_start" name="date_start" placeholder="Placeholder" id="first_name" type="date" class="validate">
                                        <label for="first_name">Fecha inicio de liquidación</label>
                                    </div>
                                    <div class="input-field col m3 s12">
                                        <input value="date_end" name="date_end"  placeholder="Placeholder" id="first_name" type="date" class="validate">
                                        <label for="first_name">Fecha fin de liquidación</label>
                                    </div>
                                </div>
                                <div class="col s12 m12">
                                    <a id="ayuda" class="waves-effect waves-light indigo btn float-left" href=""download="" target="_blank">
                                        <i class="material-icons  right">help</i>Ayuda
                                    </a>
                                    <a id="plantilla" class="waves-effect green waves-light btn float-left ml-1" href="" download=""target="_blank">
                                        <i class="material-icons right">description</i>Plantilla
                                    </a>
                                    <button type="submit" class="waves-effect waves-light btn-small purple float-right">
                                        <i class="material-icons right">cloud</i>Cargar
                                    </button>
                                </div>
            
                                   <!-- <button class="btn" type="button" id="upload">Enviar Facturas  a DIAN</button>-->
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('layouts/footer') ?>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script>
    var i = 0;
    var l = 0;
    $('.dropify').dropify();
    $('#ayuda').css('display','none');
    $('#plantilla').css('display','none');

    $(document).ready(function(){
        $('#resolution').hide();
        $('#payroll').hide();
        $('#sede').hide();
        $("#tipoD").change(function(){
            $('#resolution').hide();
            $('#ayuda').css('display','block');
            $('#plantilla').css('display','block');
            var url ='<?= base_url()?>';
            var documento = $('#tipoD').val();
            if(documento == 1){
                $('#ayuda').attr('href',url+'/upload/Productos.pdf');
                $('#ayuda').attr('download','Productos.pdf');
                $('#plantilla').attr('href',url+'/upload/ProductosPlantilla.xlsx');
                //$('#plantilla').attr('href','<?= base_url("plantillaProductos")?>');
                $('#plantilla').attr('download','ProductosPlantilla.xlsx');
            }
            if(documento == 2){
                $('#ayuda').attr('href',url+'/upload/Clientes.pdf');
                $('#ayuda').attr('download','Clientes.pdf');
                $('#plantilla').attr('href',url+'/upload/Clientes.xlsx');
                $('#plantilla').attr('download','Clientes.xlsx');
            }
            if(documento == 3){
                $('#ayuda').attr('href',url+'/upload/Cuentas Contables.pdf');
                $('#ayuda').attr('download','Cuentas Contables.pdf');
                $('#plantilla').attr('href',url+'/upload/Ccontable.xlsx');
                $('#plantilla').attr('download','Cuentas Contables.xlsx');
            }

            if(documento == 5){
                //$('#resolution').show();
                $('#ayuda').attr('href',url+'/upload/Facturas.pdf');
                $('#ayuda').attr('download','Facturas.pdf');
                $('#plantilla').attr('href',url+'/upload/Inventario.xlsx');
                $('#plantilla').attr('download','Inventario.xlsx');
                $('#sede').show();
            }
            if(documento == 6){
                $('#payroll').show();
            }
        });


        $('#upload').click(function() {
            axios.get('https://facturadorv2.mifacturalegal.com/api/v1/invoices/all',  { headers :{
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    "Access-Control-Allow-Origin": "*"
                }})
                .then(function (response) {
                    // handle success
                    const invoice = response.data.data;
                    console.log(invoice);
                    var interval = setInterval(function(){
                        invoice[l].sendmail = false;
                        axios.get('https://facturadorv2.mifacturalegal.com/invoice/send/77/'+invoice[l].invoice_id, invoice[l], { headers :{
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                "Access-Control-Allow-Origin": "*"
                            }}).then(function (response) {

                            if(i == 900){
                               // clearInterval(interval);
                            }
                            l++;
                            i++;
                        })
                    }, 8000);
                    //$('#pruebas').append(i + response.data.ResponseDian.Envelope.Body.SendBillSyncResponse.SendBillSyncResult.StatusMessage + ' <br>');
                })
                .catch(function (error) {
                    // handle error
                    console.log(error);
                })
                .then(function () {
                    // always executed
                });
        });
    });
</script>

