<?php

/**
 * Controlador encargado de subir documentos electronicos por medio de archivos .zip
 * @Author wilson Andres Bachiller Ortiz <wilson@mawii.com> wabo
 * @date 07/01/2021
 */

namespace App\Controllers\Documents;

use App\Controllers\Api\Auth;
use \App\Controllers\BaseController;
use App\Controllers\Xml\Xml;
use App\Models\DocumentEvent;
use App\Models\InvoiceDocumentUpload;
use App\Models\AssociateDocument;
use App\Models\Company;
use App\Models\Document;
use App\Models\Invoice;
use App\Models\LineInvoice;
use App\Models\LineInvoiceTax;
use App\Models\Product;
use App\Models\TrackingCustomer;
use App\Models\TypeRejection;
use Config\Services;
use CodeIgniter\Files\File;


use App\Models\ShoppingEmail;
use App\Models\HistoryEmails;
use App\Models\ShoppingFiles;



class DocumentReceptionController extends BaseController
{
    /**
     * Vista en cargada de mostrar las facturas cargadas al sistema
     * @return string view()
     */
    public function index()
    {
        $document = new Document();
        $documents = $document
            ->select([
                'customers.name as customer_name',
                'documents.id',
                'documents.created_at',
                'documents.companies_id',
                'invoices.uuid',
                'invoices.resolution',
                'invoices.prefix',
                'document_status.name as status',
                'document_status.description as status_description',
                'document_status.color as color_status',
                'document_status.id as status_id',
                'companies.company as company_name',
                'documents.provider',
                'documents.uuid as status_uuid',
                'customers.id as customer_id',
                'companies.identification_number',
                'associate_document.new_name',
                'associate_document.name',
                'invoices.id as invoices_id',
                'invoices.type_documents_id as type_documents_id_invoices',
                'type_documents.name as type_documents_name',
       'type_documents.name as type_document_name',
                'documents.zip'
            ])
            ->join('invoices','invoices.id = documents.invoice_id','left')
            ->join('type_documents', 'type_documents.id = invoices.type_documents_id', 'left')
            ->join('document_status', 'document_status.id = documents.document_status_id', 'left')
            ->join('customers', 'invoices.customers_id = customers.id', 'left')
            ->join('companies', 'companies.id = documents.companies_id', 'left')
            ->join('associate_document', 'associate_document.documents_id = documents.id', 'left')
            ->where(['documents.companies_id' => Auth::querys()->companies_id, 'associate_document.extension' => 'xml'])
            ->asObject()
            ->orderBy('id', 'desc');


        $data = [
            'documents' => $documents->paginate(10),
            'pager'     => $documents->pager
        ];

        return view('document_reception/index', $data);
    }

