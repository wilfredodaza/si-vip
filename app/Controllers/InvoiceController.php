<?php


namespace App\Controllers;

use App\Models\Certificate;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Resolution;
use App\Models\IntegrationShopify;
use App\Controllers\Api\Auth;
use App\Controllers\Managers\Manager;
use App\Models\Customer;
use App\Models\DocumentInvoice;
use App\Models\LineInvoice;
use App\Models\LineInvoiceTax;
use App\Models\Product;
use App\Traits\RequestAPITrait;
use App\Traits\ValidateResponseAPITrait;
use CodeigniterExt\Queue\Queue;
use CodeigniterExt\Queue\Task;
use Config\Services;
use GroceryCrud\Core\Exceptions\Exception;
use App\Traits\InvoiceTrait;

class InvoiceController extends BaseController
{
    use InvoiceTrait, RequestAPITrait, ValidateResponseAPITrait;

    public $tableInvoices;
    public $tableResolutions;
    public $controllerHeadquarters;

    public function __construct()
    {
        $this->tableInvoices = new Invoice();
        $this->tableResolutions = new Resolution();
        $this->controllerHeadquarters = new HeadquartersController();
    }

    public function index()
    {
        $manager = $this->controllerHeadquarters->permissionManager(session('user')->role_id);
        $invoice = new Invoice();
        $invoices = $invoice->select('*, invoice_status.name as status,
		invoices.created_at as created_at,
            type_documents.name  as type_document, 
            invoices.companies_id as companies_id,
	        customers.id as customers_id,
	        companies.company,
            customers.name as customer, invoices.id as id_invoice');
        if (isset($_GET['value']) && $this->search() != 'invoices.customers_id') {
            $invoices->like($this->search(), isset($_GET['value']) ? $this->request->getGet('value') : '', 'both');
        }
        $invoices->join('type_documents', 'invoices.type_documents_id = type_documents.id')
            ->join('customers', 'invoices.customers_id = customers.id')
            ->join('companies', 'invoices.companies_id = companies.id')
            ->join('invoice_status', 'invoices.invoice_status_id = invoice_status.id')
            ->where('invoices.type_documents_id <=', 5);


        if ($manager) {
            $invoices->whereIn('invoices.companies_id', $this->controllerHeadquarters->idsCompaniesHeadquarters());
        } else {
            $invoices->where('invoices.companies_id', Auth::querys()->companies_id);
        }
        if (isset($_GET['value']) && $this->search() == 'invoices.customers_id') {
            $invoices->where('invoices.customers_id', $_GET['value']);
        }

        $invoices->orderBy('invoices.id', 'desc');

        $invoiceEnd = new Invoice();
        /*$invoiceId = $invoiceEnd->select('invoices.id')->orderBy('invoices.id', 'asc')
            ->where(['invoice_status_id' => 1, 'companies_id' => Auth::querys()->companies_id])
	    ->whereIn('invoices.type_documents_id', [1,2,3,4,5])
            ->get()
            ->getResult();*/
        $ids = [];
        $idsValidate = [];
        $customer = new Customer();
        $customers = $customer->where(['type_customer_id <' => 3,])->whereIn('companies_id',$this->controllerHeadquarters->idsCompaniesHeadquarters(Auth::querys()->companies_id))
            ->orderBy('name')
            ->get()
            ->getResult();
        foreach ($customers as $key => $client) {
            if (!is_null($client->headquarters_id)) {
                if (in_array($client->headquarters_id, $idsValidate)) {
                    array_push($ids, $key);
                } else {
                    array_push($idsValidate, $client->headquarters_id);
                }
            }
        }
        //echo json_encode();die();
        foreach ($ids as $key => $id) {
            unset($customers[$id]);
        }
        $customers = array_values($customers);


        $integration = new IntegrationShopify();
        $integrationShopify = $integration->where(['companies_id' => Auth::querys()->companies_id])
            ->asObject()
            ->first();
        $resolution = new Resolution();
        $resolutions = $resolution
            ->whereIn('type_documents_id', [1, 4, 5])
            ->whereIn('companies_id', $this->controllerHeadquarters->idsCompaniesHeadquarters())
            ->where(['status' => null])
            ->orderBy('priority', 'DESC')
            ->asObject()
            ->get()
            ->getResult();

        $data = [
            'invoices' => $invoices->paginate(10),
            'pager' => $invoices->pager,
            'customers' => $customers,
            'integrationShopify' => $integrationShopify,
            'resolutions' => $resolutions,
            'manager' => $manager
        ];

        if (Auth::querys()->role_id != 1) {
            $manager = new Manager();
            $manager->setCompanyId(Auth::querys()->companies_id);
            $manager->createNotification();
        }


        return view('invoice/index', $data);
    }

    public function edit($id = null)
    {

        $invoice = new Invoice();
        $invoices = $invoice->where([
            //'companies_id'          => Auth::querys()->companies_id,
            'id' => $id,
            'type_documents_id <' => 100
        ])->whereIn('invoice_status_id', [1, 27])->countAllResults();

        if ($invoices != 0) {
            return view('invoice/edit', ['id' => $id]);
        } else {
            return redirect()->to(base_url('/invoice'));
        }

    }

    public function create()
    {
        return view('invoice/create');
    }

    public function search()
    {
        if (isset($_GET['campo'])) {
            switch ($_GET['campo']) {
                case 'resolution':
                    $campo = 'invoices.resolution';
                    break;
                case 'Estado':
                    $campo = 'invoices.invoice_status_id';
                    break;
                case 'Cliente':
                    $campo = 'invoices.customers_id';
                    break;
                case 'Tipo_de_factura':
                    $campo = 'invoices.type_documents_id';
                    break;
            }

        } else {
            $campo = 'invoices.resolution';
        }
        return $campo;
    }

    public function pdf($company, $id)
    {
        $invoice = new Invoice();
        $data = $invoice->where(['id' => $id, 'companies_id' => $company])
            ->get()
            ->getResult();

        if ($data[0]->type_documents_id == '1' || $data[0]->type_documents_id == '2') {
            $companies = new Resolution();
            $resolution = $companies->where(['companies_id' => $company, 'resolution' => $data[0]->resolution_id, 'type_documents_id' => 1])->asObject()->get()->getResult();
            $name = 'FES-' . $resolution[0]->prefix . $data[0]->resolution;
            $secondName = 'FES-' . $data[0]->resolution;
        } else if ($data[0]->type_documents_id === '4') {
            $companies = new Resolution();
            $resolution = $companies->where(['companies_id' => $company, 'type_documents_id' => 4])->get()->getResult();
            $name = 'NCS-' . $resolution[0]->prefix . $data[0]->resolution;
            $secondName = 'NCS-' . $data[0]->resolution;
        } else if ($data[0]->type_documents_id === '5') {
            $companies = new Resolution();
            $resolution = $companies->where(['companies_id' => $company, 'type_documents_id' => 5])->get()->getResult();
            $name = 'NDS-' . $resolution[0]->prefix . $data[0]->resolution;
            $secondName = 'NDS-' . $data[0]->resolution;
        } else if ($data[0]->type_documents_id === '100') {
            $companies = new Resolution();
            $resolution = $companies->where(['companies_id' => $company, 'type_documents_id' => 100])->get()->getResult();
            $name = 'COT-' . $data[0]->resolution;

        }
        $datas = new Company();
        $companies = $datas->asObject()->find(['id' => $company]);

        //echo  $name . ".pdf";die();

        $client = Services::curlrequest();

        try {
            $res = $client->get(getenv('API') . "/download/" . $companies[0]->identification_number . "/" . $name . '.pdf');
            header("Content-disposition: attachment; filename=" . $name . ".pdf");
            header("Content-type: application/pdf");
            readfile(getenv('API') . "/download/" . $companies[0]->identification_number . "/" . $name . '.pdf');
        } catch (\Exception $e) {
            header("Content-disposition: attachment; filename=" . $secondName . ".pdf");
            header("Content-type: application/pdf");
            readfile(getenv('API') . "/download/" . $companies[0]->identification_number . "/" . $secondName . '.pdf');
        }
    }

    public function receivedMail()
    {
        $companies = new Company();
        $companies = $companies->where(['identification_number' => $this->request->getGet('nit')])->asObject()->first();
        $invoice = new Invoice();
        $invoice->set(['invoice_status_id' => 4])
            ->where([
                'companies_id' => $companies->id,
                'resolution' => $this->request->getGet('resolucion'),
                'type_documents_id' => $this->request->getGet('type_document')
            ])
            ->update();

        header('Content-type: image/png');
        $data = file_get_contents(base_url() . '/assets/img/bk.png');
        readfile($data);
    }

    public function attachedDocument($id = null)
    {

        $invoice = new Invoice();
        $data = $invoice->where(['id' => $id, 'companies_id' => Auth::querys()->companies_id])
            ->get()
            ->getResult();
        if (strtotime($data[0]->created_at) <= strtotime('2021-08-18 00:00:00')) {


            if ($data[0]->type_documents_id == '1' || $data[0]->type_documents_id == '2') {
                $companies = new Resolution();
                $resolution = $companies
                    ->where(['companies_id' => Auth::querys()->companies_id, 'type_documents_id' => 1, 'resolution' => $data[0]->resolution_id])->asObject()->get()->getResult();
                $name = 'Attachment-' . $resolution[0]->prefix . $data[0]->resolution;
            } else if ($data[0]->type_documents_id === '4') {
                $companies = new Resolution();
                $resolution = $companies->where(['companies_id' => Auth::querys()->companies_id, 'type_documents_id' => 4])->get()->getResult();
                $name = 'Attachment-' . $resolution[0]->prefix . $data[0]->resolution;
            } else if ($data[0]->type_documents_id === '5') {
                $companies = new Resolution();
                $resolution = $companies->where(['companies_id' => Auth::querys()->companies_id, 'type_documents_id' => 5])->get()->getResult();
                $name = 'Attachment-' . $resolution[0]->prefix . $data[0]->resolution;
            }
            $company = new Company();
            $company_data = $company->asObject()->find(Auth::querys()->companies_id);


            header("Content-disposition: attachment; filename=" . $name . ".zip");
            header("Content-type: application/zip, application/octet-stream, application/x-zip-compressed, multipart/x-zip");
            readfile(getenv('API') . "/download/" . $company_data->identification_number . "/" . $name . '.zip');
        } else {
            $client = Services::curlrequest();
            $companies = new Company();
            $company = $companies->select(['certificates.password', 'certificates.name', 'companies.identification_number'])
                ->join('certificates', 'certificates.companies_id = companies.id')
                ->where(['companies.id' => $data[0]->companies_id])
                ->asObject()
                ->first();


            $client->setHeader('Content-Type', 'application/json');
            $client->setHeader('Accept', 'application/json');


            $res = $client->post(
                getenv('API') . '/ubl2.1/statusdocument', [
                    'form_params' => [
                        'cufe' => $data[0]->uuid,
                        'certificate' => base64_encode(file_get_contents(base_url() . '/assets/upload/certificates/' . $company->name)),
                        'password' => $company->password,
                        'ambiente' => 'PRODUCCION'

                    ],
                ]
            );


            $json = json_decode($res->getBody());
            if (isset($json->errors)) {
                echo json_encode($json);
                die();
            }

            $filename = str_replace('nd', 'ad', str_replace('nc', 'ad', str_replace('fv', 'ad', $json->ResponseDian->Envelope->Body->GetStatusResponse->GetStatusResult->XmlFileName)));
            header("Content-disposition: attachment; filename=" . $filename . ".zip");
            header("Content-type: application/zip, application/octet-stream, application/x-zip-compressed, multipart/x-zip");
            readfile(getenv('API') . "/download/" . $company->identification_number . "/" . $filename . '.zip');
        }


    }

    public function consolidation()
    {

        $model = new Company();
        $company = $model
            ->where(['id' => Auth::querys()->companies_id])
            ->asObject()
            ->first();

        $model = new Resolution();
        $resolutions = $model->where([
            'companies_id' => Auth::querys()->companies_id,
            'type_documents_id <=' => 5
        ])
            ->whereIn('type_documents_id', [1, 4, 5])
            ->asObject()
            ->get()
            ->getResult();
        // echo json_encode($resolutions);die();

        foreach ($resolutions as $resolution) {
            switch ($resolution->type_documents_id) {
                // case 20:
                case 1:
                    $this->createInvoice([1, 2], $company->identification_number, $resolution->resolution);

                case 4:

                    $this->createInvoice([4], $company->identification_number, $resolution->resolution);


                case 5:
                    //$this->createInvoice([5],       $company->identification_number, $resolution->resolution ?? null);

            }

        }

        return redirect()->to(base_url('/invoice'))->with('success', 'Los documentos fueron descargados correctamente.');
    }

    public function createInvoice($typeDocument, $identificationNumber, $resolutionNumber)
    {

        $invoice = new Invoice();
        $resolution = $invoice->select(['resolution'])
            ->where(['companies_id' => Auth::querys()->companies_id, 'resolution_id' => $resolutionNumber])
            ->whereIn('type_documents_id', $typeDocument)
            ->orderby('CAST(resolution as UNSIGNED)', 'DESC')
            ->asObject()
            ->first();


        $query = [
            'state_document_id' => 1,
            'identification_number' => $identificationNumber,
            'JSON_EXTRACT(request_api, "$.resolution_number")' => $resolutionNumber
        ];

        if (!is_null($resolution)) {
            $query['CAST(number as UNSIGNED) >'] = $resolution->resolution;
        }


        $model = new  DocumentInvoice();
        $documents = $model
            ->select(['documents.*', 'CAST(request_api as JSON) as request_api'])
            ->where($query)
            ->whereIn('type_document_id', $typeDocument)
            ->asObject()
            ->get()
            ->getResult();


        foreach ($documents as $document) {
            $data = json_decode($document->request_api);
            $customer = new Customer();
            $customers = $customer->where(['identification_number' => $data->customer->identification_number])
                ->asObject()
                ->first();

            if (is_null($customers)) {
                $idCustomer = $customer->insert([
                    'name' => $data->customer->name,
                    'email' => $data->customer->email,
                    'phone' => $data->customer->phone,
                    'address' => $data->customer->address,
                    'type_regime_id' => isset($data->customer->type_regime_id) ? $data->customer->type_regime_id : null,
                    'type_customer_id' => 1,
                    'municipality_id' => isset($data->customer->municipality_id) ? $data->customer->municipality_id : null,
                    'type_organization_id' => $data->customer->type_organization_id,
                    'identification_number' => $data->customer->identification_number,
                    'merchant_registration' => $data->customer->merchant_registration,
                    'type_document_identifications_id' => $data->customer->type_document_identification_id,
                    'companies_id' => Auth::querys()->companies_id,
                    'created_at' => $document->created_at
                ]);
            }

            $model = new Invoice();
            $idInvoice = $model->insert([
                'resolution' => $data->number,
                'payment_forms_id' => $data->payment_form->payment_form_id ?? null,
                'payment_methods_id' => $data->payment_form->payment_method_id ?? null,
                'payment_due_date' => $data->payment_form->payment_due_date ?? null,
                'duration_measure' => $data->payment_form->duration_measure ?? null,
                'line_extesion_amount' => $data->legal_monetary_totals->line_extension_amount,
                'tax_exclusive_amount' => $data->legal_monetary_totals->tax_exclusive_amount,
                'tax_inclusive_amount' => $data->legal_monetary_totals->tax_inclusive_amount,
                'allowance_total_amount' => $data->legal_monetary_totals->allowance_total_amount,
                'charge_total_amount' => $data->legal_monetary_totals->charge_total_amount,
                'payable_amount' => $data->legal_monetary_totals->payable_amount,
                'type_documents_id' => $data->type_document_id,
                'customers_id' => is_null($customers) ? $idCustomer : $customers->id,
                'companies_id' => Auth::querys()->companies_id,
                'resolution_id' => $data->resolution_number,
                'invoice_status_id' => 2,
                'notes' => $data->notes ?? null,
                'idcurrency' => isset($data->idcurrency) ? $data->idcurrency : 35,
                'calculationrate' => isset($data->calculationrate) ? $data->calculationrate : 1,
                'calculationratedate' => isset($data->calculationratedate) ? $data->calculationratedate : date('Y-m-d'),
                'uuid' => $document->cufe,
                'created_at' => $document->created_at
            ]);

            $lineas = [];
            if (isset($data->invoice_lines)) {
                $lineas = $data->invoice_lines;
            } else if (isset($data->credit_note_lines)) {
                $lineas = $data->credit_note_lines;
            } else if (isset($data->debit_note_lines)) {
                $lineas = $data->debit_note_lines;
            }

            foreach ($lineas as $line) {
                $product = new Product();
                $products = $product->where(['code' => $line->code])
                    ->asObject()
                    ->first();

                if (is_null($products)) {
                    $idProduct = $product->insert([
                        'companies_id' => Auth::querys()->companies_id,
                        'category_id' => 1,
                        'unit_measures_id' => 70,
                        'reference_prices_id' => 1,
                        'type_item_identifications_id' => 4,
                        'code' => $line->code,
                        'name' => $line->description,
                        'description' => $line->description,
                        'valor' => $line->price_amount,
                        'free_of_charge_indicator' => $line->free_of_charge_indicator
                    ]);
                }

                $model = new LineInvoice();
                $idLineInvoice = $model->insert([
                    'discount_amount' => isset($lineas->allowance_charges) ? $lineas->allowance_charges[0]->amount : 0,
                    'discounts_id' => 1,
                    'quantity' => $line->invoiced_quantity,
                    'line_extension_amount' => $line->line_extension_amount,
                    'price_amount' => $line->price_amount,
                    'products_id' => is_null($products) ? $idProduct : $products->id,
                    'description' => $line->description,
                    'invoices_id' => $idInvoice
                ]);

                if (!isset($line->tax_totals)) {
                    $model = new LineInvoiceTax();
                    $model->insert([
                        'line_invoices_id' => $idLineInvoice,
                        'taxes_id' => 1,
                        'tax_amount' => 0,
                        'taxable_amount' => 0,
                        'percent' => 0
                    ]);
                } else {
                    $model = new LineInvoiceTax();
                    $model->insert([
                        'line_invoices_id' => $idLineInvoice,
                        'taxes_id' => 1,
                        'tax_amount' => $line->tax_totals[0]->tax_amount,
                        'taxable_amount' => $line->tax_totals[0]->taxable_amount,
                        'percent' => $line->tax_totals[0]->percent,
                    ]);
                }

                $model = new LineInvoiceTax();
                $model->insert([
                    'line_invoices_id' => $idLineInvoice,
                    'taxes_id' => 5,
                    'tax_amount' => 0,
                    'taxable_amount' => isset($line->tax_totals) ? $line->tax_totals[0]->taxable_amount : $line->line_extension_amount,
                    'percent' => 0,
                ]);
                $model = new LineInvoiceTax();
                $model->insert([
                    'line_invoices_id' => $idLineInvoice,
                    'taxes_id' => 6,
                    'tax_amount' => 0,
                    'taxable_amount' => isset($line->tax_totals) ? $line->tax_totals[0]->taxable_amount : $line->line_extension_amount,
                    'percent' => 0,
                ]);

                $model = new LineInvoiceTax();
                $model->insert([
                    'line_invoices_id' => $idLineInvoice,
                    'taxes_id' => 7,
                    'tax_amount' => 0,
                    'taxable_amount' => isset($line->tax_totals) ? $line->tax_totals[0]->taxable_amount : $line->line_extension_amount,
                    'percent' => 0,
                ]);
            }
        }

    }

    /**
     * Funci�n que realiza el eliminado l�gico de las facturas
     * @return false|string
     */
    public function delete()
    {
        try {
            header('Content-Type: application/json');
            $invoice = $this->tableInvoices->where(['id' => $_POST['id']])->asObject()->first();
            if (!is_null($invoice)) {
                if (!empty($invoice->resolution)) {
                    throw new \Exception('No se puede eliminar la factura ya que tiene n�mero de consecutivo asignado.');
                }
            }
            if ($this->tableInvoices->set(['invoice_status_id' => 25])->where(['id' => $_POST['id']])->update()) {
                if ($this->tableInvoices->delete($_POST['id'])) {
                    return json_encode([
                        'status' => 'aceptada',
                        'observation' => 'Factura eliminada con ex�to',
                    ]);
                } else {
                    throw new \Exception('No se logr� eliminar la factura');
                }
            } else {
                throw new \Exception('No se logr� eliminar la factura');
            }
        } catch (\Exception $e) {
            return json_encode([
                'status' => 'Rechazada',
                'observation' => $e->getMessage()
            ]);
        }
    }

    /**
     * Funci�n que realiza el env�o masivo de facturas para que queden guardadas como tareas
     * @return \CodeIgniter\HTTP\RedirectResponse
     * @throws \ReflectionException
     */
    public function sendMultiple()
    {

        $model = new Invoice();
        $documents = $model->select(['invoices.id'])
            ->whereIn('invoices.type_documents_id', [1, 2])
            ->where([
                'invoice_status_id' => 1,
                'companies_id' => Auth::querys()->companies_id,
                'send' => 'False'
            ])->get()
            ->getResult();

        $resolution = $this->tableResolutions->where(['id' => $this->request->getPost('resolution_id')])->asObject()->first();
        $i = 0;
        foreach ($documents as $item) {
            $model = new Invoice();
            $model->set('invoice_status_id', 26)
                ->where(['id' => $item->id])
                ->update();

            $task = new Task;
            $task->setName('App/Controllers/Queues/SendInvoice')
                ->setData([
                    'id' => $item->id,
                    'resolution' => $resolution->id,
                    'companies_id' => Auth::querys()->companies_id
                ])->setPriority(Task::PRIORITY_HIGH)
                ->setUniqueId('Invoices-' . $item->id . $resolution->resolution . Auth::querys()->companies_id);
            $queue = new Queue();
            $queue->addTask($task);
            $i++;
        }
        return redirect()->to(base_url('invoice'));

    }


    /**
     * M�todo encargado de validar el cufe de la factura electronica.
     * @return void
     */
    public function validationUUID($id = null)
    {

        $invoice = new Invoice();
        $data = $invoice->select([
            'invoices.resolution',
            'companies.token',
            'certificates.name',
            'certificates.password',
            'resolutions.prefix',
            'invoices.uuid',
            'invoices.type_documents_id',
            'customers.identification_number as customer_id'
        ])
            ->join('companies', 'invoices.companies_id = companies.id')
            ->join('customers', 'invoices.customers_id = customers.id')
            ->join('certificates', 'companies.id = certificates.companies_id')
            ->join('resolutions', 'resolutions.resolution = invoices.resolution_id')
            ->where([
                'invoices.companies_id' => Auth::querys()->companies_id,
                'invoices.id' => $id
            ])
            ->asObject()
            ->first();


        // echo json_encode($data);die();

        $client = Services::curlrequest();
        $client->setHeader('Content-Type', 'application/json');
        $client->setHeader('Accept', 'application/json');
        $client->setHeader('Authorization', "Bearer " . $data->token);

        //echo getenv('API').'/api/ubl2.1/validation-cufe';die();

        $res = $client->post(
            getenv('API') . '/ubl2.1/validation-cufe', [
                'form_params' => [
                    'certificate' => base64_encode(file_get_contents(base_url() . '/assets/upload/certificates/' . $data->name)),
                    'password' => $data->password,
                    'cufe' => $data->uuid,
                    'ambiente' => (getenv('DEVELOPMENT') == 'true' ? 'HABILITACION' : 'PRODUCCION'),
                    'prefix' => $data->prefix,
                    'resolution' => $data->resolution,
                    'client_id' => $data->customer_id,
                    'type_document_id' => $data->type_documents_id
                ],
                'http_errors' => false

            ]
        );
        $json = json_decode($res->getBody());

        if ($json->transaction == 'true') {
            $invoice = new Invoice();
            $invoice->set('invoice_status_id', 2)
                ->where(['invoices.id' => $id])
                ->update();
            return redirect()->to(base_url('invoice'))->with('success', 'Documento validado con exito.');
        } else {
            return redirect()->to(base_url('invoice'))->with('errors', 'No pudo ser verificado el CUFE por favor comun�cate con soporte t�cnico.');
        }
        if (isset($json->errors)) {
            echo json_encode($json);
            die();
        }
    }

}


