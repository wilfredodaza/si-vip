<?php if(session('notification_package')): ?>
    <div class="card-alert card <?= session('notification_package')['type'] ?>">
        <div class="card-content white-text">
            <span class="card-title white-text darken-1">
                <i class="material-icons">notifications</i> Notificación</span>
            <p>  <?= session('notification_package')['message'] ?></p>
        </div>
        <button type="button" class="close white-text" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">×</span>
        </button>   
    </div>
<?php endif; ?>
<?php if(session('notification_resolution_date')): ?>
    <div class="card-alert card <?= session('notification_resolution_date')['type'] ?>">
        <div class="card-content white-text">
            <span class="card-title white-text darken-1">
                <i class="material-icons">notifications</i> Notificación</span>
            <p>  <?= session('notification_resolution_date')['message'] ?></p>
        </div>
        <button type="button" class="close white-text" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">×</span>
        </button>   
    </div>
<?php endif; ?>
<?php if(session('notification_resolution_number')): ?>
    <div class="card-alert card <?= session('notification_resolution_number')['type'] ?>">
        <div class="card-content white-text">
            <span class="card-title white-text darken-1">
                <i class="material-icons">notifications</i> Notificación</span>
            <p>  <?= session('notification_resolution_number')['message'] ?></p>
        </div>
        <button type="button" class="close white-text" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">×</span>
        </button>   
    </div>
<?php endif; ?>
<?php if(session('notification_subscription')): ?>
    <div class="card-alert card <?= session('notification_subscription')['type'] ?>">
        <div class="card-content white-text">
            <span class="card-title white-text darken-1">
                <i class="material-icons">notifications</i> Notificación</span>
            <p>  <?= session('notification_subscription')['message'] ?></p>
        </div>
        <button type="button" class="close white-text" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">×</span>
        </button>   
    </div>
<?php endif; ?>
