$(document).ready(function () {
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
            text: '¿Necesitas ayuda.?',
            attachTo: {
                element: '.step-1',
                on: 'top'
            },
            buttons: [
                {
                    action: function () {
                        $('body').removeClass('shepherd-active');
                        localStorage.setItem('active_tour_quotation', true);
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
            text:  `Si daremos clic en el botón registrar cuando deseemos crear una cotización
                    `,
            attachTo: {
                element: '.step-2',
                on: 'right'
            },
            buttons: [
                {
                    action: function () {
                        $('body').removeClass('shepherd-active');
                        $('thead tr th:nth-child(5), tbody tr td:nth-child(5)').removeClass('active-red');
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
        });

        tour.addStep({
            title: 'Acciones',
            text:  `La tabla maneja dos acciones: 
                    <table class="striped">
                        <tbody>
                            <tr>
                                <td>
                                    <button class="btn btn-small pink darken-1" style="padding:0px 10px;">
                                       <i class="material-icons">insert_drive_file</i>
                                    </button>
                                </td>
                                <td>Descargar cotización.</td>
                            </tr>
                             <tr>
                                <td> 
                                    <button class="btn btn-small yellow darken-2" style="padding:0px 10px;">
                                       <i class="material-icons">create</i>
                                    </button>
                                </td>
                                <td>Editar cotización.</td>
                            </tr>
                             <tr>
                                <td> 
                                    <button class="btn btn-small" style="padding:0px 10px;">
                                     <i class="material-icons">email</i>
                                   </button>
                                </td>
                                <td>Enviar cotizacion por correo electronico.</td>
                            </tr>
                             <tr>
                                <td> 
                                    <button class="btn btn-small green" style="padding:0px 10px;">
                                     <i class="material-icons">assignment</i>
                                   </button>
                                </td>
                                <td>Seguimientos.</td>
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
                        $('body').removeClass('shepherd-active');
                        $('thead tr th:nth-child(5), tbody tr td:nth-child(5)').removeClass('active-red');
                        $('thead tr th:nth-child(6), tbody tr td:nth-child(6)').removeClass('active-red');
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
        });

        tour.addStep({
            title: 'Estados',
            text:  `La tabla maneja dos estados: 
            <table class="striped">
                        <tbody>
                            <tr>
                                <td>
                                  <span class="badge new green darken-1 "  data-badge-caption="Abierta" ></span>
                                </td>
                                <td>Cotización abierta.</td>
                            </tr>
                             <tr>
                                <td> 
                                    <span class="badge new red darken-1"  data-badge-caption="Cerrada" ></span>
                                </td>
                                <td>Cotización cerrada.</td>
                            </tr>
                        </tbody>
                    </table>
            `,
            attachTo: {
                element: '.step-4',
                on: 'right'
            },
            buttons: [
                {
                    action: function () {
                        $('body').removeClass('shepherd-active');
                        $('thead tr th:nth-child(5), tbody tr td:nth-child(5)').removeClass('active-red');
                        $('thead tr th:nth-child(6), tbody tr td:nth-child(6)').removeClass('active-red');
                        return this.cancel();
                    },
                    classes: 'btn btn-light-indigo',
                    text: 'Terminar'
                },
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
                        $('thead tr th:nth-child(5), tbody tr td:nth-child(5)').removeClass('active-red');
                        return this.next();
                    },
                    classes: 'btn indigo',
                    text: 'Siguiente'
                }
            ],
        });

        tour.addStep({
            title: 'Filtrar',
            text:  `Los filtros le permitan buscar las cotización de forma más rápida.`,
            attachTo: {
                element: '.step-5',
                on: 'right'
            },
            buttons: [

                {
                    action: function () {
                        $('thead tr th:nth-child(5), tbody tr td:nth-child(5)').addClass('active-red');
                        return this.back();
                    },
                    classes: 'btn btn-light-indigo',
                    text: 'Atrás'
                },
                {
                    action: function () {
                        $('body').removeClass('shepherd-active');
                        localStorage.setItem('active_tour_quotation', true);
                        return this.cancel();
                    },
                    classes: 'btn indigo',
                    text: 'Terminar'
                }
            ],
        });


        tour.start();
    }

    $('.help').click(function () {
        localStorage.removeItem('active_tour_quotation');
        $('body').addClass('shepherd-active');
        $('thead tr th:nth-child(5), tbody tr td:nth-child(5)').removeClass('active-red');
        $('thead tr th:nth-child(6), tbody tr td:nth-child(6)').removeClass('active-red');
        tourInit();
    });

    if(!localStorage.getItem('active_tour_quotation')) {
        $('thead tr th:nth-child(5), tbody tr td:nth-child(5)').removeClass('active-red');
        $('thead tr th:nth-child(6), tbody tr td:nth-child(6)').removeClass('active-red');
        tourInit();
    }
});



