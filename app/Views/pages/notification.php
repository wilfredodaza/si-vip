<?= view('layouts/header') ?>
<?= view('layouts/navbar_horizontal') ?>
<?= view('layouts/navbar_vertical') ?>


<!-- BEGIN: Page Main-->
<div id="main">
    <div class="row">
        <div class="col s12">


            <div class="container">
                <div class="card">
                    <div class="card-content"  >
                        <div style="display: flex; align-items: center;">
                        <i class="material-icons">notifications_none</i> <div style="padding-left: 20px; font-size: 20px;">Notificaciones</div>
                        </div>
                    </div>
                </div>

                <div class="section">
                    <ul class="collapsible">
                        <?php foreach (notification('all') as $item): ?>
                        <li>
                            <div class="collapsible-header"><i class="material-icons"><?= $item->icon ?></i><?= $item->title ?></div>
                            <div class="collapsible-body" <?= (isset($_GET['nota']) and $_GET['nota'] == $item->id) ? 'style="display: block;"' : '' ?>><span><?= $item->body ?></span></div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>


    <?= view('layouts/footer') ?>
