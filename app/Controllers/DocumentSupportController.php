<?php

/**
 * Clase encargado de manejar todo el tema de documento soportes no obligados a facturar
 * @author Wilson Andres Bachiller Ortiz <wilson@mawii.com>
 * @date 29/07/2022
 *
 */

namespace App\Controllers;

use App\Controllers\Configuration\EmailController;
use App\Models\Customer;
use App\Models\User;
use App\Controllers\Api\Auth;
use App\Models\Invoice;
use App\Models\InvoiceDocumentUpload;
use App\Models\TypeDocumentIdentifications;
use App\Models\Municipalities;
use App\Models\TypeRegimes;
use App\Models\TypeOrganizations;
use App\Models\TrackingCustomer;
use App\Models\Resolution;
use App\Models\Company;
use App\Models\InvoiceStatus;
use App\Models\WithholdingInvoice;
use App\Traits\DocumentSupportTrait;
use App\Traits\ExcelValidationTrait;
use CodeigniterExt\Queue\Queue;
use CodeigniterExt\Queue\Task;
use Config\Services;



class DocumentSupportController extends BaseController
{
    use DocumentSupportTrait, ExcelValidationTrait;


    /**
     * Método encargado de mostrar el listado de documentos
     * soporte electrónico por medio de una tabla
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function index()
    {
        $invoicesState = new InvoiceStatus();
        $invoicesStatus = $invoicesState->where([
            'id >=' => 8,
            'id <=' => 11
        ])
            ->get()
            ->getResult();

        $querys = [];

        if (!session('user')) {
            return redirect()->to(base_url());
        }

        if (session('user')->role_id == 5) {
            if ($this->request->getGet('customer')) {
                $querys = array_merge($querys, ['invoices.companies_id' => $this->request->getGet('customer')]);
            }
        } else {
            if ($this->request->getGet('customer')) {
                $querys = array_merge($querys, ['invoices.customers_id' => $this->request->getGet('customer')]);
            }
        }


        if ($this->request->getGet('start_date')) {
            $querys = array_merge($querys, ['invoices.created_at >=' => $this->request->getGet('start_date') . ' 00:00:00']);
        }

        if ($this->request->getGet('end_date')) {
            $querys = array_merge($querys, ['invoices.created_at <=' => $this->request->getGet('end_date') . ' 23:59:59']);
        }

        if ($this->request->getGet('status') && $this->request->getGet('status') != 0) {
            $querys = array_merge($querys, ['invoices.invoice_status_id =' => $this->request->getGet('status')]);
        }


        if (Auth::querys()->role_id == 5) {
            $customer = new Customer();
            $customers = $customer->where(['email' => Auth::querys()->username])->asObject()->first();
            $documentSupport = new Invoice();
            $documentSupports  = $documentSupport
                ->select([
                    'invoices.created_at',
                    'invoices.id',
                    'CONVERT(invoices.resolution,UNSIGNED INTEGER) as resolution',
                    'companies.company as  customer',
                    'payable_amount as total',
                    'invoice_status.name as status',
                    'invoices.invoice_status_id',
                    'invoices.type_documents_id',
                    'invoices.resolution',
                    'invoices.prefix',
                    'type_documents.name as type_document_name'
                ])
                ->join('companies', 'companies.id = invoices.companies_id')
                ->join('invoice_status', 'invoice_status.id = invoices.invoice_status_id')
                ->join('type_documents', 'type_documents.id = invoices.type_documents_id')
                ->where(['invoices.customers_id' => $customers->id])
                ->where($querys)
                ->whereIn('type_documents_id', [105, 106, 11])
                ->orderBy('invoices.created_at', 'DES')
                ->asObject();

            $customer = new Customer();
            $customers = $customer->where(['email' => Auth::querys()->username])->asObject()->first();

            $providers = new Customer();
            $providers = $customer->select([
                'companies.id',
                'companies.company as name',
                'companies.identification_number as identificationNumber',
                'companies.dv',
                'companies.municipalities_id as municipality_id'
            ])
                ->join('companies', 'companies.id = customers.companies_id')
                ->where(['customers.email' => Auth::querys()->username])
                ->get()
                ->getResult();

            if (empty($customers->rut) || empty($customers->firm) || empty($customers->bank_certificate)) {
                return redirect()->to(base_url('home'))->with('warning', 'Por favor ingresa el RUT, firma y la certificación bancaria');
            }
        } else {
            $documentSupport = new Invoice();
            $documentSupports  = $documentSupport
                ->select([
                    'invoices.created_at',
                    'invoices.id',
                    'customers.name as  customer',
                    'payable_amount as total',
                    'invoice_status.name as status',
                    'invoices.invoice_status_id',
                    'invoices.resolution as resolution',
                    'invoices.prefix',
                    'invoices.type_documents_id',
                    'type_documents.name as type_document_name'
                ])
                ->join('customers', 'customers.id = invoices.customers_id')
                ->join('invoice_status', 'invoice_status.id = invoices.invoice_status_id')
                ->join('type_documents', 'type_documents.id = invoices.type_documents_id')
                ->where(['invoices.companies_id' => Auth::querys()->companies_id])
                ->whereIn('type_documents_id', [105, 106, 11])
                ->where($querys)
                ->orderBy('invoices.created_at', 'DESC')
                ->asObject();

            $customer = new Customer();
            $providers = $customer->select(['*', 'customers.identification_number as identificationNumber'])->where(['type_customer_id' => '2', 'companies_id' => Auth::querys()->companies_id])->get()->getResult();
        }
        $resolution = new Resolution();
        $resolutions = $resolution
            ->whereIn('type_documents_id', [106, 105, 11])
            ->where(['companies_id' => Auth::querys()->companies_id])
            ->orderBy('id',  'DESC')
            ->asObject()
            ->get()
            ->getResult();

        return view('document_support/index', [
            'documentSupports' => $documentSupports->paginate(10),
            'pager'            => $documentSupports->pager,
            'providers'        => $providers,
            'invoicesStatus'   => $invoicesStatus,
            'resolutions'       => $resolutions
        ]);
    }

    /**
     * Método encargado de mostrar el formulario de creación
     * de documento soporte electrónico
     * @return string
     */
    public function create()
    {
        return view('document_support/create');
    }

