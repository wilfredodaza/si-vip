<?php


namespace App\Controllers;


use App\Controllers\Api\Auth;
use App\Models\BudgetPurchaseOrder;
use App\Models\Invoice;
use App\Models\LineInvoice;
use App\Models\LineInvoiceTax;
use App\Models\Notification;
use App\Models\TrackingCustomer;
use App\Models\YearsPurchaseOrder;
use Config\Services;


class PurchaseOrderController extends BaseController
{
    protected $tableTrackingCustomer;
    protected $tableInvoices;
    protected $servicesClient;
    protected $controllerHeadquarters;
    protected $tableBudget;
    protected $tableYears;
    protected $tableLineInvoices;
    protected $tableTaxLineInvoices;

    public function __construct()
    {
        $this->tableTrackingCustomer = new TrackingCustomer();
        $this->tableInvoices = new Invoice();
        $this->servicesClient = Services::curlrequest();
        $this->controllerHeadquarters = new HeadquartersController();
        $this->tableBudget = new BudgetPurchaseOrder();
        $this->tableYears = new YearsPurchaseOrder();
        $this->tableLineInvoices = new LineInvoice();
        $this->tableTaxLineInvoices = new LineInvoiceTax();
    }

    public function index()
    {
        $manager = $this->controllerHeadquarters->permissionManager(session('user')->role_id);
        $months = [
            (object)['id' => 1, 'name' => 'Enero'],
            (object)['id' => 2, 'name' => 'Febrero'],
            (object)['id' => 3, 'name' => 'Marzo'],
            (object)['id' => 4, 'name' => 'Abril'],
            (object)['id' => 5, 'name' => 'Mayo'],
            (object)['id' => 6, 'name' => 'Junio'],
            (object)['id' => 7, 'name' => 'Julio'],
            (object)['id' => 8, 'name' => 'Agosto'],
            (object)['id' => 9, 'name' => 'Septiembre'],
            (object)['id' => 10, 'name' => 'Octubre'],
            (object)['id' => 11, 'name' => 'Noviembre'],
            (object)['id' => 12, 'name' => 'Diciembre'],
        ];
        $years = $this->tableYears->asObject()->get()->getResult();
        $createBudget = true;
        $indicadores = [];
        $year = ( !empty($this->request->getGet('year'))) ? $this->request->getGet('year') : date('Y');
        $month = ( !empty($this->request->getGet('month')))  ? $this->request->getGet('month') : date('m');
        $status = ( !empty($this->request->getGet('status')))  ? $this->request->getGet('status') : '';
        $daysMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $firtsDay = "{$year}-{$month}-01";
        $endDay = "{$year}-{$month}-{$daysMonth}";
        $query = [
            'invoices.created_at >=' => date('Y-m-d', strtotime($firtsDay)) . ' 00:00:00',
            'invoices.created_at <=' => date('Y-m-d', strtotime($endDay)) . ' 23:59:59',
            'invoices.type_documents_id' => 114,
        ];
        if($status != ''){
            $query['invoices.invoice_status_id'] = $status;
        }
        $budget = $this->tableBudget->where(['year' => $year, "month" => $month])->asObject()->first();
        if (is_null($budget)) {
            $createBudget = false;
        }
        $budgetTotal = (!is_null($budget)) ? $budget->value : 0;
        // echo json_encode($executed);die();
        array_push($indicadores, (object)[
            'id' => 'presupuesto',
            'color' => 'green',
            'icon' => 'verified_user',
            'name' => 'Presupuesto',
            'observaciones' => '',
            'total' => $budgetTotal
        ]);
        array_push($indicadores, (object)[
            'id' => 'causado',
            'color' => 'yellow',
            'icon' => 'trending_down',
            'name' => 'Causado',
            'observaciones' => 'Valor OC Realizadas',
            'total' => $this->_validateExpenses($year, $month)
        ]);
        array_push($indicadores, (object)[
            'id' => 'ejecutado',
            'color' => 'red',
            'icon' => 'trending_down',
            'name' => 'Ejecutado',
            'observaciones' => 'Valor entrada remisiones',
            'total' => $this->_validateExecuted($year, $month)
        ]);
        array_push($indicadores, (object)[
            'id' => 'saldo',
            'color' => 'blue',
            'icon' => 'attach_money',
            'name' => 'Saldo',
            'observaciones' => 'Presupuesto - causado',
            'total' => ($budgetTotal - $this->_validateExpenses($year, $month))
        ]);
        $invoices = $this->tableInvoices
            ->select('invoice_status.name as status,
            customers.name as customer, 
            invoices.id as id_invoice,
            invoices.payable_amount,
            invoices.resolution,
            invoices.created_at,
            invoices.companies_id,
            invoices.invoice_status_id,
            ')
            ->join('type_documents', 'invoices.type_documents_id = type_documents.id')
            ->join('customers', 'invoices.customers_id = customers.id')
            ->join('invoice_status', 'invoices.invoice_status_id = invoice_status.id')
            ->where($query);
        $invoices->whereIn('invoices.companies_id', $this->controllerHeadquarters->idsCompaniesHeadquarters());
        $invoices->orderBy('invoices.id', 'desc');
        $data = [
            'invoices' => $invoices->paginate(10),
            'pager' => $invoices->pager,
            'manager' => $manager,
            'indicadores' => $indicadores,
            'months' => $months,
            'valuesExecuted' => $this->_validateValueExecuted($year, $month),
            'years' => $years
        ];

        return view('PurchaseOrder/index', $data);
    }

    public function create()
    {
        return view('PurchaseOrder/create');
    }

    public function email($id)
    {
        try {
            $invoices = $this->tableInvoices->select('customers.email, customers.email2, customers.email3, customers.name, 
            invoices.resolution, companies.identification_number, companies.email as email_company')
                ->join('companies', 'invoices.companies_id = companies.id')
                ->join('customers', 'invoices.customers_id = customers.id')
                ->where(['invoices.companies_id' => Auth::querys()->companies_id, 'invoices.id' => $id])
                ->get()
                ->getResult();


            if (count($invoices) > 0) {

                $res = $this->servicesClient->post(getenv('API') . '/purchaseOrder/email',
                    [
                        'http_errors' => false,
                        'form_params' => [
                            'email' => $invoices[0]->email,
                            'email2' => $invoices[0]->email2,
                            'email3' => $invoices[0]->email3,
                            'name' => $invoices[0]->name,
                            'identification_number' => $invoices[0]->identification_number,
                            'resolution' => $invoices[0]->resolution,
                            'email_company' => $invoices[0]->email_company,
                        ],
                        'headers' => [
                            'Accept' => 'application/json'
                        ],

                    ]);

                $response = json_decode($res->getBody());

                if (isset($response->status) && $response->status == 200) {
                    return redirect()->to(base_url() . route_to('purchaseOrder-index'))->with('success', $response->message);
                } else {
                    return redirect()->to(base_url() . route_to('purchaseOrder-index'))->with('error', 'No se pudo enviar el documento');
                }

            }
        } catch (\Exception $e) {
            return redirect()->to(base_url() . route_to('purchaseOrder-index'))->with('error', $e->getMessage());
        }

    }

    public function edit($id)
    {
        $bloqueo = 'true';
        $manager = $this->controllerHeadquarters->permissionManager(session('user')->role_id);
        if ($manager) {
            $bloqueo = 'false';
        }
        return view('PurchaseOrder/edit', ['id' => $id, 'bloqueo' => $bloqueo]);
    }

    public function editRemision($id){
        return view('PurchaseOrder/editRemision', ['id' => $id]);
    }

    public function tracking($id = null)
    {
        $data = $this->tableTrackingCustomer
            ->where(['table_id' => $id, '`type_tracking_id' => 4])
            ->orderBy('id', 'desc')
            ->get()
            ->getResult();
        $invoicesTracking = $this->tableInvoices->select('invoices.invoice_status_id')
            ->where('id', $id)
            ->get()
            ->getResult();
        return view('PurchaseOrder/tracking', ['id' => $id, 'data' => $data, 'tracking' => $invoicesTracking]);
    }

    public function indexBudget()
    {
        $causados = [];
        $month = [];
        $months = [
            (object)['id' => 1, 'name' => 'Enero'],
            (object)['id' => 2, 'name' => 'Febrero'],
            (object)['id' => 3, 'name' => 'Marzo'],
            (object)['id' => 4, 'name' => 'Abril'],
            (object)['id' => 5, 'name' => 'Mayo'],
            (object)['id' => 6, 'name' => 'Junio'],
            (object)['id' => 7, 'name' => 'Julio'],
            (object)['id' => 8, 'name' => 'Agosto'],
            (object)['id' => 9, 'name' => 'Septiembre'],
            (object)['id' => 10, 'name' => 'Octubre'],
            (object)['id' => 11, 'name' => 'Noviembre'],
            (object)['id' => 12, 'name' => 'Diciembre'],
        ];
        $budget = $this->tableBudget->asObject();
        $budgets = $budget->get()->getResult();
        foreach ($budgets as $i => $item) {
            $causados[$item->id] = $this->_validateExpenses($item->year, $item->month);
            foreach ($months as $key){
                if($item->month == $key->id){
                    $month[$item->id] = $key->name;
                }
            }
        }
        $data = [
            'budgets' => $budget->paginate(10),
            'pager' => $budget->pager,
            'causados' => $causados,
            'months' => $months,
            'month' => $month
        ];
        return view('PurchaseOrder/budget', $data);
    }

    public function createBudget()
    {
        if ($this->tableBudget->save(['year' => $_POST['year'], 'month' => $_POST['month'], 'value' => $_POST['value']])) {
            return redirect()->to(base_url() . route_to('purchaseOrder-indexBudget'))->with('success', 'Presupuesto guardado con exíto');
        } else {
            return redirect()->to(base_url() . route_to('purchaseOrder-indexBudget'))->with('errors', 'No se puedo guardar presupuesto');
        }
    }

    public function validateExpiration(){
        $fechaActual = date('Y-m-d');
        $datetime2 = date_create($fechaActual);
        $invoices = $this->tableInvoices
            ->select('
            invoices.id,
            invoices.created_at
            ')
            ->where(['invoices.type_documents_id' => 114, 'invoices.invoice_status_id' => 5])->asObject()->get()->getResult();
        foreach ($invoices as $order){
            $datetime1 = date_create(date('Y-m-d', strtotime($order->created_at)));
            $contador = date_diff($datetime1, $datetime2);
            $differenceFormat = '%a';
            $diferencia =$contador->format($differenceFormat);
            if($diferencia > 10){
                $notificacion = new Notification();
                $data = [
                    'title' => "Vencida la orden de compra con Id N° {$order->id}",
                    'body' => "La orden de compra con Id N° {$order->id} tiene más de 10 dias de haber sido registrada en el sistema y no se ha cerrado",
                    'icon' => 'receipt',
                    'color' => 'cyan',
                    'companies_id' => session('user')->companies_id,
                    'status' => 'Active',
                    'created_at' => date('Y-m-d'),
                    'view' => 'false',
                    'type_document_id' => 114,
                    'url' => 'purchaseOrder/edit'
                ];
                $notificacion->save($data);
            }
        }
    }

    private function _validateExpenses($year, $month)
    {
        $db = db_connect();
        $sql = 'SELECT SUM(payable_amount) as total FROM  invoices  WHERE invoices.type_documents_id = :id: AND MONTH(invoices.created_at) = :m: AND YEAR(invoices.created_at) = :y:';
        $data = $db->query($sql, [
            'id' => 114,
            'm' => $month,
            'y' => $year,
        ])->getResultObject()[0];

        return $data->total;
    }
    private function _validateExecuted($year, $month)
    {
        $ids = [];
        $db = db_connect();
        $sql = 'SELECT invoices.id FROM  invoices  WHERE invoices.type_documents_id = :id: AND MONTH(invoices.created_at) = :m: AND YEAR(invoices.created_at) = :y:';
        $data = $db->query($sql, [
            'id' => 114,
            'm' => $month,
            'y' => $year,
        ])->getResultObject();
        foreach ($data as $item){
            array_push($ids, $item->id);
        }
        if(empty($ids)){
            return 0;
        }
        $executeds = new Invoice();
        $executed = $executeds
            ->select('
                SUM(payable_amount) AS total'
            )
            ->whereIn('invoices.resolution_credit', $ids)
            ->asObject()->get()->getResult()[0];

        return $executed->total;
    }
    private function _validateValueExecuted($year, $month){
        $values = [];
        $db = db_connect();
        $sql = 'SELECT invoices.id FROM  invoices  WHERE invoices.type_documents_id = :id: AND MONTH(invoices.created_at) = :m: AND YEAR(invoices.created_at) = :y:';
        $data = $db->query($sql, [
            'id' => 114,
            'm' => $month,
            'y' => $year,
        ])->getResultObject();
        $executeds = new Invoice();
        foreach ($data as $item){
            $executed = $executeds
                ->select('
                SUM(payable_amount) AS total'
                )
                ->where(['invoices.resolution_credit' => $item->id])
                ->asObject()->get()->getResult()[0];
           $values[$item->id] = (is_null($executed->total))?0:$executed->total;
        }
        return $values;
    }
    public function view($id)
    {

        $taxTotal = 0;
        $document = $this->tableInvoices
            ->select([
                'invoices.id',
                'invoices.notes',
                'invoices.line_extesion_amount',
                'invoices.tax_inclusive_amount',
                'invoices.tax_exclusive_amount',
                'customers.email',
                'type_documents.name as nameDocument',
                'type_document_identifications.name as typeDocumentIdentification',
                'invoices.payable_amount',
                'invoices.issue_date',
                'invoices.payment_due_date',
                'invoices.resolution',
                'customers.name as name',
                'customers.identification_number as identification',
                'customers.phone',
                'customers.address',
            ])
            ->join('customers', 'customers.id = invoices.customers_id')
            ->join('type_documents', 'invoices.type_documents_id = type_documents.id')
            ->join('type_document_identifications', 'customers.type_document_identifications_id = type_document_identifications.id')
            ->where(['invoices.id' => $id])->asObject()->first();
        $lineDocuments = $this->tableLineInvoices
            ->select([
                'line_invoices.line_extension_amount',
                'line_invoices.description',
                'products.free_of_charge_indicator',
                'products.code',
                'products.name',
                'line_invoices.id',
                'line_invoices.quantity',
                'line_invoices.products_id',
                'line_invoices.price_amount',
                'line_invoices.provider_id',
                'line_invoices.discount_amount'
            ])
            ->join('products', 'products.id = line_invoices.products_id')
            ->where(['invoices_id' => $document->id])
            ->asObject()
            ->findAll();
        foreach ($lineDocuments as $item){
            $taxes = $this->tableTaxLineInvoices->where(['line_invoices_id' => $item->id])->whereIn('taxes_id', [5,6,7])->asObject()->get()->getResult();
            foreach ($taxes as $tax){
                $taxTotal += $tax->tax_amount;
            }
        }
        $quantities = [];
        $orders = $this->tableInvoices->select([
            'line_invoices.quantity',
            'line_invoices.products_id'
        ])
            ->join('line_invoices', 'invoices.id = line_invoices.invoices_id')
            ->where(['invoices.resolution_credit' => $id])
            ->asObject()->get()->getResult();
        if(count($orders) > 0){
            foreach ($orders as $order) {
                if(!isset($quantities[$order->products_id])){
                    $quantities[$order->products_id] = (int)$order->quantity;
                }else{
                    $quantities[$order->products_id] = (int)$quantities[$order->products_id] + (int)$order->quantity;
                }
            }
        }
        // data remission
        $remissions = $this->tableInvoices->select([
            'SUM(line_invoices.quantity) as quantity',
            'invoices.created_at',
            'invoices.payable_amount',
            'companies.company',
            'invoices.id',
            'invoices.resolution'
        ])
            ->join('line_invoices', 'invoices.id = line_invoices.invoices_id')
            ->join('companies', 'companies.id = invoices.companies_id')
            ->where(['invoices.resolution_credit' => $id])
            ->groupBy('invoices.id')
            ->asObject()->get()->getResult();
         // echo json_encode($remissions);die();
        // echo json_encode($lineDocuments);die();
        return view('PurchaseOrder/view', [
            'id' => $id,
            'document' => $document,
            'lineDocuments' => $lineDocuments,
            'taxTotal' => $taxTotal,
            'quantities' => $quantities,
            'remissions' => $remissions
        ]);
    }
}
