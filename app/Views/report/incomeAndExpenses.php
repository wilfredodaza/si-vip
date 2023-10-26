<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?> Ingresos y Egresos <?= $this->endSection() ?>
<?= $this->section('content') ?>

<div id="main">
    <div class="row">
        <div class="breadcrumbs-inline pt-3 pb-1" id="breadcrumbs-wrapper">
            <div class="container">
                <div class="row">
                    <div class="col s12">
                        <?= view('layouts/alerts') ?>
                    </div>
                    <div class="col s12 m12 l12 breadcrumbs-left">
                        <h5 class="breadcrumbs-title mt-0 mb-0 display-inline hide-on-small-and-down">
                            <span>
                                Informe de Movimientos
                            </span>
                        </h5>
                        <ol class="breadcrumbs mb-0">
                            <li class="breadcrumb-item"><a href="<?= base_url() ?>/home">Home</a></li>
                            <li class="breadcrumb-item active"><a href="#">Informe de Movimientos</a></li>
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
                            <div class="card-title">
                                <button data-target="filter" class="btn btn-small btn-light-indigo modal-trigger  right"
                                        style="margin-right: 5px;">
                                    Filtrar <i class="tiny material-icons right">filter_list</i>
                                </button>
                                <?php if (isset($_GET['start_date']) || isset($_GET['end_date']) || isset($_GET['customer'])
                                    || isset($_GET['number']) || isset($_GET['option'])): ?>
                                    <a href="<?= base_url('reports/incomeAndExpenses') ?>"
                                       class="btn right btn-light-red btn-small ml-1"
                                       style="padding-left: 10px;padding-right: 10px; margin-right: 10px; ">
                                        <i class="material-icons left">close</i>
                                        Quitar Filtro
                                    </a>
                                <?php endif; ?>
                                <br>
                            </div>
                            <div class="divider"></div>
                            <table class="table table-responsive">
                                <thead>
                                <tr>
                                    <th class="center">Fecha</th>
                                    <th class="center">Cliente</th>
                                    <th class="center"># Documento</th>
                                    <th class="center">Tipo de Documento</th>
                                    <th class="center">Metodo pago</th>
                                    <th class="center">Sede Origen</th>
                                    <th class="center">Sede Destino</th>
                                    <?php if(session('user')->companies_id != 69 || session('user')->role_id == 15): ?>
                                        <th class="center">Valor</th>
                                    <?php endif ?>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $balance = 0;
                                $count = 0;
                                $balanceE = 0;
                                $countE = 0;
                                $income = 0;
                                $expenses = 0;
                                $nameCompany = '';
                                $methodPayment = '';
                                foreach ($total as $value):
                                    $balance += $value->payable_amount;
                                    $count += 1;
                                endforeach;
                                foreach ($totalE as $item):
                                    $balanceE += $item->payable_amount - ($item->withholdings + $item->balance);
                                    $countE += 1;
                                endforeach;
                                ?>


                                <?php foreach ($info as $item):?>
                                    <tr>

                                        <!-- < if (($item->payable_amount - $item->withholdings - $item->balance - ($item->credit_note - $item->credit_note_withholdings)) <= 0) {
                                            statusPay($item->id);
                                        } ?> -->
                                        <td class="center"><?= $item->created_at ?></td>
                                        <td class="center"><?= ucwords($item->name) ?></td>
                                        <td class="center"><?= $item->resolution ?></td>
                                        <td class="center"><?= $item->name_document ?></td>
                                        <td class="center"><?= $item->method_payment ?></td>
                                        <td class="center"><?= $item->company ?></td>
                                        <td class="center"><?= $item->company_destination ?></td>
                                        
                                        <?php if(session('user')->companies_id != 69 || session('user')->role_id == 15): ?>
                                            <td class="center">
                                                $ <?= number_format($item->payable_amount, '2', ',', '.') ?></td>
                                            <td class="center">
                                                <div class="btn-group" role="group">
                                                    <a href="<?= base_url() ?>/reports/view/<?= $item->id ?>" target="_blank"
                                                    class="btn btn-small green darken-1  tooltipped" data-position="top" data-tooltip="ver detalle">
                                                        <i class="material-icons">insert_drive_file</i>
                                                    </a>
                                                </div>
                                            </td>
                                        <?php endif ?>
                                    </tr>
                                <?php endforeach; ?>
                                
                                <?php if(session('user')->companies_id != 69 || session('user')->role_id == 15): ?>
                                <tr>
                                    <th class="center" colspan="4">Ingresos : $ <?= (isset($_GET['option']) && $_GET['option'] == 'Egresos')?0:number_format($balance, '2', ',', '.') ?></th>
                                    <th class="center" colspan="4">Egresos : $ <?= (isset($_GET['option']) && $_GET['option'] == 'Ingresos')?0:number_format($balanceE, '2', ',', '.') ?></th>
                                </tr>
                                <?php endif ?>
                                <?php if (count($info) == 0): ?>
                                    <tr>
                                        <td colspan="4">
                                            <p class="center red-text py-2">No hay ningún elemento.</p>
                                        </td>
                                    </tr>
                                <?php endif ?>
                                </tbody>
                            </table>
                            <?= $pager->links() ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--modal de filtro de busqeuda-->
