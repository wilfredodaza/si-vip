<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?> About <?= $this->endSection() ?>



<?= $this->section('content') ?>
<!-- BEGIN: Page Main-->
<div id="main">
    <div class="row">
        <div class="col s12">
            <div class="container">
                <div class="section">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-title">Listado de Versiones</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col s12">
                            <ul class="collapsible collapsible-accordion">
                                <li>
                                    <div class="collapsible-header"><i class="material-icons">arrow_upward</i> Versión 4
                                        <span class="badge new   teal darken-2"
                                            data-badge-caption="15 de marzo del 2021"></span>
                                    </div>
                                    <div class="collapsible-body" style="display: block;">
                                        <ul>
                                            <li>Envio de indicadores por correo electrónico.</li>
                                            <li>Módulo de recepción de documentos en formatos XML y ZIP.</li>
                                            <li>Creación y administración de documentos soporte para proveedor no
                                                frecuente.</li>
                                            <li>Creación y administración de documentos soporte para proveedor
                                                frecuente.</li>
                                        </ul>
                                    </div>
                                </li>
                                <li>
                                    <div class="collapsible-header"><i class="material-icons">arrow_upward</i> Versión 3
                                        <span class="badge new orange darken-4"
                                            data-badge-caption="09 de Noviembre del 2020"></span>
                                    </div>
                                    <div class="collapsible-body">
                                        <ul>
                                            <li>Registro de cotización como factura.</li>
                                            <li>Envió de cotización por correo electrónico.</li>
                                            <li>Descarga de cotizaciones en pdf.</li>
                                            <li>Se instala módulo de cotizaciones.</li>
                                        </ul>
                                    </div>
                                </li>
                                <li>
                                    <div class="collapsible-header"><i class="material-icons">arrow_upward</i> Versión
                                        2.2
                                        <span class="badge new blue-gray"
                                            data-badge-caption="23 de octubre del 2020"></span>
                                    </div>
                                    <div class="collapsible-body">
                                        <ul>
                                            <li>Botón de validación de documentos ante la DIAN.</li>
                                            <li>Se instalan restricciones en nota crédito y nota débito.</li>
                                            <li>Nuevo diseño en el menú vertical.</li>
                                            <li>Nuevo sistema de notificaciones.</li>
                                            <li>Se ajustan valores en cartera cuando se genera nota crédito y nota
                                                débito.</li>
                                        </ul>
                                    </div>
                                </li>
                                <li>
                                    <div class="collapsible-header"><i class="material-icons">arrow_upward</i> Versión
                                        2.1
                                        <span class="badge new blue" data-badge-caption="19 de octubre del 2020"></span>
                                    </div>
                                    <div class="collapsible-body">
                                        <ul>
                                            <li>Búsqueda del cliente por número de cedula, correo electrónico, nombre.
                                            </li>
                                            <li>Búsqueda del producto por código y nombre.</li>
                                            <li>Descuento por porcentaje o valor.</li>
                                            <li>Se soluciona paginado desbordado.</li>
                                        </ul>
                                    </div>
                                </li>
                                <li>
                                    <div class="collapsible-header"><i class="material-icons">arrow_upward</i> Versión
                                        2.0
                                        <span class="badge new red"
                                            data-badge-caption="21 de septiembre del 2020"></span>
                                    </div>
                                    <div class="collapsible-body">
                                        <ul>
                                            <li>Manejo de cuentas contables.</li>
                                            <li>Módulo de cartera.</li>
                                            <li>Informes de impuestos y ventas.</li>
                                            <li>Manejo de múltiples Resoluciones.</li>
                                            <li>Factura de Exportación y monedas COL, USD, EUR.</li>
                                            <li>Mejora en presentación de la plantilla de facturación.</li>
                                        </ul>
                                    </div>
                                </li>
                                <li>
                                    <div class="collapsible-header"><i class="material-icons">arrow_upward</i> Versión
                                        1.1
                                        <span class="badge new orange" data-badge-caption="23 de junio del 2020"></span>
                                    </div>
                                    <div class="collapsible-body">
                                        <ul>
                                            <li>Mejora interface de navegación.</li>
                                            <li>Módulo de notificaciones.</li>
                                        </ul>
                                    </div>
                                </li>
                                <li>
                                    <div class="collapsible-header"><i class="material-icons">arrow_upward</i> Versión
                                        1.0
                                        <span class="badge new green" data-badge-caption="28 de mayo del 2020"></span>
                                    </div>
                                    <div class="collapsible-body">
                                        <ul>
                                            <li>Se implementa grocery crud enterprise.</li>
                                            <li>Actualizacion de codeigniter 3 a codeigniter 4.</li>
                                            <li>Se implementa plantilla gentella.</li>
                                        </ul>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