    /**
     * Método encargado de mostrar el formulario de edición
     * de documento soporte electrónico
     * @param string $id Id del documento electronico
     * @return string
     */
    public function edit(string $id = null)
    {
        return view('document_support/edit', ['id' => $id]);
    }

    /**
     * Método encargado para el envio de documento soporte
     * electrónico al API
     * @param string|null $id
     * @return void
     */
    public function send(string $id = null, string $resolution = null)
    {

     // header('Content-Type: application/json; charset=utf-8');
     //   http_response_code(200);

        if($resolution == null) {
            $resolution   = $this->request->getPost('resolution_id');
        }

        $data = $this->createDocument($id, $resolution);



        $model = new Invoice();
        $model->set('resolution', $data['number'])
            ->set('resolution_id', $data['resolution_number'])
            ->set('prefix', $data['prefix'])
            ->where(['id' => $id])
            ->update();

        $client = Services::curlrequest();
        $client->setHeader('Content-Type', 'application/json');
        $client->setHeader('Accept', 'application/json');
        $client->setHeader('Authorization', "Bearer " . $this->_token);

        if($data['type_document_id'] == 11) {
            $url = getenv('API').'/ubl2.1/support-document';
        }else {
            $url = getenv('API').'/ubl2.1/sd-credit-note';
        }
        $res = $client->post(
            $url, [
                'http_errors' => false,
                'form_params' => $data,
            ]
        );

        $json = json_decode($res->getBody());
        switch ($res->getStatusCode()) {
            case 299:

                break;
            case 200:
                if($json->ResponseDian->Envelope->Body->SendBillSyncResponse->SendBillSyncResult->IsValid == 'false') {
                    $model = new Invoice();
                    $model->set('error', $json)
                    ->where(['id' => $id])
                        ->update();
                    $errorText = '';
                    foreach ($json->ResponseDian->Envelope->Body->SendBillSyncResponse->SendBillSyncResult->ErrorMessage->string as $error ){
                        $errorText .= '<p>' . $error . '</p>';
                    }
                    return redirect()->to(base_url().route_to('document_support.index'))->with('errors', $errorText);
                } else {
                    $model = new Invoice();
                    $model->set('uuid', $json->cuds)
                        ->set('invoice_status_id', 9)
                        ->set('response', $json)
                        ->where(['id' => $id])
                        ->update();

                    return redirect()->to(base_url().route_to('document_support.index', $json->ResponseDian->Envelope->Body->SendBillSyncResponse->SendBillSyncResult->StatusDescription));
                }
                break;
            case 401:

                break;
            case 422:
                $errorText = '';
                foreach($json->errors as $error) {
                    foreach ($error as $value) {
                        $errorText .= '<p>' . $value . '</p>';
                    }
                }
                return redirect()->to(base_url().route_to('document_support.index'))->with('errors', $errorText);
            case 500:
                return redirect()->to(base_url().route_to('document_support.index'))->with('errors', 'HTTP 500 - Error del Servidor');
        }


    }

    /**
     * Método encargado de crear el documento soporte por medio del api
     * y descargar el PDF
     * @param null $id
     * @return void
     */
    public function previsualization($id = null) {
        header('Content-Type: application/json; charset=utf-8');
        $resolution   = null;
        http_response_code(200);


        $resolution   = $this->request->getPost('resolution_id');
        $data = $this->createDocument($id, $resolution, true);


        $client = Services::curlrequest();
        $client->setHeader('Content-Type', 'application/json');
        $client->setHeader('Accept', 'application/json');
        $client->setHeader('Authorization', "Bearer " . $this->_token);

        if($data['type_document_id'] == 11) {
            $url = getenv('API').'/ubl2.1/previsualization/support-document';
        }else {
            $url = getenv('API').'/ubl2.1/previsualization/support-document-adjust';
        }
        $res = $client->post(
            $url, [
                'http_errors' => false,
                'form_params' => $data,
            ]
        );

        $json = json_decode($res->getBody());



        $model = new Company();
        $company = $model->where(['id' => Auth::querys()->companies_id ])->asObject()->first();

        $name = 'DSS-PREV'.$id;
        if($data['type_document_id'] == 13) {
            $name = 'NDSNS-PRNA'.$id;
        }

        header('Content-disposition: attachment; filename='.$name.'.pdf');
        header('Content-type: application/pdf');
        readfile(getenv('API') . "/invoice/".$company->identification_number."/".$name.'.pdf');

    }

