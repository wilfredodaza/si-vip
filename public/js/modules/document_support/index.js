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
                element: '.step-1',
                on: 'top'
            },
            buttons: [
                {
                    action: function () {
                        localStorage.setItem('active_tour_document_support', true);
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
            title: 'Registrar',
            text:  'Para elaborar documentos soporte deberás dar clic en registrar.',
            attachTo: {
                element: '.step-2',
                on: 'right'
            },
            buttons: [
                {
                    action: function () {
                        localStorage.setItem('active_tour_invoice', true);
                        $('thead tr th:nth-child(6), tbody tr td:nth-child(6)').removeClass('active-red');
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
                        $('thead tr th:nth-child(6), tbody tr td:nth-child(6)').addClass('active-red');
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
            text:  `La tabla maneja seis acciones: 
                <table class="striped">
                    <tbody>
                        <tr>
                            <td>
                                <button class="btn pink darken-1 " style="padding:0px 10px;">
                                    <i class="large material-icons">insert_drive_file</i>
                                </button>
                            </td>
                             <td>Descargar documento soporte.</td>
                        </tr>
                        <tr>
                            <td>
                                <button class="btn blue" style="padding:0px 10px;">
                                    <i class="large material-icons">email</i>
                                </button>
                            </td>
                             <td>Enviar invitación.</td>
                        </tr>
                        <tr>
                        <td> 
                            <button class="btn yellow darken-1" style="padding:0px 10px;">
                                <i class="large material-icons">create</i>
                            </button>
                        </td>
                        <td>Editar el documento.</td>
                        </tr>
                            <tr>
                                <td> 
                                    <button class="btn green" style="padding:0px 10px;">
                                        <i class="large material-icons">check</i>
                                    </button>
                                </td>
                                <td>Aceptar documento.</td>
                            </tr>
                        <tr>
                            <td>
                                <button class="btn red" style="padding:0px 10px;">
                                    <i class="large material-icons">close</i>
                                </button>
                            </td>
                            <td>Rechazar documento.</td>
                        </tr>
                        <tr>
                            <td>
                                <button class="btn grey lighten-5 grey-text text-darken-4" style="padding:0px 10px;">
                                    <i class="large material-icons">add</i>
                                </button>
                            </td>
                            <td>Otras configuraciones.</td>
                        </tr>
                    </tbody>
                </table>
                `,
            attachTo: {
                element: '.step-3',
                on: 'right'
            },
            buttons: [
                {
                    action: function () {
                        localStorage.setItem('active_tour_document_support', true);
                        $('body').removeClass('shepherd-active');
                        $('thead tr th:nth-child(6), tbody tr td:nth-child(6)').removeClass('active-red');
                        $('thead tr th:nth-child(5), tbody tr td:nth-child(5)').removeClass('active-red');
                        return this.cancel();
                    },
                    classes: 'btn btn-light-indigo',
                    text: 'Terminar'
                },
                {
                    action: function () {
                        $('thead tr th:nth-child(6), tbody tr td:nth-child(6)').removeClass('active-red');
            
                        return this.back();
                    },
                    classes: 'btn btn-light-indigo',
                    text: 'Atrás'
                },
                {
                    action: function () {
                        $('thead tr th:nth-child(6), tbody tr td:nth-child(6)').removeClass('active-red');
                        $('thead tr th:nth-child(5), tbody tr td:nth-child(5)').addClass('active-red');
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
                         <!-- <span class="new badge yellow darken-4 left" data-badge-caption="" style="padding:0px 10px; display: inline-block;">-->
                         En proceso
                         <!-- </span>-->
                        </td>
                        <td>El archivo se encuentra creado y pendiente para firmarlo</td>
                    </tr>
                    <tr>
                    <td>
                    <!-- <span class="new badge left yellow darken-2" data-badge-caption="" style="padding:0px 10px; display: inline-block;">-->
                        Firmado
                        <!--</span>-->
                    </td>
                        <td>
                           El archivo esta fimado
                        </td>
                    </tr>
                    <tr>
                    <td>
                     <!--<span class="new badge left green"  data-badge-caption="" style="padding:0px 10px; display: inline-block;">-->
                       Aceptado
                       <!--</span>-->
                    </td>
                    <td>
                      El documento soporte fue aceptado por la empresa.
                    </td>
                    </tr>
                    <tr>
                    <td>
                    <!--<span class="new badge left red" data-badge-caption="" style="padding:0px 10px; display: inline-block;">-->
                        Rechazado
                        <!--</span>-->
                    </td>
                    <td>
                        El documento soporte fue rechazado por la empresa y solicita que realice la corrección.
                    </td>
                    </tr>      
                </tbody>
                </table>
                `,
            attachTo: {
                element: '.step-4',
                on: 'top'
            },
            buttons: [
                {
                    action: function () {
                        $('thead tr th:nth-child(6), tbody tr td:nth-child(6)').addClass('active-red');
                        $('thead tr th:nth-child(5), tbody tr td:nth-child(5)').removeClass('active-red');
                        return this.back();
                    },
                    classes: 'btn btn-light-indigo',
                    text: 'Atrás'
                },
                {
                    action: function () {
                        localStorage.setItem('active_tour_document_support', true);
                        $('thead tr th:nth-child(6), tbody tr td:nth-child(6)').removeClass('active-red');
                        $('thead tr th:nth-child(5), tbody tr td:nth-child(5)').removeClass('active-red');
                        $('body').removeClass('shepherd-active');
                        return this.cancel();
                    },
                    classes: 'btn indigo',
                    text: 'Terminar'
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
                        localStorage.setItem('active_tour_document_support', true);
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

    if(!localStorage.getItem('active_tour_document_support')) {
        $('body').removeClass('shepherd-active');
        $('thead tr th:nth-child(6), tbody tr td:nth-child(6)').removeClass('active-red');
        $('thead tr th:nth-child(3), tbody tr td:nth-child(3)').removeClass('active-red');
        tourInit();
    }


    $('.help').click(function () {
        $('thead tr th:nth-child(5), tbody tr td:nth-child(5)').removeClass('active-red');
        $('thead tr th:nth-child(6), tbody tr td:nth-child(6)').removeClass('active-red');
        localStorage.removeItem('active_tour_document_support');
        $('body').addClass('shepherd-active');
        tourInit();
    });

    $('.button-cancel').click(function() {
        var id = $(this).data('id-cancel');
        $('#form-cancel').attr('action', `${localStorage.getItem('url')}/document_support/cancel/${id}`);
    });
    $('.modals-close').click(function() {
        $('#information').removeClass('open');
        $('#information').css({
            display: 'none'
        });
        $('.modals-overlay').css({
            display: 'none'
        });
    })

    window.localStorage.removeItem("id")

    $('.send').click(function () {
        let id = $(this).data('id');
        const url = window.localStorage.getItem('url');
        $('#form-resolution').attr('action', url + '/document_support/send/' + id);
    });
});




