<?php

namespace App\Controllers;

use App\Models\AccountingAcount;
use App\Models\Category;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\LineInvoice;
use App\Models\LineInvoiceTax;
use App\Models\Payroll;
use App\Models\Wallet;
use App\Models\WalletLineInvoice;
use App\Models\User;
use App\Models\Product;
use App\Models\PaymentMethod;
use App\Traits\PayrollTrait;
use App\Traits\RequestAPITrait;
use App\Traits\ValidateResponseAPITrait;
use CodeIgniter\API\ResponseTrait;
use Config\Services;
use App\Controllers\Api\Auth;
use CodeigniterExt\Queue\Queue;
use CodeigniterExt\Queue\Task;
use phpDocumentor\Reflection\TypeResolver;


class  PayrollController extends BaseController
{
    use PayrollTrait, ResponseTrait, RequestAPITrait, ValidateResponseAPITrait;

    /**
     * View de create payroll
     * @return string
     */
    public function create()
    {
        return view('payroll/create');
    }

    /**
     * View edit payroll
     * @param null $id
     * @return string
     */
    public function edit($id = null)
    {
        return view('payroll/edit', ['id' => $id]);
    }

    /**
     * Send of payroll  the DIAN
     * @param null $id
     * @return \CodeIgniter\HTTP\RedirectResponse
     * @throws \ReflectionException
     */

    public function send($id = null)
    {
        $resolution = $this->request->getPost('resolution');
        $data = $this->group($id, $resolution, Auth::querys()->companies_id);


        $model = new Company();
        $company = $model->asObject()->find(Auth::querys()->companies_id);

        $link = $data['type_document_id'] == 10 ? 'payroll-adjust-note' : 'payroll';
        $env = $company->type_environment_payroll_id == 2 ? '/' . $company->testId : '';

        $model = new Payroll();
        $payroll = $model->select(['period_id'])->where(['invoice_id' => $id])->asObject()->first();


        try {
            $res = $this->sendRequest(getenv('API') . '/ubl2.1/' . $link . $env, $data, 'post', $company->token);
            $documentStatus = $this->validStatusCodeHTTP($id, $company->type_environment_payroll_id, $res, $data['type_document_id']);
        } catch (\Exception $e) {
            echo $e->getMessage();
            /*if($data['type_document_id'] == 9) {
                return redirect()->to(base_url('periods/'.$payroll->period_id))->with('errors', 'HTTP 500 - Error del Servidor');
            }else {
                return redirect()->to(base_url('worker/'.$data['customer_id']))->with('errors', 'HTTP 500 - Error del Servidor');
            }*/
        } catch (\TypeError $e2) {
            echo $e2->getMessage();
            /*   if($data['type_document_id'] == 9) {
                   return redirect()->to(base_url('periods/'.$payroll->period_id))->with('errors', 'HTTP 500 - Error del Servidor');
               }else {
                   return redirect()->to(base_url('worker/'.$data['customer_id']))->with('errors', 'HTTP 500 - Error del Servidor');
               }*/
        }


        if ($documentStatus->error) {
            if ($data['type_document_id'] == 9) {
                return redirect()->to(base_url('periods/' . $payroll->period_id))->with('errors', $documentStatus->messages);
            } else {
                return redirect()->to(base_url('worker/' . $data['customer_id']))->with('errors', $documentStatus->messages);
            }
        } else {
            $invoice = new Invoice();
            $invoice->update($id, [
                'invoice_status_id' => 14,
                'resolution' => $data['consecutive'],
                'resolution_id' => $data['resolution_number'],
                'prefix' => $data['prefix']
            ]);
            if ($data['type_document_id'] == 9) {
                return redirect()->to(base_url('periods/' . $payroll->period_id))->with('success', $documentStatus->messages);
            } else {
                return redirect()->to(base_url('worker/' . $data['customer_id']))->with('success', $documentStatus->messages);
            }
        }
    }

    /**
     * Send multiple of payrolls the DIAN
     * @param null $id
     * @return \CodeIgniter\HTTP\RedirectResponse
     * @throws \ReflectionException
     */

