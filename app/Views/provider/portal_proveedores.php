<?= $this->extend('layouts/main') ?>


<?= $this->section('title') ?> Facturación <?= $this->endSection() ?>
<?= $this->section('styles') ?>
<link rel="stylesheet" type="text/css" href="<?= base_url() ?>/app-assets/vendors/sweetalert/sweetalert.css">

<link rel="stylesheet" type="text/css" href="<?= base_url() ?>/app-assets/vendors/data-tables/css/jquery.dataTables.min.css">
  
<link rel="stylesheet" type="text/css" href="<?= base_url() ?>/app-assets/vendors/data-tables/extensions/responsive/css/responsive.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="<?= base_url() ?>/app-assets/vendors/data-tables/css/select.dataTables.min.css">

<link rel="stylesheet" type="text/css" href="<?= base_url() ?>/app-assets/css/pages/data-tables.css">
<link rel="stylesheet" type="text/css" href="<?= base_url() ?>/app-assets/vendors/dropify/css/dropify.min.css">

<link rel="stylesheet" href="<?= base_url() ?>/app-assets/vendors/select2/select2.min.css" type="text/css">
<link rel="stylesheet" href="<?= base_url() ?>/app-assets/vendors/select2/select2-materialize.css" type="text/css">
<link rel="stylesheet" type="text/css" href="<?= base_url() ?>/app-assets/css/pages/form-select2.css">
<?= $this->endSection() ?>


