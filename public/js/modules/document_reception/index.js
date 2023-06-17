Dropzone.autoDiscover = false;
$(document).ready(function () {
    $('.tooltipped').tooltip();

    var tour = new Shepherd.Tour({
        defaultStepOptions: {
            cancelIcon: {
                enabled: false
            },
            classes: 'dark',
            scrollTo: { behavior: 'smooth', block: 'center' }
        }
    });

    function tourInit() {
        $('body').addClass('shepherd-active');
        tour.addStep({
            text: '¿Necesitas ayuda?',
            attachTo: {
                element: '.sept-1',
                on: 'top'
            },
            buttons: [
                {
                    action: function () {
                        localStorage.setItem('active', true);
                        $('body').removeClass('shepherd-active');
                        return this.cancel();
                    },
                    classes: 'btn btn-light-indigo',
                    text: 'No'
                },
                {
                    action: function () {
                        return this.next();
                    },
                    classes: 'btn indigo',
                    text: 'Si'
                }
            ],
            id: 'welcome'
        });
        tour.addStep({
            title: 'Cargar Archivo',
            text:  'En este botón podremos subir los archivos PDF, XML y ZIP.',
            attachTo: {
                element: '.sept-2',
                on: 'right'
            },
            buttons: [
                {
                    action: function () {
                        localStorage.setItem('active_tour_invoice', true);
                        $('body').removeClass('shepherd-active');
                        $('thead tr th:nth-child(8), tbody tr td:nth-child(8)').removeClass('active-red');
                        $('thead tr th:nth-child(3), tbody tr td:nth-child(3)').removeClass('active-red');
                        $('thead tr th:nth-child(5), tbody tr td:nth-child(5)').addClass('active-red');
                        return this.cancel();
                    },
                    classes: 'btn btn-light-indigo',
                    text: 'Terminar'
                },
                {
                    action: function () {
                        return this.back();
                    },
                    classes: 'btn btn-light-indigo',
                    text: 'Atrás'
                },
                {
                    action: function () {
                        $('thead tr th:nth-child(8), tbody tr td:nth-child(8)').addClass('active-red');
                        return this.next();
                    },
                    classes: 'btn indigo',
                    text: 'Siguiente'
                }
            ],
            id: 'welcome'
        });
        tour.addStep({
            title: 'Acciones',
            text:  `La tabla maneja tres acciones: 
                <table class="striped">
                    <tbody>
                        <tr>
                            <td>
                                <button class="btn yellow darken-2" style="padding:0px 10px;">
                                    <i class="large material-icons">file_upload</i>
                                </button>
                            </td>
                             <td>Subir factura.</td>
                        </tr>
                        <tr>
                        <td> <button class="btn green" style="padding:0px 10px;">
                                <i class="large material-icons">assignment</i>
                            </button>
                        </td>
                        <td>Asociar productos.</td>
                        </tr>
                        <tr>
                        <td> <button class="btn red" style="padding:0px 10px;">
                            <i class="large material-icons">delete</i>
                        </button></td>
                        <td>Eliminar factura.</td>
                        </tr>
                    </tbody>
                </table>
                `,
            attachTo: {
                element: '.sept-3',
                on: 'right'
            },
            buttons: [
                {
                    action: function () {
                        localStorage.setItem('active_tour_invoice', true);
                        $('body').removeClass('shepherd-active');
                        $('thead tr th:nth-child(8), tbody tr td:nth-child(8)').removeClass('active-red');
                        $('thead tr th:nth-child(3), tbody tr td:nth-child(3)').removeClass('active-red');
                        $('thead tr th:nth-child(5), tbody tr td:nth-child(5)').addClass('active-red');
                        return this.cancel();
                    },
                    classes: 'btn btn-light-indigo',
                    text: 'Terminar'
                },
                {
                    action: function () {
                        $('thead tr th:nth-child(8), tbody tr td:nth-child(8)').removeClass('active-red');
                        return this.back();
                    },
                    classes: 'btn btn-light-indigo',
                    text: 'Atrás'
                },
                {
                    action: function () {
                        $('thead tr th:nth-child(8), tbody tr td:nth-child(8)').removeClass('active-red');
                        $('thead tr th:nth-child(3), tbody tr td:nth-child(3)').addClass('active-red');
                        return this.next();
                    },
                    classes: 'btn indigo',
                    text: 'Siguiente'
                }
            ],
            id: 'welcome'
        });
        tour.addStep({
            title: 'Estado',
            text:  `La tabla maneja cuatro estados: 
                <table class="striped">
                   <tbody>
                      <tr>
                         <td>
                           <span class="new badge yellow darken-4 left" data-badge-caption="" style="padding:0px 10px; display: inline-block;">Cargado</span>
                        </td>
                        <td>El archivo se encuentra subido pendiente para realizar el <cargue class=""></cargue></td>
                    </tr>
                    <tr>
                    <td>
                     <span class="new badge left yellow darken-2" data-badge-caption="" style="padding:0px 10px; display: inline-block;">
                        Pendiente
                    </span>
                    </td>
                        <td>
                           El archivo está cargado en base de datos.
                        </td>
                    </tr>
                    <tr>
                    <td>
                     <span class="new badge left green"  data-badge-caption="" style="padding:0px 10px; display: inline-block;">
                       OK
                    </span>
                    </td>
                    <td>
                      El documento es válido ante la DIAN.
                    </td>
                    </tr>
                    <tr>
                    <td>
                    <span class="new badge left red" data-badge-caption="" style="padding:0px 10px; display: inline-block;">
                        Error
                    </span>
                    </td>
                    <td>
                     El documento es invalido ante la DIAN.
                    </td>
                    </tr>      
                </tbody>
                </table>
                `,
            attachTo: {
                element: '.sept-4',
                on: 'top'
            },
            buttons: [
                {
                    action: function () {
                        localStorage.setItem('active_tour_invoice', true);
                        $('body').removeClass('shepherd-active');
                        $('thead tr th:nth-child(8), tbody tr td:nth-child(8)').removeClass('active-red');
                        $('thead tr th:nth-child(3), tbody tr td:nth-child(3)').removeClass('active-red');
                        $('thead tr th:nth-child(5), tbody tr td:nth-child(5)').removeClass('active-red');
                        return this.cancel();
                    },
                    classes: 'btn btn-light-indigo',
                    text: 'Terminar'
                },
                {
                    action: function () {
                        $('thead tr th:nth-child(8), tbody tr td:nth-child(8)').addClass('active-red');
                        $('thead tr th:nth-child(3), tbody tr td:nth-child(3)').addClass('active-red');
                        return this.back();
                    },
                    classes: 'btn btn-light-indigo',
                    text: 'Atrás'
                },
                {
                    action: function () {
                        $('thead tr th:nth-child(5), tbody tr td:nth-child(5)').addClass('active-red');
                        return this.next();
                    },
                    classes: 'btn indigo',
                    text: 'Siguiente'
                }
            ]
        });
        tour.addStep({
            title: 'Estados de los item',
            text:  `La tabla maneja tres estados: 
                <table class="striped">
                  <tr>
                     <td>
                        <i class="material-icons small text-red red-text breadcrumbs-title" >brightness_1</i>
                    </td>
                    <td>
                        La factura presenta una inconsistencia.
                    </td>
                </tr>
                <tr>
                <td>
                 <i class="material-icons small text-yellow yellow-text darken-2 breadcrumbs-title" >brightness_1</i>
                </td>
                    <td>
                      Falta completar algunos datos.
                    </td>
                </tr>
                <tr>
                <td>
                <i class="material-icons small text-green green-text breadcrumbs-title" >brightness_1</i>
                </td>
                <td>
                    Cargado correctamente.
                </td>
                </tr>  
                </table>
                `,
            attachTo: {
                element: '.sept-5',
                on: 'top'
            },
            buttons: [

                {
                    action: function () {
                        $('thead tr th:nth-child(5), tbody tr td:nth-child(5)').removeClass('active-red');
                        $('thead tr th:nth-child(3), tbody tr td:nth-child(3)').addClass('active-red');
                        return this.back();
                    },
                    classes: 'btn btn-light-indigo',
                    text: 'Atrás'
                },
                {
                    action: function () {
                        localStorage.setItem('active', true);
                        $('thead tr th:nth-child(8), tbody tr td:nth-child(8)').removeClass('active-red');
                        $('thead tr th:nth-child(3), tbody tr td:nth-child(3)').removeClass('active-red');
                        $('thead tr th:nth-child(5), tbody tr td:nth-child(5)').removeClass('active-red');
                        $('body').removeClass('shepherd-active');
                        return this.cancel();
                    },
                    classes: 'btn indigo',
                    text: 'Terminar'
                }
            ],
        });
        tour.start();
    }

    if(!localStorage.getItem('active')) {
        $('body').removeClass('shepherd-active');
        $('thead tr th:nth-child(8), tbody tr td:nth-child(8)').removeClass('active-red');
        $('thead tr th:nth-child(3), tbody tr td:nth-child(3)').removeClass('active-red');
        $('thead tr th:nth-child(5), tbody tr td:nth-child(5)').removeClass('active-red');
        tourInit();
    }


    $('.help').click(function () {
        $('thead tr th:nth-child(8), tbody tr td:nth-child(8)').removeClass('active-red');
        $('thead tr th:nth-child(3), tbody tr td:nth-child(3)').removeClass('active-red');
        $('thead tr th:nth-child(5), tbody tr td:nth-child(5)').removeClass('active-red');
        localStorage.removeItem('active');
        $('body').addClass('shepherd-active');
        tourInit();
    });


    $(".btn-delete-document").click(function (e) {
        e.preventDefault();
        swal({
            title: "Eliminar",
            text: 'Desea eliminar el documento.',
            icon: "info",
            buttons: ["cancelar", " Aceptar"],
            className: "Custom_Cancel",
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $(this).parent('form').submit();
            }
        });
    });


    $('#my-dropzone').dropzone({
        url: null,
        autoProcessQueue: false,
        addRemoveLinks: true,
        enqueueForUpload: false,
        maxFilesize: 1,
        uploadMultiple: false,
        dictRemoveFile:"Quitar Archivo",
      //  acceptedMimeTypes: 'text/xml, application/x-zip-compressed, application/pdf application/zip, application/octet-stream, application/x-zip-compressed, multipart/x-zip',
        dictInvalidFileType: 'No puede cargar archivos de este tipo',
        dictDefaultMessage: `
            <h5 style="color: #e0e0e0;">SUBIR DOCUMENTOS</h5>
            <small style="color: #e0e0e0;">Por favor arrastre el documento o de clic aquí</small>`,
        init: function() {
            var myDropzone = this;
            $('#submit-all').click(function() {
                 myDropzone.processQueue();
                if(myDropzone.files.length !== 0){
                    $('.container-sprint.js-send').show();
                    $('.container-sprint.js-send').css('display', 'flex');
                }
            });

            this.on("success", function (file){
               location.reload();
            });
        }
    });

    $("#payment_upload_files").validate({
        rules: {
            description: {
                required: true,
                maxlength: 255
            },
            file_name: {
                required: true
            },
        },
        messages: {
            description: {
                required: 'El campo descripción es obligatorio.',
                maxlength: 'El campo descripción tiene un máximo de 255 caracteres.'
            },
            file_name: {
                required: 'El campo pago es obligatorio.',
            }
        },
        errorElement : 'div',
        errorPlacement: function (error, element) {
            var placement = $(element).data('error');
            if (placement) {
                $(placement).append(error)
            } else {
                error.insertAfter(element);
            }
        }
    });

    $('.payment_upload').click(function() {
       var id = $(this).data('document_id');
        $('#payment_upload_files').attr('action', localStorage.getItem('url') + '/documents/payment_upload/'  + id)
    });



});


