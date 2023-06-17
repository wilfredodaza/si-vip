const inputs = [];

function eliminar(i) {
    inputs.splice( i, 1 );
    clear();
}


function clear()
{
    var li = '';
    if(inputs.length == 0) {
        $('.date-info').show();
        $('#btncargue').prop('disabled', true);
    }else {
        $('.date-info').hide();
        $('#btncargue').prop('disabled', false);
    }

    for(let i = 0 ;  i < inputs.length; i++) {
        li += '<li class="collection-item" >'+ inputs[i] + '<i class="material-icons right deletes" onclick="eliminar('+ i +')">delete</i> </li>';
    }
    $('.addDates').html(li);

    $('.payment_dates').val(inputs);
}

$( document ).ready(function() {
    $('.addDates').hide();
    $('#btncargue').prop('disabled', true);
    $('.addDate').click(function() {
        if($('#payment_dates').val() != '') {
            inputs.push($('#payment_dates').val())
            $('#payment_dates').val('');
            $('.addDates').show();
        }
        clear();
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
                    localStorage.setItem('active_tour_payroll', true);
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
            title: 'Registrar',
            text: `Para elaborar una nomina debes dar clic en registrar.`,
            attachTo: {
                element: '.step-2',
                on: 'right'
            },
            buttons: [{
                action: function() {
                    localStorage.setItem('active_tour_invoice', true);
                    $('body').removeClass('shepherd-active');
                    $('thead tr th:nth-child(7), tbody tr td:nth-child(7)').removeClass('active-red');
                    $('thead tr th:last-child, tbody tr td:last-child').removeClass('active-red');
                    return this.cancel();
                },
                classes: 'btn btn-light-indigo',
                text: 'Terminar'
            },
                {
                    action: function() {
                        $('thead tr th:nth-child(7), tbody tr td:nth-child(7)').removeClass('active-red');
                        $('thead tr th:last-child, tbody tr td:last-child').removeClass('active-red');
                        return this.back();
                    },
                    classes: 'btn btn-light-indigo',
                    text: 'Atrás'
                },
                {
                    action: function() {
                        $('thead tr th:nth-child(7), tbody tr td:nth-child(7)').addClass('active-red');
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
            text: `Los periodos cuentan con las siguientes acciones:
                    <table class="striped">
                        <tbody>
                            <tr>
                                <td>
                                     <button  class="btn btn-small yellow darken-2" style="padding:0px 10px;">
                                        <i class="material-icons">remove_red_eye</i>
                                    </button>
                                </td>
                                 <td>Ver nomina</td>
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
                    localStorage.setItem('active_tour_payroll', true);
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
            text: `<p>Los periodos de nomina cuenta con los siguientes estados: </p>
                    <table class="striped">
                        <tbody>
                            <tr>
                            <td>
                                <span  class="badge new yellow darken-2"   style="width:140px;" data-badge-caption="En Proceso"></span>
                            </td>
                            <td>La nómina se encuentra en proceso de emisión.</td>
                            </tr>
                              <tr>
                            <td>
                                <span class="badge new green lighten-1" style="width:140px;"    data-badge-caption="Terminado"></span>
                            </td>
                            <td>Todas las nóminas se encuentran emitidas a la DIAN.</td>
                            </tr>
                        </tbody>
                    </table>`,
            attachTo: {
                element: '.step-4',
                on: 'left'
            },
            buttons: [{
                action: function() {
                    localStorage.setItem('active_tour_invoice', true);
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
                element: '.step-5',
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
                        localStorage.setItem('active_tour_payroll', true);
                        $('thead tr th:nth-child(6), tbody tr td:nth-child(6)').removeClass('active-red');
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
        localStorage.removeItem('active_tour_payroll');
        $('body').addClass('shepherd-active');
        $('thead tr th:nth-child(6), tbody tr td:nth-child(6)').removeClass('active-red');
        $('thead tr th:last-child, tbody tr td:last-child').removeClass('active-red');
        tourInit();
    });

    if (!localStorage.getItem('active_tour_payroll')) {
        tourInit();
        $('thead tr th:nth-child(6), tbody tr td:nth-child(6)').removeClass('active-red');
        $('thead tr th:last-child, tbody tr td:last-child').removeClass('active-red');
    }
})