<?= $this->section('content') ?>
<div id="main">
    <div class="row">
    <div class="breadcrumbs-inline  pb-1" id="breadcrumbs-wrapper">
            <div class="container">
                <div class="row">
                    <div class="col s10 m6 l6 breadcrumbs-left">
                        <h4>Portal de Proveedores</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <?php foreach($status as $key => $statu): ?>
            <div class="col s12 m6 l6 xl3">
                <div class="card  <?= $statu->color ?>  gradient-shadow min-height-100 white-text animate fadeLeft">
                <div class="padding-4">
                    <div class="row">
                        <div class="col s7 m7">
                            <i class="material-icons background-round mt-5"><?= $statu->icon ?></i>
                            <p><?= $statu->name ?></p>
                        </div>
                        <div class="col s5 m5 right-align">
                            <h5 class="mb-0 white-text"><?= $statu->total ?></h5>
                            <p class="no-margin">Facturas</p>
                            <p>$<?= number_format($statu->suma, 0, ',', '.') ?></p>
                        </div>
                    </div>
                </div>
                </div>
            </div>
            <?php endforeach ?>
         <!-- <div class="col s12 m6 l6 xl4">
            <div class="card gradient-45deg-light-blue-cyan gradient-shadow min-height-100 white-text animate fadeLeft">
               <div class="padding-4">
                  <div class="row">
                     <div class="col s7 m7">
                        <i class="material-icons background-round mt-5">attach_money</i>
                        <p>Pendientes</p>
                     </div>
                     <div class="col s5 m5 right-align">
                        <h5 class="mb-0 white-text">13</h5>
                        <p class="no-margin">Facturas</p>
                        <p>$120.000.000</p>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="col s12 m6 l6 xl4">
            <div class="card  gradient-45deg-red-pink  gradient-shadow min-height-100 white-text animate fadeRight">
               <div class="padding-4">
                  <div class="row">
                     <div class="col s7 m7">
                        <i class="material-icons background-round mt-5">info_outline</i>
                        <p>Rechazadas</p>
                     </div>
                     <div class="col s5 m5 right-align">
                        <h5 class="mb-0 white-text">3</h5>
                        <p class="no-margin">Facturas</p>
                        <p>$30.000.000</p>
                     </div>
                  </div>
               </div>
            </div>
         </div> -->
         <!--<div class="col s12 m6 l6 xl3">
            <div class="card gradient-45deg-green-teal gradient-shadow min-height-100 white-text animate fadeRight">
               <div class="padding-4">
                  <div class="row">
                     <div class="col s7 m7">
                        <i class="material-icons background-round mt-5">attach_money</i>
                        <p>Profit</p>
                     </div>
                     <div class="col s5 m5 right-align">
                        <h5 class="mb-0 white-text">$890</h5>
                        <p class="no-margin">Today</p>
                        <p>$25,000</p>
                     </div>
                  </div>
               </div>
            </div>
         </div>-->
      </div>

    </div>
    <div class="row">
        <div class="col s12">

            <div class="card">
                <div class="card-content"  style="margin-bottom: 70px">
                    <div class="row">
                        <div class="col s12">
                            <ul class="tabs">
                                <?php foreach ($estados as $key => $estado): ?>
                                    <li class="tab col m3"><a class="<?= $key == 0 ? 'active': '' ?>" href="#status_<?= $estado->id ?>" onclick="reinit(`<?= $estado->id ?>`)"><?= $estado->name ?></a></li>
                                <?php endforeach ?>
                            </ul>
                        </div>
                        <?php foreach ($estados as $key => $estado): ?>
                            <div id="status_<?= $estado->id ?>" class="col s12 section-data-tables">
                                <table class="display" id="table_<?= $estado->id ?>">
                                    <thead>
                                        <tr>
                                            <?php if($estado->id == 19 || $estado->id == 'todos'): ?>
                                                <th>#</th>
                                                <th>Factura</th>
                                                <th>Fecha</th>
                                                <th>Valor</th>
                                                <th>D. Requeridos</th>
                                                <th>Estado</th>
                                                <th>Vence</th>
                                                <th>Acciones</th>
                                            <?php elseif($estado->id == 20 || $estado->id == 21): ?>
                                                <th>#</th>
                                                <th>Factura</th>
                                                <th>Fecha Factura</th>
                                                <th>Fecha <br> de Radicacion</th>
                                                <th>Fecha vence</th>
                                                <th>Condición <br> de pago</th>
                                                <th>Valor <br> a Pagar</th>
                                                <th>Fecha <br><?= $estado->id == 20 ? 'de Rechazo':'programada' ?></th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            <?php endif ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <!-- <tfoot>
                                        <tr>
                                            <?php if($estado->id == 19 || $estado->id == 'todos'): ?>
                                                <th>#</th>
                                                <th>Factura</th>
                                                <th>Fecha</th>
                                                <th>Valor</th>
                                                <th>D. Requeridos</th>
                                                <th>Estado</th>
                                                <th>Vence</th>
                                                <th>Acciones</th>
                                            <?php elseif($estado->id == 20 || $estado->id == 21): ?>
                                                <th>#</th>
                                                <th>Factura</th>
                                                <th>Fecha Factura</th>
                                                <th>Fecha <br> de Radicacion</th>
                                                <th>Fecha vence</th>
                                                <th>Condición <br> de pago</th>
                                                <th>Valor <br> a Pagar</th>
                                                <th>Fecha <br><?= $estado->id == 20 ? 'de Rechazo':'programada' ?></th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            <?php endif ?>
                                        </tr>
                                    </tfoot> -->
                                </table>
                            </div>
                        <?php endforeach ?>
                        
                    </div>
                    <!-- <div class="row">
                        <div class="col s12">
                            <ul class="tabs">
                            <li class="tab col m3"><a class="active" href="#test1">1</a></li>
                            <li class="tab col m3"><a href="#test2">Test 2</a></li>
                            <li class="tab col m3"><a href="#test3">3</a></li>
                            <li class="tab col m3"><a href="#test4">Test 4</a></li>
                            </ul>
                        </div>
                        <div id="test1" class="col s12">
                            <table class="bordered striped centered responsive-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Factura</th>
                                        <th>Fecha</th>
                                        <th>Valor</th>
                                        <th>E. almacen</th>
                                        <th>OC</th>
                                        <th>Remisión</th>
                                        <th>Estado</th>
                                        <th>Vence</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th>1</th>
                                        <td>1005</td>
                                        <td>2022-01-12</td>
                                        <td>$10.000.000</td>
                                        <td>
                                            <a class="waves-effect waves-light  modal-trigger" href="#modal1"><i class="material-icons text-green green-text">check</i></a>
                                        </td>
                                        <td>  <a class="waves-effect waves-light  modal-trigger" href="#modal1"><i class="material-icons text-green green-text">check</i></a></td>
                                        <td>  <a class="waves-effect waves-light  modal-trigger" href="#modal1"><i class="material-icons text-green green-text">check</i></a></td>
                                
                                        <td>
                                            <span class="new badge yellow darken-2 gradient-shadow" data-badge-caption="Sin revisar"></span>
                                        </td>
                                        <td>24h <i class="material-icons text-green green-text tiny">brightness_1</i></td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?=  base_url('providers/show') ?>" class="btn indigo btn-small"><i class="material-icons">add</i></a>    
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>2</th>
                                        <td>1006</td>
                                        <td>2022-01-12</td>
                                        <td>$10.000.000</td>
                                        <td>
                                        <a class="waves-effect waves-light  modal-trigger" href="#modal1"><i class="material-icons text-green green-text">check</i></a>
                                        </td>
                                        <td>  <a class="waves-effect waves-light  modal-trigger" href="#modal1"><i class="material-icons text-green green-text">check</i></a></td>
                                        <td><a class="waves-effect waves-light  modal-trigger" href="#modal1"><i class="material-icons text-green red-text">close</i></a></td>
                                
                                        <td>
                                            <span class="new badge red  gradient-shadow" data-badge-caption="Pendiente"></span>
                                        </td>
                                        <td>20h <i class="material-icons text-green green-text tiny">brightness_1</i></td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?=  base_url('providers/show') ?>" class="btn indigo btn-small"><i class="material-icons">add</i></a>    
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>3</th>
                                        <td>1010</td>
                                        <td>2022-01-12</td>
                                        <td>$10.000.000</td>
                                        <td>
                                            <a class="waves-effect waves-light  modal-trigger" href="#modal1"><i class="material-icons text-green red-text">close</i></a>
                                        </td>
                                        <td><a class="waves-effect waves-light  modal-trigger" href="#modal1"><i class="material-icons text-green red-text">close</i></a></td>
                                        <td><a class="waves-effect waves-light  modal-trigger" href="#modal1"><i class="material-icons text-green red-text">close</i></a></td>
                                
                                        <td>
                                        <span class="new badge red  gradient-shadow" data-badge-caption="Pendiente"></span>
                                        </td>
                                        <td>12h <i class="material-icons text-yellow yellow-text tiny">brightness_1</i></td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?=  base_url('providers/show') ?>" class="btn indigo btn-small"><i class="material-icons">add</i></a>    
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>4</th>
                                        <td>1025</td>
                                        <td>2022-01-12</td>
                                        <td>$10.000.000</td>
                                        <td>
                                            <a class="waves-effect waves-light  modal-trigger" href="#modal1"><i class="material-icons text-green red-text">close</i></a>
                                        </td>
                                        <td>  <a class="waves-effect waves-light  modal-trigger" href="#modal1"><i class="material-icons text-green green-text">check</i></a></td>
                                        <td><a class="waves-effect waves-light  modal-trigger" href="#modal1"><i class="material-icons text-green red-text">close</i></a></td>
                                
                                        <td>
                                            <span class="new badge yellow darken-2 gradient-shadow" data-badge-caption="Por revisar"></span>
                                        </td>
                                        <td>10h <i class="material-icons text-yellow yellow-text tiny">brightness_1</i></td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?=  base_url('providers/show') ?>" class="btn indigo btn-small"><i class="material-icons">add</i></a>    
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>5</th>
                                        <td>1026</td>
                                        <td>2022-01-12</td>
                                        <td>$10.000.000</td>
                                        <td>
                                            <a class="waves-effect waves-light  modal-trigger" href="#modal1"><i class="material-icons text-green red-text">close</i></a>
                                        </td>
                                        <td><a class="waves-effect waves-light  modal-trigger" href="#modal1"><i class="material-icons text-green red-text">close</i></a></td>
                                        <td>  <a class="waves-effect waves-light  modal-trigger" href="#modal1"><i class="material-icons text-green green-text">check</i></a></td>
                                
                                        <td>
                                        <span class="new badge red  gradient-shadow" data-badge-caption="Pendiente"></span>
                                        </td>
                                        <td>11h <i class="material-icons text-yellow yellow-text tiny">brightness_1</i></td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?=  base_url('providers/show') ?>" class="btn indigo btn-small"><i class="material-icons">add</i></a>    
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>6</th>
                                        <td>1027</td>
                                        <td>2022-01-12</td>
                                        <td>$10.000.000</td>
                                        <td>
                                        <a class="waves-effect waves-light  modal-trigger" href="#modal1"><i class="material-icons text-green green-text">check</i></a>
                                        </td>
                                        <td><a class="waves-effect waves-light  modal-trigger" href="#modal1"><i class="material-icons text-green red-text">close</i></a></td>
                                        <td><a class="waves-effect waves-light  modal-trigger" href="#modal1"><i class="material-icons text-green red-text">close</i></a></td>
                                
                                        <td>
                                            <span class="new badge yellow darken-2 gradient-shadow" data-badge-caption="Sin revisar"></span>
                                        </td>
                                        <td>4h <i class="material-icons text-green red-text tiny">brightness_1</i></td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?=  base_url('providers/show') ?>" class="btn indigo btn-small"><i class="material-icons">add</i></a>    
                                            </div>
                                        </td>
                                    </tr>


                                </tbody>
                            </table>
                            <ul class="pagination">
                                <li class="disabled"><a href="#!"><i class="mdi-navigation-chevron-left"></i></a></li>
                                <li class="active"><a href="#!">1</a></li>
                                <li class="waves-effect"><a href="#!">2</a></li>
                                <li class="waves-effect"><a href="#!">3</a></li>
                                <li class="waves-effect"><a href="#!">4</a></li>
                                <li class="waves-effect"><a href="#!">5</a></li>
                                <li class="waves-effect"><a href="#!"><i class="mdi-navigation-chevron-right"></i></a></li>
                            </ul>
                        </div>
                        <div id="test2" class="col s12">
                            <table class="bordered striped centered responsive-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Factura</th>
                                        <th>Fecha <br> Factura</th>
                                        <th>Fecha  de  <br> Radicación</th>
                                        <th>Fecha  <br> Vence</th>
                                        <th>Condición <br>de  Pago</th>
                                        <th>Valor <br> a Pagar</th>
                                        <th>Fecha <br> programación</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th>1</th>
                                        <td>1004</td>
                                        <td>2022-01-15</td>
                                        <td>2022-01-16</td>
                                        <td>2022-01-17</td>
                                        <td>
                                            2D
                                        </td>
                                        <td>$1.299.000</td>
                                        <td>2022-01-24</td>
                                        <td>     <span class="new badge green gradient-shadow" data-badge-caption="Pagada"></span></td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?=  base_url('providers/show') ?>" class="btn indigo btn-small"><i class="material-icons">add</i></a>    
                                                <a href="#modal5"  class="btn  btn-small  modal-trigger">
                                                    <i class="material-icons icon-small">
                                                        monetization_on
                                                    </i>
                                                </a>    
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>2</th>
                                        <td>1001</td>
                                        <td>2022-01-15</td>
                                        <td>2022-01-16</td>
                                        <td>2022-01-18</td>
                                        <td>
                                            3D
                                        </td>
                                        <td>$1.500.000</td>
                                        <td>2022-01-24</td>
                                        <td>     <span class="new badge indigo gradient-shadow" data-badge-caption="Tesorería"></span></td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?=  base_url('providers/show') ?>" class="btn indigo btn-small"><i class="material-icons">add</i></a>    
                                                <a href="#modal5"  class="btn  btn-small  modal-trigger">
                                                    <i class="material-icons icon-small">
                                                        monetization_on
                                                    </i>
                                                </a>    
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>3</th>
                                        <td>1003</td>
                                        <td>2022-01-15</td>
                                        <td>2022-01-16</td>
                                        <td>2022-01-16</td>
                                        <td>
                                            1D
                                        </td>
                                        <td>$1.300.000</td>
                                        <td>2022-01-24</td>
                                        <td>     <span class="new badge purple gradient-shadow" data-badge-caption="Contabilidad"></span></td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?=  base_url('providers/show') ?>" class="btn indigo btn-small"><i class="material-icons">add</i></a>
                                                <a href="#modal5"  class="btn  btn-small  modal-trigger">
                                                    <i class="material-icons icon-small">
                                                        monetization_on
                                                    </i>
                                                </a>    
                                            </div>
                                        </td>
                                    </tr>
                                    

                                </tbody>
                            </table>
                            <ul class="pagination">
                                <li class="disabled"><a href="#!"><i class="mdi-navigation-chevron-left"></i></a></li>
                                <li class="active"><a href="#!">1</a></li>
                                <li class="waves-effect"><a href="#!">2</a></li>
                                <li class="waves-effect"><a href="#!">3</a></li>
                                <li class="waves-effect"><a href="#!">4</a></li>
                                <li class="waves-effect"><a href="#!">5</a></li>
                                <li class="waves-effect"><a href="#!"><i class="mdi-navigation-chevron-right"></i></a></li>
                            </ul>
                        </div>
                        <div id="test3" class="col s12">
                            <table class="bordered striped centered responsive-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Factura</th>
                                        <th>Fecha <br> Factura</th>
                                        <th>Fecha  de  <br> Radicación</th>
                                        <th>Fecha  <br> Vence</th>
                                        <th>Condición <br>de  Pago</th>
                                        <th>Valor <br> a Pagar</th>
                                        <th>Fecha de <br> Rechazo</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th>1</th>
                                        <td>1004</td>
                                        <td>2022-01-15</td>
                                        <td>2022-01-16</td>
                                        <td>2022-01-17</td>
                                        <td>
                                            2D
                                        </td>
                                        <td>$1.299.000</td>
                                        <td>2022-01-24</td>
                                        <td>  <span class="new badge red gradient-shadow" data-badge-caption="Rechazada"></span></td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?=  base_url('providers/show') ?>" class="btn indigo btn-small"><i class="material-icons">add</i></a>    
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>2</th>
                                        <td>1001</td>
                                        <td>2022-01-15</td>
                                        <td>2022-01-16</td>
                                        <td>2022-01-18</td>
                                        <td>
                                            3D
                                        </td>
                                        <td>$1.500.000</td>
                                        <td>2022-01-24</td>
                                        <td>       <span class="new badge red gradient-shadow" data-badge-caption="Rechazada"></span></td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?=  base_url('providers/show') ?>" class="btn indigo btn-small"><i class="material-icons">add</i></a>    
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>3</th>
                                        <td>1003</td>
                                        <td>2022-01-15</td>
                                        <td>2022-01-16</td>
                                        <td>2022-01-16</td>
                                        <td>
                                            1D
                                        </td>
                                        <td>$1.300.000</td>
                                        <td>2022-01-24</td>
                                        <td>     <span class="new badge red gradient-shadow" data-badge-caption="Rechazada"></span></td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?=  base_url('providers/show') ?>" class="btn indigo btn-small"><i class="material-icons">add</i></a>    
                                                
                                            </div>
                                        </td>
                                    </tr>
                                    

                                </tbody>
                            </table>
                            <ul class="pagination">
                                <li class="disabled"><a href="#!"><i class="mdi-navigation-chevron-left"></i></a></li>
                                <li class="active"><a href="#!">1</a></li>
                                <li class="waves-effect"><a href="#!">2</a></li>
                                <li class="waves-effect"><a href="#!">3</a></li>
                                <li class="waves-effect"><a href="#!">4</a></li>
                                <li class="waves-effect"><a href="#!">5</a></li>
                                <li class="waves-effect"><a href="#!"><i class="mdi-navigation-chevron-right"></i></a></li>
                            </ul>
                        </div>
                        <div id="test4" class="col s12">
                            <table class="bordered striped centered responsive-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Factura</th>
                                        <th>Fecha <br> Factura</th>
                                        <th>Fecha  de  <br> Radicación</th>
                                        <th>Fecha  <br> Vence</th>
                                        <th>Condición <br>de  Pago</th>
                                        <th>Valor <br> a Pagar</th>
                                        <th>Fecha <br> programación</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th>1</th>
                                        <td>1004</td>
                                        <td>2022-01-15</td>
                                        <td>2022-01-16</td>
                                        <td>2022-01-17</td>
                                        <td>
                                            2D
                                        </td>
                                        <td>$1.299.000</td>
                                        <td>2022-01-24</td>
                                        <td>     <span class="new badge green gradient-shadow" data-badge-caption="Pagada"></span></td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?=  base_url('providers/show') ?>" class="btn indigo btn-small"><i class="material-icons">add</i></a>    
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>2</th>
                                        <td>1001</td>
                                        <td>2022-01-15</td>
                                        <td>2022-01-16</td>
                                        <td>2022-01-18</td>
                                        <td>
                                            3D
                                        </td>
                                        <td>$1.500.000</td>
                                        <td>2022-01-24</td>
                                        <td>     <span class="new badge indigo gradient-shadow" data-badge-caption="Tesorería"></span></td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?=  base_url('providers/show') ?>" class="btn indigo btn-small"><i class="material-icons">add</i></a>    
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>3</th>
                                        <td>1003</td>
                                        <td>2022-01-15</td>
                                        <td>2022-01-16</td>
                                        <td>2022-01-16</td>
                                        <td>
                                            1D
                                        </td>
                                        <td>$1.300.000</td>
                                        <td>2022-01-24</td>
                                        <td>     <span class="new badge purple gradient-shadow" data-badge-caption="Contabilidad"></span></td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?=  base_url('providers/show') ?>" class="btn indigo btn-small"><i class="material-icons">add</i></a>    
                                            </div>
                                        </td>
                                    </tr>
                                    

                                </tbody>
                            </table>
                            <ul class="pagination">
                                <li class="disabled"><a href="#!"><i class="mdi-navigation-chevron-left"></i></a></li>
                                <li class="active"><a href="#!">1</a></li>
                                <li class="waves-effect"><a href="#!">2</a></li>
                                <li class="waves-effect"><a href="#!">3</a></li>
                                <li class="waves-effect"><a href="#!">4</a></li>
                                <li class="waves-effect"><a href="#!">5</a></li>
                                <li class="waves-effect"><a href="#!"><i class="mdi-navigation-chevron-right"></i></a></li>
                            </ul>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>
    </div>
