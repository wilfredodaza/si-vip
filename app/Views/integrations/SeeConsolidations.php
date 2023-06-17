<?= $this->extend('layouts/main') ?>


<?= $this->section('title') ?> Facturación <?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    td {
        padding-top: 3px;
        padding-bottom: 3px;
        font-size: 12px;
    }
</style>


<div id="main">
    <div class="row" id="filters">
        <div class="col s12">
            <div class="container">
                <div class="section">
                    <div class="card">
                        <div class="card-content">
                            <div class="row">
                                <div class="col s12">
                                    <?= $this->include('layouts/alerts') ?>
                                </div>
                                <div class="col s12">
                                    <div class="divider"></div>
                                    <span class="card-title" style="margin-top: 20px;margin-bottom: 0px;">
                                         <a onclick="regresar()"
                                            class="btn btn-small grey lighten-5 grey-text text-darken-4 pull-right "
                                            style="margin-top: 10px; float: right;">
                                            regresar
                                            <i class="material-icons right">arrow_back</i>
                                        </a>
                                        Conciliaciones realizadas<br>

                                    </span>
                                    <small style="margin-bottom: 10px; display: block;">Aquí se podra observar los consolidados de las facturas</small>
                                    <div class="divider"></div>
                                    <div class="table-response" style="overflow-x:auto;">
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <td style="text-align: center;">Dia de creación</td>
                                                <td style="text-align: center;">Observación</td>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            foreach ($consolidations as $consolidation):
                                                ?>
                                                <tr>
                                                    <td style="text-align: center;"><?= $consolidation->created_at ?></td>
                                                    <td style="text-align: center;"><?= $consolidation->note ?></td>
                                                </tr>
                                            <?php
                                            endforeach;
                                            ?>
                                            </tbody>
                                        </table>
                                        <?php if(count($consolidations) == 0): ?>
                                            <p class="center red-text pt-1" >No hay ningún elemento.</p>
                                        <?php endif ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal acciones -->


<?= $this->endSection() ?>



<?= $this->section('scripts') ?>
<script src="<?= base_url('/js/advance-ui-modals.js') ?>"></script>
<script src="<?= base_url('/js/shepherd.min.js') ?>"></script>
<script src="<?= base_url('/js/vue.js') ?>"></script>
<script src="<?= base_url('/js/views/invoice.js') ?>"></script>
<script src="<?= base_url('/js/ui-alerts.js') ?>"></script>
<script>
    const regresar = () => {
        window.history.back();
    }
</script>
<?= $this->endSection() ?>

