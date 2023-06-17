<?= view('layouts/header') ?>
<?= view('layouts/navbar_horizontal') ?>
<?= view('layouts/navbar_vertical') ?>
    <!-- vista -->
    <style>
        .text-center {
            text-align: center;
        }

        td {
            padding: 3px 5px !important;
        }

        .container-sprint-email, .container-sprint-send {
            background: rgba(0, 0, 0, 0.51);
            z-index: 2000;
            position: absolute;
            width: 100%;
            top: 0px;
            height: 100vh;
            justify-content: center !important;
            align-content: center !important;
            flex-wrap: wrap;
            display: none;
        }
    </style>

    <!-- BEGIN: Page Main-->
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
                                    <span aria-hidden="true">x</span>
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
                                    <span aria-hidden="true">x</span>
                                </button>
                            </div>
                        <?php endif; ?>
                        <div class="card">
                            <div class="card-content">
                                <div class="divider"></div>
                                <div class="row">
                                    <div class="col s12 m12 text-center" style="position: relative;">
                                        <div class="card-title"><h4>Subir foto de producto
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <div class="divider"></div>
                                <br>
                                <div class="row">
                                    <form action="<?= base_url()?>/images/subir" method="post" enctype="multipart/form-data">
                                        <div class="col m6 s12">
                                            <div class="input-field col s12">
                                                <select name="product">
                                                    <option value="" disabled selected>Seleccione Producto</option>
                                                    <?php foreach($products as $product): ?>
                                                        <option value="<?= $product->id ?>" data-icon="<?= ($product->foto != '')?base_url().'/upload/products/'.$product->foto:base_url().'/upload/products/foto.png'?>"><?= $product->name ?></option>
                                                    <?php endforeach;?>
                                                </select>
                                                <label>Productos</label>
                                            </div>
                                        </div>
                                        <div class="col m6 s12">
                                            <input type="file" name="img" required class="dropify" data-height="300" />
                                        </div>

                                        <div class="col m12 s12">
                                            <button type="submit" class="btn btn-large" >Guardar</button>
                                        </div>
                                    </form>
                                </div>
                                <br>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--<div style="bottom: 50px; right: 19px;" class="fixed-action-btn direction-top"><a class="btn-floating btn-large gradient-45deg-light-blue-cyan gradient-shadow"><i class="material-icons">add</i></a>
            <ul>
                <li><a href="https://api.whatsapp.com/send?phone=+573013207088&text=Hola, tengo una duda en mifacturalegal." target="_blank" class="btn-floating light-green darken-3"><i class="fa fa-whatsapp"></i></a></li>
            </ul>
        </div>-->
    <!-- fin vista-->
<?= view('layouts/footer') ?>
<script>
    $('.dropify').dropify();
</script>