</div>

<div id="retenciones" class="modal modal-fixed-footer">
    <div class="modal-content">
        <div class="row">
            <div class="col s12  section-data-tables">
                <h6>Retenciones</h6>
                <table id="table-retenciones">
                    <thead>
                        <tr>
                            <th style=" padding: 5px !important;">Impuesto</th>
                            <th style=" padding: 5px !important;">Valor</th>
                        </tr>
                    </thead>
                        <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <a href="#!" class="modal-action modal-close waves-effect waves-purple btn-flat ">Cerrar</a>
    </div>
</div>

<div id="modal5" class="modal modal-fixed-footer" style="height: 300px;">
    <div class="modal-content">
        <div class="row">
            <div class="col s12">
                <h6>Retenciones</h6>
                <table class="bordered striped centered responsive-table">
                    <thead>
                        <tr>
                            <th style=" padding: 5px !important;">Impuesto</th>
                            <th style=" padding: 5px !important;">Sigla</th>
                            <th style=" padding: 5px !important;">Valor</th>
                        </tr>
                    </thead>
                        <tbody>
                        <tr>
                            <td style=" padding: 5px !important;">Rete Fuente</td>
                            <td style=" padding: 5px !important;">RTF</td>
                            <td style=" padding: 5px !important;">$ 20.000</td>
                        </tr>
                        <tr>
                            <td style=" padding: 5px !important;">Rete IVA</td>
                            <td style=" padding: 5px !important;">RTFIVA</td>
                            <td style=" padding: 5px !important;">$ 10.000</td>
                        </tr>
                        <tr>
                            <td style=" padding: 5px !important;">Rete ICA</td>
                            <td style=" padding: 5px !important;">RTFICA</td>
                            <td style=" padding: 5px !important;">$ 50.000</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <a href="#!" class="modal-action modal-close waves-effect waves-purple btn-flat ">Cerrar</a>
    </div>