    /**
     * Método encargado de descagar los archivo PDF de los documentos soporte
     * @param null $id id del documento soporte
     * @return string|void
     */
    public function pdf($id = null)
    {
        $model      = new Customer();
        $customer   = $model->select(['customers.id'])
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
            ->where(['invoices.id' =>  $id])
            ->asObject()
            ->first();

        if($invoice->type_documents_id == 11) {
            if(Auth::querys()->role_id == 7) {
                if($customer) {
                    if($invoice->customers_id != $customer->id) {
                        return view('errors/html/error_401');
                    }
                }
            }
            $name = 'DSS-'.$invoice->prefix.''.$invoice->resolution;
            header('Content-disposition: attachment; filename='.$name.'.pdf');
            header('Content-type: application/pdf');
            readfile(getenv('API') . "/invoice/".$invoice->identification_number."/".$name.'.pdf');
        }else {

            $invoices = new Invoice();
             $invoice = $invoices
                 ->select([
                     'invoices.prefix',
                     'invoices.resolution',
                     'invoices.notes as notes',
                     'invoices.payable_amount',
                     'invoices.created_at',
                     'invoices.companies_id',
                     'companies.company as company_name',
                     'companies.identification_number as company_identification_number',
                     'companies.phone as company_phone',
                     'companies.email as company_email',
                     'companies.address as company_address',
                     'companies.id as companies_id',
                     'customers.name as customer_name',
                     'customers.identification_number as customer_identification_number',
                     'customers.phone as customer_phone',
                     'customers.email as customer_email',
                     'customers.address as customer_address',
                     'config.logo',
                     'invoices.resolution_id',
                     'municipality_companies.name as municipality_company_name',
                     'municipality_customer.name as municipality_customer_name',
                 ])
                 ->join('companies', 'companies.id = invoices.companies_id')
                 ->join('config', 'config.companies_id = companies.id', 'left')
                 ->join('customers', 'customers.id = invoices.customers_id')
                 ->join('municipalities as municipality_companies', 'municipality_companies.id = companies.municipalities_id')
                 ->join('municipalities as municipality_customer', 'municipality_customer.id = customers.municipality_id')
                 ->where(['invoices.id' => $id])
                 ->asObject()
                 ->first();





             $resolutionData = new Resolution();
             $resolution = $resolutionData
                 ->whereIn('type_documents_id', [106, 105])
                 ->where(['companies_id' => $invoice->companies_id])
                 ->orderBy('id',  'DESC')
                 ->asObject()
                 ->first();

             $invoiceDocumentUpload = new InvoiceDocumentUpload();
             $firm = $invoiceDocumentUpload
                 ->select(['file'])
                 ->where(['invoice_id' => $id, 'title' => 'firma'])
                 ->asObject()
                 ->first();

             $withholdings   = new WithholdingInvoice();
             $withholding    = $withholdings
                 ->select([
                     'withholding_invoices.percent',
                     'accounting_account.name',
                     'withholding_invoices.id'
                 ])
                 ->join('accounting_account', 'accounting_account.id = withholding_invoices.accounting_account_id')
                 ->where(['invoice_id' => $id])
                 ->get()
                 ->getResult();

             // echo json_encode(['tempDir' => APPPATH . 'temp']);die();
             //header("Content-Type: application/pdf");

             $mpdf  = new \Mpdf\Mpdf([
                 'default_font_size'             => 9,
                 'default_font'                  => 'Roboto',
                 'margin_left'                   => 5,
                 'margin_right'                  => 5,
                 'margin_top'                    => 35,
                 'margin_bottom'                 => 5,
                 'margin_header'                 => 5,
                 'margin_footer'                 => 2
             ]);

             $stylesheet = file_get_contents(base_url() . '/assets/css/bootstrap.css');


             $mpdf->WriteHtml($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);
             $mpdf->SetHTMLHeader(view('pdfs/document_support/header', [
                 'invoice'       => $invoice,
                 'firm'          => $firm,
                 'resolution'    => $resolution,
                 'withholding'   => $withholding
             ]));
             $mpdf->WriteHtml(view('pdfs/document_support/body', [
                 'invoice'       => $invoice,
                 'firm'          => $firm,
                 'resolution'    => $resolution,
                 'withholding'   => $withholding
             ]), \Mpdf\HTMLParserMode::HTML_BODY);
             $mpdf->Output();

             die();
        }





    }

    /**
     * Método encargado del envió por correo electrónico de documento soporte
     * @param null $id id del documento soporte
     * @return void
     */
    public function email($id = null)
    {
        $invoice    = new Invoice();
        $data       = $invoice->select('*')
            ->join('companies', 'invoices.companies_id = companies.id')
            ->where(['invoices.companies_id' => Auth::querys()->companies_id, 'invoices.id' => $id])
            ->whereIn('invoices.type_documents_id', ['11', '13'])
            ->asObject()
            ->first();

        if($data->type_documents_id == 11 || $data->type_documents_id == 13) {
            $resolution = new Resolution();
            $resolutions = $resolution->where(['resolution' => $data->resolution_id, 'type_documents_id' => $data->type_documents_id])
                ->asObject()
                ->first();
        }
        $client = Services::curlrequest();
        $client->setHeader('Content-Type', 'application/json');
        $client->setHeader('Accept', 'application/json');
        $client->setHeader('Authorization', "Bearer " . $data->token);

        $res = $client->post(
            getenv('API')."/send-email-customer/Now", [
                'http_errors' => false,
                'form_params' => [
                    "company_idnumber"      => $data->identification_number,
                    "prefix"                => empty($resolutions->prefix) ? ' ': trim($resolutions->prefix),
                    "number"                => $data->resolution
                ],
            ]
        );
        $json = json_decode($res->getBody());
        if(isset($json->success) && $json->success == 'true') {
            $invoice = new Invoice();
            $invoice->set(['invoice_status_id' => 10])
                ->where(['id' => $id])
                ->update();

        }else {
            if($data->type_documents_id == 11) {
                return redirect()->to(base_url('/document_support'))->with('errors', 'El correos electrónicos no pudo ser enviado.');
            }else {
                return redirect()->back()->with('errors', 'El correos electrónicos no pudo ser enviado.');
            }

        }
        if($data->type_documents_id == 11) {
            return redirect()->to(base_url('/invoice'))->with('success', $data->message);
        }else {
            return redirect()->back()->with('success', $data->message);
        }


    }

