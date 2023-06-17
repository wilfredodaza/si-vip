
const vue = new Vue({
    el: '#main',
    data: {
        active: true,
        urlForm: '',
        nameProduct: '',
        height: 250
    },
    methods: {
        create() {
            this.active = !this.active;
            if (this.active) {
                this.height = 250;
            } else {
                this.height = 500;
            }

        },
        url(id, invoiceId, name) {
            this.nameProduct = name.slice(0, 45);
            this.urlForm = localStorage.getItem('url') + '/documents/product_created/' + id + '/' + invoiceId + '/0';
        }
    }
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
            title: 'Ayuda',
            text: 'Necesitas ayuda.',
            attachTo: {
                element: '.step-1',
                on: 'top'
            },
            buttons: [{
                    action: function() {
                        localStorage.setItem('active_tour_file_show', true);
                        $('body').removeClass('shepherd-active');
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
            title: 'Acciones',
            text: `La tabla maneja una acciones: 
                    <br>
                    <table class="striped">
                        <tbody>
                            <tr>
                                <td>
                                    <button class="btn btn-small modals-trigger modals-triggers2 step-6 next-tour" style="padding-left: 10px; padding-right:10px;" >
                                        <i class="material-icons">add_shopping_cart</i>
                                    </button>
                                </td>
                                <td>Asociar Producto.</td>
                            </tr>
                            <tr>
                                <td>
                                    <button class="btn btn-small red modals-trigger modals-triggers2 step-6 next-tour" style="padding-left: 10px; padding-right:10px;">
                                        <i class="material-icons">remove_shopping_cart</i>
                                    </button>
                                </td>
                                <td>No Asociar Producto.</td>
                            </tr>
                        </tbody>
                    </table>
                    `,
            attachTo: {
                element: '.step-2',
                on: 'top'
            },
            buttons: [{
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
            ],
        });

        tour.addStep({
            title: 'Estados',
            text: `La tabla maneja dos estados:
            <br>
            <table class="striped">
                       <tbody>
                          <tr>
                             <td>
                                <span class="new badge tooltipped yellow darken-2 " data-position="top" data-badge-caption="" data-tooltip="Producto sin asociar">
                                   Sin cargar
                                </span>
                            </td>
                            <td>Producto sin cargar.</td>
                        </tr>
                        <tr>
                        <td>
                         <span class="new badge left green" data-badge-caption="" style="padding:0px 10px; display: inline-block;">
                           Cargado
                        </span>
                        </td>
                            <td>
                              Producto cargado exitosamente.
                            </td>
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
            ],
        });

        tour.addStep({
            title: 'Regresar',
            text: `Regresar a la página de cartera.`,
            attachTo: {
                element: '.step-4',
                on: 'right'
            },
            buttons: [{
                    action: function() {
                        return this.back();
                    },
                    classes: 'btn btn-light-indigo',
                    text: 'Atrás'
                },
                {
                    action: function() {
                        $('body').removeClass('shepherd-active');
                        localStorage.setItem('active_tour_file_show', true);
                        return this.cancel();
                    },
                    classes: 'btn indigo',
                    text: 'Terminar'
                }
            ],
        });


        tour.start();
    }

    $('.help').click(function() {
        localStorage.removeItem('active_tour_file_show');
        $('body').addClass('shepherd-active');
        tourInit();
    });

    if (!localStorage.getItem('active_tour_file_show')) {
        tourInit();
    }
});

Vue.config.productionTip = false
Vue.config.devtools = false

$(document).ready(function () {
    $('.not-refence').click(function() {
        swal({
            title: "Desasociar",
            text: "Desea no asociar el producto al inventario.",
            icon: 'warning',
            buttons: {
                cancel: true,
                delete: 'Continuar'
            }
        }).then((data) => {
            console.log(data);
            if(data == 'delete') {
                var url  = window.localStorage.getItem('url');
                var line = $(this).data('line');
                var id = $(this).data('id');
                $.post(url + '/documents/product_created/' +  line  + '/' + id , { id_product:2712 } , function(data, status){
                    location.reload()
                });
            }
        })
    })
})