<?= view('layouts/header') ?>
<?= view('layouts/navbar_horizontal') ?>
<?= view('layouts/navbar_vertical') ?>
<!-- vista general -->
<div id="main">
    <div class="row">
        <div class="col s12">
            <div class="container">
                <div class="section">
                <div class="card">
                        <?php if($caja->role_id == 2):?>
                        <div class="card-content">
                            <div class="divider"></div>
                            <div class="row">
                            <div class="col s12 m4 " style="">
                                    <div class="card-title text-center"><?= $company->company ;?></div>
                                </div>
                                <div class="col s12 m4">
                                    <div><span>Cierre de Caja <?= $fecha ?></span></div>
                                </div>
                                <div class="col s12 m4">
                                    <div> Usuario: <span class="green-text"> <?= session('user')->name ?></span></div>
                                </div>
                                <div class="col m12 s12 ">
                                    <form action="" method="get" class="hide-on-small-only">
                                        <div class="row float-right">
                                            <div class="col m8 s12">
                                                <div class="input-field  s12">
                                                    <input type="date" name="value">
                                                </div>
                                            </div>
                                            <div class="col m4 s12">
                                                <button class="btn" style="margin-top: 20px">
                                                    <i class="material-icons">search</i>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="divider"></div>
                            <br>
                            <div class="row">
                                <div class="col s12 m3" style="position: relative;">
                                    <div class="card-title text-center">Ventas Efectivo: $ <span><?= number_format($subtotal) ;?></span></div>
                                </div>
                                <div class="col s12 m3" style="position: relative;">
                                    <div class="card-title text-center">Ventas Credito: <span>$0</span></div>
                                </div>
                                <div class="col s12 m3" style="position: relative;">
                                    <div class="card-title text-center">Ventas (T.credito): <span>$0</span></div>
                                </div>
                                <div class="col s12 m3" style="position: relative;">
                                    <div class="card-title text-center">Ventas (T.debito): <span>$0</span></div>
                                </div>
                                <div class="col s12 m12 green lighten-5" style="position: relative;">
                                    <div class="card-title black-text float-right">Total Ventas: $ <span> <?= number_format($subtotal) ?></span></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col s12 m3" style="position: relative;">
                                    <div class="card-title text-center">Compras Efectivo: <span>$0</span></div>
                                </div>
                                <div class="col s12 m3" style="position: relative;">
                                    <div class="card-title text-center">Compras Credito: <span>$0</span></div>
                                </div>
                                <div class="col s12 m3" style="position: relative;">
                                    <div class="card-title text-center">Gastos: <span>$0</span></div>
                                </div>
                                <div class="col s12 m12 green lighten-3" style="position: relative;">
                                    <div class="card-title black-text float-right">Total Cierre: $ <span><?= number_format($subtotal) ?></span></div>
                                </div>
                            </div>
                            <br>
                            <div class="divider black"></div>
                        <div class="row">
                            <div class="col s12 m6 ">
                                <div class="text-center green-text"><span>Resumen de ventas</span></div>
                                    <div class="table-response" style="overflow-x:auto;">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th class="text-center">Producto</th>
                                                    <th class="text-center">Cantidad</th>
                                                    <th class="text-center">Valor A  pagar</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach ($productos as $producto): ?>
                                                <tr>
                                                    <td class="text-center"><?= $producto->name ?></td>
                                                    <td class="text-center"><?= $producto->cantidad ?></td>
                                                    <td class="text-center">$ <?= number_format($producto->valor) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                                <tr class="green lighten-3">
                                                    <td class="text-center"></td>
                                                    <td class="text-center black-text">Total</td>
                                                    <td class="text-center black-text">$ <?= number_format($subtotal) ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col s12 m6 ">
                                <div class="text-center green-text"><span>Resumen de Impuestos</span></div>
                                <div class="table-response" style="overflow-x:auto;">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th class="text-center">impuestos</th>
                                                    <th class="text-center">porcentajes</th>
                                                    <th class="text-center">Total Impuesto</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach ($impuestos as $impuesto): ?>
                                                <tr>
                                                    <td class="text-center">
                                                    <?php 
                                                    if($impuesto->impuesto == 1){
                                                        echo 'Iva';
                                                    }elseif($impuesto->impuesto == 5){
                                                        echo 'ReteIva';
                                                    }elseif($impuesto->impuesto == 6){
                                                        echo 'ReteRenta';
                                                    }elseif($impuesto->impuesto == 7){
                                                        echo 'ReteIca';
                                                    }
                                                    ?>
                                                    </td>
                                                    <td class="text-center"><?= $impuesto->porcentaje ?></td>
                                                    <td class="text-center">$ <?= number_format($impuesto->totalImpuesto) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- -->

                                <div class="col s12 m12 ">
                                <div class="divider black"></div>
                                <div class="text-center green-text"><span>Resumen de Facturas</span></div>
                                <div class="table-response" style="overflow-x:auto;">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th class="text-center">Facturas</th>
                                                    <th class="text-center">Inicio</th>
                                                    <th class="text-center">Fin</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-center">Venta Nacional</td>
                                                    <td class="text-center"><?= ($ventasN[0]->minimo != '' || $ventasN[0]->minimo != null )?$ventasN[0]->minimo:'N/A'; ?></td>
                                                    <td class="text-center"><?= ($ventasN[0]->maxima != '' || $ventasN[0]->maxima != null )?$ventasN[0]->maxima:'N/A'; ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">Nota Crédito</td>
                                                    <td class="text-center"><?= ($notaC[0]->minimo != '' || $notaC[0]->minimo != null )?$notaC[0]->minimo:'N/A'; ?></td>
                                                    <td class="text-center"><?= ($notaC[0]->maxima != '' || $notaC[0]->maxima != null )?$notaC[0]->maxima:'N/A'; ?></td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">Nota Débito</td>
                                                    <td class="text-center"><?= ($notaD[0]->minimo != '' || $notaD[0]->minimo != null )?$notaD[0]->minimo:'N/A'; ?></td>
                                                    <td class="text-center"><?= ($notaD[0]->maxima != '' || $notaD[0]->maxima != null )?$notaD[0]->maxima:'N/A'; ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            
                            
                        </div>
                        <?php else:?>
                        <?php endif;?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- vista general js -->

<?= view('layouts/footer') ?>