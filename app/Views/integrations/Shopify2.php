<?= view('layouts/header') ?>
<div>
    <div class="row">
        <div class="breadcrumbs-inline pt-3 pb-1" id="breadcrumbs-wrapper">
            <div class="container">
                <div class="row">
                    <div class="col s12">
                        <?= $this->include('layouts/alerts') ?>
                    </div>
                    <div class="col s10 m6 l6 breadcrumbs-left">
                        <h5 class="breadcrumbs-title mt-0 mb-0 display-inline hide-on-small-and-down ">
                            <span>
                               Integraciones
                            </span>
                        </h5>
                        <ol class="breadcrumbs mb-0">
                            <li class="breadcrumb-item"><a href="<?= base_url('integrations/') ?>">Aplicaciones</a></li>
                            <li class="breadcrumb-item"><a href="<?= base_url('integrations/shopify') ?>">Shopify</a>
                            </li>
                        </ol>

                    </div>
                </div>
            </div>
        </div>
        <div class="col s12">
            <div class="container">
                <div class="section">
                    <div class="card">
                        <div class="card-content">
                            <?php if(!empty($name_shopify)): ?>
                                <p class="">
                                <h5>Guarda tu información</h5>
                                Por favor diligencia los siguiente datos para terminar con el registro.
                                </p>
                                <form action="<?= base_url('integrations/shopify/update_register') ?>" method="post">
                                    <ul class="stepper linear">
                                        <li class="step active">
                                            <div class="step-title waves-effect">Información General</div>
                                            <div class="step-content">
                                                <div class="row">
                                                    <div class="input-field col m12 s12">
                                                        <label class="active" for="name">Nombre de tu tienda: <span
                                                                class="red-text">*</span></label>
                                                        <input type="text" id="name" name="name" class="validate"
                                                               value="<?= $name_shopify ?>" required="">
                                                    </div>
                                                    <div class="input-field col m6 s12">
                                                        <label class="active" for="name">Nit empresa: <span
                                                                class="red-text">*</span></label>
                                                        <input type="text" id="nit" name="nit" class="validate"
                                                               placeholder=""  required="">
                                                    </div>
                                                    <div class="input-field col m6 s12">
                                                        <label class="active" for="status_invoice">Estado de las
                                                            facturas <span
                                                                class="red-text">*</span></label>
                                                        <select id="status_invoice"
                                                                class="select2 browser-default validate"
                                                                name="status_invoice" required>
                                                            <option value="" disabled="" selected="">Seleccione estado
                                                            </option>
                                                            <option value="Borrador">Borrador</option>
                                                            <option value="Por pagar">Por pagar</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="step-actions">
                                                    <div class="row">
                                                        <div class="col m4 s12 mb-3">
                                                            <button id="btn_name" type="submit"
                                                                    class=" dark btn btn-light-indigo next-step">
                                                                Guardar
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </form>
                            <?php else: ?>
                                <p class="">
                                <h5>El proceso de registro a terminado</h5>
                                <a href="<?= base_url() ?>" class="btn btn-light-indigo">Salir</a>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<footer class="page-footer footer footer-static footer-light navbar-border navbar-shadow" style="position:fixed; bottom: 0px; width: 100%;">
    <div class="footer-copyright">
        <div class="container"><span><?= isset(configInfo()['footer']) ? configInfo()['footer'] : '' ?></span></div>
    </div>
</footer>
<script>localStorage.setItem('url', '<?= base_url() ?>')</script>


<script src="<?= base_url() ?>/assets/js/vendors.min.js"></script>
<script src="<?= base_url() ?>/assets/js/jquery.formatter.min.js" ></script>
<script src="<?= base_url() ?>/assets/js/jquery.validate.js"></script>
<script src="https://unpkg.com/materialize-stepper@3.1.0/dist/js/mstepper.min.js"></script>
<script src="<?= base_url() ?>/assets/js/shepherd.min.js"></script>
<script src="<?= base_url() ?>/assets/js/plugins.min.js"></script>
<script src="<?= base_url() ?>/assets/js/search.min.js"></script>
<script src="<?= base_url() ?>/assets/js/select2.full.min.js"></script>
<script src="<?= base_url() ?>/assets/js/select2.full.min.js"></script>
<script src="<?= base_url() ?>/assets/js/chart.min.js"></script>
<script src="<?= base_url() ?>/assets/js/custom-script.min.js"></script>
<script src="<?= base_url() ?>/assets/js/ui-alerts.js"></script>
<script src="<?= base_url() ?>/assets/js/advance-ui-modals.js"></script>
<script src="<?= base_url() ?>/assets/js/additional-methods.js"></script>
<script src="<?= base_url() ?>/assets/js/form-wizard.js"></script>

