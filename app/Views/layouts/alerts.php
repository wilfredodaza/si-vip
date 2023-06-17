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
<?php if (session('warning')): ?>
    <div class="card-alert card yellow darken-2">
        <div class="card-content white-text">
            <?= session('warning') ?>
        </div>
        <button type="button" class="close white-text" data-dismiss="alert"
                aria-label="Close">
            <span aria-hidden="true">×</span>
        </button>
    </div>
<?php endif; ?>