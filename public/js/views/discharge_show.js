$(document).ready(function() {
    /*$('.edit').click(function () {
        var url = localStorage.getItem('url');
        var id = $(this).data('id');

        var invoice = $(this).data('invoice');
        $.get(`${url}/api/v1/wallet/edit/${id}`, function (data) {
            $('#form').attr('action', `${url}/wallet/update/${id}/${invoice}`);
            $('#value').val(data.data.value);
            $('#description').val(data.data.description);
            $('select[name=payment_method_id]').val(data.data.payment_method_id)
        });
    });*/


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
                        localStorage.setItem('active_tour_wallet_show', true);
                        $('body').removeClass('shepherd-active');
                        $('thead tr th:nth-child(5), tbody tr td:nth-child(5)').removeClass('active-red');
                        return this.cancel();
                    },
                    classes: 'btn btn-light-indigo',
                    text: 'No'
                },
                {
                    action: function () {
                        $('thead tr th:nth-child(5), tbody tr td:nth-child(5)').addClass('active-red');
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
                            <td> <button class="btn  yellow darken-2" style="padding:0px 10px;">
                                    <i class="large material-icons">edit</i>
                                </button>
                            </td>
                            <td>Editar pago.</td>
                            </tr>
                             <tr>
                            <td> <button class="btn btn-light-blue-grey" style="padding:0px 10px;">
                                    <i class="material-icons">file_download</i>
                                    </button>
                                </td>
                            <td>Descargar soporte.</td>
                            </tr>
                        </tbody>
                    </table>
                    `,
            attachTo: {
                element: '.step-2',
                on: 'top'
            },
            buttons: [
                {
                    action: function () {
                        $('thead tr th:nth-child(5), tbody tr td:nth-child(5)').removeClass('active-red');
                        $('body').removeClass('shepherd-active');
                        localStorage.setItem('active_tour_wallet_show', true);
                        return this.cancel();
                    },
                    classes: 'btn btn-light-indigo',
                    text: 'Terminar'
                },
                {
                    action: function () {
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
            title: 'Regresar',
            text:  `Regresar a la página de cartera.`,
            attachTo: {
                element: '.step-3',
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
                        $('thead tr th:nth-child(5), tbody tr td:nth-child(5)').removeClass('active-red');
                        $('body').removeClass('shepherd-active');
                        localStorage.setItem('active_tour_wallet_show', true);
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
        localStorage.removeItem('active_tour_wallet_show');
        $('body').addClass('shepherd-active');
        tourInit();
    });

    if(!localStorage.getItem('active_tour_wallet_show')) {
        tourInit();
    }

    $(".deleteInvoice").click(function () {

        swal({
            title: "¿Estás seguro?",
            text: "Recuerda al momento de eliminar la factura, no se podrá deshacer esta acción.",
            icon: "info",
            buttons: ["cancelar", " Aceptar "],
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.post(localStorage.getItem('url') + '/discharge/'+$(this).attr("data-id"),
                    {
                        _method: 'delete',
                    },
                    function (data, status) {
                        var resp = JSON.parse(data);
                        if (resp.status == '200') {
                            swal({
                                title: "Pago eliminado con éxito.",
                                text: resp.observation,
                                icon: "success",
                            });
                            setTimeout(() => {
                                location.reload();
                            }, 100);
                        } else {
                            swal({
                                title: "El pago no pudo ser eliminado.",
                                text: resp.observation,
                                icon: "warning",
                            });
                        }
                    });

            } else {
                swal({
                    title: "Cancelado!",
                    //text: "You clicked the button!",
                    icon: "error",
                });
            }
        });
    });
});