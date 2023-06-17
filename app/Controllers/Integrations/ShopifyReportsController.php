<?php


namespace App\Controllers\Integrations;

use App\Controllers\BaseController;
use App\Models\IntegrationShopify;
use App\Models\IntegrationShopifyConsolidation;
use App\Models\IntegrationsOrdersShopify;
use App\Models\IntegrationTrafficLight;
use App\Models\Invoice;
use App\Models\Resolution;
use GroceryCrud\Core\Exceptions\Exception;

class ShopifyReportsController extends BaseController
{
    private $tableIntegrationTrafficLigth;
    private $tableIntegrationOrdersShopify;
    private $tableIntegrationShopify;
    private $tableInvoices;
    private $tableResolutions;
    private $tableIntegrationShopifyConsolidation;

    public function __construct()
    {
        $this->tableIntegrationShopify = new IntegrationShopify();
        $this->tableIntegrationTrafficLigth = new IntegrationTrafficLight();
        $this->tableIntegrationOrdersShopify = new IntegrationsOrdersShopify();
        $this->tableIntegrationShopifyConsolidation = new IntegrationShopifyConsolidation();
        $this->tableInvoices = new Invoice();
        $this->tableResolutions = new Resolution();
    }

    public function uploadConsolidation()
    {
        try {
            $data = [
                'companies_id' => company()->id,
                'integration_shopify_id' => $_POST['integrationShopifyid'],
                'integrationTraffic' => $_POST['integrationTrafficid'],
                'note' => $_POST['note']
            ];
            //echo json_encode($data);die();
            $this->tableIntegrationShopifyConsolidation->insert($data);

        } catch (\Exception $e) {
            return redirect()->to(base_url() . '/integrations/shopify/report_conciliation')->with('errors', $e->getMessage());
        }
        return redirect()->to(base_url() . '/integrations/shopify/see_consolidation/' . $_POST['integrationShopifyid'] . '/' . $_POST['integrationTrafficid'])->with('success', 'Consolidación guardada con exíto');
    }

    public function conciliation()
    {
        $dataConciliation = [];
        $dateStart = (isset($_GET['date_start'])) ? $_GET['date_start'] : date('y-m-d');
        $dateEnd = (isset($_GET['date_end'])) ? $_GET['date_end'] : date('y-m-d');
        $shopActives = $this->tableIntegrationShopify->where(['companies_id' => company()->id])->get()->getResult();
        foreach ($shopActives as $shop) {
            //$orderShopify= $this->tableIntegrationOrdersShopify->where(['companies_id' => company()->id, 'integration_shopify_id' => $shop->id, 'value' => null ])->asObject()->first();
            $url = 'https://' . $shop->name_shopify . '/admin/api/2022-07/orders.json?status=any&limit=250&processed_at_min=' . $dateStart . 'T00:00:00-00:00:00&processed_at_max=' . $dateEnd . 'T23:59:59-23:59:59&fields=created_at,id,name,total_price,current_total_price, cancel_reason,fulfillment_status,order_number, note';
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'X-Shopify-Access-Token:' . $shop->token,
                    'Accept: application/json',
                    'Content-Type: application/json'
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            //echo $response;die();
            $data = json_decode($response);
            $orders = $data->orders;
            foreach ($orders as $order) {
                if (is_null($order->cancel_reason)) {
                    if ($order->fulfillment_status == 'fulfilled') {
                        $statusShopify = 'Preparado';
                    } else {
                        $statusShopify = 'No Preparado';
                    }
                } else {
                    $statusShopify = 'Cancelado';
                }
                $info = [
                    'number_mfl' => '',
                    'statusShopify' => $statusShopify,
                    'status' => 'No enviada',
                    'idShopify' => $order->id,
                    'orderNumber' => $order->order_number,
                    'dateCreate' => date("Y-m-d", strtotime($order->created_at)),
                    'dataSend' => '',
                    'valueShopify' => $order->current_total_price,
                    'value' => '0',
                    'shop' => $shop->name_shopify,
                    'typeDocument' => 'pedido Shopify',
                    'prefix' => 'SETP',//$resolution->prefix,
                    'diferencia' => $order->current_total_price,
                    'idTraffic' => null,
                    'idShop' => $shop->id,
                    'consolidations' => 0
                ];
                $traffics = $this->tableIntegrationTrafficLigth
                    ->where(['companies_id' => company()->id, 'integration_shopify_id' => $shop->id, 'id_shopify' => $order->id])
                    ->get()->getResult();
                if (count($traffics) > 0) {
                    foreach ($traffics as $traffic) {
                        $invoice = $this->tableInvoices
                            ->where(['companies_id' => company()->id, 'resolution' => $traffic->number_mfl, 'resolution_id' => $shop->resolucion_id])
                            ->asObject()->first();
                        $consolidaciones = $this->tableIntegrationShopifyConsolidation
                            ->where(['companies_id' => company()->id, 'integration_shopify_id' => $shop->id, 'integrationTraffic' => $traffic->id])
                            ->get()->getResult();
                        $info['number_mfl'] = $traffic->number_mfl;
                        $info['status'] = $traffic->status;
                        $info['dataSend'] = $traffic->created_at;
                        $info['value'] = (!is_null($invoice)) ? $invoice->payable_amount : '0';
                        $info['typeDocument'] = $traffic->type_document_id;
                        $info['diferencia'] = ($info['diferencia'] - $info['value']);
                        $info['idTraffic'] = $traffic->id;
                        $info['consolidations'] = (count($consolidaciones) > 0) ? count($consolidaciones) : 0;
                        array_push($dataConciliation, $info);
                    }
                } else {
                    array_push($dataConciliation, $info);
                }
            }

        }
        $totals = $this->calculate($dataConciliation);
        //echo json_encode($totals);die();
        echo view('integrations/ConciliationShopify', ['invoices' => $dataConciliation,'totals' => $totals]);
    }

    public function seeConsolidation($shopId, $trafficId)
    {
        $consolidations = $this->tableIntegrationShopifyConsolidation
            ->where(['companies_id' => company()->id, 'integration_shopify_id' => $shopId, 'integrationTraffic' => $trafficId])
            ->get()->getResult();
        echo view('integrations/SeeConsolidations', ['consolidations' => $consolidations]);
    }

    public function calculate($orders): array
    {
        $quantityPSD = 0;
        $quantityPSE = 0;
        $quantityPCD = 0;
        $quantityPCDC = 0;

        foreach ($orders as $order) {
            if ($order['diferencia'] < 1 && $order['status'] == 'aceptada') {
                $quantityPSD ++;
            } elseif ($order['valueShopify'] == $order['diferencia']) {
                $quantityPSE ++;
            } elseif ($order['consolidations'] > 0) {
                $quantityPCDC ++;
            } else {
                $quantityPCD ++;
            }
        }
        return [
            'quantityPSD' => $quantityPSD,
            'quantityPSE' => $quantityPSE,
            'quantityPCD' => $quantityPCD,
            'quantityPCDC' => $quantityPCDC
        ];
    }

}