</div>

<div id="modal1" class="modal modal-fixed-footer" style="height: 700px !important;">
    <div class="modal-content">
        <div class="row">
            <div class="input-field col s12">
                <h6>Agregar Información</h6>
                <input placeholder="Entrada Almacen" id="first_name" type="text">
            </div>
            
            <div class="input-field col s12">
                <textarea id="textarea1" class="materialize-textarea" data-length="120"></textarea>
                <label for="textarea1">Observaciones</label>
            </div>
            <div class="col s12">
                <button class="btn indigo right">Guardar</button>
            </div>
            <div class="col s12">
            <table class="bordered striped centered responsive-table">
                    <tr>
                        <th style=" padding: 5px !important;">#</th>
                        <td style=" padding: 5px !important;">Número</td>
                        <td style=" padding: 5px !important;">Observación</td>
                    </tr>
                    <tr>
                        <th style=" padding: 5px !important;">1</th>
                        <td style=" padding: 5px !important;">8abd9f754d8fg</td>
                        <td style=" padding: 5px !important;">Entrada verifica</td>
                    </tr>
                    <tr>
                        <th style=" padding: 5px !important;">2</th>
                        <td style=" padding: 5px !important;">51089f2w4d8fg</td>
                        <td style=" padding: 5px !important;">Entrada verifica</td>
                    </tr>
                    <tr>
                        <th style=" padding: 5px !important;">3</th>
                        <td style=" padding: 5px !important;">7a8d9f7w4d8fg</td>
                        <td style=" padding: 5px !important;">Entrada verifica</td>
                    </tr>
                </table>
            </div>

            <div class="col s3">
                <p>
                    <label>
                        <input name="group1" type="radio" checked />
                        <span>Aceptada</span>
                    </label>
                </p>
                </div>
                <div class="col s9">
                <p>
                    <label>
                        <input name="group1" type="radio" />
                        <span>Rechazada</span>
                    </label>
                </p>
            </div>
            <div class="col s12">
                <h6>Datos de Entrada</h6>
                <table class="bordered striped centered responsive-table">
                    <tr>
                        <th style=" padding: 5px !important;">#</th>
                        <td style=" padding: 5px !important;">Cliente</td>
                        <td style=" padding: 5px !important;">Fecha</td>
                    </tr>
                    <tr>
                        <th style=" padding: 5px !important;">FEV-1026</th>
                        <td style=" padding: 5px !important;"> Pepito Perez</td>
                        <td style=" padding: 5px !important;">12-20-2021</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <a href="#!" class="modal-action modal-close waves-effect waves-purple btn-flat ">Cerrar</a>
        <a href="#!" class="modal-action btn purple modal-close waves-effect ">Guardar</a>
    </div>
