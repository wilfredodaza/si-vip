<?= view('layouts/header') ?>
<?= view('layouts/navbar_horizontal') ?>
<?= view('layouts/navbar_vertical') ?>

<!-- BEGIN: Page Main-->
<div id="main">
    <div class="row">
        <div class="col s12">
            <div class="container">
                <div class="section">
                    <?= view('layouts/alerts') ?>
                    <div class="card">
                        <div class="card-content">
                            <h4 class="card-title"><?= $title ?></h4>
                            <p><?= $subtitle ?></p>
                                <?=  $output ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div style="bottom: 50px; right: 19px;" class="fixed-action-btn direction-top"><a class="btn-floating btn-large gradient-45deg-light-blue-cyan gradient-shadow"><i class="material-icons">add</i></a>
        <ul>
            <li><a href="https://api.whatsapp.com/send?phone=+573013207088&text=Hola, tengo una duda en mifacturalegal." target="_blank" class="btn-floating light-green darken-3"><i class="fa fa-whatsapp"></i></a></li>
        </ul>
    </div>

<?= view('layouts/footer_grocery') ?>