    /**
     * Metodo encargado de subir archivos y registrar los documentos cargados
     * Tabla de documents y associate_documents
     * @return \CodeIgniter\HTTP\RedirectResponse
     * @throws \ReflectionException
     */
    public function create()
    {
        $companies = new Company();
        $data = $companies
            ->select('identification_number')
            ->asObject()
            ->find(Auth::querys()->companies_id);

        if($file = $this->request->getFile('file')) {
            if(!is_dir(WRITEPATH.'uploads/document_reception/'.$data->identification_number)) {
                mkdir(WRITEPATH.'uploads/document_reception/'.$data->identification_number, 0777);
            }
            $file =  upload('document_reception/'.$data->identification_number.'/zip', $file);
            $document = [
                'name'              =>  $file['name'],
                'new_name'          =>  $file['new_name'],
                'extension'         =>  'zip',
                'created_at'        =>  date('Y-m-d H:i:s'),
                'invoice_id'        =>  null,
                'companies_id'      =>  Auth::querys()->companies_id,
                'document_status_id'=>  1
            ];
            $documents = new Document();
            $documents->save($document);
            $idDocument = $documents->getInsertID();

            $files = decompress(WRITEPATH.'uploads/document_reception/'.$data->identification_number.'/zip/'.$file['new_name'],
                WRITEPATH.'uploads/document_reception/'.$data->identification_number.'/zip/');

            foreach ($files as $file){
                $newFile = new File(WRITEPATH.'uploads/document_reception/'.$data->identification_number.'/zip/'.$file);
                $associateDocument = new AssociateDocument();
                $name       = $newFile->getFilename();
                $newName    = $newFile->getRandomName();
                $associateDocument->save([
                    'documents_id' => $idDocument,
                    'name'         => $name,
                    'new_name'     => $newName,
                    'extension'    => $newFile->getExtension()
                ]);
                switch($newFile->getExtension()) {
                    case 'pdf':
                        if(!is_dir(WRITEPATH.'uploads/document_reception/'.$data->identification_number.'/pdf')) {
                            mkdir(WRITEPATH.'uploads/document_reception/'.$data->identification_number.'/pdf', 0777);
                        }
                        $newFile->move(WRITEPATH.'uploads/document_reception/' . $data->identification_number . '/pdf/', $newName);
                        break;
                    case 'xml':
                        if(!is_dir(WRITEPATH.'uploads/document_reception/'.$data->identification_number.'/xml')) {
                            mkdir(WRITEPATH.'uploads/document_reception/'.$data->identification_number.'/xml', 0777);
                        }
                        $newFile->move(WRITEPATH.'uploads/document_reception/'.$data->identification_number.'/xml/', $newName);
                        break;
                }
            }
            // $documentController = new Document();
            // $document = $documentController->where(['id' => $id_document])->asObject()->first();

            // $emailController = new ShoppingEmail();
            // $dataEmail['invoices_id'] = $document->invoice_id;
            // $emailController->save($dataEmail);
            // $validador = true;
            // $id_shopping = $emailController->getInsertID();
            // $historyM = new HistoryEmails();
            // $dataHistory = [
            //     'shopping_emails_id' => $id_shopping,
            //     'users_id' => session('user')->id,
            //     'observation' => 'Recepción de Email'
            // ];
            // $historyM->save($dataHistory);
            // $dataFiles = [
            //     'shopping_email_id' => $id_shopping,
            //     'name' => $attachment->id.'.zip',
            // ];
            // $emailFileModel = new ShoppingFiles();
            // $emailFileModel->save($dataFiles);
            return redirect()->to(base_url().route_to('document-index'))->with('success', 'Documento cargado con exito');
        }else{
            return redirect()->to(base_url().route_to('document-index'))->with('danger', 'El documento no puede ser subido al sistema.');
        }
    }

    /**
     * Vista encargada del manejo de eventos "RADIAN", mostrar PDF de
     * factura o documento cargado, y descarga de archivo attached .zip
     * emitido ala DIAN
     * @param null|string $id Id de factura cargada al sistema
     * @return string view()
     */
    public function show($id = null)
    {
        $document = new Document();
        $documents = $document->select([
                'companies.identification_number',
                'documents.new_name as zip',
                'documents.name as zip_name',
                'associate_document.new_name as pdf',
                'associate_document.name as name_pdf'
            ])

            ->join('invoices','invoices.id = documents.invoice_id','left')
            ->join('companies','invoices.companies_id = companies.id','left')
            ->join('associate_document', 'associate_document.documents_id = documents.id', 'left')
            ->where(['documents.companies_id' => Auth::querys()->companies_id, 'invoices.id' => $id, 'associate_document.extension'=> 'pdf'])
            ->asObject()
            ->first();

        $model = new TypeRejection();
        $typeRejections = $model->get()->getResult();

        $model = new DocumentEvent();
        $documentEvents = $model->select(['document_event.id as document_event_id', 'document_event.created_at', 'events.name as event_name'])
            ->join('events','document_event.event_id = events.id','left')
            ->join('documents','document_event.document_id = documents.id','left')
            ->where(['documents.invoice_id' => $id, 'documents.companies_id' => Auth::querys()->companies_id])
            ->orderBy('document_event.id', 'desc')
            ->get()
            ->getResult();

        return view('document_reception/show', ['documents' => $documents, 'id' => $id, 'typeRejections' => $typeRejections, 'events' => $documentEvents ]);
    }

