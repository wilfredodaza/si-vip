var val = [];

function group() {
    val = [];
    $('.checkbox-active:checkbox:checked').each(function(i){
        val[i] = $(this).val();
    });
    $('#payrolls').val(val);

    console.log(val);
    if(val.length > 0) {
        $('.send_multiple').prop('disabled', false);
    }else {
        $('.send_multiple').prop('disabled', true);
    }
}

$(document).ready(function() {
    $('.send').click(function(){
        console.log(`${localStorage.getItem('url')}/payrolls/send/${$(this).data('send')}`);
        $('#form-resolution').attr('action', `${localStorage.getItem('url')}/payrolls/send/${$(this).data('send')}`)
    });


    $('.checkbox-active').change(function() {
        group();
    });

    $('.checkbox-todo').change(function() {
        if( $(this).prop('checked')  == true) {
            $("input:checkbox").prop('checked', $(this).prop("checked"));
        }else {
            $("input:checkbox").prop('checked', $(this).prop("checked"));
        }
        group();
    });

    $('.resolution_multiple').click(function() {
        $('#payrolls').val(val);
        $('#form-resolution-multiple').attr('action', `${localStorage.getItem('url')}/payroll/send_multiple/${$(this).data('send')}`)
    });


});

$(document).ready(function() {
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
            text: '¿Necesitas Ayuda?',
            attachTo: {
                element: '.step-1',
                on: 'bottom'
            },
            buttons: [{
                action: function() {
                    localStorage.setItem('active_tour_payroll_show', true);
                    $('body').removeClass('shepherd-active');
                    $('thead tr th:nth-child(6), tbody tr td:nth-child(6)').removeClass('active-red');
                    $('thead tr th:last-child, tbody tr td:last-child').removeClass('active-red');
                    return this.cancel();
                },
                classes: 'btn btn-light-indigo',
                text: 'No'
            },
                {
                    action: function() {
                        return this.next();
                    },
                    classes: 'btn indigo',
                    text: 'Si'
                }
            ],
            id: 'welcome'
        });

        tour.addStep({
            title: 'Añadir empleado',
            text: `Este botón te permite ingresar un nuevo empleado a la nómina.`,
            attachTo: {
                element: '.step-2',
                on: 'right'
            },
            buttons: [{
                action: function() {
                    localStorage.setItem('active_tour_invoice_show', true);
                    $('body').removeClass('shepherd-active');
                    $('thead tr th:nth-child(5), tbody tr td:nth-child(5)').removeClass('active-red');
                    $('thead tr th:last-child, tbody tr td:last-child').removeClass('active-red');
                    return this.cancel();
                },
                classes: 'btn btn-light-indigo',
                text: 'Terminar'
            },
                {
                    action: function() {
                        $('thead tr th:nth-child(5), tbody tr td:nth-child(5)').removeClass('active-red');
                        $('thead tr th:last-child, tbody tr td:last-child').removeClass('active-red');
                        return this.back();
                    },
                    classes: 'btn btn-light-indigo',
                    text: 'Atrás'
                },
                {
                    action: function() {
                        $('thead tr th:nth-child(5), tbody tr td:nth-child(5)').addClass('active-red');
                        $('thead tr th:last-child, tbody tr td:last-child').addClass('active-red');
                        return this.next();
                    },
                    classes: 'btn indigo',
                    text: 'Siguiente'
                }
            ],
        });

        tour.addStep({
            title: 'Acciones',
            text: `Las nomina cuentan con las siguientes acciones:
                    <table class="striped">
                        <tbody>
                            <tr>
                                <td>
                                    <button  class="btn btn-small pink darken-1" style="padding:0px 10px;">
                                        <i class="material-icons">insert_drive_file</i>
                                    </a>
                                </td>
                                <td>
                                    Descargar PDF de la Nomina
                                </td>
                            </tr>
                            <tr>
                                <td>
                                     <button  class="btn btn-small indigo" style="padding:0px 10px;">
                                           <i class="material-icons">edit</i>
                                     </button>
                                </td>
                                <td>
                                    Editar Nomina
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <button class="btn btn-small light-blue" style="padding:0px 10px;">
                                         <i class="material-icons">send</i>
                                    </button>
                                </td>
                                <td>
                                    Emitir Nomina A la DIAN
                                </td>
                            </tr>
                            <tr>
                                <td>
                                     <button class="btn btn-small" style="padding:0px 10px;">
                                         <i class="material-icons">attach_file</i>
                                     </button>
                                </td>
                                <td> Descargar XML de la  Nomina </td>
                            </tr>
                        </tbody>
                    </table>
                    `,
            attachTo: {
                element: '.step-3',
                on: 'left'
            },
            buttons: [{
                action: function() {
                    localStorage.setItem('active_tour_invoice_show', true);
                    $('body').removeClass('shepherd-active');
                    $('thead tr th:nth-child(5), tbody tr td:nth-child(5)').removeClass('active-red');
                    $('thead tr th:last-child, tbody tr td:last-child').removeClass('active-red');
                    return this.cancel();
                },
                classes: 'btn btn-light-indigo',
                text: 'Terminar'
            },
                {
                    action: function() {
                        $('thead tr th:nth-child(5), tbody tr td:nth-child(5)').removeClass('active-red');
                        $('thead tr th:last-child, tbody tr td:last-child').removeClass('active-red');
                        return this.back();
                    },
                    classes: 'btn btn-light-indigo',
                    text: 'Atrás'
                },
                {
                    action: function() {
                        $('thead tr th:nth-child(6), tbody tr td:nth-child(6)').addClass('active-red');
                        $('thead tr th:last-child, tbody tr td:last-child').removeClass('active-red');
                        return this.next();
                    },
                    classes: 'btn indigo',
                    text: 'Siguiente'
                }
            ],
        });

        tour.addStep({
            title: 'Estados',
            text: `<p>Las nóminas cuenta con los siguientes estados: </p>
                    <table class="striped">
                        <tbody>
                            <tr>
                            <td>
                               <span class="badge new pink darken-1 " style="width:140px;" data-badge-caption="En proceso" ></span>
                            </td>
                            <td>La nómina se encuentra sin ningún valor de deducciones y devengados.</td>
                            </tr>
                            <tr>
                                <td>
                                   <span  class="badge new yellow darken-2"  style="width:140px;" data-badge-caption="Por emitir"></span>
                                </td>
                                <td>Emitir a la DIAN.</td>
                            </tr>
                            <tr>
                                <td>
                                    <span  class="badge new light-blue" style="width:140px;"  data-badge-caption="Enviada a la DIAN"></span>
                                </td>
                                <td>La nómina fue emitida con éxito a la DIAN.</td>
                            </tr>
                            <tr>
                                <td>
                                   <span  class="badge new red" style="width:140px;"  data-badge-caption="Error al emitir"></span>
                                </td>
                                <td>La nómina presenta un error por favor revise el inconveniente y intente de nuevo.</td>
                            </tr>
                            <tr>
                                <td>
                                   <span  class="badge new orange" style="width:140px;"  data-badge-caption="Cargando"></span>
                                </td>
                                <td>La nómina se esta emitiendo a la DIAN.</td>
                            </tr>
                        </tbody>
                    </table>`,
            attachTo: {
                element: '.step-4',
                on: 'left'
            },
            buttons: [{
                action: function() {
                    localStorage.setItem('active_tour_invoice_show', true);
                    $('body').removeClass('shepherd-active');
                    $('thead tr th:nth-child(6), tbody tr td:nth-child(6)').removeClass('active-red');
                    $('thead tr th:last-child, tbody tr td:last-child').removeClass('active-red');
                    return this.cancel();
                },
                classes: 'btn btn-light-indigo',
                text: 'Terminar'
            },
                {

                    action: function() {
                        $('thead tr th:last-child, tbody tr td:last-child').addClass('active-red');
                        return this.back();
                    },
                    classes: 'btn btn-light-indigo',
                    text: 'Atrás'
                },
                {
                    action: function() {
                        $('thead tr th:nth-child(1), tbody tr td:nth-child(1)').addClass('active-red');
                        return this.next();
                    },
                    classes: 'btn indigo',
                    text: 'Siguiente'
                }
            ]
        });

        tour.addStep({
            title: 'Seleccionar multiple',
            text: `Este check te permitira seleccionar todas las nominas.`,
            attachTo: {
                element: '.step-5',
                on: 'rigth'
            },
            buttons: [
                {
                    action: function() {
                        localStorage.setItem('active_tour_invoice_show', true);
                        $('thead tr th:nth-child(6), tbody tr td:nth-child(6)').removeClass('active-red');
                        $('thead tr th:last-child, tbody tr td:last-child').removeClass('active-red');
                        $('body').removeClass('shepherd-active');
                        return this.cancel();
                    },
                    classes: 'btn btn-light-indigo',
                    text: 'Terminar'
                },
                {
                    action: function() {
                        $('thead tr th:nth-child(5), tbody tr td:nth-child(5)').addClass('active-red');
                        return this.back();
                    },
                    classes: 'btn btn-light-indigo',
                    text: 'Atrás'
                },
                {
                    action: function() {
                        $('thead tr th:nth-child(6), tbody tr td:nth-child(6)').removeClass('active-red');
                        return this.next();
                    },
                    classes: 'btn indigo',
                    text: 'Siguiente'
                }
            ]
        });

        tour.addStep({
            title: 'Envio multiple de nominas',
            text: `Este boton te permite realizar el envio multiple de nominas.`,
            attachTo: {
                element: '.step-6',
                on: 'rigth'
            },
            buttons: [
                {
                    action: function() {
                        localStorage.setItem('active_tour_invoice_show', true);
                        $('thead tr th:nth-child(1), tbody tr td:nth-child(1)').removeClass('active-red');
                        $('thead tr th:last-child, tbody tr td:last-child').removeClass('active-red');
                        $('body').removeClass('shepherd-active');
                        return this.cancel();
                    },
                    classes: 'btn btn-light-indigo',
                    text: 'Terminar'
                },
                {
                    action: function() {
                        $('thead tr th:nth-child(5), tbody tr td:nth-child(5)').addClass('active-red');
                        return this.back();
                    },
                    classes: 'btn btn-light-indigo',
                    text: 'Atrás'
                },
                {
                    action: function() {
                        $('thead tr th:nth-child(6), tbody tr td:nth-child(6)').removeClass('active-red');
                        return this.next();
                    },
                    classes: 'btn indigo',
                    text: 'Siguiente'
                }
            ]
        });

        tour.addStep({
            title: 'Filtrar',
            text: `Para buscar fácilmente puedes dar click en Filtrar.`,
            attachTo: {
                element: '.step-7',
                on: 'left'
            },
            buttons: [{
                action: function() {
                    $('thead tr th:nth-child(6), tbody tr td:nth-child(6)').addClass('active-red');
                    return this.back();
                },
                classes: 'btn btn-light-indigo',
                text: 'Atrás'
            },
                {
                    action: function() {
                        localStorage.setItem('active_tour_invoice_show', true);
                        $('thead tr th:nth-child(1), tbody tr td:nth-child(1)').removeClass('active-red');
                        $('thead tr th:last-child, tbody tr td:last-child').removeClass('active-red');
                        $('body').removeClass('shepherd-active');
                        return this.cancel();
                    },
                    classes: 'btn indigo',
                    text: 'Terminar'
                }
            ]
        });

        tour.start();
    }

    $('.help').click(function() {
        localStorage.setItem('active_tour_invoice_show', true);
        $('body').addClass('shepherd-active');
        $('thead tr th:last-child, tbody tr td:last-child').removeClass('active-red');
        tourInit();
    });

    if (!localStorage.getItem('active_tour_payroll_show')) {
        tourInit();
        $('thead tr th:last-child, tbody tr td:last-child').removeClass('active-red');
    }
});


var bar1 = new ldBar("#myItem1");
var bar2 = document.getElementById('myItem1').ldBar;