</div>

<div id="modal2" class="modal modal-fixed-footer" style="height: 200px !important; width: 450px !important;">
    <div class="modal-content">
        <h6>Aceptar Documento</h6>
        Por favor valide que la información se correcta antes de dar clic en aceptar.
    </div>
    <div class="modal-footer">
        <a href="#!" class="modal-action modal-close waves-effect waves-purple btn-flat ">Cancelar</a>
        <a href="#!" class="modal-action btn purple modal-close waves-effect ">Aceptar</a>
    </div>
</div>

<div id="modal3" class="modal modal-fixed-footer" style="height: 200px !important; width: 450px !important;">
    <div class="modal-content">
        <h6>Rechazar Documento</h6>
        Por favor valide que la información se correcta antes de dar clic en aceptar.
    </div>
    <div class="modal-footer">
        <a href="#!" class="modal-action modal-close waves-effect waves-purple btn-flat ">Cancelar</a>
        <a href="#!" class="modal-action btn purple modal-close waves-effect ">Aceptar</a>
    </div>
</div>



<div id="modal4" class="modal modal-fixed-footer" style="height: 700px !important;">
    <div class="modal-content">
        <h6>Radicados</h6>
        <div class="row">  
            <div class="col s12">
                <label>Sistema</label>
                <select class="browser-default">
                    <option value="" disabled selected>Seleccione una opción</option>
                    <option value="1">Workmanager</option>
                    <option value="2">UNOE</option>
                    <option value="2">Otros</option>
                </select>
            </div>
            <div class="input-field col s12">
        
                <input placeholder="Numero de Radicado" id="first_name" type="text">
            </div>
            <div class="input-field col s12">
                <textarea id="textarea1" class="materialize-textarea" data-length="120"></textarea>
                <label for="textarea1">Observaciones</label>
            </div>

            <div class="col s12">
                <button class="btn indigo right">Guardar</button>
            </div>
            <div class="col s12">
            <table class="bordered striped centered responsive-table">
                    <tr>
                        <th style=" padding: 5px !important;">#</th>
                        <td style=" padding: 5px !important;">Fecha</td>
                        <td style=" padding: 5px !important;">Número</td>
                        <td style=" padding: 5px !important;">Sistema</td>
                        <td style=" padding: 5px !important;">Observaciones</td>

                    </tr>
                    <tr>
                        <th style=" padding: 5px !important;">1</th>
                        <td style=" padding: 5px !important;">23/12/2021</td>
                        <td style=" padding: 5px !important;">8abd9f754d8fg</td>
                        <td style=" padding: 5px !important;">UNOE</td>
                        <td style=" padding: 5px !important;">Entrada verifica</td>
                    </tr>
                    <tr>
                        <th style=" padding: 5px !important;">2</th>
                        <td style=" padding: 5px !important;">23/12/2021</td>
                        <td style=" padding: 5px !important;">51089f2w4d8fg</td>
                        <td style=" padding: 5px !important;">Otros</td>
                        <td style=" padding: 5px !important;">Entrada verifica</td>
                    </tr>
                    <tr>
                        <th style=" padding: 5px !important;">3</th>
                        <td style=" padding: 5px !important;">23/12/2021</td>
                        <td style=" padding: 5px !important;">7a8d9f7w4d8fg</td>
                        <td style=" padding: 5px !important;">Workmanager</td>
                        <td style=" padding: 5px !important;">Entrada verifica</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <a href="#!" class="modal-action modal-close waves-effect waves-purple btn-flat ">Cerrar</a>
        <a href="#!" class="modal-action btn purple modal-close waves-effect ">Guardar</a>
    </div>