    /**
     * Eliminar facturas y eliminar documentos zip, xml y pdf
     * @param null|string $id Id del documento para la eliminación
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function delete($id = null)
    {
        $model = new Company();
        $company = $model->where(['id' => Auth::querys()->companies_id])
            ->asObject()
            ->first();

        $model = new Document();
        $document = $model
            ->select(['id', 'new_name', 'extension', 'invoice_id'])
            ->where(['id' => $id, 'companies_id' => Auth::querys()->companies_id])
            ->asObject()
            ->first();

        $model = new DocumentEvent();
        $events = $model->where(['document_id' => $document->id])
            ->get()
            ->getResult();

        $invoiceM = new Invoice();
        $invoice = $invoiceM->where(['id' => $document->invoice_id])->asObject()->first();

        if (!empty($invoice)) {
            return redirect()->to(base_url().route_to('document-index'))->with('warning', 'El documento no puede ser eliminado.');
        }

        if (count($events) > 0) {
            return redirect()->to(base_url().route_to('document-index'))->with('warning', 'El documento no puede ser eliminado, cuenta con un proceso.');
        }
        if(!$document) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $model = new AssociateDocument();
        $associateDocuments = $model->select(['id', 'new_name', 'extension'])
            ->where(['documents_id' => $id])
            ->get()
            ->getResult();

        foreach ($associateDocuments as $associateDocument) {
            if($associateDocument->extension == 'pdf') {
                deleteFile('document_reception/'.$company->identification_number.'/pdf', $associateDocument->new_name);
            }
            if($associateDocument->extension == 'xml') {
                deleteFile('document_reception/'.$company->identification_number.'/xml', $associateDocument->new_name);
            }
        }
        if($document) {
            deleteFile('document_reception/'.$company->identification_number.'/zip', $document->new_name);
        }

        $model = new AssociateDocument();
        $model->where(['documents_id' => $id])->delete();

        $model = new Document();
        $model->where(['id' => $id])->delete();

        $model = new Invoice();
        $model->where(['id' =>  $document->invoice_id])->delete();

        return  redirect()->to(base_url().route_to('document-index'))->with('success', 'El documento fue eliminado correctamente.');
    }

    /**
     * Vista donde se muesta el listado de  archivos de pago cargados al sistema
     * @param null|string $id
     * @return string view()
     */
    public function payment($id = null)
    {
        $invoice = new Invoice();
        $invoices = $invoice->select([
            'tracking_customer.created_at',
            'tracking_customer.message',
            'invoice_document_upload.file',
            'invoice_document_upload.title',
            'tracking_customer.id as tracking_customer_id',
            'invoices.id as invoice_id',
            'invoice_document_upload.id as invoice_document_upload_id'
        ])
            ->join('invoice_document_upload','invoice_document_upload.invoice_id = invoices.id', 'left')
            ->join('tracking_customer', 'tracking_customer.table_id = invoice_document_upload.id', 'left')
            ->where(['invoices.id' => $id, 'type_tracking_id' => 3])
            ->asObject();

        return view('document_reception/payroll', [
            'invoices' => $invoices->paginate(10),
            'pager'    => $invoices->pager,
            'id'      => $id
        ]);
    }

    /**
     * Función encargada de descargar el archivo .ZIP
     * @param string $filename nombre del archivo
     * @return void
     */
    public function download($filename = null)
    {
        $companies = new Company();
        $company = $companies
            ->select(['identification_number'])
            ->asObject()
            ->find(Auth::querys()->companies_id);
        try{
            download('/document_reception/'.$company->identification_number.'/zip/', $filename);
        }catch (\Exception $e) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        

    }

