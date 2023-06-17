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
            text: `Este botón te permite registrar un nuevo empleado a la nómina.`,
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
                                    <button  class="btn btn-small yellow darken-2" style="padding:0px 10px;">
                                        <i class="material-icons">remove_red_eye</i>
                                    </button>
                                </td>
                                <td>
                                   Ver Empleado
                                </td>
                            </tr>
                            <tr>
                                <td>
                                     <button class="btn btn-small green darken-1" style="padding:0px 10px;">
                                        <i class="material-icons">edit</i>
                                    </button>
                                </td>
                                <td>
                                    Editar Empleado
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <button  class="btn btn-smallblue darken-1" style="padding:0px 10px;">
                                         <i class="material-icons">check</i>
                                    </button>
                                </td>
                                <td>
                                    Inactivar Empleado
                                </td>
                            </tr>
                            <tr>
                                <td>
                                      <button  class="btn btn-small tooltipped  blue darken-1" style="padding:0px 10px;">
                                            <i class="material-icons">close</i>
                                      </button>
                                </td>
                                <td> Activar Empleado </td>
                            </tr>
                            <tr>
                                <td>
                                    <button class="btn btn-small red darken-2" style="padding:0px 10px;">
                                        <i class="material-icons">delete</i>
                                    </button>
                                </td>
                                <td>
                                    Eliminar Empleado
                                </td>
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
            title: 'Importar',
            text: 'Por medio de este botón podrás subir en un Excel los empleados de tu empresa.',
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
                        $('thead tr th:nth-child(6), tbody tr td:nth-child(6)').addClass('active-red');
                        return this.back();
                    },
                    classes: 'btn btn-light-indigo',
                    text: 'Atrás'
                },
                {
                    action: function() {

                        return this.next();
                    },
                    classes: 'btn indigo',
                    text: 'Siguiente'
                }
            ]
        });

        tour.addStep({
            title: 'Exportar',
            text: 'Por medio de este botón podran descargar los datos de los empreados en un formato de Excel.',
            attachTo: {
                element: '.step-5',
                on: 'left'
            },
            buttons: [{
                action: function() {
                    localStorage.setItem('active_tour_invoice_show', true);
                    $('body').removeClass('shepherd-active');
                    return this.cancel();
                },
                classes: 'btn btn-light-indigo',
                text: 'Terminar'
            },
                {

                    action: function() {
                        return this.back();
                    },
                    classes: 'btn btn-light-indigo',
                    text: 'Atrás'
                },
                {
                    action: function() {

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
                element: '.step-6',
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