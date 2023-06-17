<?= view('layouts/header') ?>
<?= view('layouts/navbar_horizontal') ?>
<?= view('layouts/navbar_vertical') ?>
<!-- vista -->

<!-- BEGIN: Page Main-->
<div id="main">
    <div class="row">
        <div class="col s12">
            <div class="container">
                <div class="section">
                    <div class="card">
                        <form action="<?= base_url('import/invoice') ?>" method="post" enctype="multipart/form-data">
                            <div class="card-content">
                                <div class="row">
                                    <div class="col s12 m12">
                                        <div class="file-field input-field">
                                            <div class="btn">
                                                <span>Archivo</span>
                                                <input type="file" name="file">
                                            </div>
                                            <div class="file-path-wrapper">
                                                <input class="file-path validate"  type="text">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col s12 m12">
                                        <button type="submit" class="waves-effect waves-light btn green float-right">
                                            <i class="material-icons right">cloud</i>
                                            Cargar Excel
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<!--<div style="bottom: 50px; right: 19px;" class="fixed-action-btn direction-top"><a class="btn-floating btn-large gradient-45deg-light-blue-cyan gradient-shadow"><i class="material-icons">add</i></a>
        <ul>
            <li><a href="https://api.whatsapp.com/send?phone=+573013207088&text=Hola, tengo una duda en mifacturalegal." target="_blank" class="btn-floating light-green darken-3"><i class="fa fa-whatsapp"></i></a></li>
        </ul>
    </div>-->
<!-- fin vista-->
<?= view('layouts/footer') ?>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script>
    var i = 0;
    var l = 0;
    $('.dropify').dropify();
    $('#ayuda').css('display','none');
    $('#plantilla').css('display','none');

    $(document).ready(function(){
        $("#tipoD").change(function(){
            $('#ayuda').css('display','block');
            $('#plantilla').css('display','block');
            var url ='<?= base_url()?>';
            var documento = $('#tipoD').val();
            if(documento == 1){
                $('#ayuda').attr('href',url+'/upload/Productos.pdf');
                $('#ayuda').attr('download','Productos.pdf');
                $('#plantilla').attr('href',url+'/upload/Productos.xlsx');
                $('#plantilla').attr('download','Productos.xlsx');
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
                   /* var interval = setInterval(function(){
                        invoice[l].sendmail = false;
                        axios.get('https://facturadorv2.mifacturalegal.com/invoice/send/117/'+invoice[l].invoice_id, invoice[l], { headers :{
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
                    }, 8000);*/
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

        $('#upload-email').click(function() {
            axios.get('https://facturadorv2.mifacturalegal.com/api/v1/email_invoices',  { headers :{
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Access-Control-Allow-Origin': '*'
            }}).then(async function (response)  {
                console.log(response.data.data.length);
                const invoice = response.data.data;

                    var interval   = setInterval(function(){
                    for(let data of invoice) {
                      axios.get('https://facturadorv2.mifacturalegal.com/invoice/email/'+data.companies_id+'/'+data.id, { headers :{
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'Access-Control-Allow-Origin': '*'
                        }}).then(function (response) {
                            console.log(response);
                        })
                    }
                    }, 20000);

                

            })
        });
    });
</script>