    /**
     * Vista de mostrar los productos cargados al sistema y permite asociarlos
     * @param string $id Id del documento cargado
     * @return string view()
     */
    public function associateProduct($id = null)
    {
        $invoice = new Document();
        $invoices = $invoice
            ->asObject()
            ->find($id);

        $lineInvoice = new LineInvoice();
        $lineInvoices = $lineInvoice
            ->select(['line_invoices.*' , 'invoices.resolution'])
            ->where(['line_invoices.invoices_id' => $invoices->invoice_id])
            ->join('invoices', 'invoices.id = line_invoices.invoices_id')
            ->asObject();

        $accountingAcount = new \App\Models\AccountingAcount();
        $entryCredit = $accountingAcount->where([
            'companies_id'                  =>  Auth::querys()->companies_id,
            'nature'                        =>  'Crédito',
            'type_accounting_account_id'    =>  '1'
        ])
            ->get()
            ->getResult();

        $accountingAcount = new \App\Models\AccountingAcount();
        $entryDebit = $accountingAcount->where([
            'companies_id'                  =>  Auth::querys()->companies_id,
            'nature'                        =>  'Débito',
            'type_accounting_account_id'    =>  '1'
        ])
            ->get()
            ->getResult();

        $accountingAcount = new \App\Models\AccountingAcount();
        $taxPay = $accountingAcount->where([
            'companies_id'                  =>  Auth::querys()->companies_id,
            'type_accounting_account_id'    =>  '2'
        ])
            ->get()
            ->getResult();


        $accountingAcount = new \App\Models\AccountingAcount();
        $accountPay = $accountingAcount->where([
            'companies_id'                  =>  Auth::querys()->companies_id,
            'type_accounting_account_id'    =>  '4'
        ])
            ->get()
            ->getResult();

        $accountingAcount = new \App\Models\AccountingAcount();
        $taxAdvance = $accountingAcount->where([
            'companies_id'                  =>  Auth::querys()->companies_id,
            'type_accounting_account_id'    =>  '3'
        ])
            ->get()
            ->getResult();


        $product    = new Product();
        $products   = $product->where(['companies_id' => Auth::querys()->companies_id])->get()->getResult();

        return view('document_reception/associate_product', [
            'lineInvoices'  => $lineInvoices->paginate(10),
            'pager'         => $lineInvoice->pager,
            'entryCredit'   => $entryCredit,
            'entryDebit'    => $entryDebit,
            'taxPay'        => $taxPay,
            'taxAdvance'    => $taxAdvance,
            'accountPay'    => $accountPay,
            'products'      => $products,
            'id'            => $id
        ]);
    }

      /**
     * Metodo encargado de asociar o crear producos y subirlos al inventario
     * @param string $id Id de la line o producto de la factura
     * @param string $idDocument Id de documento electronico (factura, nota crédito, nota débito)
     * @return \CodeIgniter\HTTP\RedirectResponse
     * @throws \ReflectionException
     */
    
    public function productCreated($id = null, $idDocument = null, $origin = null)
    {
        $lineInvoice = new LineInvoice();
        $lineInvoice->set([
            'products_id'   => $this->request->getPost('id_product'),

        ])
            ->where(['id' => $id])
            ->update();

        if($idProduct = $this->request->getPost('id_product')) {
            $lineInvoice = new LineInvoice();
            $lineInvoice->set([
                'products_id'       => ($idProduct == 2712 ? null : $idProduct),
                'upload'            =>  ($idProduct == 2712 ? 'Sin Referencia' : 'Cargado'),
                'cost_center_id'    => !empty($this->request->getPost('cost_center')) ? $this->request->getPost('cost_center') : null
            ])
                ->where(['id' => $id])
                ->update();
        }else {
            $data = [
                'name'                          => $this->request->getPost('name'),
                'code'                          => $this->request->getPost('code'),
                'valor'                         => $this->request->getPost('value'),
                'description'                   => $this->request->getPost('description'),
                'unit_measures_id'              => 70,
                'type_item_identifications_id'  => 4,
                'free_of_charge_indicator'      => $this->request->getPost('free'),
                'companies_id'                  => Auth::querys()->companies_id,
                'reference_prices_id'           => 1,
                'entry_credit'                  => $this->request->getPost('entry_credit'),
                'entry_debit'                   => $this->request->getPost('entry_debit'),
                'iva'                           => $this->request->getPost('iva'),
                'retefuente'                    => $this->request->getPost('retefuente'),
                'reteica'                       => $this->request->getPost('reteica'),
                'reteiva'                       => $this->request->getPost('reteiva'),
                'account_pay'                   => $this->request->getPost('account_pay'),
                'brandname'                     => $this->request->getPost('brandname'),
                'modelname'                     => $this->request->getPost('modelname')
            ];
            $product = new Product();
            $product->save($data);


            $lineInvoice = new LineInvoice();
            $lineInvoice->set([
                'products_id'   => $product->getInsertID(),
                'upload'        => ($idProduct == 2712 ? 'Sin Referencia' : 'Cargado'),
                'cost_center_id'    => !empty($this->request->getPost('cost_center')) ? $this->request->getPost('cost_center') : null
            ])
                ->where(['id' => $id])
                ->update();

        }
        $lineInvoice  = new LineInvoice();
        $lineInvoicesId = $lineInvoice->where([ 'id' => $id])->get()->getResult();
        if(count($lineInvoicesId) > 0) {
            $lineInvoices       = $lineInvoice->where([
                'invoices_id' => $lineInvoicesId[0]->invoices_id, 'upload' => 'En espera'
            ])->countAllResults();
            if($lineInvoices == 0) {
                $document = new Document();
                $document->update($idDocument, ['document_status_id' => 3]);
            }
        }



        if($origin == 1) return json_encode(true);

        return redirect()->to(base_url('documents/associate_product/'. $idDocument))->with('success', 'El producto fue asociado correctamente.');

    }

