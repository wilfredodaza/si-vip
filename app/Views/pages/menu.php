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
                            <div class="card-title">Menu</div>
                        </div>
                    </div>
                    <div class="row center">
                        <?php foreach($modules as $module): ?>
                            <?php if($module->status == "Active"): ?>
                                <a href="#"  data-url="<?= base_url('menu') ?>" class="module" data-position="<?= $module->id ?>">
                                    <div class="col s12 m3" >
                                        <div class="card">
                                            <div class="card-content">
                                                <div class="center">
                                                    <img src="<?= base_url('assets/img/'.$module->img) ?>" alt="" style="width: 100%; display: block;">
                                                </div>
                                                <p class="center" style="color: #022858; height: 40px;"><b><?= $module->name ?></b></p>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