    /**
     * Envio masivo de documentos soporte por queue
     * @param null $id id del documento soporte
     * @return void
     */
    public function sendMultiple($id = null)
    {
        $documents  = $this->request->getPost('payrolls');
        $resolution = $this->request->getPost('resolution');
        $i = 0;
        foreach(explode(',', $documents[0]) as $item) {
            $model = new Invoice();
            $invoice = $model->select(['companies_id'])->asObject()->find($item);

            if($invoice->companies_id == Auth::querys()->companies_id) {
                $model = new Invoice();
                $model->update($item, ['invoice_status_id' => 16]);

                $queue  = new Queue();
                $task   = new Task;
                $task->setName('App/Controllers/Queues/SendPayroll')
                    ->setData([
                        'id'            => $item,
                        'resolution'    => $resolution,
                        'companies_id'  => Auth::querys()->companies_id
                    ])->setPriority(Task::PRIORITY_HIGH)
                    ->setUniqueId('payrolls-'. $i .$item.Auth::querys()->companies_id);
                $queue->addTask($task);

                $i++;
            }
        }
        return redirect()->to(base_url('periods/'. $id));

    }




    public function sendingInvitationProvider($id = null)
    {
        $customer  = new Customer();
        $customers = $customer->asObject()->find($id);

        $user = new User();
        $users = $user
            ->where(['username' => $customers->email])
            ->get()
            ->getResult();

        $password = '';
        if (count($users) == 0) {
            helper('text');
            $password = random_string('alnum', 8);
            $user = new User();
            $user->save([
                'name'          => $customers->name,
                'username'      => $customers->email,
                'email'         => $customers->email,
                'password'      => password_hash($password, PASSWORD_DEFAULT),
                'status'        => 'active',
                'role_id'       => 5,
                'photo'         => null,
                'companies_id'  => Auth::querys()->companies_id
            ]);

            $customer->where(['id' => $id])
                ->set('user_id', $user->getInsertID())
                ->update();
        }

        $company = new Company();
        $companies = $company->asObject()->find(Auth::querys()->companies_id);

        $email = new EmailController();
        $email->send('soporte@planetalab.xyz', 'Soporte MiFacturaLegal', $customers->email, 'MFL: Documento Soporte “Validación y Firma”', view('emails/document_support_provider', [
            'customer'  => $customers,
            'company'   => $companies,
            'password'  => $password
        ]));

        return redirect()->back()->with('success', 'Invitación enviada exitosamente al proveedor frecuente.');
    }

    public function sendingInvitation($id = null)
    {
        $invoices  = new Invoice();
        $invoiceData    = $invoices
            ->select(['customers.*, invoices.id', 'invoices.invoice_status_id'])
            ->join('customers', 'invoices.customers_id = customers.id')
            ->where(['invoices.id' => $id])
            ->asObject()
            ->first();

        $emailCustomer = $invoiceData->email;

        $trackings = new TrackingCustomer();
        $tracking   = $trackings
            ->where([
                'companies_id'      => Auth::querys()->companies_id,
                'table_id'          => $id,
                'type_tracking_id'     => 2
            ])
            ->get()
            ->getResult();

        $data = [];
        $data['id']                     = $invoiceData->id;
        $data['name']                   = $invoiceData->name;
        $data['identification_number']  = $invoiceData->identification_number;

        $company = new Company();
        $companies = $company->asObject()->find(Auth::querys()->companies_id);

        $encriptLink = base64_encode(json_encode($data));
        if ($invoiceData->invoice_status_id !=  10) {
            if (count($tracking) > 0) {
                $invoice = new Invoice();
                $invoice->update($id, ['uuid' => $encriptLink, 'invoice_status_id' => 8]);
            } else {
                $invoice = new Invoice();
                $invoice->update($id, ['uuid' => $encriptLink]);
            }
        } else {
            $invoices = new Invoice();
            $invoice = $invoices->asObject()->find($invoiceData->id);
            $email = new EmailController();
            $email->send('soporte@planetalab.xyz', 'Soporte MiFacturaLegal', $email, 'MFL: Documento Soporte “Validación y Firma”', view(
                'emails/document_support',
                [
                    'invoice'   => $invoiceData,
                    'trackings'  => $tracking,
                    'company'   => $companies
                ]
            ));
        }


        $invoices  = new Invoice();
        $invoiceData    = $invoices
            ->join('customers', 'invoices.customers_id = customers.id')
            ->where(['invoices.id' => $id])
            ->asObject()
            ->first();


        $email = new EmailController();
        $email->send('soporte@mifacturalegal.com', 'Soporte MiFacturaLegal', $emailCustomer, 'MFL: Documento Soporte “Validación y Firma”', view('emails/document_support', [
            'invoice'   => $invoiceData,
            'trackings'  => $tracking,
            'company'   => $companies
        ]));
        return redirect()->to(base_url('document_support'))->with('success', 'Documento soporte enviado correctamente al proveedor no frecuente.');
    }