    /**
     * Metodo encargado en subir los datos de la factura y validar si los datos
     * subido par el proveedor se ha correcto.
     * @param $id
     * @return \CodeIgniter\HTTP\RedirectResponse
     * @throws \ReflectionException
     */
    public function validations($id = null, $validador = null, $email = null)
    {

        $documents = new Document();
        $document = $documents->select([
            'associate_document.new_name',
            'companies.identification_number'
        ])
            ->asObject()
            ->join('companies', 'companies.id = documents.companies_id')
            ->join('associate_document', 'associate_document.documents_id = documents.id')
            ->where(['documents.id' => $id, 'associate_document.extension' => 'xml'])
            ->first();



        $xmlController  = new Xml(WRITEPATH . 'uploads/document_reception/'.$document->identification_number.'/xml/'. $document->new_name);
        $data           = $xmlController->assignmentDocument();


        if(isset($data['error'])) {
            $session = '';

            foreach ($data['error'] as $item) {
                $session.=  $item.'<br>';
            }
            return redirect()->to(base_url('documents'))->with('error', $session);
        }


        $info = [];
        isset($data['resolution_number'])   ? $info['resolution_id']    = $data['resolution_number'] : NULL;
        isset($data['number'])              ? $info['resolution']       = $data['number']: NULL;
        $info['companies_id']                                           = Auth::querys()->companies_id;



        $invoice = new Invoice();
        $invoiceCounter = $invoice->where($info)->get()->getResult();

        if(count($invoiceCounter)> 0) {
            if(!empty($email))
                return null;
            return redirect()->to(base_url('documents'))->with('warning', 'La factura ya se encuentra cargada.');
        }


        Xml::registerDocumentXml($data, $id);

        if(!empty($validador)){
            $dataEmail = [
                'companies_id' => Auth::querys()->companies_id,
                'subject' => '<span>Documento cargado desde el facturador</span>',
                'body' => '<p>Documento cargado desde el facturador</p>'
            ];
    
            
            $documentController = new Document();
            $document_email = $documentController->where(['id' => $id])->asObject()->first();
    
            $emailController = new ShoppingEmail();
            $dataEmail['invoices_id'] = $document_email->invoice_id;
            $emailController->save($dataEmail);
            $id_shopping = $emailController->getInsertID();
            $historyM = new HistoryEmails();
            $dataHistory = [
                'shopping_emails_id' => $id_shopping,
                'users_id' => session('user')->id,
                'observation' => 'Cargue del documento'
            ];
            $historyM->save($dataHistory);
        }


        // $UUIDValidate = $this->validationXmlCufe($data['uuid'], $id);
        $UUIDValidate = true;

        if($document->identification_number !=  $data['customer']['identification_number']) {
            $customerValidate = false;
            $documents = new Document();
            $documents->update($id, ['provider' => $data['customer']['name']]);
        } else {
            $customerValidate = true;
        }



        // if($UUIDValidate) {
            // if($UUIDValidate && $document->identification_number ==  $data['customer']['identification_number']) {
            $documents = new Document();
            $documents->update($id, ['document_status_id' => 2]);
        // }else {
        //     $documents = new Document();
        //     $documents->update($id, ['document_status_id' => 4]);
        // }


        return redirect()->to(base_url('documents'))->with('success', 'Factura registrada correctamente.');
    }

