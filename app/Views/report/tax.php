<?= view('layouts/header') ?>
<?= view('layouts/navbar_horizontal') ?>
<?= view('layouts/navbar_vertical') ?>


<style>
    td {
        padding-top: 3px;
        padding-bottom: 3px;
    }
</style>
<div id="main">
    <div class="row">
        <div class="col s12">
            <div class="container">
                <div class="section">
                    <?php if (session('success')): ?>
                        <div class="card-alert card green">
                            <div class="card-content white-text">
                                <?= session('success') ?>
                            </div>
                            <button type="button" class="close white-text" data-dismiss="alert"
                                    aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                    <?php endif; ?>
                    <?php if (session('errors')): ?>
                        <div class="card-alert card red">
                            <div class="card-content white-text">
                                <?= session('errors') ?>
                            </div>
                            <button type="button" class="close white-text" data-dismiss="alert"
                                    aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                    <?php endif; ?>
                    <div class="card">
                        <div class="card-content">
                            <div class="divider"></div>
                            <div class="row">
                                <div class="col s12 m6 " style="position: relative;">
                                    <div class="card-title"><h4>Informe de Impuestos</h4></div>
                                </div>
                                <div class="col m6 s6">
                                    <form action="" method="get" class="hide-on-small-only">
                                        <div class="row">
                                            <div class="col m4 s12 push-m8 push-l8">
                                                <a href="<?= base_url('/report') ?>" class="btn  float-right" style="margin-top: 20px"
                                                   data-toggle="modals">
                                                    Regresar
                                                </a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="divider"></div>
                            <div>
                                <div class="table-response" style="overflow-x:auto;">
                                    <table>
                                        <thead>
                                        <tr>
                                            <td style="text-align: center;">Nro</td>
                                            <td style="text-align: center;">Nit</td>
                                            <td style="text-align: center;">Nombre</td>
                                            <td style="text-align: center;">Producto</td>
                                            <td style="text-align: center;">Cantidad</td>
                                            <td style="text-align: center;">CTA</td>
                                            <td style="text-align: center;">IVA</td>
                                            <td style="text-align: center;">Base</td>
                                            <td style="text-align: center;">%</td>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($lineInvoices as $item): ?>
                                            <?php foreach ($item->account as $item2): ?>
                                                <?php if ($item2->type_accounting_account_id  != 1 && $item2->type_accounting_account_id != 4 && $item2->percent != 0): ?>
                                                    <tr>
                                                        <td style="text-align: center;"><?= $item->resolution ?></td>
                                                        <td style="text-align: center;"><?= $item->identification_number ?></td>
                                                        <td style="text-align: center;"><?= $item->customer_name ?></td>
                                                        <td style="text-align: center;"><?= $item->product_name ?></td>
                                                        <td style="text-align: center;"><?= $item->quantity ?></td>
                                                        <td style="text-align: center;"><?= $item2->account_name.'-'.$item2->code ?></td>
                                                        <td style="text-align: center;">$ <?= number_format(($item2->percent * $item->price_amount) / 100, '2', ',', '.')?></td>
                                                        <td style="text-align: center;">$ <?= number_format($item->price_amount , '2', ',', '.') ?></td>
                                                        <td style="text-align: center;"><?= $item2->percent ?> %</td>
                                                    </tr>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="<?= base_url() ?>/assets/js/vendors.min.js"></script>
<script src="<?= base_url() ?>/assets/js/select2.full.min.js"></script>
<script src="<?= base_url() ?>/assets/js/plugins.min.js"></script>
<script src="<?= base_url() ?>/assets/js/search.js"></script>
<script src="<?= base_url() ?>/assets/js/custom-script.min.js"></script>
<script src="<?= base_url() ?>/assets/js/form-select2.js"></script>
<script src="<?= base_url() ?>/assets/js/advance-ui-modals.js"></script>
<script src="<?= base_url() ?>/dropify/js/dropify.min.js"></script>
<script src="<?= base_url() ?>/assets/js/ui-alerts.js"></script>
<script>
    // Basic Select2 select
    $(document).ready(function () {


        $(".select2").select2({
            dropdownAutoWidth: true,
            width: '100%'
        });
        // Select With Icon
        $(".select2-icons").select2({
            dropdownAutoWidth: true,
            width: '100%',
            minimumResultsForSearch: Infinity,
            templateResult: iconFormat,
            templateSelection: iconFormat,
            escapeMarkup: function (es) {
                return es;
            }
        });

        // Format icon
        function iconFormat(icon) {
            var originalOption = icon.element;
            if (!icon.id) {
                return icon.text;
            }
            var $icon = "<i class='material-icons'>" + $(icon.element).data('icon') + "</i>" + icon.text;
            return $icon;
        }

        // Theme support
        $(".select2-theme").select2({
            dropdownAutoWidth: true,
            width: '100%',
            placeholder: "Classic Theme",
            theme: "classic"
        });
    });
</script>


</body>
</html>