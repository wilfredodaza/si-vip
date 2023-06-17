<?=  view('layouts/header_email') ?>
    <h4>Estimado(a) usuario(a):</h4>
    <p>A continuación, los indicadores de gestión generados automáticamente por el módulo
     de inteligencia de negocios de <strong>MiFacturaLegal.com</strong> correspondientes al mes de <?=  mes(date('m'))?> de <?= date('Y')?>. </p>

    <center>
    <table class="row">
        <tbody>
            <tr>
                <th class="small-12  columns first" >
                    <table>
                        <tr >
                            <th class="card animate fadeLeft" style="background-color: #00bcd4 !important; width:230px;" >
                                    <p class="card-stats-title" style="text-align:center; padding-top:1rem;color:white; font-weight:100;">
                                        <img src="<?= base_url('assets/img/graph-up.svg') ?>" alt="" style="color:white; font-size:20px;filter: invert(1);width:18px;">
                                        <span style="display:inline-block;">
                                        Ventas del mes
                                    </span>   
                                    </p>
                                    <?php  
                                        $totalInvoicePrevius         = count($invoicePrevius) > 0 ?$invoicePrevius[0]->total_mes : 0;
                                        $totalInvoiceReal            = count($invoiceReal) > 0 ?$invoiceReal[0]->total_mes : 0;
                                        $totalInvoicePreviusCurrency = count($invoicePreviusCurrency) > 0 ? (int)$invoicePreviusCurrency[0]->total : 0;
                                        $totalInvoiceRealCurrency    = count($invoiceRealCurrency) > 0 ? (int) $invoiceRealCurrency[0]->total : 0;

                                  
                                    ?>
                                    <h2 class="card-stats-number white-text" style="font-weight:100;color:white;">$ <?= number_format(($totalInvoiceReal + $totalInvoiceRealCurrency), '0', '.', '.') ?> COP</h2>
                                    <p class="card-stats-compare" style="color:white; font-weight:100;">
                                        <img src="<?= base_url('assets/img/chevron-up.svg') ?>" alt="" style="color:white; font-size:14px;filter: invert(1);width:14px;">
                                        <?php 
                                            if($totalInvoicePrevius != 0 && $totalInvoicePreviusCurrency != 0 ){
                                                $total = ((($totalInvoiceReal + $totalInvoiceRealCurrency) / ($totalInvoicePrevius + $totalInvoicePreviusCurrency))- 1) * 100;
                                            }else {
                                                $total = 100;
                                            }
                                           
                                        ?>
                                        <small><?= round($total, 2) ?>% mes anterior</small>
                                    </p>
    
                                    <p id="clients-bar" class="center-align" style="padding:16px 24px;border-top:1px solid rgba(160, 160, 160, .2);margin:0px;background-color: #00bcd4 !important;color:white;">
                                        <a href="<?= base_url() ?>" style="color:white;text-decoration:none;display:inline;margin-left:0px; font-size:10px !important;" ><small>Ver informe en MiFacturaLegal<small></a>   
                                    </p>
                               
                            </th>
                        </tr>
                    </table>
                </th>


                <th class="small-12 large-6 columns " style="width: 230px !important;">
                    <table  style="width: 100% !important;">
                        <tr>
                            <th class="card animate fadeLeft accent-2" style="background-color: #ff5252 !important; width: 100% !important;">
                                <p class="card-stats-title" style="text-align:center; padding-top:1rem;color:white; font-weight:100;">
                                <img src="<?= base_url('assets/img/bag-check.svg') ?>" alt="" style="color:white; font-size:14px;filter: invert(1);width:18px;">
                                <span style="display:inline-block;">
                                    Total productos/servicios
                                </span>   
                                </p>
                                <h2 class="card-stats-number white-text"  style="font-weight:100;color:white;">
                                <?= $productPrevius[0]->quantity ?></h2>
                                <p class="card-stats-compare" style="color:white; font-weight:100;">
                                <img src="<?= base_url('assets/img/chevron-up.svg')?>" alt="" style="color:white; font-size:14px;filter: invert(1);width:14px;">
                                    <small><?=  $productReal[0]->quantity ?>  nuevos este mes</small>
                                </p>
                            
                                <p id="clients-bar" class="center-align red" style="padding:16px 24px;border-top:1px solid rgba(160, 160, 160, .2);margin:0px;background-color: #f44336 !important;color:white;">
                                    <a href="<?= base_url() ?>" style="color:white;text-decoration:none;display:inline;margin-left:0px; font-size:10px !important;" ><small>Ver informe en MiFacturaLegal</small></a>   
                                </p>
                            </th>
                        </tr>
                    </table>
                </th>        
            </tr>
            <tr>
                <th class="small-12 large-6 columns first">
                    <table style="width: 100%;">
                        <tr>
                            <th class="card animate fadeLeft " style=" background-color: #ffa726 !important">
                                <p class="card-stats-title" style="text-align:center; padding-top:1rem; color:white; font-weight:100;">
                                    <!--<i class="material-icons" style="display:inline-block;font-size: 14px;;">person_outline</i>-->
                                    <img src="<?= base_url('assets/img/people.svg') ?>" alt="" style="color:white; font-size:10px;filter: invert(1);width:18px;">
                                    <span style="display:inline-block;">
                                  
                                    Total clientes</span>   
                                </p>
                                <h2 class="card-stats-number white-text" style="font-weight:100;color:white;">
                                
                                <?=  $customerPrevius  + $customerNew  ?>
                            </h2>
                                <p class="card-stats-compare" style="color:white; font-weight:100;">
                                <img src="<?= base_url('assets/img/chevron-up.svg') ?>" alt="" style="color:white; font-size:14px;filter: invert(1);width:14px;">
                                    <small>	 <?= $customerNew ?> nuevos este mes</small>
                                </p>
                                <p id="clients-bar" class="center-align" style="padding:16px 24px;border-top:1px solid rgba(160, 160, 160, .2);margin:0px;background-color: #ff9800 !important;color:white;">
                                    <a href="<?= base_url() ?>" style="color:white;text-decoration:none;display:inline;margin-left:0px; font-size:10px !important;" ><small>Ver informe en MiFacturaLegal</small></a> 
                                </p>
                         
                            </th>
                        </tr>
                    </table>
                </th>
                <th class="small-12 large-6 columns last">
                    <table  style="width: 100%;">
                        <tr>
                            <th class="card animate fadeLeft "style=" background-color: #66bb6a !important;">
                                <p class="card-stats-title" style="text-align:center; padding-top:1rem; color:white; font-weight:100;">
                                    <img src="<?= base_url('assets/img/cash.svg') ?>" alt="" style="color:white; font-size:14px;filter: invert(1);width:18px;">
                                    <span style="display:inline-block;">Cartera</span>   
                                </p>
                                <?php 
                                    $countInvoice = 0;
                                    foreach($walletsCount as $item):
                                        $countInvoice += $item->invoice;
                                    endforeach;

                                    $totalWallet            = count($walletsTotal) > 0          ? $walletsTotal[0]->total: 0;
                                    $totalWalletCurrency    = count($walletsTotalCurrency) > 0  ? $walletsTotalCurrency[0]->total: 0;

                                    
                                ?>
                                <h2 class="card-stats-number white-text" style="font-weight:100;color:white;">$<?= number_format(($totalWalletCurrency + $totalWallet), '0', ',', '.') ?> COP</h2>
                                <p class="card-stats-compare" style="color:white; font-weight:100;">
                                <img src="<?= base_url('assets/img/chevron-up.svg') ?>" alt="" style="color:white; font-size:10px;filter: invert(1);width:14px;">
                                    <small><?= count($walletsCount) ?> clientes y <?= $countInvoice ?> facturas</small>
                                </p>
                                <p id="clients-bar" class="center-align green" style="padding:16px 24px;border-top:1px solid rgba(160, 160, 160, .2);margin:0px;background-color: #4caf50 !important;color:white;">
                                    <a href="<?= base_url() ?>" style="color:white;text-decoration:none;display:inline;margin-left:0px; font-size:10px !important;" ><small>Ver informe en MiFacturaLegal</small></a> 
                                 </p>
                                  
                            </th>
                        </tr>
                    </table>
                </th>        
            </tr>
        </tbody>
    </table>

    <table>
        <tr>
            <th class=" animate fadeLeft " > 
                <a href="<?= base_url() ?>" style="background-color:#8021B5;color:white; display:block; width:100%; padding:10px 20px; border:none;text-decoration:none;">Iniciar Sesión</a>
            </th>
        </tr>
    </table>
             
    </center>


    


<?= view('layouts/footer_email') ?>