    public function sendMultiple($id = null)
    {
        $documents = $this->request->getPost('payrolls');
        $resolution = $this->request->getPost('resolution');
        $i = 0;
        foreach (explode(',', $documents[0]) as $item) {
            $model = new Invoice();
            $invoice = $model->select(['companies_id'])->asObject()->find($item);

            if ($invoice->companies_id == Auth::querys()->companies_id) {
                $model = new Invoice();
                $model->update($item, ['invoice_status_id' => 16]);

                $queue = new Queue();
                $task = new Task;
                $task->setName('App/Controllers/Queues/SendPayroll')
                    ->setData([
                        'id' => $item,
                        'resolution' => $resolution,
                        'companies_id' => Auth::querys()->companies_id
                    ])->setPriority(Task::PRIORITY_HIGH)
                    ->setUniqueId('payrolls-' . $i . $item . Auth::querys()->companies_id);
                $queue->addTask($task);

                $i++;
            }
        }
        return redirect()->to(base_url('periods/' . $id));

    }

    public function previsualization($id = null)
    {

        $data = $this->group($id, 0, Auth::querys()->companies_id);
        $model = new Customer();
        $customer = $model->select(['customers.id'])
            ->where(['customers.user_id' => Auth::querys()->id])
            ->asObject()
            ->first();


        if (Auth::querys()->role_id == 7) {
            if ($customer) {
                if ($data['customer_id'] != $customer->id) {
                    return view('errors/html/error_401');
                }
            }
        }

        $model = new Company();
        $company = $model->asObject()->find(Auth::querys()->companies_id);
        $data['type_document_id'] = 9;
        $res = $this->sendRequest(getenv('API') . '/ubl2.1/previsualization/payroll', $data, 'post', $company->token);
        switch ($res->status) {
            case '200':
                return redirect()->back()->with('success', 'La nomina fue editada correctamente.');
            case '422':
                $errorText = '';
                $errors = $res->data;
                foreach ($errors->errors as $error => $key) {
                    $errorText .= '<p>' . lang('payroll_errors.payroll_errors.' . $error) . '</p>';
                }
                $model = new Invoice();
                $model->update($id, ['invoice_status_id' => 15, 'errors' => $errorText]);
                return redirect()->back()->with('errors', $errorText);
            default:
                $model = new Invoice();
                $model->update($id, ['invoice_status_id' => 15, 'errors' => $res->data]);
                return redirect()->back()->with('errors', 'HTTP 500 - Error del Servidor');
        }


    }

    public function downloadPrevisualization($id = null)
    {

        $this->previsualization($id);
        $model = new Invoice();
        $invoice = $model
            ->select([
                'companies.identification_number',
                'invoices.prefix',
                'invoices.resolution'
            ])
            ->join('companies', 'invoices.companies_id = companies.id')
            ->where(['invoices.id' => $id])
            ->asObject()
            ->first();

        $model = new Customer();
        $customer = $model->select(['customers.id'])
            ->where(['customers.user_id' => Auth::querys()->id])
            ->asObject()
            ->first();


        if (Auth::querys()->role_id == 7) {
            if ($customer) {
                if ($invoice->customers_id != $customer->id) {
                    return view('errors/html/error_401');
                }
            }
        }


        $name = 'NIS-PREV' . $id;
        $this->downloadFile(getenv('API') . "/invoice/" . $invoice->identification_number . "/" . $name . '.pdf', 'application/pdf', $name . '.pdf');
    }

    public function download($id = null)
    {
        $model = new Customer();
        $customer = $model->select(['customers.id'])
            ->where(['customers.user_id' => Auth::querys()->id])
            ->asObject()
            ->first();

        $model = new Invoice();
        $invoice = $model
            ->select([
                'companies.identification_number',
                'invoices.prefix',
                'invoices.resolution',
                'invoices.type_documents_id',
                'invoices.customers_id'
            ])
            ->join('companies', 'invoices.companies_id = companies.id')
            ->where(['invoices.id' => $id])
            ->asObject()
            ->first();

        if (Auth::querys()->role_id == 7) {
            if ($customer) {
                if ($invoice->customers_id != $customer->id) {
                    return view('errors/html/error_401');
                }
            }
        }


        $name = 'NIS-' . $invoice->prefix . '' . $invoice->resolution;
        $this->downloadFile(getenv('API') . "/invoice/" . $invoice->identification_number . "/" . $name . '.pdf', 'application/pdf', $name . '.pdf');
    }