<form action="" method="get">
    <div id="filter" class="modal" role="dialog" style="height:auto; width: 600px">
        <div class="modal-content">
            <h4 class="modal-title">Filtrar</h4>
            <div class="row">
                <div class="col s12 m6 input-field">
                    <input type="date" id="start_date" name="start_date"
                           value="<?= $_GET['start_date'] ?? '' ?>">
                    <label for="start_date">Fecha de inicio</label>
                </div>
                <div class="col s12 m6 input-field">
                    <input id="end_date" type="date" name="end_date"
                           value="<?= $_GET['end_date'] ?? '' ?>">
                    <label for="end_date">Fecha fin</label>
                </div>
            </div>
            <div class="row">
                <div class="col s12 m6">
                    <label for="customer">Cliente</label>
                    <select class="browser-default" id="customer" name="customer">
                        <option value="">Seleccione ...</option>
                        <?php foreach ($customers as $customer) : ?>
                            <option value="<?= $customer->id ?>" <?= (isset($_GET['customer']) && $_GET['customer'] == $customer->id) ? 'selected' : '' ?>>
                                <?= $customer->name ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col s12 m6 input-field">
                    <input type="text" id="number" name="number"
                           value="<?= $_GET['number'] ?? '' ?>">
                    <label for="number"># Documento</label>
                </div>
            </div>
            <div class="row">
                <div class="col s12 m6">
                    <label for="company">Sede</label>
                    <select class="browser-default" id="company" name="company" <?= session('user')->role_id == 19 ? 'disabled' : '' ?>>
                        <option value="">Seleccione ...</option>
                        <?php foreach ($companies as $company) : ?>
                            <option value="<?= $company->id ?>" <?= (isset($_GET['company']) && $_GET['company'] == $company->id) ||  (session('user')->companies_id == $company->id && session('user')->role_id == 19) ? 'selected' : '' ?>>
                                <?= $company->company ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col s12 m6">
                    <label for="payment_method">Metodo de pago</label>
                    <select class="browser-default" id="payment_method" name="payment_method">
                        <option value="">Seleccione ...</option>
                        <?php foreach ($paymentMethod as $key) : ?>
                            <option value="<?= $key->id ?>" <?= (isset($_GET['payment_method']) && $_GET['payment_method'] == $key->id) ? 'selected' : '' ?>>
                                <?= $key->name ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col s12 m12">
                    <label for="option">Opciones</label>
                    <select class="" id="option" name="option">
                        <?php if(session('user')->role_id == 16): ?>
                            <option <?= session('user')->role_id == 16 ? 'selected' : '' ?>value="Ingresos">Ingresos</option>
                        <?php else: ?>
                            <option value="Todos">Todas</option>
                            <?php foreach($typeDocuments as $typeDocument): ?>
                                <option value="<?= $typeDocument->id ?>"><?= $typeDocument->name ?></option>
                            <?php endforeach; ?>
                        <?php endif ?>
                        <!-- <option <?= session('user')->role_id == 16 ? 'disabled' : '' ?> value="Egresos">Egresos</option> -->
                    </select>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-action modal-close waves-effect waves-red btn-flat mb-5 ">Cerrar</a>
            <button class="modals-action waves-effect waves-green btn indigo mb-5">Filtrar</button>
        </div>
    </div>
</form>
<!--end modal de filtro de busqeuda-->

<!--sprint loader-->
<div class="container-sprint-send">
    <div class="preloader-wrapper big active">
        <div class="spinner-layer spinner-blue-only">
            <div class="circle-clipper left">
                <div class="circle"></div>
            </div>
            <div class="gap-patch">
                <div class="circle"></div>
            </div>
            <div class="circle-clipper right">
                <div class="circle"></div>
            </div>
        </div>
    </div>
    <span class="text-insert"></span>
</div>
<!--end sprint loader -->
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
    $(".select2").select2({
        dropdownAutoWidth: true,
        width: '100%'
    });
</script>
<script src="<?= base_url('/js/shepherd.min.js') ?>"></script>
<script src="<?= base_url('/js/ui-alerts.js') ?>"></script>
<script src="<?= base_url('/js/advance-ui-modals.js') ?>"></script>
<script src="<?= base_url('/js/sprint.js') ?>"></script>
<?= $this->endSection() ?>
