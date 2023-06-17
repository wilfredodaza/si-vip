$(document).ready(function() {

    $('.edit').click(function () {
        var url = localStorage.getItem('url');
        var id = $(this).data('id');
        $('#form').attr('action', `${url}/wallet/store/${id}`);
        $('#name_customer').html($(this).data('customer'));
    });


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
                on: 'top'
            },
            buttons: [

                {
                    action: function () {
                        localStorage.setItem('active_tour_wallet', true);
                        $('thead tr th:nth-child(6), tbody tr td:nth-child(6)').removeClass('active-red');
                        $('thead tr th:last-child, tbody tr td:last-child').removeClass('active-red');
                        $('body').removeClass('shepherd-active');
                        return this.cancel();
                    },
                    classes: 'btn btn-light-indigo',
                    text: 'No'
                },
                {
                    action: function () {
                        $('thead tr th:last-child, tbody tr td:last-child').addClass('active-red');
                        return this.next();
                    },
                    classes: 'btn indigo',
                    text: 'Si'
                }
            ],
            id: 'welcome'
        });

        tour.addStep({
            title: 'Acciones',
            text:  `La tabla maneja dos acciones: 
                    <table class="striped">
                        <tbody>
                            <tr>
                                <td>
                                    <button class="btn green " style="padding:0px 10px;">
                                        <i class="large material-icons">attach_money</i>
                                    </button>
                                </td>
                                 <td>Subir pago.</td>
                            </tr>
                            <tr>
                            <td> <button class="btn  yellow darken-2" style="padding:0px 10px;">
                                    <i class="large material-icons">visibility</i>
                                </button>
                            </td>
                            <td>Ver pagos de la factura.</td>
                            </tr>
                        </tbody>
                    </table>
                    `,
            attachTo: {
                element: '.step-2',
                on: 'right'
            },
            buttons: [
                {
                    action: function () {
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
                    action: function () {
                        $('thead tr th:nth-child(6), tbody tr td:nth-child(6)').removeClass('active-red');
                        $('thead tr th:last-child, tbody tr td:last-child').removeClass('active-red');
                        return this.back();
                    },
                    classes: 'btn btn-light-indigo',
                    text: 'Atrás'
                },
                {
                    action: function () {
                        $('thead tr th:last-child, tbody tr td:last-child').removeClass('active-red');
                        $('thead tr th:nth-child(6), tbody tr td:nth-child(6)').addClass('active-red');
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
                               <span class="new badge pink darken-1 left" data-badge-caption="" style="width: 120px;">Pendiente</span>
                            </td>
                            <td>No se realizado el pago total de la factura.</td>
                        </tr>
                        <tr>
                        <td>
                         <span class="new badge left green" data-badge-caption="" style="width: 120px;">
                            Pago
                        </span>
                        </td>
                            <td>
                              El pago esta completo.
                            </td>
                        </tr>
                    </tbody>
                    </table>
                    `,
            attachTo: {
                element: '.step-3',
                on: 'top'
            },
            buttons: [
                {
                    action: function () {
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
                    action: function () {
                        $('thead tr th:nth-child(6), tbody tr td:nth-child(6)').removeClass('active-red');
                        $('thead tr th:last-child, tbody tr td:last-child').addClass('active-red');
                        return this.back();
                    },
                    classes: 'btn btn-light-indigo',
                    text: 'Atrás'
                },
                {
                    action: function () {
                        $('thead tr th:last-child, tbody tr td:last-child').removeClass('active-red');
                        $('thead tr th:nth-child(6), tbody tr td:nth-child(6)').removeClass('active-red');
                        return this.next();
                    },
                    classes: 'btn indigo',
                    text: 'Siguiente'
                }
            ],
        });
        tour.addStep({
            title: 'Filtro',
            text:  `Los filtros le permitan buscar las factura de forma mas rapida.`,
            attachTo: {
                element: '.step-4',
                on: 'left'
            },
            buttons: [
                {
                    action: function () {
                        $('thead tr th:nth-child(6), tbody tr td:nth-child(6)').addClass('active-red');
                        return this.back();
                    },
                    classes: 'btn btn-light-indigo',
                    text: 'Atrás'
                },
                {
                    action: function () {
                        localStorage.setItem('active_tour_wallet', true);
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

    $('.help').click(function () {
        localStorage.removeItem('active_tour_wallet');
        $('thead tr th:last-child, tbody tr td:last-child').removeClass('active-red');
        $('thead tr th:nth-child(6), tbody tr td:nth-child(6)').removeClass('active-red');
        $('body').addClass('shepherd-active');
        tourInit();
    });

    if(!localStorage.getItem('active_tour_wallet')) {
        $('thead tr th:last-child, tbody tr td:last-child').removeClass('active-red');
        $('thead tr th:nth-child(6), tbody tr td:nth-child(6)').removeClass('active-red');
        tourInit();
    }

});