    public function xml($id = null)
    {
        $model = new Customer();
        $customer = $model->select(['customers.id'])
            ->where(['customers.user_id' => Auth::querys()->id])
            ->asObject()
            ->first();

        $model = new Invoice();
        $invoice = $model
            ->select([
                'companies.identification_number',
                'invoices.prefix',
                'invoices.resolution',
                'invoices.type_documents_id',
                'invoices.customers_id'
            ])
            ->join('companies', 'invoices.companies_id = companies.id')
            ->where(['invoices.id' => $id])
            ->asObject()
            ->first();


        if ($invoice->type_documents_id == 9) {
            $name = 'NIS-' . $invoice->prefix . '' . $invoice->resolution;
        } else {
            $name = 'NAS-' . $invoice->prefix . '' . $invoice->resolution;
        }


        if (Auth::querys()->role_id == 7) {
            if ($customer) {
                if ($invoice->customers_id != $customer->id) {
                    return view('errors/html/error_401');
                }
            }
        }


        $this->downloadFile(getenv('API') . '/invoice/' . $invoice->identification_number . '/' . $name . '.xml', 'application/xml', $name . '.xml');

    }

    public function statusZIP($id = null)
    {
        $invoice = new Invoice();
        $payroll = $invoice->select([
            'invoices.zipkey',
            'invoices.id as id',
            'payrolls.id as payroll_id',
            'payrolls.period_id',
            'invoices.companies_id',
            'certificates.name',
            'certificates.password'
        ])
            ->join('payrolls', 'payrolls.invoice_id = invoices.id')
            ->join('certificates', 'certificates.companies_id = invoices.companies_id')
            ->asObject()
            ->find($id);


        $client = Services::curlrequest();

        $client->setHeader('Content-Type', 'application/json');
        $client->setHeader('Accept', 'application/json');

        $data = [
            'certificate' => base64_encode(file_get_contents(base_url() . '/assets/upload/certificates/' . $payroll->name)),
            'password' => $payroll->password,
            'zipkey' => $payroll->zipkey,
            'ambiente' => 'HABILITACION'
        ];

        $res = $client->post(getenv('API') . '/ubl2.1/statuszip', [
                'http_errors' => false,
                'form_params' => $data,
            ]
        );
        $status = json_decode($res->getBody());
        if ((string)$status->ResponseDian->Envelope->Body->GetStatusZipResponse->GetStatusZipResult->DianResponse->IsValid == 'true') {
            return redirect()->to(base_url(base_url('periods/' . $payroll->period_id)))->with('success', $status->ResponseDian->Envelope->Body->GetStatusZipResponse->GetStatusZipResult->DianResponse->StatusDescription);
        } else {
            $invoice = new Invoice();
            $invoice->update($id, ['invoice_status_id' => 15]);
            $model = new Invoice();
            $model->update($payroll->id, ['errors' => $status->ResponseDian->Envelope->Body->GetStatusZipResponse->GetStatusZipResult->DianResponse->StatusDescription]);
        }

        return redirect()->to(base_url('periods/' . $payroll->period_id));
    }

    /**
     * Validation DIAN
     * @param null $cune
     * @return \CodeIgniter\HTTP\RedirectResponse
     */

    public function cune($CUNE = null)
    {
        return redirect()->to('https://catalogo-vpfe.dian.gov.co/Document/ShowDocumentToPublic/' . $CUNE);
    }

