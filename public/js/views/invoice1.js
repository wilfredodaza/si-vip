const vue = new Vue({
    el: '#main',
    data: {
        filter: 'resolution',
        resolution: true,
        Tipo_de_factura: false,
        Cliente: false,
        Estado: false,
        isValid: true,
    },
    methods: {
        select() {
            switch (this.filter) {
                case 'resolution':
                    this.resolution = true;
                    this.Tipo_de_factura = false;
                    this.Cliente = false;
                    this.Estado = false;
                    break;
                case 'Tipo_de_factura':
                    this.resolution = false;
                    this.Tipo_de_factura = true;
                    this.Cliente = false;
                    this.Estado = false;
                    break;
                case 'Cliente':
                    this.resolution = false;
                    this.Tipo_de_factura = false;
                    this.Cliente = true;
                    this.Estado = false;
                    break;
                case 'Estado':
                    this.resolution = false;
                    this.Tipo_de_factura = false;
                    this.Cliente = false;
                    this.Estado = true;
                    break;
            }
            this.isValid = true;
        }
    }
});


$(document).ready(function() {
    $('.send').click(function () {
        let id = $(this).data('id');
        const url = window.localStorage.getItem('url');
        $('.form-resolution').attr('action', url + '/invoice/send/' + id);
    });
    $('.email').click(function() {
        $('.container-sprint.js-email').show();
        $('.container-sprint.js-email').css('display', 'flex');
        $('html, body').css({
            overflow: 'hidden',
            height: '100%'
        });

    });

    $(".btn-upload-invoice").click(function () {
        swal({
            title: "Esto puede tardar varios minutos ¿Está seguro de continuar?.",
            icon: "info",
            buttons: ["cancelar", " Aceptar"],
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $(".btn-upload-invoice").attr('disabled', true);
                $('.container-sprint.js-send').show();
                $('.container-sprint.js-send').css('display', 'flex');
                $('html, body').css({
                    overflow: 'hidden',
                    height: '100%'
                });
                $('.container-sprint.js-send .text-insert').html("Replicando las facturas de Shopify");
                window.location.href = localStorage.getItem('url') + '/consolidate/invoice';

            }
        });
    });



    $('.otros').click(function() {
        const id = $(this).data('id');
        var URLactual = localStorage.getItem('url');
        fetch(URLactual + '/api/invoices/cufe/' + id)
            .then(function(response) {
                return response.json();
            })
            .then(function(myJson) {
                var dates = myJson;
                $('#DIAN').attr('href', dates.url);
            });
        $('#noteCredit').attr('href', `${URLactual}/noteCredit/${id}`);
        $('#noteDebit').attr('href', `${URLactual}/noteDebit/${id}`);
        $('#attached').attr('href', `${URLactual}/invoice/attached_document/${id}`);


        if ($(this).data('type') == 'Nota Débito' || $(this).data('type') == 'Nota Crédito') {
            $('#noteCredit').hide();
            $('#noteDebit').hide();

        } else {
            $('#noteCredit').show();
            $('#noteDebit').show();
        }
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
                on: 'bottom'
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
            text: `Para elaborar una factura debes dar clic en registrar.`,
            attachTo: {
                element: '.step-2',
                on: 'right'
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
                        $('thead tr th:nth-child(6), tbody tr td:nth-child(6)').removeClass('active-red');
                        $('thead tr th:last-child, tbody tr td:last-child').removeClass('active-red');
                        return this.back();
                    },
                    classes: 'btn btn-light-indigo',
                    text: 'Atrás'
                },
                {
                    action: function() {
                        $('thead tr th:nth-child(6), tbody tr td:nth-child(6)').addClass('active-red');
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
            text: `El facturador cuenta con cinco acciones para todos los documentos: 
                    <table class="striped">
                        <tbody>
                            <tr>
                                <td>
                                    <button class="btn btn-small  pink darken-1" style="padding:0px 10px;">
                                        <i class="material-icons">insert_drive_file</i>
                                    </button>
                                </td>
                                 <td>Descargar</td>
                            </tr>
                            <tr>
                            <td> <button class="btn  btn-small yellow darken-2" style="padding:0px 10px;">
                                    <i class="material-icons">create</i>
                                </button>
                            </td>
                            <td>Editar</td>
                            </tr>
                             <tr>
                            <td> 
                                <button class="btn  btn-small" style="padding:0px 10px;">
                                    <i class="material-icons">email</i>
                                </button>
                            </td>
                            <td>Enviar al cliente</td>
                            </tr>
                              <tr>
                            <td>  
                            <button class="btn  btn-small blue" style="padding:0px 10px;">
                                    <i class="material-icons">send</i>
                                </button>
                            </td>
                            <td>Enviar a la DIAN</td>
                            </tr>
                             <tr>
                                <td>  
                                <button class="btn btn-small grey text-black-50 black-text lighten-3" style="padding:0px 10px;">
                                        <i class="material-icons">add</i>
                                    </button>
                                </td>
                                <td>Otros: Notas crédito, Notas débito, Validar con la Dian</td>
                            </tr>
                        </tbody>
                    </table>
                    `,
            attachTo: {
                element: '.step-3',
                on: 'right'
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
                        $('thead tr th:nth-child(6), tbody tr td:nth-child(6)').removeClass('active-red');
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
            text: `<p>El facturador cuenta con los siguientes estados en los que puede estar la factura: </p>
                    <table class="striped">
                        <tbody>
                            <tr>
                                <td width="150px">
                                   <span class="badge new pink darken-1 "  style="width:140px;" data-badge-caption="Guardada" ></span>
                                </td>
                                 <td>Guardada: Se encuentra guardada pero no enviada.</td>
                            </tr>
                            <tr>
                            <td> 
                            <span  class="badge new yellow darken-2"   style="width:140px;" data-badge-caption="Enviada a la DIAN"></span>
                            </td>
                            <td>Recibida por la DIAN: Esta enviada a la DIAN. Es un documento oficial.</td>
                            </tr>
                             <tr>
                            <td> 
                                <span  class="badge new light-blue"   style="width:140px;"  data-badge-caption="Email Enviado"></span>
                            </td>
                            <td>Enviada al cliente: Esta enviada al cliente a través del correo electrónico.</td>
                            </tr>
                              <tr>
                            <td>  
                                <span class="badge new green lighten-1" style="width:140px;"    data-badge-caption="Recibido por el cliente"></span>
                            </td>
                            <td>Recibida por el cliente: El correo electrónico fue leído por el cliente.</td>
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
                        localStorage.setItem('active_tour_invoice', true);
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
        localStorage.removeItem('active_tour_invoice');
        $('body').addClass('shepherd-active');
        $('thead tr th:nth-child(6), tbody tr td:nth-child(6)').removeClass('active-red');
        $('thead tr th:last-child, tbody tr td:last-child').removeClass('active-red');
        tourInit();
    });

    if (!localStorage.getItem('active_tour_invoice')) {
        tourInit();
        $('thead tr th:nth-child(6), tbody tr td:nth-child(6)').removeClass('active-red');
        $('thead tr th:last-child, tbody tr td:last-child').removeClass('active-red');
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
                $.post(localStorage.getItem('url') + '/invoice/delete',
                    {
                        id: $(this).attr("data-id"),
                    },
                    function (data, status) {
                        var resp = JSON.parse(data);
                        console.log(data.data);
                        if (resp.status == 'aceptada') {
                            swal({
                                title: "Genial!!",
                                text: resp.observation,
                                icon: "success",
                            });
                            setTimeout(() => {
                                location.reload();
                            }, 3000);
                        } else {
                            swal({
                                title: "ups..",
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

    $('.showError').click(function() {
        const cadena = $(this).data('error');
        const url = $(this).data('error-url');
        document.getElementById('divErrors').innerHTML = cadena;
        const index = cadena.indexOf("Regla: 90");
        if(index > 0) {
            console.log(index);
            document.getElementById('divErrors').innerHTML += `
            </br>
            <a href="${url}" class="btn btn-small purple right"> Solucionar </a>
            </br>
            `;
        }
    });

});