    /**
     * Metodo encargao en validar si el cufe extraido de documento electronico
     * es valido ante la DIAN
     * @param string $uuid CUFE O CUDE del documento
     * @param string $id Id del documento cargando en el sistema
     * @return bool
     * @throws \ReflectionException
     */
    public function validationXmlCufe($uuid, $id)
    {
        $companies = new Company();
        $company = $companies
            ->join('certificates', 'companies.id = certificates.companies_id')
            ->where(['companies.id' => Auth::querys()->companies_id])
            ->asObject()
            ->first();

        $client = Services::curlrequest();
        $client->setHeader('Content-Type', 'application/json');
        $client->setHeader('Accept', 'application/json');
        $client->setHeader('Authorization', "Bearer " . $company->token);

        $res = $client->post(
            getenv('API').'/ubl2.1/statusdocument', [
                'form_params' => [
                    'certificate'   => base64_encode(file_get_contents(base_url() . '/assets/upload/certificates/' . $company->name)),
                    'password'      => $company->password,
                    'cufe'          => $uuid,
                    'ambiente'      => $company->type_environments_id == 1 ? 'PRODUCCION' : 'PRODUCCION'
                ],
            ]
        );
        $json        = json_decode($res->getBody());
        $statusUUID  = $json->ResponseDian->Envelope->Body->GetStatusResponse->GetStatusResult->StatusDescription;

        if ( $statusUUID == 'TrackId no existe en los registros de la DIAN.') {
            $document = new Document();
            $document->update($id, ['uuid' => 'false']);
            return false;
        } else {
            $document = new Document();
            $document->update($id, ['uuid' => 'true']);
            return true;
        }

    }

    /**
     * Metodo encargado de descargar los archivos de la vista de pagos asociados
     * @param string $filename Nombre del archivo
     * @return void
     */
    public function downloadFile($filename)
    {
        try{
            download('payment_file', $filename);
        }catch (\Exception $e) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
    }

    /**
     * Metodo encargado de la eliminacion de archivos de pago subidos al sistema
     * @param string $idDocument Id del documento
     * @param string $idTracking Id del Tracking
     * @param string $invoiceId Id de la factura
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    public function deleteFile($idDocument, $idTracking, $invoiceId)
    {

        $invoice = new Invoice();
        $invoices = $invoice
            ->join('invoice_document_upload','invoice_document_upload.invoice_id = invoices.id')
            ->where(['invoice_document_upload.id' => $idDocument, 'companies_id' => Auth::querys()->companies_id ])
            ->asObject()
            ->countAllResults();

        if($invoices  == 0) {
            return redirect()->to(base_url('documents/payment/'.$invoiceId))->with('errors', 'No estas autorizado para eliminar el documento.');

        }else {
            $invoiceDocument = new InvoiceDocumentUpload();
            $invoiceDocument->delete($idDocument);
            $trackingCustomer = new TrackingCustomer();
            $trackingCustomer->delete(['id' => $idTracking]);
            return redirect()->to(base_url('documents/payment/'.$invoiceId))->with('success', 'El pago fue eliminado correctamente.');
        }

    }

    /**
     * Metodo encargado del carque de archivos de pago
     * @param string $id Id del documento subido al sistema
     * @return \CodeIgniter\HTTP\RedirectResponse
     * @throws \ReflectionException
     */
    public function paymentUpload($id =  null)
    {
        $file = $this->request->getFile('file');
        if ($file->isValid()) {
            $invoiceDocument = new InvoiceDocumentUpload();
            $data = [
                'title'         => $file->getName(),
                'file'          => $file->getRandomName(),
                'invoice_id'    => $id
            ];
            $invoiceDocument->insert($data);

            $trackingCustomer = new TrackingCustomer();
            $trackingCustomer->insert([
                'type_tracking_id'  => 3,
                'companies_id'      => Auth::querys()->companies_id,
                'table_id'          => $invoiceDocument->getInsertID(),
                'created_at'        => Date('Y-m-d H:i:s'),
                'message'           => $this->request->getPost('description'),
                'username'          => Auth::querys()->username
            ]);


            $file->move(WRITEPATH.'/uploads/payment_file', $data['file']);
            return redirect()->to(base_url('documents/payment/'.$id))->with('success', 'El pago fue guardado correctamente.');
        }
        return redirect()->to(base_url('documents/payment/'.$id))->with('success', 'El documento subido no es valido.');

    }
}