    public function index()
    {
        $tableUser = new User();
        $invoices = new Invoice();
        $querys = [
            'invoices.type_documents_id' => 118
        ];
        $user = (object)[];
        $seller = 0;
        $month = $this->request->getGet('date') ? $this->request->getGet('date') : 0;
        $year = $this->request->getGet('year') ? $this->request->getGet('year') : 0;
        $validDate = date('Y-m') == $year.'-'.$month ? true : false;
        $nomina = $invoices->where([
            'type_documents_id' => 120,
            'MONTH(payment_due_date)' => $month,
            'YEAR(payment_due_date)' => $year,
            'seller_id' => $this->request->getGet('user'),
        ])->asObject()->first();
        if($nomina) $nomina->line_invoice = $invoices->getLineInvoices($nomina->id);
        // var_dump($nomina); die;

        if ($this->request->getGet('user')) {
            $seller = $this->request->getGet('user');
            $user = $tableUser
                ->select(['users.name', 'users.phone', 'users.address', 'users.status', 'customer_worker.salary'])
                ->join('customer_worker', 'users.id = customer_worker.user_id', 'left')
                ->where(['users.id' => $this->request->getGet('user')])->asObject()->first();
            // var_dump($user); die;
        }

        $data = $invoices
        ->select([
            'invoices.id',
            'invoices.payment_due_date',
            'invoices.notes',
            'invoices.payable_amount',
            'SUM(COALESCE(wallet_line_invoice.value, 0)) as valor_pagado',
            'invoices.payable_amount - SUM(COALESCE(wallet_line_invoice.value, 0)) as balance'
        ])
        ->where([
            'type_documents_id' => 118,
            'seller_id' => $this->request->getGet('customer'),
            'status_wallet' => 'Pendiente'
        ])
        ->join('wallet', 'invoices.id = wallet.invoices_id', 'left')
        ->join('line_invoices', 'invoices.id = line_invoices.invoices_id', 'left')
        ->join('wallet_line_invoice', 'line_invoices.id = wallet_line_invoice.line_invoice_id', 'left')
        ->groupBy('invoices.id')
        ->having('balance !=', 0)
        ->asObject()->get()->getResult();

        foreach ($data as $key => $value) {
            $value->line_invoice = $invoices->getLineInvoices($value->id);
        }

        // var_dump($data); die();

        $db = db_connect();
        $sql = 'SELECT invoices.created_at, invoices.id,payable_amount as total,products.name as product_name,line_invoices.start_date,line_invoices.line_extension_amount FROM line_invoices INNER JOIN invoices ON invoices.id = line_invoices.invoices_id INNER JOIN products ON products.id = line_invoices.products_id INNER JOIN category ON products.category_id = category.id WHERE invoices.type_documents_id = :id: AND invoices.status_wallet = "Pendiente" AND  invoices.seller_id = :seller: AND category.payroll = "si"';
        // $data = $db->query($sql, )->getResultObject();
        // echo json_encode($data);die();
        $users = $tableUser->asObject()->get()->getResult();
        
        //$data = $invoices->where($querys)->asObject()->get()->getResult();
        $companyM = new Company();
        $controllerHeadquarters = new HeadquartersController();
        $headquarters = $companyM
                    ->select('companies.id, companies.company')
                    ->whereIn('id', $controllerHeadquarters->idsCompaniesHeadquarters())
                    ->asObject()->get()->getResult();

        if (count($data) == 0) {
            $data = [];
        }
        // var_dump($data); die();

        $accountingAcountM = new AccountingAcount();
        $paymentMethod = $accountingAcountM->where(['type_accounting_account_id' => 5])->asObject()->get()->getResult();

        $categories = new Category();
        $category = $categories->select(['id'])->where(['payroll' => 'si', 'expenses' => 'si'])->asObject()->first();
        $products = new Product();
        $product = $products->select()->where(['code' => 'paymentOtro'])->asObject()->first();
        // validamos si el producto es nulo
        if (empty($product)) {
            // devolvemos id producto
            $product = $this->createProductPaymentPayroll($category->id, 'Pago Otros', 'paymentOtro');
            $product = $products->select()->where(['id' => $product])->asObject()->first();
        }

        // $paymentMethodCompanys = new AccountingAcount();
        // $paymentMethod = $paymentMethodCompanys
        // ->where(['companies_id' => Auth::querys()->companies_id, 'type_accounting_account_id' => 5])->asObject()->get()->getResult();
        return view('payroll/payroll', [
            'users' => $users,
            'user' => $user,
            'data' => $data,
            'months' => $this->months(),
            'sedes' => $headquarters,
            'paymentMethod' => $paymentMethod,
            'nomina'    => $nomina,
            'productOtros' => $product,
            'validDate' => $validDate,
        ]);
    }

    private function months(): array
    {
        return [
            (object)['id' => '01', 'name' => 'Enero']
            , (object)['id' => '02', 'name' => 'Febrero']
            , (object)['id' => '03', 'name' => 'Marzo']
            , (object)['id' => '04', 'name' => 'Abril']
            , (object)['id' => '05', 'name' => 'Mayo']
            , (object)['id' => '06', 'name' => 'Junio']
            , (object)['id' => '07', 'name' => 'Julio']
            , (object)['id' => '08', 'name' => 'Agosto']
            , (object)['id' => '09', 'name' => 'Septiembre']
            , (object)['id' => '10', 'name' => 'Octubre']
            , (object)['id' => '11', 'name' => 'Noviembre']
            , (object)['id' => '12', 'name' => 'Diciembre']
        ];
    }

