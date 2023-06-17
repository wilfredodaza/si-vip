<?php


namespace App\Controllers\Integrations;

use App\Controllers\Api\Auth;
use App\Controllers\BaseController;
use App\Controllers\Configuration\EmailController;
use App\Controllers\IntegrationsController;
use App\Models\Company;
use App\Models\DocumentInvoice;
use App\Models\IntegrationShopify;
use App\Models\IntegrationsOrdersShopify;
use App\Models\Invoice;
use App\Models\IntegrationTrafficLight;
use App\Models\Municipalities;
use App\Models\Resolution;
use App\Models\ShopifyApplicantDiscount;
use App\Models\ShopifyApps;
use App\Models\ShopifyExceptions;
use App\Models\ShopifyLog;
use App\Models\ShopifyProductsVatExempt;
use Config\Services;
use mysql_xdevapi\Exception;
use Shopify\Clients\HttpHeaders;
use Shopify\Context;
use Shopify\Auth\FileSessionStorage;
use Shopify\Auth\OAuthCookie;
use CodeIgniter\Cookie\Cookie;
use CodeIgniter\Cookie\CookieStore;
use Shopify\Webhooks\Registry;
use Shopify\Webhooks\Topics;
use App\Webhook\Handlers\AppUninstalled;
use DateTime;


class ShopifyController extends BaseController
{
    private $nameAplication;
    private $client_id;
    private $secret_id;
    private $redirectShopify;
    private $typeApp;
    private $scope = 'read_all_orders,read_orders,write_orders,read_assigned_fulfillment_orders,read_locations,read_inventory,read_products,write_products,write_inventory,write_assigned_fulfillment_orders,read_customers,write_customers,read_draft_orders,write_draft_orders,read_content,write_content,unauthenticated_read_content';

    public $url_permissions;
    private $resolution;
    private $invoice;
    private $error;
    private $returns;
    public $orders = [];
    private $controllerIntegration;
    private $tableCompany;
    public $cookie;
    public $exceptionShopify;
    public $appShopify;
    private $email;
    public $orderNumberId;
    public $typeOrder;
    protected $idCompany;
    public $countLineInvoices = 0;
    public $taxes_included = '';
    public $vat;
    public $discountShopify;
    // tables
    public $tableShopifyApplicantDiscount;
    public $tableMunicipality;
    private $tableIntegrationTrafficLight;
    private $tableShopifyExceptions;
    private $tableShopifyApps;
    private $table_integration_shopify;
    private $tableShopifyLog;
    private $tableIntegrationsOrdersShopify;
    private $tableDocumentsInvoices;
    private $tableShopifyProductsVatExempt;

    public function __construct()
    {
        //session_start();
        $this->table_integration_shopify = new IntegrationShopify();
        $this->resolution = new Resolution();
        $this->tableIntegrationTrafficLight = new IntegrationTrafficLight();
        $this->controllerIntegration = new IntegrationsController();
        $this->tableCompany = new Company();
        $this->tableShopifyApps = new ShopifyApps();
        $this->tableShopifyExceptions = new ShopifyExceptions();
        $this->tableShopifyApplicantDiscount = new ShopifyApplicantDiscount();
        $this->tableMunicipality = new Municipalities();
        $this->tableShopifyLog = new ShopifyLog();
        $this->tableIntegrationsOrdersShopify = new IntegrationsOrdersShopify();
        $this->tableDocumentsInvoices = new DocumentInvoice();
        $this->tableShopifyProductsVatExempt = new ShopifyProductsVatExempt();
        $this->email = new EmailController();
        if (session('user')) {
            $this->companyExceptionShopiy(session('user')->companies_id);
        } else {
            $this->companyExceptionShopiy(0);
        }
        Context::initialize(
            $this->client_id,
            $this->secret_id,
            $this->scope,
            (getenv('DEVELOPMENT')) ? 'planetalab.xyz' : 'facturadorv2.mifacturalegal.com',
            new FileSessionStorage('../../../tmp/php_sessions'),
            '2022-07',
            true,
            false
        );

    }

    public function auth()
    {
        header('Content-Security-Policy: frame-ancestors "none";');
        $url = \Shopify\Auth\OAuth::begin(
            $_POST['name'],
            '/integrations/shopify/token_access',
            true,
            $data = function ($cookie) {
                $session = session();
                $session->set('value', $cookie->getValue());
                $cookies = new Cookie(
                    $cookie->getName(),
                    $cookie->getValue(),
                    [
                        'expires' => $cookie->getExpire(),
                        'secure' => $cookie->isSecure(),
                        'httponly' => $cookie->isSecure(),
                    ]
                );
                return true;
            }
        );


        $a = str_replace('https', 'http', $url);
        $a = str_replace('%2C', '&', $url);
        $a = str_replace('%3A', ':', $a);
        $a = str_replace('%5B', '[', $a);
        $a = str_replace('%5D', ']', $a);
        $a = str_replace('%2F', '/', $a);

        return redirect()->to($a);
    }

    public function index()
    {
        if (session('user')) {
            $this->indexPage(company()->id);
        } else {
            if (isset($_GET['shop'])) {
                $company = $this->table_integration_shopify->where(['name_shopify' => $_GET['shop']])->asObject()->first();
            } else {
                return redirect()->to(base_url());
            }
            if (!is_null($company)) {
                $this->indexPage($company->companies_id, $company->id);
                $_SESSION['idCompany'] = $company->companies_id;
                $_SESSION['idIntegrationShopify'] = $company->id;
            } elseif (isset($_SESSION['idCompany'])) {
                $this->indexPage($_SESSION['idCompany'], $_SESSION['idIntegrationShopify']);
            } else {
                $data = [
                    'name_shopify' => ($_GET['shop'] ?? ''),
                    'active' => false
                ];
                echo view('integrations/Shopify2', $data);
            }
        }

    }

    public function save_name()
    {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        header("Allow: GET, POST, OPTIONS, PUT, DELETE");
        $dta = json_decode(file_get_contents('php://input'));
        try {
            $this->table_integration_shopify->save([
                'companies_id' => company()->id,
                'name_shopify' => $dta->nombre,
                'status_invoice' => $dta->status
            ]);
        } catch (\Exception $e) {
            return json_encode([
                'status' => 'error',
                'descripción' => 'Inconveniente al guardar nombre de Shopify'
            ]);
        }
        return json_encode([
            'status' => 'success',
            'descripción' => 'Nombre guardado con exíto'
        ]);
    }

    public function updateRegister()
    {
        $company = $this->tableCompany->where(['identification_number' => $_POST['nit']])->asObject()->first();
        $this->companyExceptionShopiy($company->id, $_POST['name']);
        //echo json_encode($this->appShopify);die();
        $company_shopify = $this->table_integration_shopify
            ->where(['companies_id' => $company->id, 'name_shopify' > $_POST['name']])
            ->asObject()->first();
        if (!is_null($company_shopify)) {
            return redirect()->to(base_url() . '/integrations/shopify')
                ->with('errors', 'Usted ya se encuentra registrado en la plataforma para esta integración');
        }
        try {
            $this->table_integration_shopify->save([
                'companies_id' => $company->id,
                'name_shopify' => $_POST['name'],
                'status_invoice' => 'Borrador',
                'status' => 'Inactive'
            ]);

        } catch (\Exception $e) {
            return redirect()->to(base_url() . '/integrations/shopify')->with('errors', 'Inconveniente al guardar información');
        }
        header('Location: https://' . $_POST['name'] . '/admin/oauth/authorize?client_id=' . $this->client_id . '&scope=' . $this->scope . '&redirect_uri=' . $this->redirectShopify . '&state=' . $this->nameAplication);
        exit;
    }

