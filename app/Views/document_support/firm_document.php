<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>/assets/css/dropzone.css">
    <link rel="stylesheet" href="<?= base_url() ?>/assets/css/style.css">
    <style>
    .dropzone{
        border: #a53394 dashed 2px;
        height: 160px !important;
        min-height:160px !important;
        padding: 3px 20px;
    }
    .dropzone .dz-message {
        margin-top: 0px;
        margin-bottom: 0px;
    }
    .row{
        margin: 0px !important;
    }

    .margin-top{
        margin-top: 10px !important;
    }

</style>
</head>
<body>
<nav>
    <div class="nav-wrapper gradient-45deg-purple-deep-orange">
    <a href="https://mifacturalegal.com/" class="brand-logo left">
    <img src="<?= base_url('assets/img/logo-mifacturalegal.png') ?>" alt="" style="display:block; width:60px; heigth:70px;margin-top:5px;"></a>
      <ul id="nav-mobile" class="right hide-on-med-and-down">

        <li><a href="https://mifacturalegal.com/">MiFacturaLegal.com</a></li>

      </ul>
    </div>
  </nav>
        
<div id="main">
        <div class="row">
        <div class="col s12">
       
            <div class="">
                
                <div class="section">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-title">
                                <a href="<?= base_url('document_support/create_pdf/'.$invoice->id) ?>" class="btn right indigo btn-small">Ver documento Soporte</a>
                                <strong>Documento soporte</strong> <br>
      
                            </div>
                            <p style="font-weight:400;color:gray;">Para la validación y firma del documento soporte deberás verificar y/o actualizar la siguiente información:</p>
                       
                            <form action="<?= base_url('document_support/update_provider/'. $customer->id.'/'.$uuid) ?>" method="POST" class="margin-top">
                                <div class="row">
                                    <div class="input-field col s12 m4">
                                        <input name="name" value="<?= $customer->name ?>" placeholder="Nombre" id="name" type="text" class="validate">
                                        <label for="name">Nombre</label>
                                    </div>
                                    <div class="col s12 m4">
                                        <label for="type_document_identifications_id" class="active">Tipo de documento</label>
                                        <select class="select2 browser-default" name="type_document_identifications_id" value="<?=$customer->type_document_identifications_id?>" id="type_document_identifications_id">
                                            <option value="" disabled selected>Elige tu opción</option>
                                            <?php foreach($typeDocumentIdentification as $item): ?>
                                                <option value="<?= $item->id  ?>" 
                                                    <?= $item->id == $customer->type_document_identifications_id ? 'selected': '' ?>>
                                                    <?=  $item->code ?> - <?= $item->name  ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                      
                                    </div>
                                    <div class="input-field col s12 m4">
                                        <input name="identification_number" placeholder="Numero de documento" id="name" type="number" class="validate" value="<?=$customer->identification_number ?>">
                                        <label for="identification_number">Número de documento</label>
                                    </div>
                                    
                                </div>
                                <div class="row">
                                    <div class="input-field col s12 m4">
                                        <input placeholder="Teléfono" id="phone" name="phone" value="<?=$customer->phone ?>" type="number" class="validate">
                                        <label for="phone">Teléfono</label>
                                    </div>
                                   
                                    <div class="input-field col s12 m4">
                                        <input placeholder="Correo electrónico" id="email" name="email" type="text" class="validate" value="<?=$customer->email ?>">
                                        <label for="email">Correo electrónico</label>
                                    </div>
                                    <div class="input-field col s12 m4">
                                        <input placeholder="Dirección" name="address" id="address" type="text" class="validate" value="<?=$customer->address ?>">
                                        <label for="address">Dirección</label>
                                    </div>
                                    
                                </div>
                              
                                <div class="row">
                                  

                                    <div class="col col s12 m4">
                                        <label class="active">Ciudad</label>
                                        <select name="municipality_id" class="select2 browser-default">
                                            <?php foreach($municipalities as $item): ?>
                                                <option value="<?= $item->id ?>" <?= $item->id == $customer->municipality_id ? 'selected': '' ?>><?= $item->code ?> - <?= $item->name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col col s12 m4">
                                        <label class="active">Tipo de régimen </label>
                                        <select class="select2 browser-default" name="type_regime_id" >
                                            <option value="" disabled selected>Elige tu opción</option>
                                            <?php foreach($typeRegimes as $item): ?>
                                                <option value="<?= $item->id ?>"
                                                <?= $item->id == $customer->type_regime_id ? 'selected': '' ?>
                                                
                                                ><?= $item->code ?> - <?= $item->name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col col s12 m4">
                                        <label class="active">Tipo de organización</label>
                                        <select class="select2 browser-default" name="type_organization_id">
                                            <option value="" disabled selected>Elige tu opción</option>
                                            <?php foreach($typeOrganizations as $item): ?>
                                                <option value="<?= $item->id ?>" 
                                                <?= $item->id == $customer->type_organization_id ? 'selected': '' ?>
                                                ><?= $item->code ?> - <?= $item->name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="row margin-top">
                                    <div class="col s12 ">
                                            <button class="btn indigo right" id="update-customer" data-alert="<?= session('update') ?>">Actualizar</button>
                                    </div>
                                </div>
                            </form>

                
                            <div class="card-title margin-top">
                                <strong>Cargar Documentos</strong>
                            </div>
                            <p>En los siguientes recuadros podrás adjuntar los documentos requeridos:</p>
                            <div class="row margin-top">
                                
                                <div class="col s12 m4">
                                    <label for="">Certificación Bancaria 
                                        <?php if($customer->bank_certificate): ?> 
                                            <a href="<?= base_url('document_support/delete/bank_certificate/'.$customer->id) ?>"><span data-badge-caption="Eliminar" id="bank_certificate_button" class="new badge indigo"></span></a>
                                        <?php endif; ?>
                                    </label>
                                    <form action="<?=  base_url('/customer/bank_certificate/'.$customer->id) ?>" class="dropzone dropzone_file" id="bank_certificate"  method="post" data-info="<?= $customer->bank_certificate ?>" >
                                    </form>
                                    <?php if($customer->bank_certificate): ?> 
                                        <p class="center" style="border: 1px dashed #a53394; padding:10px 20px;" id="bank_certificate_download">
                                            <a class="btn purple" href="<?= base_url('upload/bank_certificate/'.$customer->bank_certificate) ?>" >
                                                Descargar
                                            </a>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <div class="col s12 m4">
                                        <label for="">RUT 
                                        <?php if($customer->rut): ?> 
                                            <a href="<?= base_url('document_support/delete/rut/'.$customer->id) ?>"><span id="rut_button" data-badge-caption="Eliminar" class="new badge indigo"></span></a>
                                        <?php endif; ?>
                                        </label>
                                        <form action="<?=  base_url('/customer/rut/'.$customer->id) ?>" class="dropzone dropzone_file" id="RUT"  method="post" data-info="<?= $customer->rut ?>">
                                        </form>
                                        <?php if($customer->rut): ?> 
                                        <p class="center" style="border: 1px dashed #a53394; padding:10px 20px;" id="rut_download">
                                            <a class="btn btn-light-indigo purple" href="<?= base_url('/upload/rut/'.$customer->rut) ?>" >
                                            Descargar 
                                            </a>
                                        </p>
                                        <?php endif; ?>
                                </div>
                                <div class="col s12 m4">
                                    <label for="">Firma 
                                        <?php if($customer->firm): ?> 
                                            <a href="<?= base_url('document_support/delete/firm/'.$customer->id) ?>"><span data-badge-caption="Eliminar"  id="firm_button" class="new badge indigo"></span></a>
                                        <?php endif; ?>
                                    </label>
                                    <form action="<?=  base_url('/customer/firm/'.$customer->id) ?>" class="dropzone dropzone_file" id="firm"  method="post" data-info="<?= $customer->firm ?>">
                                    </form>
                                    <?php if($customer->firm): ?> 
                                        <p class="center" id="firm_download" style="border: 1px dashed #a53394; padding:10px 20px;">
                                            <a class="btn  purple" href="<?= base_url('/upload/firm/'.$customer->firm) ?>"  target="_blank">
                                                Descargar
                                            </a>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="row margin-top">
                                <div class="col s12 m6 ">
                                    <label for="">Otros soportes necesarios (Ej. Pago a seguridad social, Orden de Compra, entre otros)</label>
                                    <form action="<?=  base_url('/customer/attached_document/'.$invoice->id) ?>" class="dropzone dropzone_file" id="document_supports"  method="post" data-info="<?= $customer->firm ?>">
                                    </form>
                                </div>
                                <div class="col s12 m6 center">
                                        <?php if(count($invoiceDocumentUploads) == 0): ?>
                                            <small class=" red-text" style="padding:20px;display:block;">No hay ninguno documento anexo.</small>
                                        <?php else: ?>
                                            <label for="">Documentos adjuntos</label>
                                            <?php foreach($invoiceDocumentUploads as $item): ?>
                                                <p><small><?= $item->title ?> <a href="<?= base_url('customer/attached_document/delete/'.$item->id.'/'.$uuid) ?>"> Eliminar</a></small></p>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                </div>
                            
                            </div>
                            <div class="row margin-top">
                                <div class="col s12 center">
                                    <a href="<?= base_url('document_support/firm/'.$uuid) ?>" class="btn" id="alert" data-alert="<?= session('success') ?>" data-errors="<?= session('errors') ?>">Firmar Documento Soporte</a>
                                </div>
                            </div> 
                               
                        </div>
                    </div>
                </div>
             </div>
        </div>
    </div>

    <br>

    <footer class="page-footer footer purple footer-static footer-light navbar-border navbar-shadow" style="position:fixed; bottom: 0px; width: 100%;padding-top:0px;">
        <div class="footer-copyright">
            <div><span><?= isset(configInfo()['footer']) ? configInfo()['footer'] : '' ?></span></div>
        </div>
    </footer>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="<?= base_url('/assets/js/sweetalert2@10.js') ?>"></script>
    <script src="<?= base_url('/assets/js/polyfill.js') ?>"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script src="<?= base_url() ?>/assets/js/dropzone.js"></script>
    <script>
            $('.card-alert.green').hide();
            
            
            var dataFirma = $('#firm').data('info');
            if(!dataFirma == '') {
                $('#firm').hide();
            }
            var dataRUT = $('#RUT').data('info');
            if(!dataRUT == '') {
                $('#RUT').hide();
            }

            var dataBankCertificate = $('#bank_certificate').data('info');
            if(!dataBankCertificate == '') {
                $('#bank_certificate').hide();
            }

     

           


   
        if($('#alert').data('alert') !== '') {
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'El Documento Soporte ha sido firmado.',
                text: $('#alert').data('alert'),
                showConfirmButton: true,
                confirmButtonText: `Cerrar`,
            }).then((result) => {
                if (result.isConfirmed) {
                    location.href = "https://mifacturalegal.com/";
                } 
            });
        }
	if($('#update-customer').data('alert') !== '') {
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: 'Los datos fueron actualizados.',
                text: $('#update-customer').data('alert'),
                showConfirmButton: true,
                confirmButtonText: `Cerrar`,
            })
        }

        if($('#alert').data('errors') !== '') {
            Swal.fire({
                position: 'center',
                icon: 'error',
                html: $('#alert').data('errors'),
                showConfirmButton: true,
                confirmButtonText: `Cerrar`,
            })
        }
   
    

        $('#bank_certificate').dropzone({
            url: null,
            addRemoveLinks: true,
            enqueueForUpload: false,
            maxFilesize: 2,
            acceptedFiles: 'application/pdf',
            dictFileTooBig: 'El tamaño del documento debe ser menor a  2MB',
            uploadMultiple: false,
            dictRemoveFile:"Quitar Archivo",
            dictInvalidFileType: 'El formato del archivo no es válido solo se admiten PDF',
            dictDefaultMessage: `
                <h5 style="color: #e0e0e0;">Certificación Bancaria actualizada no mayor a 30 días.</h5>
                <small style="color: #a53394;">Por favor arrastre el documento o de clic aquí</small>`,
                init: function() {
                var myDropzone = this; // closure
                this.on("success", function (file){
                location.reload();
            });
            }
        });

        $('#document_supports').dropzone({
            url: null,
            addRemoveLinks: true,
            enqueueForUpload: false,
            uploadMultiple: true,
            acceptedFiles: 'application/pdf',
            maxFilesize: 2,
            dictFileTooBig: 'El tamaño del documento debe ser menor a  2MB',
            dictRemoveFile:"Quitar Archivo",
            dictInvalidFileType: 'El formato del archivo no es válido solo se admiten PDF',
            dictDefaultMessage: `
                <h5 style="color: #e0e0e0;">Adjuntar Documento</h5>
                <small style="color: #a53394;">Por favor arrastre el documento o de clic aquí</small>`,
                init: function() {
                var myDropzone = this; // closure
                this.on("success", function (file){
                location.reload();
            });
            }
        });


       


        $('#RUT').dropzone({
            url: null,
            addRemoveLinks: true,
            enqueueForUpload: false,
            maxFilesize: 2,
            acceptedFiles: 'application/pdf',
            uploadMultiple: false,
            dictRemoveFile:"Quitar Archivo",
            dictFileTooBig: 'El tamaño del documento debe ser menor a 2MB',
            dictInvalidFileType: 'El formato del archivo no es válido solo se admiten PDF',
            dictDefaultMessage: `
                <h5 style="color: #e0e0e0;">RUT actualizado</h5>
                <small style="color: #a53394;">Por favor arrastre el documento o de clic aquí</small>`,
                init: function() {
                var myDropzone = this; // closure
                this.on("success", function (file){
                location.reload();
            });
            }
        })

        $('#firm').dropzone({
            url: null,
            addRemoveLinks: true,
            enqueueForUpload: false,
            maxFilesize: 2,
            acceptedFiles: 'image/jpeg,image/png,image/gif',
            uploadMultiple: false,
            dictRemoveFile:"Quitar Archivo",
            dictFileTooBig: 'El tamaño de la imagen debe ser menor a  2MB',
            dictInvalidFileType: 'El formato del archivo no es válido solo se admiten jpg, png jpeg',
            dictDefaultMessage: `
                <h5 style="color: #e0e0e0;">Firma</h5>
                <small style="color: #a53394;">Por favor arrastre el documento o de clic aquí</small>`,
            init: function() {
                var myDropzone = this; // closure
                this.on("success", function (file){
                location.reload();
            });
            }
        })
    </script>

</body>
</html>