    /**
     * Función encargada de realizar el proceso de pago de nomina
     * @param $seller
     * @param $valor
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function createClosePayroll()//$seller, $valor
    {
        $requests = $this->request->getJSON();
        try {
            $model = new Invoice();
            $categories = new Category();
            $products = new Product();
            $lineInvoices = new LineInvoice();
            // validamos si existe una categoria enfocada en nomina
            $category = $categories->select(['id'])->where(['payroll' => 'si', 'expenses' => 'si'])->asObject()->first();
            // validamos is categoria es nulo
            if (!is_null($category)) {
                // Buscamos el producto tipo gatos con el codigo de pago nomina
                $product = $products->select(['id'])->where(['kind_product_id' => 3, 'category_id' => $category->id, 'code' => 'paymentPayroll'])->asObject()->first();
                // validamos si el producto es nulo
                if (!is_null($product)) {
                    // devolvemos id producto
                    $id_product = $product->id;
                } else {
                    // creamos el producto con codigo pago nomina
                    $id_product = $this->createProductPaymentPayroll($category->id);
                    // si este no se logra crear se retorna a la vista del cliente
                    if($id_product == 0){
                        throw  new \Exception('No existe producto para liquidación de nomina');
                    }
                }
            } else {
                throw  new \Exception('No Existe categoria tipo nomina');
            }

            $account = new AccountingAcount();
            $account = $account->where(['id' => $requests->payment_method_id])->asObject()->first();
            $invoice = $model->where(['type_documents_id' => 120])->orderBy('id', 'DESC')->asObject()->first();
            
            $model = new Invoice();
            $invoiceId = $model->insert([
                'resolution'                => $invoice ? ((int)$invoice->resolution + 1) : 1,
                'type_documents_id' => 120,
                'invoice_status_id' => 8,
                'companies_id' => Auth::querys()->companies_id,
                'company_destination_id' => Auth::querys()->companies_id, //$requests->sede_id
                'customers_id' => null,
                'user_id' => Auth::querys()->id,
                'seller_id' => $requests->user,
                'payment_forms_id' => 1,
                'payment_methods_id' => ($account->type_entry == 1 ? 47 : 10),
                'payment_due_date' => $requests->year.'-'.$requests->date.'-'.date('t', strtotime("$requests->year-$requests->date-01")),
                'duration_measure' => 0,
                'line_extesion_amount' => $requests->salary, //(($requests->salary + $requests->bono) - (int)$requests->expense)
                'tax_exclusive_amount' => (int)$requests->expense, //(($requests->salary + $requests->bono) - (int)$requests->expense)
                'tax_inclusive_amount' => $requests->bono, //(($requests->salary + $requests->bono) - (int)$requests->expense)
                'allowance_total_amount' => 0,
                'charge_total_amount' => 0,
                'notes' => 'Pago nomina',
                'pre_paid_amount' => 0,
                'status_wallet' => 'Paga',
                'payable_amount' => (($requests->salary + $requests->bono) - (int)$requests->expense),
            ]);

            if (!isset($invoiceId)) {
                throw  new \Exception('Inconveniente al crear gasto');
            }
            $lineInvoiceId = $lineInvoices->insert([
                'invoices_id' => $invoiceId,
                'discounts_id' => 1,
                'products_id' => $id_product,
                'discount_amount' => (double)0,
                'quantity' => 1,
                'line_extension_amount' => (double)$requests->salary,
                'price_amount' => (double)$requests->salary,
                'description' => 'Pago Nomina',
                'type_generation_transmition_id' => null,
                'start_date' => date('Y-m-d'),
                'payroll' => 'true',
            ]);
            
            
            if (!isset($lineInvoiceId)) {
                throw  new \Exception('Inconveniente al crear linea de gasto');
            }
            // $model = new LineInvoiceTax();
            // if ($model->insert([
            //     'line_invoices_id' => $lineInvoiceId,
            //     'taxes_id' => 1,
            //     'tax_amount' => 0,
            //     'taxable_amount' => (int)$requests->salary,
            //     'percent' => 0
            // ])) {
                foreach ($requests->detail as $key => $detail) {
                    if(isset($detail->value_payroll) && $detail->value_payroll != 0){
                        $lineInvoices = new LineInvoice();
                        $lineInvoiceId = $lineInvoices->insert([
                            'invoices_id' => $invoiceId,
                            'discounts_id' => 1,
                            'products_id' => $detail->products_id,
                            'discount_amount' => (double)0,
                            'quantity' => $detail->quantity,
                            'line_extension_amount' => (double)$detail->value_payroll,
                            'price_amount' => (double)$detail->value_payroll,
                            'description' => $detail->description,
                            'type_generation_transmition_id' => null,
                            'start_date' => date('Y-m-d'),
                            'payroll' => $detail->payroll,
                        ]);
    
                        if (!isset($lineInvoiceId)) {
                            throw  new \Exception('Inconveniente al crear linea de gasto');
                        }

                        if(isset($detail->id)){
                            $walletLineInvoiceM = new WalletLineInvoice();
                            $walletLineInvoiceM->save([
                                'line_invoice_id' => $detail->id,
                                'value' => (double)$detail->value_payroll,
                                'wallet_id' => $lineInvoiceId
                            ]);
                        }


                        // wallet_line_invoice
                    }

                }
                $wallet = [
                    'value' => (($requests->salary + $requests->bono) - (int)$requests->expense),
                    'description' => "Se realiza pago de nomina",
                    'payment_method_id' => $requests->payment_method_id,
                    'invoices_id' => $invoiceId,
                    'created_at' => date("Y-m-d H:i:s"),
                    'user_id' => Auth::querys()->id,
                    'companies_id' => Auth::querys()->companies_id,
                ];
                $tableWallet = new Wallet();
                $tableWallet->save($wallet);
                return $this->respond($requests);
                return redirect()->to(base_url("payrolls?customer={$seller}&date={$_POST['date']}&year={$_POST['year']}"))->with('success', 'Nomina pagada con exito');
            // } else {
            //     throw  new \Exception('Inconveniente al realizar proceso de pagar nomina');
            // }

        } catch (\Exception $e) {
            return $this->respond([$e->getMessage()], 200);
            return redirect()->to(base_url("payrolls?customer={$seller}&date={$_POST['date']}&year={$_POST['year']}"))->with('errors', $e->getMessage());
        }

    }

    private function createProductPaymentPayroll($id, $name = 'Pago Nomina', $code = 'paymentPayroll'): int
    {
        try {
            $cc = new AccountingAcount();
            $products = new Product();
            $acounting = $cc->where(['code' => '0000000'])->asObject()->first();
            $productId = $products->insert([
                'name' =>  $name,
                'code' =>  $code,
                'valor' => 0,
                'cost' => 0,
                'description' =>  $name,
                'unit_measures_id' => 70,
                'type_item_identifications_id' => 4,
                'reference_prices_id' => 1,
                'free_of_charge_indicator' => false,
                'companies_id' => company()->id,
                'iva' => $acounting->id,
                'category_id' => $id,
                'kind_product_id' => 3,
                'entry_credit' => array_keys($this->_getAccountingAccount(1, 'Crédito'))[0],
                'entry_debit' => array_keys($this->_getAccountingAccount(1, 'Débito'))[0],
                'retefuente' => array_keys($this->_getAccountingAccount(3))[0],
                'reteica' => array_keys($this->_getAccountingAccount(3))[0],
                'reteiva' => array_keys($this->_getAccountingAccount(3))[0],
                'account_pay' => array_keys($this->_getAccountingAccount(4))[0],
            ]);
            return $productId;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function _getAccountingAccount(int $id, string $nature = ''): array
    {
        $account = new AccountingAcount();
        if ($id == 1) {
            $data = $account->where([
                'type_accounting_account_id' => $id,
                'nature' => $nature])
                ->get()
                ->getResult();
        } else {
            $data = $account->where([
                'type_accounting_account_id' => $id])
                ->get()
                ->getResult();
        }

        $info = [];
        foreach ($data as $item) {
            if ($id != 1 && $id != 4) {
                $info = array_merge($info, array($item->name . ' (' . $item->percent . '%' . ') ' => $item->id));
            } else {
                $info = array_merge($info, array($item->name => $item->id));
            }
        }
        $info = array_flip($info);
        return $info;
    }

}