    /**
     * Metodo para recibir token temporal y pedir y recibir un còdigo permanente
     */
    public function token_access()
    {
        $info_shopify = $this->table_integration_shopify->where(['name_shopify' => $_GET['shop']])->get()->getResult()[0];
        $this->companyExceptionShopiy($info_shopify->companies_id, $_GET['shop']);
        //echo json_encode($this->typeApp);die();
        if ($this->typeApp != 'private') {
            $mockCookies = [
                \Shopify\Auth\OAuth::SESSION_ID_SIG_COOKIE_NAME => hash_hmac('sha256', $_SESSION['value'], Context::$API_SECRET_KEY),
                \Shopify\Auth\OAuth::SESSION_ID_COOKIE_NAME => $_SESSION['value']
            ];
            $var = \Shopify\Auth\OAuth::callback($mockCookies, $_GET);
            $token = $var->getAccessToken();
        } else {
            $code = $_GET['code'];
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://' . $info_shopify->name_shopify . '/admin/oauth/access_token',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode([
                    "client_id" => $this->client_id,
                    "client_secret" => $this->secret_id,
                    "code" => $code
                ]),
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/json",
                    "Accept: application/json",
                ),
            ));

            $response = curl_exec($curl);
            curl_close($curl);
            $data = json_decode($response);
            $token = $data->access_token;
        }
        try {
            $this->table_integration_shopify->set(['token' => $token])
                ->where(['companies_id' => $info_shopify->companies_id, 'name_shopify' => $_GET['shop']])
                ->update();

        } catch (\Exception $e) {
            return redirect()->to(base_url() . '/integrations/shopify')->with('errors', 'Error al solicitar permisos');
        }
        if (session('user')) {
            return redirect()->to(base_url() . '/integrations/shopify')->with('success', 'Integración Completa, ya puedes disfrutar de shopify con Mawii');
        } else {
            header('Location: https://' . $info_shopify->name_shopify . '/admin/apps');
            exit;
        }
    }

    public function webhooksAction($request)
    {
        try {
            $response = \Shopify\Webhooks\Registry::process($this->processHeaders, json_encode($this->processBody));

            if ($response->isSuccess()) {
                echo json_encode("Responded to webhook!");
                // Respond with HTTP 200 OK
            } else {
                // The webhook request was valid, but the handler threw an exception
                echo json_encode("Webhook handler failed with message: " . $response->getErrorMessage());
            }
        } catch (\Exception $error) {
            // The webhook request was not a valid one, likely a code error or it wasn't fired by Shopify
            echo json_encode($error->getMessage());
        }
    }


    public function activationCompanies()
    {
        $companies = $this->table_integration_shopify->get()->getResult();
        foreach ($companies as $company) {
            $dataCompany = $this->tableCompany->where(['identification_number' => $company->identification_number])->get()->getResult();
            //$this->orders($dataCompany);
        }
    }

    /**
     * Metodo para controlar y saber que pedidos debe realizar y traer de shopify
     * @param null $companyID
     */
    public function controlOrders($companyID = null)
    {
        $this->orderNumberId = ($_GET['order'] ?? null);
        //$this->typeOrder = ($_POST['type_order'] ?? null);
        if (isset($_GET['idIntegrationShopify']) && !empty($_GET['idIntegrationShopify'])) {
            $info_shopify = $this->table_integration_shopify->where(['companies_id' => $companyID, 'id' => $_GET['idIntegrationShopify']])->asObject()->first();
        } else {
            $info_shopify = $this->table_integration_shopify->where(['companies_id' => $companyID])->asObject()->first();
        }
        $this->orders($companyID, null, false, ($_GET['idIntegrationShopify'] ?? null));
        return redirect()->to(base_url() . '/integrations/shopify?shop=' . $info_shopify->name_shopify);
    }

    /**
     * Metodo para traer los pedidos de shopify
     * @param null $companyId
     * @param null $idSingleOrder
     */
    public function orders($companyId = null, $idSingleOrder = null, $regenerate = false, $idIdentificationShopify = null)
    {
        $this->idCompany = $companyId;
        $info_shopify = (is_null($idIdentificationShopify)) ? $this->table_integration_shopify
            ->where(['companies_id' => $this->idCompany])
            ->asObject()->first() : $this->table_integration_shopify
            ->where(['companies_id' => $this->idCompany, 'id' => $idIdentificationShopify])
            ->asObject()->first();
        if (!is_null($this->orderNumberId)) {
            $searchIdShopify = $this->tableIntegrationsOrdersShopify
                ->where(['companies_id' => $this->idCompany, 'integration_shopify_id' => $info_shopify->id, 'shopify_number' => $this->orderNumberId])->asObject()->first();
            if (!is_null($searchIdShopify)) {
                $idSingleOrder = $searchIdShopify->shopify_id;
            } else {
                return redirect()->to(base_url() . '/integrations/shopify?shop=' . $info_shopify->name_shopify)
                    ->with('warning', 'No se encontró pedido ' . $this->orderNumberId);
            }
        }
        $curl = curl_init();
        if (is_null($idSingleOrder)) {
            if ($info_shopify->status_invoice == 'Borrador') {
                $url = 'https://' . $info_shopify->name_shopify . '/admin/api/2022-07/draft_orders.json?status=open';
            } else {
                $url = 'https://' . $info_shopify->name_shopify . '/admin/api/2022-07/orders.json?status=any&limit=250';
                //$url = 'https://' . $info_shopify->name_shopify . '/admin/api/2022-07/orders/4726534766815.json';
            }
        } else {
            $url = 'https://' . $info_shopify->name_shopify . '/admin/api/2022-07/orders/' . $idSingleOrder . '.json';
        }

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
                'X-Shopify-Access-Token:' . $info_shopify->token,
                'Accept: application/json',
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        //echo $response;die();
        $data = json_decode($response);
        //echo $data->customer;die();
        $null = false;
        if (is_null($idSingleOrder)) {
            if (isset($data->orders)) {
                $orders = $data->orders;
            } else {
                $orders = $data->draft_orders;
            }
            foreach ($orders as $order) {
                $this->taxes_included = $order->taxes_included;
                $this->discountShopify = $order->discount_applications;
                //echo json_encode($order->tax_lines[0]->rate);die();
                $this->vat = (isset($order->tax_lines[0]->rate)) ? $order->tax_lines[0]->rate : 0;
                $saveIdShopify = $this->tableIntegrationsOrdersShopify
                    ->where(['companies_id' => $this->idCompany, 'integration_shopify_id' => $info_shopify->id, 'shopify_number' => $order->order_number])->first();
                if (is_null($saveIdShopify)) {
                    $fechaCreacion = strtotime($order->created_at);
                    $this->tableIntegrationsOrdersShopify->save([
                        'companies_id' => $this->idCompany,
                        'shopify_number' => $order->order_number,
                        'integration_shopify_id' => $info_shopify->id,
                        'shopify_id' => $order->id,
                        'value' => $order->total_price,
                        'status' => $order->fulfillment_status,
                        'create_at_shopify' => date("Y-m-d", $fechaCreacion)
                    ]);
                }
                if ($order->fulfillment_status == "fulfilled" && is_null($order->cancel_reason) && $order->financial_status != "voided") {
                    //echo json_encode($order);die();
                    $count = $this->tableIntegrationTrafficLight
                        ->where(['companies_id' => $this->idCompany, 'integration_shopify_id' => $info_shopify->id,
                            'type_document_id' => 1])
                        ->whereIn('status', ['devuelto_prod','aceptada'])
                        ->whereIn('id_shopify', [$order->id,$order->id.'0'])
                        ->countAllResults();
                    $companyResolutionId = $this->resolution
                        ->where(['companies_id' => $this->idCompany, 'resolution' => $info_shopify->resolucion_id, 'type_documents_id' => 1])
                        ->asObject()->first();
                    if ($count < 1) {
                        $this->createInvoice($order, $companyResolutionId, $info_shopify->id);
                        if (count($this->error)) {
                            //enviar errores al correo correspondiente
                            $this->enviarErrores($order, $companyId);
                            continue;
                        }
                        if ($this->typeOrder == 'json') {
                            echo json_encode($this->invoice);
                            die();
                        }
                        $this->validationInvoiceZero();
                        $this->send($this->invoice, $order->id, ($order->order_number ?? $order->name),
                            $companyResolutionId->prefix, null, 1, null, $info_shopify->id);
                    }
                } else {
                    $this->saveLog($companyId, $order->order_number, 'La orden tiene un estado diferente a preparado. estado actual: ' . $order->fulfillment_status);
                }
            }
        } else {
            if (!isset($data->order)) {
                $this->saveLog($companyId, $idSingleOrder, 'La orden no se encontró');
                return 'no encontrada';
            }
            $order = $data->order;
            $this->taxes_included = $order->taxes_included;
            $this->discountShopify = $order->discount_applications;
            $this->vat = (isset($order->tax_lines[0]->rate)) ? $order->tax_lines[0]->rate : 0;
            //echo json_encode($order);die();
            if ($order->fulfillment_status == "fulfilled" && is_null($order->cancel_reason) && $order->financial_status != "voided") {
                //echo json_encode($order);die();
                $count = $this->tableIntegrationTrafficLight
                    ->where(['companies_id' => $this->idCompany, 'integration_shopify_id' => $info_shopify->id,
                        'type_document_id' => 1])
                    ->whereIn('status', ['devuelto_prod','aceptada'])
                    ->whereIn('id_shopify', [$order->id,$order->id.'0'])
                    ->countAllResults();
                $companyResolutionId = $this->resolution
                    ->where(['companies_id' => $this->idCompany, 'resolution' => $info_shopify->resolucion_id, 'type_documents_id' => 1])
                    ->asObject()->first();
                if ($count < 1) {
                    $this->createInvoice($order, $companyResolutionId, $info_shopify->id);
                    if (count($this->error)) {
                        //enviar errores al correo correspondiente
                        $this->enviarErrores($order, $companyId);
                    }
                    //echo json_encode($this->invoice);die();
                    $this->validationInvoiceZero();
                    if ($regenerate) {
                        return $this->send($this->invoice, $order->id, ($order->order_number ?? $order->name),
                            $companyResolutionId->prefix, null, 1, null, $info_shopify->id);
                    } else {
                        $this->send($this->invoice, $order->id, ($order->order_number ?? $order->name), $companyResolutionId->prefix,
                            null, 1, null, $info_shopify->id);
                    }
                }
            } else {
                $this->saveLog($companyId, $order->order_number, 'La orden tiene un estado diferente a preparado. estado actual: ' . $order->fulfillment_status);
            }
        }

        if (count($this->orders) > 0) {
            return redirect()->to(base_url() . '/integrations/shopify?shop=' . $info_shopify->name_shopify)
                ->with('success', 'Facturas Desplegadas con exíto');
        } else {
            return redirect()->to(base_url() . '/integrations/shopify?shop=' . $info_shopify->name_shopify)
                ->with('warning', 'No se encontraron facturas para enviar');
        }

    }

    /**
     * Metodo para la creación de facturas para evitar duplicidad de código en llamada a varios o a uno
     * al traer datos de shopify
     * @param $order
     * @param $companyResolutionId
     */
    private function createInvoice($order, $companyResolutionId, $idIntegrationShopify): void
    {
        $valorDescuento = 0;
        $selectDiscount = null;
        $invoicesRejected = $this->tableIntegrationTrafficLight
            ->where(['companies_id' => $this->idCompany, 'id_shopify' => $order->id, 'integration_shopify_id' => $idIntegrationShopify,
                'status' => 'rechazada'])->orderBy('id', 'desc')
            ->limit(1)->get()->getResult();
        $resolutions = $this->tableIntegrationTrafficLight->selectMax('number_mfl')
            ->where(['companies_id' => $this->idCompany, 'integration_shopify_id' => $idIntegrationShopify])
            ->asObject()->first();
        $infoShopify = $this->table_integration_shopify
            ->where(['companies_id' => $this->idCompany, 'id' => $idIntegrationShopify])
            ->asObject()->first();
        if (count($invoicesRejected) > 0) {
            $resolution = $invoicesRejected[0]->number_mfl;
        } else {
            if (is_null($resolutions->number_mfl)) {
                $resolution = $this->_resolution(1, $companyResolutionId->id);
            } else {
                $resolution = $resolutions->number_mfl + 1;
            }
        }
        //echo json_encode($resolution);die();
        $this->invoice = [];
        $this->error = [];
        $this->invoice['template'] = 1;
        if ($infoShopify->name_shopify == 'morado-chromatic-mood.myshopify.com') {
            $this->invoice['establishment_phone'] = '3237864521';
            $this->invoice['establishment_municipality'] = 149;
            $this->invoice['establishment_address'] = 'CARRERA 60 D No. 98 A – 49';
        }
        $this->invoice['number'] = $resolution;
        $this->invoice['type_document_id'] = 1;
        $this->invoice['resolution_number'] = $companyResolutionId->resolution;
        $this->invoice['date'] = date('Y-m-d');
        $this->invoice['time'] = date('H:i:s');
        if (!empty($order->note)) {
            $notes = explode('**', $order->note);
            if (isset($notes[1])) {
                $this->invoice['notes'] = '<h5>Pedido #' . $order->order_number . '</h5> ' . $notes[1];
            } else {
                $this->invoice['notes'] = '<h5>Pedido #' . $order->order_number . '</h5> ';
            }
            if (isset($notes[2])) {
                $valorDescuento = (int)$notes[2];
            }
        } else {
            $this->invoice['notes'] = '<h5>Pedido #' . $order->order_number . '</h5> ';
        }
        try {
            $this->customer($order->customer, $order->shipping_address);
        } catch (\Exception $e) {
            array_push($this->error, ['errorMessage' => 'No existen datos de envío.']);
        }
        //echo json_encode($this->invoice);die();
        $this->paymentForms($order);
        if (isset($order->discount_applications[0]->value) && $valorDescuento == 0) {
            if ($order->discount_applications[0]->value_type == 'percentage') {
                $valorDescuento = $order->discount_applications[0]->value;
            } elseif ($order->discount_applications[0]->value_type == 'fixed_amount') {
                $valorDescuento = -1;
            }
            $selectDiscount = $order->discount_applications[0]->target_selection;
        } elseif ($valorDescuento == 0) {
            $applicantDiscount = $this->tableShopifyApplicantDiscount
                ->where(['companies_id' => $this->idCompany, 'order_number_shopify' => $order->order_number])
                ->asObject()->first();
            if (!is_null($applicantDiscount)) {
                $valorDescuento = $applicantDiscount->percentage;
            } else {
                $valorDescuento = 0;
            }
        }
        $this->lineInvoice($order, $valorDescuento, $selectDiscount, $idIntegrationShopify);
        //echo json_encode(count($this->invoice['invoice_lines']));die();
        $this->_legalMonetaryTotals();
        $this->_taxesTotals();
    }

    /**
     * Metodo para realizar la búsqueda de datos del cliente
     * @param $customer
     * @param $shipping_address
     */
    public function customer($customer, $shipping_address)
    {
        try {
            // nombre
            if (!isset($customer->default_address->name) || empty($customer->default_address->name)) {
                $this->invoice['customer']['name'] = $customer->first_name . " " . $customer->last_name;
            } elseif (isset($customer->default_address->name)) {
                $this->invoice['customer']['name'] = $customer->first_name . " " . $customer->last_name;
                //$this->invoice['customer']['name'] = $customer->default_address->name;
            } else {
                throw new \Exception('El campo " nombre " no se ha encontrado según los parámetros acordados');
            }
            // número identicación
            if (isset($shipping_address->company) && !empty($shipping_address->company)) {
                if (strpos($shipping_address->company, '-')) {
                    $datos = explode('-', $shipping_address->company);
                    $identificacion = $datos[0];
                    $this->invoice['customer']['dv'] = $datos[1];
                    $identification_type = 6;
                } else {
                    $numero = str_replace('.', '', trim($shipping_address->company));
                    if (is_numeric($numero)) {
                        $identificacion = $numero;
                        $identification_type = 3;
                    } else {
                        throw new \Exception('El campo " numero identificación " no se ha encontrado según los parámetros acordados');
                    }
                }
            } else {
                $identificacion = 222222222222;
                $identification_type = 3;
            }
            $this->invoice['customer']['type_document_identification_id'] = $identification_type;
            $this->invoice['customer']['identification_number'] = $identificacion;
            // teléfono
            if (isset($customer->phone)) {
                $this->invoice['customer']['phone'] = str_replace('+57 ', '', $customer->phone);
            } elseif (!isset($customer->phone) && isset($shipping_address->phone)) {
                $this->invoice['customer']['phone'] = str_replace('+57 ', '', $shipping_address->phone);
            } else {
                throw new \Exception('El campo " teléfono " no se ha encontrado según los parámetros acordados');
            }
            // dirección
            if (isset($customer->default_address->address1)) {
                $this->invoice['customer']['address'] = $customer->default_address->address1;
            } elseif (!isset($customer->default_address->address1) && isset($shipping_address->address1)) {
                $this->invoice['customer']['address'] = $shipping_address->address1;
            } else {
                throw new \Exception('El campo " dirección " no se ha encontrado según los parámetros acordados');
            }
            // correo
            if (isset($customer->email) && !empty($customer->email)) {
                $this->invoice['customer']['email'] = $customer->email;
                $this->invoice['customer']['email2'] = 'facturasventa@morado.app';
            } else {
                throw new \Exception('El campo " correo " no se ha encontrado según los parámetros acordados');
            }
            $this->invoice['customer']['merchant_registration'] = '000000';
            $this->invoice['customer']['type_organization_id'] = 1;
            if (isset($customer->default_address->city) && !empty($customer->default_address->city)) {
                $municipality = $this->tableMunicipality->like('name', $customer->default_address->city, 'both')->asObject()->first();
                if (!is_null($municipality)) {
                    $this->invoice['customer']['municipality_id'] = $municipality->id;
                } else {
                    $companyMunicipalityId = $this->tableCompany->where(['id' => $this->idCompany])->asObject()->first();
                    if (!is_null($companyMunicipalityId)) {
                        $this->invoice['customer']['municipality_id'] = $companyMunicipalityId->municipalities_id;
                    } else {
                        throw new \Exception('La ciudad no se encuentra en la base de datos' . $customer->default_address->city);
                    }
                }
            } else {
                throw new \Exception('El campo " ciudad " no se ha encontrado según los parámetros acordados');
            }
            $this->invoice['customer']['type_regime_id'] = 2;
        } catch (\Exception $e) {
            array_push($this->error, ['errorMessage' => $e->getMessage()]);
        }
    }

    /**
     * Metodo para forma de pago
     */
    public function paymentForms($order)
    {
        $this->invoice['payment_form']['payment_form_id'] = 1;
        $this->invoice['payment_form']['payment_method_id'] = 10;
        $this->invoice['payment_form']['payment_due_date'] = date('Y-m-d');
        $this->invoice['payment_form']['duration_measure'] = 0;
        $tags = explode(',', $order->tags);
        foreach ($tags as $tag){
            if(strtolower($tag) == 'credito'){
                $this->invoice['payment_form']['payment_form_id'] = 2;
                $this->invoice['payment_form']['payment_method_id'] = 30;
                $this->invoice['payment_form']['duration_measure'] = 30;
            }
        }

    }

    /**
     * Metodo para extraer los datos de los productos e ingresarlos en las invoices lines
     * @param $orders
     * @param $porcentajeDescuento
     * @param $number_order
     */
    public function lineInvoice($orders, $porcentajeDescuento, $selectDiscount = null, $idIntegrationShopify)
    {
        try {
            $this->countLineInvoices = 0;
            $shipping_lines = null;
            $this->lineInvoicesReturn($orders);
            if (isset($orders->shipping_lines[0]) && $orders->shipping_lines[0]->price != 0) {
                $shipping_lines = $orders->shipping_lines[0];
            }
            if (!empty($orders->fulfillments)) {
                foreach ($orders->fulfillments as $orden) {
                    $this->extractedLineInvoices($orden, $porcentajeDescuento, $selectDiscount, $idIntegrationShopify, $shipping_lines);
                }
                $this->countLineInvoices = 0;
            } else {
                $orden = $orders;
                $this->extractedLineInvoices($orden, $porcentajeDescuento, $selectDiscount, $idIntegrationShopify, $shipping_lines);
            }
            if (!isset($this->invoice['invoice_lines'][0]['with_holding_tax_total'])) {
                $this->invoice['with_holding_tax_total'] = [];
            }
        } catch (\Exception $e) {
            array_push($this->error, ['errorMessage' => $e->getMessage() . ' ,Por favor reportar a soporte']);
        }
    }

    /**
     * metodo encargado de realizar el proceso de line_items de acuerdo al fulfillment u orders normales
     * @param $orden
     * @param $porcentajeDescuento
     */
    private function extractedLineInvoices($orden, $porcentajeDescuento, $selectDiscount, $idIntegrationShopify, $shipping_lines): void
    {
        try {
            foreach ($orden->line_items as $line_item) {
                // verificar productos excentos de iva
                $date = strtotime(date("d-m-Y H:i:00", time()));
                $initDate = strtotime("17-06-2022 00:00:00");
                $endDate = strtotime("17-06-2022 23:59:59");
                $productExempt = null;
                if ($date >= $initDate && $date <= $endDate) {
                    $productExempt = $this->tableShopifyProductsVatExempt
                        ->where(['companies_id' => $this->idCompany,
                            'integration_shopify_id' => $idIntegrationShopify,
                            'sku_shopify' => $line_item->sku,
                            'status' => 'active'
                        ])->asObject()->first();
                }
                $quantity = $line_item->quantity;
                if (isset($this->returns[$line_item->id])) {
                    $quantity = $line_item->quantity - $this->returns[$line_item->id];
                    if ($quantity == 0) {
                        continue;
                    }
                }
                // valor precio base
                $discountBase = $this->getDiscountBase($line_item);
                if ($this->taxes_included) {
                    $precio_base = ($line_item->price / ($this->vat + 1)) - $discountBase;
                    if ($this->vat == 0) {
                        $precio_base = ($line_item->price / 1.19) - $discountBase;
                    }
                } else {
                    $precio_base = ($line_item->price - $discountBase);
                }
                // valor descuento
                $this->valorDescuento($porcentajeDescuento, $line_item, $quantity, $selectDiscount, $precio_base, $productExempt);
                //$l++;
            }
            if (!is_null($shipping_lines)) {
                $this->productDelivery($shipping_lines, $this->countLineInvoices);
            }
        } catch (\Exception $e) {
            array_push($this->error, ['errorMessage' => $e->getMessage() . ' ,Por favor reportar a Soporte']);
        }
    }

    /**
     * Metodo para revision de devolución de productos
     * @param $orders
     */
    private function lineInvoicesReturn($orders)
    {
        $this->returns = [];
        foreach ($orders->refunds as $refund) {
            foreach ($refund->refund_line_items as $refund_line_item) {
                if ($refund_line_item->restock_type == 'return') {
                    $this->returns[$refund_line_item->line_item_id] = $refund_line_item->quantity;
                }
            }
        }
    }

    /**
     * Este metodo se encarga de obtener los totales correspondientes de la factura
     */
    private function _legalMonetaryTotals()
    {
        $lineExtensionAmount = 0; // precio base
        $taxExclusiveAmount = 0; // precio base sin impuestos
        $taxInclusiveAmount = 0; // precio base màs impuestos
        $allowanceAmount = 0; // total descuento general

        foreach ($this->invoice['invoice_lines'] as $item) {
            $lineExtensionAmount += $item['line_extension_amount'];
            $taxExclusiveAmount += $item['line_extension_amount'];
            if (isset($item['tax_totals'])) {
                foreach ($item['tax_totals'] as $tax) {
                    if ($tax['tax_id'] == 1) {
                        $taxInclusiveAmount += $tax['tax_amount'];
                    }
                }
            }

        }
        $this->invoice['legal_monetary_totals']['line_extension_amount'] = $lineExtensionAmount;
        $this->invoice['legal_monetary_totals']['tax_exclusive_amount'] = $taxExclusiveAmount;
        $this->invoice['legal_monetary_totals']['tax_inclusive_amount'] = $taxInclusiveAmount + $lineExtensionAmount;
        $this->invoice['legal_monetary_totals']['allowance_total_amount'] = "0.00";
        $this->invoice['legal_monetary_totals']['charge_total_amount'] = "0.00";
        $this->invoice['legal_monetary_totals']['payable_amount'] = ($taxInclusiveAmount + $lineExtensionAmount);

        // descuento general a la factura
        /*if($number_order < 1677 && $porcentaje != 0){
            $this->invoice['allowance_charges'][0]['discount_id'] = 1;
            $this->invoice['allowance_charges'][0]['charge_indicator'] = false;
            $this->invoice['allowance_charges'][0]['allowance_charge_reason'] = 'DESCUENTO GENERAL';
            $this->invoice['allowance_charges'][0]['amount'] = ($porcentaje == -1)?$order->current_total_discounts:(($taxInclusiveAmount + $lineExtensionAmount) * $porcentaje/100);
            $this->invoice['allowance_charges'][0]['base_amount'] = $taxInclusiveAmount + $lineExtensionAmount;
        }*/
    }

    /**
     * Metodo para sacar el total de la taxes
     */
    private function _taxesTotals()
    {

        $iva = [];
        $percent = [];
        foreach ($this->invoice['invoice_lines'] as $value) {
            if (isset($value['tax_totals'])) {
                foreach ($value['tax_totals'] as $item) {
                    if (!array_key_exists($item['percent'], $percent)) {
                        array_push($iva, $item);
                        $percent["" . $item['percent'] . ""] = $item['percent'];
                    } else {
                        $m = 0;
                        foreach ($iva as $valid) {
                            if ($valid['percent'] == $item['percent']) {
                                $iva[$m]['tax_amount'] += $item['tax_amount'];
                                $iva[$m]['taxable_amount'] += $item['taxable_amount'];
                            }
                            $m++;
                        }

                    }

                }
            }
        }


        $retention = [];
        $l = 0;


        foreach ($this->invoice['invoice_lines'] as $item) {
            if (isset($item['with_holding_tax_total'])) {
                foreach ($item['with_holding_tax_total'] as $taxTotal) {
                    if ($taxTotal['tax_id'] == '5' || $taxTotal['tax_id'] == '6' || $taxTotal['tax_id'] == '7') {
                        if (array_key_exists($taxTotal['tax_id'], $retention)) {
                            if (isset($retention[$taxTotal['tax_id']][$l]['percent']) && $retention[$taxTotal['tax_id']][$l]['percent'] == $taxTotal['percent']) {
                                $k = 0;
                                foreach ($retention[$taxTotal['tax_id']] as $values) {
                                    if ($retention[$taxTotal['tax_id']][$k]['percent'] = $taxTotal['percent']) {
                                        $retention[$taxTotal['tax_id']][$k]['taxable_amount'] += $taxTotal['taxable_amount'];
                                        $retention[$taxTotal['tax_id']][$k]['tax_amount'] += $taxTotal['tax_amount'];
                                    }
                                    $k++;
                                }
                            }
                            $l++;
                            $retention[$taxTotal['tax_id']][$l] = $taxTotal;
                        } else {
                            $retention[$taxTotal['tax_id']][$l] = $taxTotal;
                        }
                    }
                }
            }
        }

        $array = [];
        $info = [];


        if (isset($retention[5])) {
            array_push($info, (array)$retention[5]);
        }

        if (isset($retention[6])) {
            array_push($info, (array)$retention[6]);
        }

        if (isset($retention[7])) {
            array_push($info, (array)$retention[7]);
        }

        foreach ($info as $item) {
            foreach ($item as $item2) {
                if ($item2['percent'] != 0.00) {
                    array_push($array, $item2);
                }
            }
        }
        if (count($array) > 0) {
            $this->invoice['with_holding_tax_total'] = $array;
        }
        if (!empty($iva)) {
            $this->invoice['tax_totals'] = $iva;
        }


    }

    /**
     * Metodo para validar el tipo el tipo de impuesto
     * @param $name
     * @return int|void
     */
    private function _validationTax($name)
    {
        if (strpos($name, 'IVA') !== false || strpos($name, 'VAT') !== false) {
            return 1;
        } else if (strpos($name, 'RteFte') !== false) {
            return 6;
        } else if (strpos($name, 'RteICA') !== false) {
            return 7;
        }
    }

    /**
     * Metodo para identificar la resolucion de la empresa
     * @param $typeDocument
     * @return int
     */
    private function _resolution($typeDocument, $resolution_id = null)
    {
        $resolution = $this->resolution->where(['companies_id' => $this->idCompany]);

        if ($typeDocument != 1) {
            //$resolution->where(['resolution' => $id]);
            $consulta = ['type_documents_id' => $typeDocument];
            $resolution->where($consulta);
        } else {
            if (is_null($resolution_id)) {
                $resolution->where(['type_documents_id' => $typeDocument]);
            } else {
                $resolution->where(['type_documents_id' => $typeDocument, 'id' => $resolution_id]);
            }
        }

        $resolution = $resolution
            ->orderBy('id', 'DESC')
            ->asObject()
            ->first();


        $invoices = new Invoice();
        $invoices->select('invoices.resolution');
        if ($resolution->id) {
            $invoices->where(['companies_id' => $this->idCompany, 'resolution_id =' => $resolution->id]);
        } else {
            $invoices->where(['companies_id' => $this->idCompany, 'type_documents_id' => $typeDocument]);
        }


        $invoices = $invoices->orderBy('id', 'DESC')
            ->asObject()
            ->first();


        //echo json_encode($resolution);die();
        if (!$invoices) {
            return $resolution->from;
        } else {
            return $invoices->resolution + 1;
        }
    }

    /**
     * metodo para realizar envios de documentos a la Dian
     * @param $invoice
     * @param $id_shopify
     * @param $name
     * @param $prefix
     * @param null $id
     * @param null $type_document_id
     * @param null $numberOriginalInvoice
     */
    public function send($invoice, $id_shopify, $name, $prefix, $id = null, $type_document_id = null, $numberOriginalInvoice = null, $idIntegrationShopify = null)
    {
        $company = $this->tableCompany->where(['id' => $this->idCompany])->asObject()->first();
        $invoicesRejected = $this->tableIntegrationTrafficLight
            ->where(['companies_id' => $this->idCompany, 'integration_shopify_id' => $idIntegrationShopify,
                'type_document_id' => ($type_document_id != 4) ? 1 : 4, 'id_shopify' => $id_shopify, 'status' => 'rechazada'])
            ->orderBy('id', 'desc')
            ->limit(1)->get()->getResult();
        $curl = curl_init();
        $url = getenv('API') . '/ubl2.1/invoice';
        if ($type_document_id == 4) {
            $url = getenv('API') . '/ubl2.1/credit-note';
        }
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($invoice),
            CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: Bearer ' . $company->token
            ),
        ));

        $response_api = curl_exec($curl);

        curl_close($curl);
        $valor = json_decode($response_api);
        if (isset($valor->ResponseDian)) {
            //echo json_encode($valor->ResponseDian->Envelope->Body->SendBillSyncResponse->SendBillSyncResult->IsValid);die();
            if ($valor->ResponseDian->Envelope->Body->SendBillSyncResponse->SendBillSyncResult->IsValid == 'true') {
                $dataorder = [
                    'companies_id' => $this->idCompany,
                    'id_shopify' => $id_shopify,
                    'integration_shopify_id' => $idIntegrationShopify,
                    'number_mfl' => ($type_document_id != 4) ? (int)$invoice['number'] : $invoice->number,
                    'number_app' => $name,
                    'uuid' => ($type_document_id != 4) ? $valor->cufe : $valor->cude,
                    'observations' => ($type_document_id != 4) ? 'Factura enviada con exíto' : 'Nota crédito enviada con exíto, # factura: ' . $numberOriginalInvoice . '.',
                    'status' => 'aceptada',
                    'type_document_id' => ($type_document_id != 4) ? 1 : 4,
                    'check_return' => 0
                ];
                if ($type_document_id == 4) {
                    $dataInvoice = $this->tableIntegrationTrafficLight
                        ->where(['number_mfl' => $numberOriginalInvoice, 'integration_shopify_id' => $idIntegrationShopify,
                            'status' => 'aceptada', 'number_app' => $name])
                        ->asObject()->first();
                    $this->tableIntegrationTrafficLight
                        ->set(['observations' => 'Se a generado nota Crédito #' . $invoice->number . ' generada.',
                            'number_app' => $name . ' - pedido devuelto',
                            'id_shopify' => $dataInvoice->id_shopify . '0',
                            'status' => ($invoice->discrepancyresponsecode == 2)?'devuelto':'devuelto_prod',
                            'check_return' => ($invoice->discrepancyresponsecode == 2)?1:0])
                        ->where(['companies_id' => $this->idCompany, 'integration_shopify_id' => $idIntegrationShopify,
                            'number_mfl' => $numberOriginalInvoice])->update();
                }
                if (is_null($id) && getenv('DEVELOPMENT') == false) {
                    $this->_email($company->identification_number, $prefix, ($type_document_id != 4) ? (int)$invoice['number'] : $invoice->number);
                }
            } else {
                $dataorder = [
                    'companies_id' => $this->idCompany,
                    'id_shopify' => $id_shopify,
                    'integration_shopify_id' => $idIntegrationShopify,
                    'number_mfl' => ($type_document_id != 4) ? (int)$invoice['number'] : $invoice->number,
                    'number_app' => $name,
                    'observations' => json_encode($valor->ResponseDian->Envelope->Body->SendBillSyncResponse->SendBillSyncResult->ErrorMessage->string),
                    'status' => 'rechazada',
                    'type_document_id' => ($type_document_id != 4) ? 1 : 4,
                    'check_return' => 0
                ];
            }

        } else {
            if (isset($valor->errors)) {
                $errors = '';
                foreach ($valor->errors as $item) {
                    foreach ($item as $value) {
                        $errors .= '<p>' . $value . '</p>';
                    }
                }
            }
            $dataorder = [
                'companies_id' => $this->idCompany,
                'id_shopify' => $id_shopify,
                'integration_shopify_id' => $idIntegrationShopify,
                'number_mfl' => ($type_document_id != 4) ? (int)$invoice['number'] : $invoice->number,
                'number_app' => $name,
                'observations' => json_encode($errors),
                'status' => 'rechazada',
                'type_document_id' => ($type_document_id != 4) ? 1 : 4,
                'check_return' => 0
            ];
            $subject = ($type_document_id != 4) ? 'MFL - Factura Rechazada.' : 'MFL - Nota Crédito Rechazada';
            $body = 'Buen dia<br>';
            $body .= 'Acontinuación encontrará las causas por la cual el pedido con numero de orden: ' . $name . ' fue rechazado por la DIAN.<br>';
            $body .= 'Error ' . json_encode($valor->message);
            $this->email->send('soporte@mifacturalegal.com', 'MiFacturaLegal', 'john@mawii.com.co', $subject, $body);
        }
        //echo json_encode($dataorder);die();
        if (count($invoicesRejected) > 0) {
            $this->tableIntegrationTrafficLight->set($dataorder)->where(['id' => $invoicesRejected[0]->id, 'status' => 'rechazada'])->update();
            $this->saveLog($this->idCompany, $name, $dataorder['observations'], $invoicesRejected[0]->id);
        } else {
            $save = $this->tableIntegrationTrafficLight->insert($dataorder);
            $this->saveLog($this->idCompany, $name, $dataorder['observations'], $save);
        }

        array_push($this->orders, $dataorder);

        $resp = [
            'status' => $dataorder['status'],
            'observations' => $dataorder['observations']
        ];
        return $resp;


    }

    /**
     * Funcion para enviar correo si algún pedido tiene error en sus datos
     * @param $order
     * @param $companyId
     */
    private function enviarErrores($order, $companyId): void
    {
        $subject = 'Pedido: ' . $order->order_number . 'No puede ser facturada.';
        $body = 'Buen dia<br>';
        $body .= 'Acontinuciòn encontrarà las causas por la cual el pedido con numero de orden: ' . $order->order_number . ' no pudo ser facturado.<br>';
        $body .= json_encode($this->error);
        $this->email->send('soporte@mifacturalegal.com', 'MiFacturaLegal', 'john@mawii.com.co', $subject, $body);
        $this->saveLog($companyId, $order->order_number, json_encode($this->error));
    }

    /**
     * Metodo para realizar envio de correos a los clientes
     * @param $identification_number
     * @param $prefix
     * @param $resolution
     */
    public function _email($identification_number, $prefix, $resolution)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.mawii.xyz/api/send-email-customer/Now",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode([
                "company_idnumber" => $identification_number,
                "prefix" => empty($prefix) ? '' : $prefix,
                "number" => (string)$resolution
            ]),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "accept: application/json"
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $data = json_decode($response);
        //var_dump($data);
    }

    /**
     * Metodo para obtener datos de la aplicaciòn de shopify a la cual nos vamos a conectar
     * @param $companyId
     */
    public function companyExceptionShopiy($companyId, $shop = null)
    {
        if (is_null($shop)) {
            $this->exceptionShopify = $this->tableShopifyExceptions->where(['companies_id' => $companyId])->asObject()->first();
        } else {
            $this->exceptionShopify = $this->tableShopifyExceptions->where(['companies_id' => $companyId, 'shop' => $shop])->asObject()->first();
        }
        if (!is_null($this->exceptionShopify)) {
            $this->appShopify = $this->tableShopifyApps->where(['id' => $this->exceptionShopify->shopify_app_id, 'status' => 'Active'])
                ->asObject()->first();
            $this->typeApp = $this->appShopify->type_app;
        } else {
            $this->appShopify = $this->tableShopifyApps->where(['name' => 'estandar', 'status' => 'Active'])->asObject()->first();
        }
        $this->nameAplication = $this->appShopify->name_app;
        $this->client_id = $this->appShopify->client_id;
        $this->secret_id = $this->appShopify->secret_id;
        $this->redirectShopify = $this->appShopify->redirect_url;
    }

    /**
     * Metodo que identifica que pagina le va a mostrar al usuario
     * @param $company
     * @param $idIntegrationShopify
     */
    private function indexPage($company, $idIntegrationShopify = null): void
    {
        $trafficLight = $this->tableIntegrationTrafficLight->where(['companies_id' => $company]);
        $info_shopify = $this->table_integration_shopify->where(['companies_id' => $company])->asObject()->first();
        if (!is_null($idIntegrationShopify)) {
            $trafficLight->where(['integration_shopify_id' => $idIntegrationShopify]);
            $info_shopify = $this->table_integration_shopify->where(['id' => $idIntegrationShopify])->asObject()->first();
        }
        if (count($this->search()) != 0) {
            $trafficLight->where($this->search());
        }
        $trafficLight->orderBy('created_at', 'desc');
        if (is_null($idIntegrationShopify)) {
            $companies = $this->tableCompany
                ->where(['companies.id' => $company])
                ->asObject()->first();
        } else {
            $companies = $this->tableCompany
                ->join('resolutions', 'resolutions.companies_id = companies.id')
                ->where(['companies.id' => $company, 'resolutions.resolution' => $info_shopify->resolucion_id])
                ->asObject()->first();
        }
        $active = false;
        if ($this->controllerIntegration->activeIntegration('shopify', $company) > 0) {
            $active = true;
        }
        $data = [
            'trafficLight' => $trafficLight->paginate(10),
            'pager' => $trafficLight->pager,
            'invoiceId' => isset($invoiceId[0]) ? $invoiceId[0]->id : 0,
            'client_id' => $this->client_id,
            'scope' => $this->scope,
            'active' => $active,
            'searchShow' => $this->search(),
            'shop' => $info_shopify->name_shopify,
            'idCompany' => $company,
            'nameCompany' => $companies->company,
            'nit' => $companies->identification_number,
            'idIntegrationShopify' => $info_shopify->id,
            'prefix' => $companies->prefix
        ];
        echo view('integrations/Shopify', $data);
    }

    public function search()
    {
        $data = [];
        if (!empty($this->request->getGet('number_app'))) {
            $data['number_app'] = $this->request->getGet('number_app');
        }

        if (!empty($this->request->getGet('number_mfl'))) {
            $data['id_shopify'] = $this->request->getGet('number_mfl');
        }
        if (!empty($this->request->getGet('status'))) {
            $data['status'] = $this->request->getGet('status');
        }
        return $data;
    }

    /**
     * Metodo que permite realizar el guardado de log de los pedidos
     * @param $company_id
     * @param $orderNumber
     * @param $message
     * @param null $traffic_id
     */
    private function saveLog($company_id, $orderNumber, $message, $traffic_id = null)
    {
        try {
            $this->tableShopifyLog->save([
                'companies_id' => $company_id,
                'traffic_id' => $traffic_id,
                'order_number' => $orderNumber,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            $subject = 'Inconveniente al guardar log';
            $body = 'Hubo un inconvenient al tratar de guardar procesos en el log';
            $this->email->send('soporte@mifacturalegal.com', 'MiFacturaLegal', 'john@mawii.com.co', $subject, $body);
        }
    }

    /**
     * metodo para realizar notas credito completa
     */
    public function noteCredit()
    {
        try {
            header('Content-Type: application/json');
            $this->idCompany = $_POST['idCompany'];
            $company = $this->tableCompany->where(['id' => $this->idCompany])->asObject()->first();
            $infoShopify = $this->table_integration_shopify->where(['companies_id' => $this->idCompany, 'name_shopify' => $_POST['shop']])->asObject()->first();
            if (!is_null($company)) {
                $dataInvoice = $this->tableIntegrationTrafficLight
                    ->where(['companies_id' => $_POST['idCompany'], 'integration_shopify_id' => $infoShopify->id, 'number_mfl' => $_POST['number'],
                        'status' => 'aceptada'])
                    ->asObject()->first();
                if (!is_null($dataInvoice)) {
                    //resolution number
                    $companyResolutionId = $this->resolution
                        ->where(['companies_id' => $_POST['idCompany'], 'type_documents_id' => 4])
                        ->asObject()->first();
                    //verificar si ya esta rechazada
                    $invoicesRejected = $this->tableIntegrationTrafficLight
                        ->where(['companies_id' => $_POST['idCompany'],
                            'integration_shopify_id' => $infoShopify->id,
                            'type_document_id' => 4,
                            'id_shopify' => $dataInvoice->id_shopify,
                            'status' => 'rechazada'])
                        ->orderBy('id', 'desc')->limit(1)->get()->getResult();
                    //verifica el numero mayor de resolucion
                    $resolutions = $this->tableIntegrationTrafficLight->selectMax('number_mfl')
                        ->where(['companies_id' => $_POST['idCompany'], 'type_document_id' => 4])
                        ->asObject()->first();
                    if (count($invoicesRejected) > 0) {
                        $resolution = $invoicesRejected[0]->number_mfl;
                    } else {
                        if (is_null($resolutions->number_mfl)) {
                            $resolution = $this->_resolution(4, $companyResolutionId->id);
                        } else {
                            $resolution = $resolutions->number_mfl + 1;
                        }
                    }
                    $document = $this->tableDocumentsInvoices
                        ->where(['state_document_id' => 1,
                            'identification_number' => $company->identification_number,
                            'cufe' => $dataInvoice->uuid])
                        ->asObject()->first();
                    if (!is_null($document)) {
                        $data = json_decode($document->request_api);
                        $data->number = $resolution;
                        $data->type_document_id = 4;
                        $data->resolution_number = $companyResolutionId->resolution;
                        $data->date = date('Y-m-d');
                        $data->time = date('H:i:s');
                        $data->billing_reference['number'] = $dataInvoice->number_mfl;
                        $data->billing_reference['uuid'] = $dataInvoice->uuid;
                        $fecha = strtotime($dataInvoice->created_at);
                        $data->billing_reference['issue_date'] = date('Y-m-d', $fecha);
                        $data->discrepancyresponsecode = 2;
                        $data->discrepancyresponsedescription = 'Anulación de factura';
                        $data->prefix = 'NC';
                        $data->notes = 'Nota crèdito pedido: ' . $dataInvoice->number_app;
                        $data->credit_note_lines = $data->invoice_lines;
                        unset($data->invoice_lines);
                        unset($data->payment_form);
                        unset($data->with_holding_tax_total);
                        //echo json_encode($data);
                        $response = $this->send($data, $dataInvoice->id_shopify, $dataInvoice->number_app, $companyResolutionId->prefix, null,
                            4, $dataInvoice->number_mfl, $infoShopify->id);
                        return json_encode([
                            'status' => $response['status'],
                            'observation' => $response['observations'],
                            'dataInvoice' => $data
                        ]);
                    } else {
                        throw  new \Exception('No se encuentra factura en la api');
                    }
                } else {
                    throw  new \Exception('No se encuentra factura asociada a este consecutivo' . $_POST['number']);
                }

            } else {
                throw new \Exception('No se a encontrado la compañia subscrita para realizar ese proceso');
            }
        } catch (\Exception $e) {
            return json_encode([
                'status' => 'Rechazada',
                'observation' => $e->getMessage()
            ]);
        }

    }

    /**
     * metodo para realizar notas credito por productos
     */
    public function noteCreditByProduct()
    {
        try {
            $this->countLineInvoices = 0;
            $valorDescuento = 0;
            $selectDiscount = null;
            header('Content-Type: application/json');
            $this->idCompany = $_POST['idCompany'];
            $company = $this->tableCompany->where(['id' => $this->idCompany])->asObject()->first();
            $infoShopify = $this->table_integration_shopify->where(['companies_id' => $this->idCompany, 'name_shopify' => $_POST['shop']])->asObject()->first();
            if (!is_null($company)) {
                $dataInvoice = $this->tableIntegrationTrafficLight
                    ->where(['companies_id' => $_POST['idCompany'], 'integration_shopify_id' => $infoShopify->id, 'number_mfl' => $_POST['number'],
                        'status' => 'aceptada'])
                    ->asObject()->first();
                if (!is_null($dataInvoice)) {
                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => 'https://' . $infoShopify->name_shopify . '/admin/api/2022-07/orders/' . $dataInvoice->id_shopify . '.json',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'GET',
                        CURLOPT_HTTPHEADER => array(
                            'X-Shopify-Access-Token:' . $infoShopify->token,
                            'Accept: application/json',
                            'Content-Type: application/json'
                        ),
                    ));

                    $response = curl_exec($curl);
                    curl_close($curl);
                    //echo $response;die();
                    $dataInvoiceShopify = json_decode($response);

                    if(isset($dataInvoiceShopify->order)){
                        $order = $dataInvoiceShopify->order;
                        $this->discountShopify = $order->discount_applications;
                        $this->vat = (isset($order->tax_lines[0]->rate)) ? $order->tax_lines[0]->rate : 0;
                    }else{
                        throw  new \Exception('No se encuentra el pedido '.$dataInvoice->number_app.' en Shopify.');
                    }
                    $this->lineInvoicesReturn($order);
                    if(!isset($this->returns) || empty($this->returns)){
                        throw  new \Exception('El pedido '.$dataInvoice->number_app.' no tiene devolución de productos realizado.');
                    }
                    //resolution number
                    $this->invoice = [];
                    $this->error = [];
                    $companyResolutionId = $this->resolution
                        ->where(['companies_id' => $_POST['idCompany'], 'type_documents_id' => 4])
                        ->asObject()->first();
                    //verificar si ya esta rechazada
                    $invoicesRejected = $this->tableIntegrationTrafficLight
                        ->where(['companies_id' => $_POST['idCompany'],
                            'integration_shopify_id' => $infoShopify->id,
                            'type_document_id' => 4,
                            'id_shopify' => $dataInvoice->id_shopify,
                            'status' => 'rechazada'])
                        ->orderBy('id', 'desc')->limit(1)->get()->getResult();
                    //verifica el numero mayor de resolucion
                    $resolutions = $this->tableIntegrationTrafficLight->selectMax('number_mfl')
                        ->where(['companies_id' => $_POST['idCompany'], 'type_document_id' => 4])
                        ->asObject()->first();
                    if (count($invoicesRejected) > 0) {
                        $resolution = $invoicesRejected[0]->number_mfl;
                    } else {
                        if (is_null($resolutions->number_mfl)) {
                            $resolution = $this->_resolution(4, $companyResolutionId->id);
                        } else {
                            $resolution = $resolutions->number_mfl + 1;
                        }
                    }
                    $document = $this->tableDocumentsInvoices
                        ->where(['state_document_id' => 1,
                            'identification_number' => $company->identification_number,
                            'cufe' => $dataInvoice->uuid])
                        ->asObject()->first();
                    if (!is_null($document)) {
                        $data = json_decode($document->request_api);
                        $data->number = $resolution;
                        $data->type_document_id = 4;
                        $data->resolution_number = $companyResolutionId->resolution;
                        $data->date = date('Y-m-d');
                        $data->time = date('H:i:s');
                        $data->billing_reference['number'] = $dataInvoice->number_mfl;
                        $data->billing_reference['uuid'] = $dataInvoice->uuid;
                        $fecha = strtotime($dataInvoice->created_at);
                        $data->billing_reference['issue_date'] = date('Y-m-d', $fecha);
                        $data->discrepancyresponsecode = 1;
                        $data->discrepancyresponsedescription = 'Anulación Parcial';
                        $data->prefix = 'NC';
                        $data->notes = 'Nota crèdito pedido: ' . $dataInvoice->number_app;
                        $noteCreditForProduct = false;
                        foreach($data->invoice_lines as $invoice_line){
                            foreach ($order->refunds as $refund) {
                                foreach ($refund->refund_line_items as $refund_line_item) {
                                    if ($refund_line_item->restock_type == 'return') {
                                        $idItem = (!is_null($refund_line_item->line_item->product_id)) ? (string)$refund_line_item->line_item->product_id : (string)$refund_line_item->line_item->sku;
                                        if( $idItem == $invoice_line->code){
                                            $noteCreditForProduct = true;
                                        }
                                    }
                                }
                            }
                        }
                        if(!$noteCreditForProduct){
                            throw  new \Exception('El pedido '.$dataInvoice->number_app.' ya se encuentra facturado sin los productos reembolsados.');
                        }
                        unset($data->invoice_lines);
                        unset($data->payment_form);
                        unset($data->with_holding_tax_total);
                        if (!empty($order->note)) {
                            $notes = explode('**', $order->note);
                            if (isset($notes[2])) {
                                $valorDescuento = (int)$notes[2];
                            }
                        }
                        if (isset($order->discount_applications[0]->value) && $valorDescuento == 0) {
                            if ($order->discount_applications[0]->value_type == 'percentage') {
                                $valorDescuento = $order->discount_applications[0]->value;
                            } elseif ($order->discount_applications[0]->value_type == 'fixed_amount') {
                                $valorDescuento = -1;
                            }
                            $selectDiscount = $order->discount_applications[0]->target_selection;
                        } elseif ($valorDescuento == 0) {
                            $applicantDiscount = $this->tableShopifyApplicantDiscount
                                ->where(['companies_id' => $this->idCompany, 'order_number_shopify' => $order->order_number])
                                ->asObject()->first();
                            if (!is_null($applicantDiscount)) {
                                $valorDescuento = $applicantDiscount->percentage;
                            } else {
                                $valorDescuento = 0;
                            }
                        }
                        foreach ($order->refunds as $refund){
                            foreach ($refund->refund_line_items as $refund_line_item) {
                                $productExempt = null;
                                $quantity = $refund_line_item->quantity;
                                if ($refund_line_item->restock_type == 'return') {
                                    $discountBase = $this->getDiscountBase($refund_line_item->line_item);
                                    if ($this->taxes_included) {
                                        $precio_base = ($refund_line_item->line_item->price / ($this->vat + 1)) - $discountBase;
                                        if ($this->vat == 0) {
                                            $precio_base = ($refund_line_item->line_item->price / 1.19) - $discountBase;
                                        }
                                    } else {
                                        $precio_base = ($refund_line_item->line_item->price - $discountBase);
                                    }
                                    // valor descuento
                                    $this->valorDescuento($valorDescuento, $refund_line_item->line_item, $quantity, $selectDiscount, $precio_base, $productExempt);
                                    //$l++;
                                }
                            }
                        }
                        $data->credit_note_lines = $this->invoice['invoice_lines'];
                        $this->_legalMonetaryTotals();
                        $this->_taxesTotals();
                        $data->legal_monetary_totals = $this->invoice['legal_monetary_totals'];
                        $data->tax_totals = $this->invoice['tax_totals'];
                        //echo json_encode($data);die();
                        $response = $this->send($data, $dataInvoice->id_shopify, $dataInvoice->number_app, $companyResolutionId->prefix, null,
                            4, $dataInvoice->number_mfl, $infoShopify->id);
                       return json_encode([
                            'status' => $response['status'],
                            'observation' => $response['observations'],
                            'dataInvoice' => $data
                        ]);
                    } else {
                        throw  new \Exception('No se encuentra factura en la api');
                    }
                } else {
                    throw  new \Exception('No se encuentra factura asociada a este consecutivo' . $_POST['number']);
                }

            } else {
                throw new \Exception('No se a encontrado la compañia subscrita para realizar ese proceso');
            }
        } catch (\Exception $e) {
            return json_encode([
                'status' => 'Rechazada',
                'observation' => $e->getMessage()
            ]);
        }

    }

    /**
     * Metodo para verificar que productos estan para devolucion
     */
    public function productsForNoteCredit(){
        try {
            $productReturn = [];
            header('Content-Type: application/json');
            $this->idCompany = $_POST['idCompany'];
            $company = $this->tableCompany->where(['id' => $this->idCompany])->asObject()->first();
            $infoShopify = $this->table_integration_shopify->where(['companies_id' => $this->idCompany, 'name_shopify' => $_POST['shop']])->asObject()->first();
            if (!is_null($company)) {
                $dataInvoice = $this->tableIntegrationTrafficLight
                    ->where(['companies_id' => $_POST['idCompany'], 'integration_shopify_id' => $infoShopify->id, 'number_mfl' => $_POST['number'],
                        'status' => 'aceptada'])
                    ->asObject()->first();
                if (!is_null($dataInvoice)) {
                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => 'https://' . $infoShopify->name_shopify . '/admin/api/2022-07/orders/' . $dataInvoice->id_shopify . '.json',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'GET',
                        CURLOPT_HTTPHEADER => array(
                            'X-Shopify-Access-Token:' . $infoShopify->token,
                            'Accept: application/json',
                            'Content-Type: application/json'
                        ),
                    ));

                    $response = curl_exec($curl);
                    curl_close($curl);
                    //echo $response;die();
                    $dataInvoiceShopify = json_decode($response);
                    if(isset($dataInvoiceShopify->order)){
                        $order = $dataInvoiceShopify->order;
                    }else{
                        throw  new \Exception('No se encuentra el pedido '.$_POST['number'].' en Shopify.');
                    }
                    foreach ($order->refunds as $refund){
                        foreach ($refund->refund_line_items as $refund_line_item) {
                            if ($refund_line_item->restock_type == 'return') {
                                array_push($productReturn, [
                                    'nameProduct' => $refund_line_item->line_item->name,
                                    'quantity' => $refund_line_item->quantity
                                ]);
                            }
                        }
                    }
                    //echo json_encode($productReturn);die();
                    return json_encode([
                        'status' => 'aceptada',
                        'data' => $productReturn
                    ]);
                }else {
                    throw  new \Exception('No se encuentra factura asociada a este consecutivo' . $_POST['number']);
                }
            }else {
                throw new \Exception('No se a encontrado la compañia subscrita para realizar ese proceso');
            }
        }catch (\Exception $e){
            return json_encode([
                'status' => 'Rechazada',
                'observation' => $e->getMessage()
            ]);
        }

    }

    /**
     * funcion para regenerar pedido y crear factura nueva
     */
    public function regenerateOrder()
    {
        header('Content-Type: application/json');
        $this->idCompany = $_POST['idCompany'];
        try {
            $order = explode('-', $_POST['order']);
            $infoShopify = $this->table_integration_shopify->where(['companies_id' => $this->idCompany, 'name_shopify' => $_POST['shop']])->asObject()->first();
            $invoiceReturn = $this->tableIntegrationTrafficLight
                ->where(['companies_id' => $this->idCompany, 'integration_shopify_id' => $infoShopify->id, 'type_document_id' => 4, 'number_app' => trim($order[0]), 'status' => 'aceptada'])
                ->asObject()->first();
            if (!is_null($invoiceReturn)) {
                $resp = $this->orders($this->idCompany, $invoiceReturn->id_shopify, true, $infoShopify->id);
                if ($resp['status'] == 'aceptada') {
                    $this->tableIntegrationTrafficLight
                        ->set(['check_return' => 0])
                        ->where(['number_app' => $_POST['order'], 'integration_shopify_id' => $infoShopify->id, 'companies_id' => $this->idCompany])->update();
                }
                return json_encode([
                    'status' => $resp['status'],
                    'observation' => $resp['observations']
                ]);
            } else {

            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }


    }

    /**
     * Funcion para identificar si la factura esta con valor cero y corregir los inconveninetes al enviarla
     *
     */

    public function validationInvoiceZero()
    {
        if ($this->invoice['legal_monetary_totals']['payable_amount'] == 0) {
            foreach ($this->invoice['invoice_lines'] as $invoice => $item) {
                $item['free_of_charge_indicator'] = true;
                $item['reference_price_id'] = 1;
                unset($item['allowance_charges']);
                $this->invoice['invoice_lines'][$invoice] = $item;
            }
            //echo json_encode($this->invoice);
        }
    }

    /**
     * Funcion la cual crea un producto para colocar el valor correspondiente del valor de envio
     * @param $shipping_lines
     */
    public function productDelivery($shipping_lines, $countInvoices)
    {
        $l = $countInvoices++;
        $this->invoice['invoice_lines'][$l]['unit_measure_id'] = 70; // unidad de medida 70 unidad
        $this->invoice['invoice_lines'][$l]['invoiced_quantity'] = 1;
        $this->invoice['invoice_lines'][$l]['line_extension_amount'] = $shipping_lines->price;
        $this->invoice['invoice_lines'][$l]['free_of_charge_indicator'] = false; // si es gratis

        $this->invoice['invoice_lines'][$l]['description'] = 'Costo de envìo';
        $this->invoice['invoice_lines'][$l]['code'] = 'ENVIO';
        $this->invoice['invoice_lines'][$l]['type_item_identification_id'] = 4; // codigo personalizado por el cliente
        $this->invoice['invoice_lines'][$l]['price_amount'] = $shipping_lines->price;
        $this->invoice['invoice_lines'][$l]['base_quantity'] = 1; // relacionado con la unidad de medida, cantidad base

        $this->invoice['invoice_lines'][$l]['tax_totals'][0]['tax_id'] = $this->_validationTax('IVA');
        $this->invoice['invoice_lines'][$l]['tax_totals'][0]['tax_amount'] = 0;
        $this->invoice['invoice_lines'][$l]['tax_totals'][0]['taxable_amount'] = $shipping_lines->price;
        $this->invoice['invoice_lines'][$l]['tax_totals'][0]['percent'] = 0;

    }

    /**
     * @param int $valorDescuento
     * @param $line_item
     * @param $quantity
     * @param $selectDiscount
     * @param $precio_base
     * @param $productExempt
     */
    private function valorDescuento(int $valorDescuento, $line_item, $quantity, $selectDiscount, $precio_base, $productExempt): void
    {
        $quantityDiscount = count($this->discountShopify);
        if ($valorDescuento == -1) {
            if (isset($line_item->discount_allocations[0]->amount)) {
                if ($quantityDiscount >= 1) {
                    $discountValue = 0;
                    $discountValueTotal = 0;
                    foreach ($line_item->discount_allocations as $discount_allocation) {
                        $index = $discount_allocation->discount_application_index;
                        if ($this->discountShopify[$index]->allocation_method == 'each' && $this->discountShopify[$index]->target_selection == 'explicit') {
                            //$discountValue += $this->discountShopify[$index]->value;
                        } else {
                            $discountValueTotal += $discount_allocation->amount / ($this->vat + 1);
                        }
                    }
                    if ($this->taxes_included) {
                        $descuento = (($discountValue / ($this->vat + 1)) * $quantity) + $discountValueTotal;
                        if ($this->vat == 0) {
                            $descuento = (($discountValue / 1.19) * $quantity) + $discountValueTotal;
                        }
                    } else {
                        $descuento = ($discountValue * $quantity) + $discountValueTotal;
                    }
                } else {
                    if ($this->taxes_included) {
                        $descuento = $line_item->discount_allocations[0]->amount / ($this->vat + 1);
                        if ($this->vat == 0) {
                            $descuento = $line_item->discount_allocations[0]->amount / 1.19;
                        }
                    } else {
                        $descuento = $line_item->discount_allocations[0]->amount;
                    }
                }
            } else {
                $descuento = 0;
            }
        } else {
            if ($selectDiscount == 'entitled' || $selectDiscount == 'explicit') {
                $descuento = 0;
                if (isset($line_item->discount_allocations) && !empty($line_item->discount_allocations)) {
                    foreach ($line_item->discount_allocations as $discount_allocation) {
                        $index = $discount_allocation->discount_application_index;
                        if ($quantityDiscount >= 1) {
                            $descuento += ($quantity * $precio_base) * ($this->discountShopify[$index]->value / 100);
                        } else {
                            $descuento += ($quantity * $precio_base) * ($valorDescuento / 100);
                        }
                    }
                }
            } else {
                $descuento = ($quantity * $precio_base) * ($valorDescuento / 100);
            }
        }
        // valor item
        $line_extension_amount = ($quantity * $precio_base) - $descuento;


        $this->invoice['invoice_lines'][$this->countLineInvoices]['unit_measure_id'] = 70; // unidad de medida 70 unidad
        $this->invoice['invoice_lines'][$this->countLineInvoices]['invoiced_quantity'] = $quantity;
        $this->invoice['invoice_lines'][$this->countLineInvoices]['line_extension_amount'] = $line_extension_amount;
        $this->invoice['invoice_lines'][$this->countLineInvoices]['free_of_charge_indicator'] = false; // si es gratis
        //  colocar el producto gratis cuando el valor es cero
        if ($line_extension_amount == 0) {
            $this->invoice['invoice_lines'][$this->countLineInvoices]['free_of_charge_indicator'] = true;
            $this->invoice['invoice_lines'][$this->countLineInvoices]['reference_price_id'] = 1;
        }

        $this->invoice['invoice_lines'][$this->countLineInvoices]['description'] = $line_item->name;
        $this->invoice['invoice_lines'][$this->countLineInvoices]['code'] = (!is_null($line_item->product_id)) ? (string)$line_item->product_id : (string)$line_item->sku;
        $this->invoice['invoice_lines'][$this->countLineInvoices]['type_item_identification_id'] = 4; // codigo personalizado por el cliente
        $this->invoice['invoice_lines'][$this->countLineInvoices]['price_amount'] = $precio_base;
        $this->invoice['invoice_lines'][$this->countLineInvoices]['base_quantity'] = 1; // relacionado con la unidad de medida, cantidad base

        $d = 0;
        if (isset($line_item->discount_allocations) && !empty($line_item->discount_allocations)) {
            foreach ($line_item->discount_allocations as $discount_allocation) {
                $this->invoice['invoice_lines'][$this->countLineInvoices]['allowance_charges'][$d]['charge_indicator'] = false;
                $this->invoice['invoice_lines'][$this->countLineInvoices]['allowance_charges'][$d]['allowance_charge_reason'] = 'DESCUENTO GENERAL';
                $this->invoice['invoice_lines'][$this->countLineInvoices]['allowance_charges'][$d]['amount'] = $descuento;
                $this->invoice['invoice_lines'][$this->countLineInvoices]['allowance_charges'][$d]['base_amount'] = ($precio_base * $quantity);
            }
            $d++;
        } else {
            $this->invoice['invoice_lines'][$this->countLineInvoices]['allowance_charges'][0]['charge_indicator'] = false;
            $this->invoice['invoice_lines'][$this->countLineInvoices]['allowance_charges'][0]['allowance_charge_reason'] = 'DESCUENTO GENERAL';
            $this->invoice['invoice_lines'][$this->countLineInvoices]['allowance_charges'][0]['amount'] = $descuento;
            $this->invoice['invoice_lines'][$this->countLineInvoices]['allowance_charges'][0]['base_amount'] = ($precio_base * $quantity);
        }


        $i = 0;
        // si no marca iva en shopify, evalua aqui
        if (!isset($line_item->tax_lines[0]->price)) {
            $taxAmount = (double)$line_extension_amount * $this->vat;
            $percent = (string)abs($this->vat * 100);
            if (!is_null($productExempt)) {
                $taxAmount = 0;
                $percent = '0';
            }
            $this->invoice['invoice_lines'][$this->countLineInvoices]['tax_totals'][$i]['tax_id'] = $this->_validationTax('IVA');
            $this->invoice['invoice_lines'][$this->countLineInvoices]['tax_totals'][$i]['tax_amount'] = $taxAmount;
            $this->invoice['invoice_lines'][$this->countLineInvoices]['tax_totals'][$i]['taxable_amount'] = (double)$line_extension_amount;
            $this->invoice['invoice_lines'][$this->countLineInvoices]['tax_totals'][$i]['percent'] = $percent;
        }

        foreach ($line_item->tax_lines as $item) {
            if ($this->_validationTax($item->title) == 1) {
                $taxAmount = (double)$line_extension_amount * $this->vat;
                $percent = (string)abs($this->vat * 100);
                if (!is_null($productExempt)) {
                    $taxAmount = 0;
                    $percent = '0';
                }
                $this->invoice['invoice_lines'][$this->countLineInvoices]['tax_totals'][0]['tax_id'] = $this->_validationTax($item->title);
                $this->invoice['invoice_lines'][$this->countLineInvoices]['tax_totals'][0]['tax_amount'] = $taxAmount;
                $this->invoice['invoice_lines'][$this->countLineInvoices]['tax_totals'][0]['taxable_amount'] = $line_extension_amount;
                $this->invoice['invoice_lines'][$this->countLineInvoices]['tax_totals'][0]['percent'] = $percent;
            } else {
                // otros impuesto pendientes
                $this->invoice['invoice_lines'][$this->countLineInvoices]['with_holding_tax_total'][$i]['tax_id'] = $this->_validationTax($item->title);
                $this->invoice['invoice_lines'][$this->countLineInvoices]['with_holding_tax_total'][$i]['tax_amount'] = $item->price;
                $this->invoice['invoice_lines'][$this->countLineInvoices]['with_holding_tax_total'][$i]['taxable_amount'] = $line_extension_amount;
                $this->invoice['invoice_lines'][$this->countLineInvoices]['with_holding_tax_total'][$i]['percent'] = (string)abs($item->rate * 100);
            }
            $i++;
        }
        $this->countLineInvoices++;
    }

    /**
     * @param $refund_line_item
     * @return float|int
     */
    private function getDiscountBase($line_item)
    {
        $discountBase = 0;
        foreach ($line_item->discount_allocations as $discount_allocation) {
            $index = $discount_allocation->discount_application_index;
            if ($this->discountShopify[$index]->allocation_method == 'each' && $this->discountShopify[$index]->target_selection == 'explicit') {
                if ($this->taxes_included) {
                    $discountBase = $this->discountShopify[$index]->value / ($this->vat + 1);
                    if ($this->vat == 0) {
                        $discountBase = $this->discountShopify[$index]->value / 1.19;
                    }
                } else {
                    $discountBase = $this->discountShopify[$index]->value;
                }
            }
        }
        return $discountBase;
    }

}