</div>






<?= $this->endSection() ?>


<?= $this->section('scripts') ?>
    <script src="<?= base_url('/js/advance-ui-modals.js') ?>"></script>
    <script src="<?= base_url() ?>/app-assets/vendors/data-tables/js/jquery.dataTables.min.js"></script>
    <script src="<?= base_url() ?>/app-assets/vendors/data-tables/extensions/responsive/js/dataTables.responsive.min.js"></script>
    <script src="<?= base_url() ?>/app-assets/vendors/data-tables/js/dataTables.select.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?= base_url() ?>/assets/js/new_scripts/funciones.js"></script>
    <script src="<?= base_url() ?>/app-assets/vendors/dropify/js/dropify.min.js"></script>
    <script src="<?= base_url() ?>/app-assets/js/scripts/form-file-uploads.js"></script>

    <script src="<?= base_url() ?>/app-assets/vendors/select2/select2.full.min.js"></script>
    <script src="<?= base_url() ?>/app-assets/js/scripts/form-select2.min.js"></script>
    <script>
        const table = [];
        const table_observation = [];
        const table_products = [];
        $(document).ready(function(){
            var estados = <?= json_encode($estados) ?>;
            estados.forEach(estado =>{
                if(estado.id == 19 || estado.id == 'todos'){
                    table[estado.id] = $(`#table_${estado.id}`).DataTable(
                        {
                            "ajax": {
                                "url": `<?= base_url() ?>/providers/table/${estado.id}`,
                                "dataSrc":""
                            },
                            "columns": [
                                { data: 'numero' },
                                { data: 'resolution' },
                                { data: 'created_at' },
                                { data: 'valor' },
                                { data: 'd_requiridos' },
                                { data: 'status_name' },
                                { data: 'vence' },
                                { data: 'action' },
                            ],
                            "responsive": false,
                            "scrollX": true,
                            "ordering": false,
                            language: { url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"},
                            initComplete: function(){
                            $('.material-tooltip').remove();
                                $('.tooltipped').tooltip();
                            }
                        });
                }else if(estado.id == 20 || estado.id == 21){
                    table[estado.id] = $(`#table_${estado.id}`).DataTable(
                    {
                        "ajax": {
                            "url": `<?= base_url() ?>/providers/table/${estado.id}`,
                            "dataSrc":""
                        },
                        "columns": [
                            { data: 'numero' },
                            { data: 'resolution' },
                            { data: 'created_at' },
                            { data: 'fecha_radicado' },
                            { data: 'payment_due_date' },
                            { data: 'vence' },
                            { data: 'valor' },
                            { data: 'fecha_aux' },
                            { data: 'status_name' },
                            { data: 'action' },
                        ],
                        "responsive": false,
                        "scrollX": true,
                        "ordering": false,
                        language: { url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"},
                        initComplete: function(){
                        $('.material-tooltip').remove();
                            $('.tooltipped').tooltip();
                        }
                    });
                }
            });
        });
        function reinit(id){
            table[`${id}`].ajax.reload(
                function(){
                    $('.material-tooltip').remove();
                    $('.tooltipped').tooltip();
                });
        }

        function retenciones(id){
            $('#retenciones').modal('open');
            if(table_products['table']  != undefined) table_products['table'] .ajax.url(`<?= base_url() ?>/providers/table_taxes/${id}`).load(function(){
                    $('.material-tooltip').remove();
                    $('.tooltipped').tooltip();
                    $('.dropdown-trigger').dropdown({
                        inDuration: 300,
                        outDuration: 225,
                        constrainWidth: false, // Does not change width of dropdown to that of the activator
                        hover: false, // Activate on hover
                        gutter: 0, // Spacing from edge
                        coverTrigger: true, // Displays dropdown below the button
                        alignment: 'left', // Displays dropdown with edge aligned to the left of button
                        stopPropagation: false // Stops event propagation
                    });
                });
        else{
            table_products['table'] = $(`#table-retenciones`).DataTable({
                "ajax": {
                    "url": `<?= base_url() ?>/providers/table_taxes/${id}`,
                    "dataSrc":""
                },
                "columns": [
                    { data: 'name' },
                    { data: 'valor' },
                ],
                pageLength : 5,
                lengthMenu: [[5, 10, 20, -1], [5, 10, 20, 'Todos']],
                searching: false,
                "responsive": false,
                "scrollX": true,
                "ordering": false,
                language: { url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"},
            });
        }
        }
    </script>

<?= $this->endSection() ?>