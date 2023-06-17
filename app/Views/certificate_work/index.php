<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?> Facturación <?= $this->endSection() ?>
<?= $this->section('content') ?>
<div id="main">
    <div class="row">
        <div class="col s12">
            <div class="container">
                <div class="section">
                    <div class="card">
                        <?php if(!is_null($validation) && $validation != 0): ?>
                            <iframe id="inlineFrameExample" title="Inline Frame Example" style="width: 100%;height:700px;border:none; padding:10px;" 
                                src="<?= base_url('work_certificate/pdf/'.$customer->customer_id) ?>">
                            </iframe>
                        <?php else: ?>
                            <div class="card-content">
                                <h3  class="center grey-text lighten-2">No se encuentra disponible</h1>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>