    public function firm($id = null)
    {
        $invoices = new Invoice();
        $invoice = $invoices
            ->select('customers.firm, invoices.id,  invoices.invoice_status_id, customers.firm')
            ->join('customers', 'customers.id = invoices.customers_id')
            ->asObject()
            ->where(['invoices.uuid' => $id])
            ->first();

        if ($invoice->firm == null) {
            return redirect()->to(base_url('/document_support/firm_document/' . $id))->with('errors', 'Por favor ingrese la firma.');
        }

        if ($invoice->invoice_status_id != 10) {
            $invoices = new Invoice();
            $invoices->update($invoice->id, ['invoice_status_id' => 9]);

            $data = [
                'title'         => 'firma',
                'file'          =>  $invoice->firm,
                'invoice_id'    =>  $invoice->id
            ];

            $invoiceDocumentUpload = new InvoiceDocumentUpload();
            $count = $invoiceDocumentUpload->where(['invoice_id' => $invoice->id, 'title' => 'firma'])->get()->getResult();

            $invoiceDocumentUpload = new InvoiceDocumentUpload();
            if (count($count) > 0) {
                if ($invoiceDocumentUpload->update($count[0]->id, $data)) {
                    return redirect()->to(base_url('/document_support/firm_document/' . $id))->with('success', '
                    Este documento será validado por la empresa, una vez confirmado recibirá un correo electrónico informado que fue aceptado o rechazado.
                    ');
                }
            }

            if ($invoiceDocumentUpload->save($data)) {
                return redirect()->to(base_url('/document_support/firm_document/' . $id))->with('success', '
                Este documento será validado por la empresa, una vez confirmado recibirá un correo electrónico informado que fue aceptado o rechazado.');
            }
        } else {
            return redirect()->to(base_url('/document_support/firm_document/' . $id))->with('success', 'El documento ya se encuentra aceptado.');
        }
    }

    public function resolucionDocumenSupport()
    {

        $data = [];
        $resolutionData = new Resolution();
        $resolutionsData = $resolutionData
            ->whereIn('type_documents_id', [106, 105])
            ->where(['companies_id' => Auth::querys()->companies_id])
            ->orderBy('id',  'DESC')
            ->asObject()
            ->first();

        if (!$resolutionsData) {
            $data['errors'] = 'Por favor ingrese una resolución para documento de soporte.';
        } else {
            $fecha_actual   = strtotime(date("Y-m-d H:i:00"), time());
            $fecha_entrada  = strtotime($resolutionsData->date_from);
            if ($fecha_entrada >= $fecha_actual) {
                $data['errors'] =  'La fecha de inicio de la resolución aun no es validad.';
            }
            $fecha_entrada  = strtotime($resolutionsData->date_to);
            if ($fecha_entrada <= $fecha_actual) {
                $data['errors'] = 'La resolución se encuentra vencida.';
            }
        }

        $resolution = new Invoice();
        $resolutions = $resolution
            ->whereIn('type_documents_id',  [105, 106])
            ->where([
                'resolution_id'     => $resolutionsData->resolution,
                'companies_id'     => Auth::querys()->companies_id
            ])
            ->orderBy('CAST(resolution as UNSIGNED)',  'DESC')
            ->asObject()
            ->get()
            ->getResult();

        if (count($resolutions) == 0) {
            $resolution = new Resolution();
            $resolutions = $resolution
                ->whereIn('type_documents_id', [106, 105])
                ->where(['companies_id' => Auth::querys()->companies_id])
                ->orderBy('id',  'DESC')
                ->asObject()
                ->get()
                ->getResult();
            $number['resolution'] = $resolutions[0]->from;
        } else {

            $number['resolution'] = $resolutions[0]->resolution + 1;
            if ($number['resolution'] == $resolutionsData->to) {
                $data['errors'] = 'La resolución se encuentra vencida.';
            }
        }
        $number['prefix']               = $resolutionsData->prefix;
        $number['resolution_id']        = $resolutionsData->resolution;

        if (array_key_exists('errors', $data)) {
            return $data;
        }
        return $number;
    }

    public function agree($id = null)
    {
        $invoices = new Invoice();
        $count = $invoices->where(['id' => $id, 'resolution !=' => null])->countAllResults();

        if ($count == 0) {
            if (array_key_exists('errors', $this->resolucionDocumenSupport())) {
                return redirect()->to(base_url('document_support'))->with('errors',  $this->resolucionDocumenSupport()['errors']);
            }
            $invoices = new Invoice();
            $invoice = $invoices->update($id, $this->resolucionDocumenSupport());
        }

        $invoices = new Invoice();
        $invoice = $invoices->select([
            'invoices.id',
            'invoices.type_documents_id',
            'companies.company',
            'customers.email',
            'customers.name as customer_name',
            'companies.identification_number',
            'companies.dv',
            'invoice_status_id'
        ])
            ->join('companies', 'companies.id = invoices.companies_id')
            ->join('customers', 'customers.id = invoices.customers_id')
            ->where(['invoices.id' => $id])
            ->asObject()
            ->first($id);


        if($invoice->invoice_status_id != 8) {
            $email = new EmailController();
            $email->send('soporte@planetalab.xyz', 'Soporte MiFacturaLegal', $invoice->email, 'MFL: Documento Soporte Estado: Aprobado', view('emails/document_support_agree', ['invoice' => $invoice]));
        }


        $invoices = new Invoice();
        $invoices->update($id, ['invoice_status_id' => 10]);
        return redirect()->to(base_url('document_support'))->with('success', 'El documento fue aceptado correctamente.');
    }

    public function cancel($id = null)
    {
        $data = [
            'type_tracking_id'  => 2,
            'companies_id'      => Auth::querys()->companies_id,
            'table_id'          => $id,
            'message'           => $this->request->getPost('observation'),
            'created_at'        => date('Y-m-d H:i:s')
        ];

        $tracking = new TrackingCustomer();
        $trackings = $tracking->save($data);

        $invoices = new Invoice();
        $invoices->update($id, ['invoice_status_id' => 11]);

        $invoice = new Invoice();
        $invoices = $invoice->select([
            'invoices.id',
            'customers.name as customer_name',
            'customers.email',
            'invoices.type_documents_id',
            'companies.company',
            'invoices.uuid',
            'companies.identification_number',
            'companies.dv',
        ])
            ->join('customers', 'customers.id = invoices.customers_id')
            ->join('companies', 'companies.id = invoices.companies_id')
            ->asObject()
            ->where(['invoices.id' => $id])
            ->first();

        $tracking = new TrackingCustomer();
        $trackings = $tracking->where(['type_tracking_id' => 2, 'table_id' => $id])->get()->getResult();


        $email = new EmailController();
        $email->send('soporte@planetalab.xyz', 'Soporte MiFacturaLegal', $invoices->email, 'MFL: Documento Soporte Estado: Rechazado/Requerido',  view('emails/document_support_cancel', [
            'invoice'   => $invoices,
            'trackings' => $trackings
        ]));

        return redirect()->to(base_url('document_support'))->with('success', 'El documento fue rechazado correctamente.');
    }

    public function firmDocument($uuid = null)
    {
        $invoice = new Invoice();
        $invoiceData = $invoice->where(['uuid' => $uuid])->asObject()->first();

        $company = new Company();
        $companies = $company->select(['companies.company'])->asObject()->find($invoiceData->companies_id);

        $typeDocumentIdentifications = new typeDocumentIdentifications();
        $typeDocumentIdentification = $typeDocumentIdentifications->get()->getResult();

        $municipality = new Municipalities();
        $municipalities = $municipality->get()->getResult();

        $typeRegime = new TypeRegimes();
        $typeRegimes = $typeRegime->get()->getResult();

        $typeOrganization = new TypeOrganizations();
        $typeOrganizations = $typeOrganization->get()->getResult();

        $customers = new Customer();
        $customer  = $customers->asObject()->find($invoiceData->customers_id);

        $invoiceDocumentUpload = new InvoiceDocumentUpload();
        $invoiceDocumentUploads = $invoiceDocumentUpload->where(['invoice_id' => $invoiceData->id, 'title !=' => 'firma'])->get()->getResult();

        return view('document_support/firm_document', [
            'customer'                      => $customer,
            'typeDocumentIdentification'    => $typeDocumentIdentification,
            'municipalities'                => $municipalities,
            'typeRegimes'                   => $typeRegimes,
            'typeOrganizations'             => $typeOrganizations,
            'uuid'                          => $uuid,
            'invoice'                       => $invoiceData,
            'invoiceDocumentUploads'        => $invoiceDocumentUploads,
            'companies'                     => $companies
        ]);
    }

    public function attachedDocumentDelete($id = null, $uuid = null)
    {
        $invoiceDocumentUpload = new InvoiceDocumentUpload();
        $invoiceDocumentUpload->delete($id);
        return redirect()->to(base_url('document_support/firm_document/' . $uuid));
    }

    public function updateProvider($id =  null, $uuid = null)
    {
        $validation =  \Config\Services::validation();

        $validation->setRules(
            [
                'name'                              => 'required',
                'type_document_identifications_id'  => 'required',
                'identification_number'             => 'required',
                'phone'                             => 'required',
                'address'                           => 'required',
                'email'                             => 'required|valid_email|is_unique[customers.email,id,' . $id . ']',
                'type_regime_id'                    => 'required',
                'type_organization_id'              => 'required',
                'municipality_id'                   => 'required'
            ],
            [   // Errors
                'name' => [
                    'required' => 'El campo nombre es obligatorio.',
                ],
                'type_document_identification' => [
                    'required' => 'El campo tipo de documento es obligatorio.'
                ],
                'identification_number' => [
                    'required' => 'El campo número de documento es obligatorio.'
                ],
                'phone' => [
                    'required' => 'El campo teléfono es obligatorio.'
                ],
                'address' => [
                    'required' => 'El campo dirección es obligatorio.'
                ],
                'email' => [
                    'required'      => 'El campo correo electrónico es obligatorio.',
                    'valid_email'   => 'El correo electrónico no es válido.',
                    'is_unique'     => 'El correo electrónico ya existe.'
                ],
                'type_regime_id' => [
                    'required' => 'El campo tipo de régimen es obligatorio.'
                ],
                'type_organization_id' => [
                    'required' => 'El campo tipo de organización es obligatorio.'
                ],
                'municipality_id' => [
                    'required' => 'El campo ciudad es obligatorio.'
                ],
            ]
        );


        if (!$validation->withRequest($this->request)->run()) {
            $errors = '';
            foreach ($validation->getErrors() as $item) {
                $errors .= $item . '<br>';
            }

            return redirect()->to(base_url('document_support/firm_document/' . $uuid))->with('errors', $errors);
        }


        $customer = new Customer();
        $data = [
            'name'                          => $this->request->getPost('name'),
            'type_document_identification'  => $this->request->getPost('type_document_identification'),
            'identification_number'         => $this->request->getPost('identification_number'),
            'phone'                         => $this->request->getPost('phone'),
            'address'                       => $this->request->getPost('address'),
            'email'                         => $this->request->getPost('email'),
            'type_regime_id'                => $this->request->getPost('type_regime_id'),
            'type_organization_id'          => $this->request->getPost('type_organization_id'),
            'municipality_id'               => $this->request->getPost('municipality_id'),
        ];

        $customer->update($id, $data);

        $user = new User();
        $data = [
            'name'                          => $this->request->getPost('name'),
            'email'                         => $this->request->getPost('email'),
            'username'                      => $this->request->getPost('email'),
        ];

        $user = $user->set($data)
            ->where(['username' => $data['email']])
            ->update();
        return redirect()->to(base_url('document_support/firm_document/' . $uuid))->with('update', 'Los datos fueron actualizados correctamente.');
    }

    public function fileInfo($id = null)
    {
        $invoices = new Invoice();
        $invoice = $invoices->asObject()->find($id);


        $invoiceDocument = new InvoiceDocumentUpload();
        $invoiceDocuments = $invoiceDocument->where(['invoice_id' => $id, 'title !=' => 'firma'])->get()->getResult();

        $customer = new Customer();
        $customers = $customer->asObject()->find($invoice->customers_id);

        $trackings = new TrackingCustomer();
        $tracking = $trackings->where([
            'type_tracking_id'  => 2,
            'table_id'          => $id
        ])->get()->getResult();


        $documentSupport = new Invoice();
        $documentSupports  = $documentSupport
            ->select([
                'invoices.created_at',
                'invoices.id',
                'customers.name as  customer',
                'payable_amount as total',
                'invoice_status.name as status',
                'invoices.invoice_status_id',
                'invoices.resolution as resolution',
                'invoices.prefix',
                'invoices.type_documents_id'
            ])
            ->join('customers', 'customers.id = invoices.customers_id')
            ->join('invoice_status', 'invoice_status.id = invoices.invoice_status_id')
            ->where(['invoices.companies_id' => Auth::querys()->companies_id, 'invoices.resolution_credit' => $invoice->prefix.$invoice->resolution])
            ->whereIn('type_documents_id', [13])
            ->orderBy('(CASE WHEN CAST(resolution as UNSIGNED) IS NULL THEN 1 ELSE 0 END)', 'desc')
            ->orderBy('CAST(resolution as UNSIGNED)', 'desc')
            ->asObject();

        $resolution = new Resolution();
        $resolutions = $resolution
            ->whereIn('type_documents_id', [13])
            ->where(['companies_id' => Auth::querys()->companies_id])
            ->orderBy('id',  'DESC')
            ->asObject()
            ->get()
            ->getResult();


        return view('document_support/file', [
            'invoice'           => $invoice,
            'customer'          => $customers,
            'invoiceDocuments'  => $invoiceDocuments,
            'trackings'         => $tracking,
            'id'                => $id,
            'resolutions'       => $resolutions,
            'documentSupports'  => $documentSupports->paginate(),
            'pager'             => $documentSupports->pager
        ]);
    }

    public function deleteDocument($type, $id)
    {
        $customers = new Customer();
        $customer =  $customers->update($id, [$type =>  null]);

        return redirect()->back();
    }

    public  function payrollDocumentSupport($id = null)
    {
        $file = $this->request->getFile('file');
        if ($file->isValid()) {
            $newName = $file->getRandomName();


            $trackingCustomer = new TrackingCustomer();
            $trackingCustomer->insert([
                'type_tracking_id'  => 2,
                'companies_id'      => Auth::querys()->companies_id,
                'table_id'          => $id,
                'created_at'        => Date('Y-m-d H:i:s'),
                'message'           => $this->request->getPost('description'),
                'username'          => Auth::querys()->username,
                'file'              => $newName,
            ]);


            $file->move('upload/attached_document', $newName);
            return redirect()->to(base_url('document_support/file_info/' . $id))->with('success', 'El pago fue guardado correctamente.');
        }
        return redirect()->to(base_url('document_support/file_info/' . $id))->with('success', 'El documento subido no es valido.');
    }

    public function deleteTracking($id = null, $idTracking = null)
    {
        $trackingCustomer = new TrackingCustomer();
        $trackingCustomer->delete($id);
        return redirect()->to(base_url('document_support/file_info/' . $idTracking))->with('success', 'El seguimiento eliminado correctamente.');
    }

    public function uploadExcel()
    {

      /*  if (!empty($_FILES['file']['name'])) {
            $pathinfo = pathinfo($_FILES["file"]["name"]);
            if (($pathinfo['extension'] == 'xlsx' || $pathinfo['extension'] == 'xls')
                && $_FILES['file']['size'] > 0
            ) {
                $inputFileName = $_FILES['file']['tmp_name'];
                // prueba
                $reader = ReaderEntityFactory::createReaderFromFile('/path/to/file.xlsx');
                $reader->setShouldFormatDates(true);
                $reader->open($inputFileName);
                $count = 1;


                $errors = [];

                foreach ($reader->getSheetIterator() as $sheet) {
                    foreach ($sheet->getRowIterator() as $row) {

                        // do stuff with the row
                        if ($count > 1) {
                            $cells = $row->getCells();
                            if (empty(trim($cells[5]))) {
                                array_push($errors, 'El campo nombre en la columna F' . $count . ' es requerido.');
                            }
                            if (empty(trim($cells[6]))) {
                                array_push($errors, 'El campo documento en la columna G' . $count . ' es requerido.');
                            }
                            if (empty(trim($cells[7]))) {
                                array_push($errors, 'El campo correo electrónico en la columna H' . $count . ' es requerido.');
                            } else if (!filter_var($cells[7], FILTER_VALIDATE_EMAIL)) {
                                array_push($errors, 'El campo correo electrónico en la columna H' . $count . ' no es valido.');
                            }
                            if (empty(trim($cells[8]))) {
                                array_push($errors, 'El campo teléfono en la columna I' . $count . ' es requerido.');
                            }

                            if (empty(trim($cells[16]))) {
                                array_push($errors, 'El campo abono en la columna Q' . $count . ' es requerido.');
                            }
                        }
                        $count++;
                    }
                }
                if (count($errors) > 0) {
                    $text = '';
                    foreach ($errors as $error) {
                        // echo json_encode($error);die();
                        $text .= $error . '<br>';
                    }
                    return redirect()->to(base_url('/document_support'))->with('errors', $text);
                } else {
                    $inputFileName2 = $_FILES['file']['tmp_name'];
                    // prueba
                    $reader2 = ReaderEntityFactory::createReaderFromFile('/path/to/file.xlsx');
                    $reader2->setShouldFormatDates(true);
                    $reader2->open($inputFileName2);

                    $count = 1;
                    foreach ($reader2->getSheetIterator() as $sheet2) {
                        foreach ($sheet2->getRowIterator() as $row2) {
                            if ($count > 1) {
                                $cells = $row2->getCells();
                                $model = new Customer();
                                $countCustomer = $model
                                    ->where(['type_customer_id' => 2, 'identification_number' => $cells[6]])
                                    ->asObject()
                                    ->first();
                                if (is_null($countCustomer)) {
                                    $customerNew = $model->insert([
                                        'type_customer_id'                  => 2,
                                        'type_regime_id'                    => 2,
                                        'type_document_identifications_id'  => 3,
                                        'municipality_id'                   => 149,
                                        'companies_id'                      => Auth::querys()->companies_id,
                                        'type_organization_id'              => 2,
                                        'type_liability_id'                 => 117,
                                        'name'                              => $cells[5],
                                        'identification_number'             => $cells[6],
                                        'dv'                                => null,
                                        'phone'                             => $cells[8],
                                        'address'                           => 'Calle Siempre viva',
                                        'email'                             => $cells[7],
                                        'merchant_registration'             => '000000',
                                        'status'                            => 'Activo'
                                    ]);
                                } else {
                                    $model->update($countCustomer->id, [
                                        'type_customer_id'                  => 2,
                                        'type_regime_id'                    => 2,
                                        'type_document_identifications_id'  => 3,
                                        'municipality_id'                   => 149,
                                        'companies_id'                      => Auth::querys()->companies_id,
                                        'type_organization_id'              => 2,
                                        'type_liability_id'                 => 117,
                                        'name'                              => $cells[5],
                                        'identification_number'             => $cells[6],
                                        'dv'                                => null,
                                        'phone'                             => $cells[8],
                                        'address'                           => 'Calle Siempre viva',
                                        'email'                             => $cells[7],
                                        'merchant_registration'             => '000000',
                                        'status'                            => 'Activo'
                                    ]);
                                }

                                $data = (object) $this->resolucionDocumenSupport();
                                $timestamp = strtotime($cells[4]); 
                                $newDate = date("Y-m-d H:i:s", $timestamp );
                                $invoice = new Invoice();
                                $idInvoice = $invoice->insert([
                                    'payment_forms_id'      => '2',
                                    'payment_methods_id'    => '10',
                                    'type_documents_id'     => '105',
                                    'idcurrency'            => '35',
                                    'invoice_status_id'     => '10',
                                    'customers_id'          => isset($countCustomer->id) ?  $countCustomer->id : $customerNew,
                                    'companies_id'          => Auth::querys()->companies_id,
                                    'delevery_term_id'      => null,
                                    'user_id'               => Auth::querys()->id,
                                    'seller_id'             => Auth::querys()->id,
                                    'resolution'            => $data->resolution,
                                    'prefix'                => $data->prefix,
                                    'resolution_id'         => $data->resolution_id,
                                    'resolution_credit'     => null,
                                    'payment_due_date'      => null,
                                    'duration_measure'      => null,
                                    'line_extesion_amount'  => $cells[16],
                                    'tax_exclusive_amount'  => $cells[16],
                                    'tax_inclusive_amount'  => $cells[16],
                                    'allowance_total_amount' => 0,
                                    'charge_total_amount'   => 0,
                                    'pre_paid_amount'       => 0,
                                    'payable_amount'        => $cells[16],
                                    'calculationrate'       => 1,
                                    'calculationratedate'   => date('Y-m-d'),
                                    'issue_date'            => null,
                                    'uuid'                  => null,
                                    'notes'                 => "CONCEPTO CASHBACK",
                                    'status_wallet'         => 'Pendiente',
                                    'send'                  => 'True',
                                    'created_at'            => $newDate
                                ]);

                                $lineInvoice = new LineInvoice();
                                $idLineInvoice = $lineInvoice->insert([
                                    'invoices_id'               => $idInvoice,
                                    'discounts_id'              => null,
                                    'products_id'               => 1,
                                    'provider_id'               => null,
                                    'code'                      => null,
                                    'quantity'                  => 1,
                                    'line_extension_amount'     => $cells[16],
                                    'price_amount'              => $cells[16],
                                    'description'               => "CONCEPTO CASHBACK",
                                    'upload'                    => null
                                ]);

                                $lineInvoiceTax = new LineInvoiceTax();
                                $lineInvoiceTax->insert([
                                    'line_invoices_id' => $idLineInvoice,
                                    'taxes_id'         => 1,
                                    'tax_amount'       => 0,
                                    'taxable_amount'   => $cells[16],
                                    'percent'          => 0
                                ]);
                                $lineInvoiceTax->insert([
                                    'line_invoices_id' => $idLineInvoice,
                                    'taxes_id'         => 5,
                                    'tax_amount'       => 0,
                                    'taxable_amount'   => $cells[16],
                                    'percent'          => 0
                                ]);
                                $lineInvoiceTax->insert([
                                    'line_invoices_id' => $idLineInvoice,
                                    'taxes_id'         => 6,
                                    'tax_amount'       => 0,
                                    'taxable_amount'   => $cells[16],
                                    'percent'          => 0
                                ]);
                                $lineInvoiceTax->insert([
                                    'line_invoices_id' => $idLineInvoice,
                                    'taxes_id'         => 7,
                                    'tax_amount'       => 0,
                                    'taxable_amount'   => $cells[16],
                                    'percent'          => 0
                                ]);
                            }
                            $count++;
                        }
                    }
                    return redirect()->to(base_url('/document_support'))->with('success', 'El cargue de los datos fue exitoso.');
                }
            }
        }*/

    }
}
