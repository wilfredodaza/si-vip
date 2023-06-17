<?php

namespace App\Controllers;


use PhpImap\Mailbox;
use App\Models\ConfigurationMail;
use PhpImap\Exceptions\ConnectionException;
use App\Models\Document;
use App\Models\Invoice;
use App\Models\ShoppingEmail;
use App\Models\HistoryEmails;
use App\Models\ShoppingFiles;
use App\Models\Companies;
use App\Models\AssociateDocument;
use ZipArchive;
use App\Controllers\Api\Auth;
use App\Controllers\Documents\DocumentReceptionController;
use App\Models\CheckEmail;
use Config\Services;
use App\Models\Company;
use App\Models\Customer;
use App\Models\ConnectEmail;
use App\Traits\ZipTrait;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use CodeIgniter\Files\File;

class ReceptionEmailController extends BaseController
{

    use ZipTrait;
    
    /**
     * @var Object $connection;
     * Info of Conection Email
     */

    private $mailbox;

    
    /**
     * Conection Email
     * __construct()
     */

    public function __construct()
    {
        $data = new ConnectEmail();
        $connection = $data->where([
            'company_id' => Auth::querys()->companies_id])
        ->asObject()
        ->first();

        if(!$connection) {
            return redirect()->to(base_url('documents/upload_files'))->with('success', 'No se han registrado ninguna credencial de correo electrónico.');
        }

        try {
            $this->mailbox = new Mailbox('{'.$connection->server.':'.$connection->port.'/imap/ssl/novalidate-cert}INBOX', $connection->email, $connection->password, null, 'US-ASCII');
        } catch(ConnectionException $e) {
            return redirect()->to(base_url('documents/upload_files'))->with('error', 'No se pudo realizar la conexión al correo electrónico valida tus credenciales.'. $e->getMessage());
        }

    }

    /**
     * Despligue
     */


    public function index($origin = null) 
    {
        $date = '2021-01-01';//$this->request->getPost('date');
        $model = new Company();
        $emailController = new ShoppingEmail();
        $data = $model->where(['id' => Auth::querys()->companies_id])
        ->asObject()
        ->first();

	$company = $data;
        
        // return var_dump(Auth::querys()->companies_id);

        if(!$data) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); 
        }


        $model = new CheckEmail();
        $checkEmail = $model
        ->where(['company_id' => Auth::querys()->companies_id, 'date' => $date])
        ->asObject()
        ->first();

        try {
            // SUBJECT "'.$data->identification_number.'"
            $mailsIds = $this->mailbox->searchMailbox('ALL SINCE "'.'1 AUG 2022'.'" BEFORE '.'"31 AUG 2022"');
            
            if(count($mailsIds) == 0) {
                return redirect()->to(base_url('documents'))->with('success', 'No se encuntran mas documentos subidos en el correo electronico.');
            }

        } catch(ConnectionException $e) {
            // return var_dump($e->getMessage());
            return redirect()->to(base_url('documents'))->with('error', 'No se pudo realizar la conexión al correo electrónico valida tus credenciales.'. $e->getMessage());
        }

        
       // echo json_encode($mailsIds);die();
        $data = [];

        foreach($mailsIds as $mailsId){
            $mail = $this->mailbox->getMail($mailsId);
            $dataEmail = [
                'email_id' => $mailsId,
                'companies_id' => Auth::querys()->companies_id,
                'subject' => $mail->subject,
                'body' => strip_tags($mail->textHtml, '<table><thead><tbody><th><td><tr><a><style><div><br><span><p><b>')
            ];
            
            $emailController->save($dataEmail);
            $id_shopping = $emailController->getInsertID();
            if (!$this->mailbox->getAttachmentsIgnore()) {
                $attachments = $mail->getAttachments();
                if(!empty($attachments)){
                    $validador = false;
                    foreach ($attachments as $attachment) {
                        if ($mail->hasAttachments()) {
                            if($attachment->mime == 'application/zip; charset=binary') {
                                if(isset($checkEmail->email_id) && $checkEmail->email_id < $mailsId){
                                    if (!is_dir(WRITEPATH.'uploads/document_reception/'.$company->identification_number)) {
                                        mkdir(WRITEPATH.'uploads/document_reception/'.$company->identification_number, 0777);
                                    }
                                    if (!is_dir(WRITEPATH.'uploads/document_reception/'.$company->identification_number.'/zip')) {
                                        mkdir(WRITEPATH.'uploads/document_reception/'.$company->identification_number.'/zip', 0777);
                                    }
                                    $attachment->setFilePath(WRITEPATH.'uploads/document_reception/'.$company->identification_number.'/zip/'.$attachment->id.'.zip');
                                    $attachment->saveToDisk();
                                    array_push($data, $attachment);
                                    $id_document = $this->_zip($attachment->id.'.zip');
                                    if($id_document == 'vacio'){
                                        $validador = true;
                                        $email = $mail->fromAddress;
                                        $customerController = new Customer();
                                        $customer = $customerController
                                            ->where(['email' => $email])
                                            ->orWhere(['email2' => $email])
                                            ->orWhere(['email3' => $email])
                                        ->asObject()->first();
                                        $invoiceCustomer = new Invoice();
                                        $invoiceData = [
                                            'companies_id' => Auth::querys()->companies_id,
                                            'type_documents_id' => 112,
                                            'invoice_status_id' => 19
                                        ];
                                        if(!empty($customer)){
                                            $invoiceData['customers_id'] = $customer->id;
                                        }else{
                                            $dataEmail2['name'] = $mail->fromName;
                                            $dataEmail2['from_address'] = $mail->fromAddress;
                                        }
                                        $invoiceCustomer->save($invoiceData);
                                        $id_invoices = $invoiceCustomer->getInsertID();
                                        $dataEmail2['invoices_id'] = $id_invoices;
                                        $emailController->set($dataEmail2)->where(['id' => $id_shopping])->update();
                                        // $emailController = new ShoppingEmail();
                                        // $emailController->save($dataEmail);
                                        $validador = true;
                                        // $id_shopping = $emailController->getInsertID();
                                        $historyM = new HistoryEmails();
                                        $dataHistory = [
                                            'shopping_emails_id' => $id_shopping,
                                            'users_id' => session('user')->id,
                                            'observation' => 'Recepción de Email'
                                        ];
                                        $historyM->save($dataHistory);
                                        $dataFiles = [
                                            'shopping_email_id' => $id_shopping,
                                            'name' => $attachment->id.'.zip',
                                        ];
                                        $emailFileModel = new ShoppingFiles();
                                        $emailFileModel->save($dataFiles);
                                    }else{
                                        if($id_document != false){
                                            $validador = true;
                                            $documentController = new Document();
                                            $document = $documentController->where(['id' => $id_document])->asObject()->first();
                                            $emailController->set(['invoices_id' => $document->invoice_id])->where(['id' => $id_shopping])->update();
                                            $historyM = new HistoryEmails();
                                            $dataHistory = [
                                                'shopping_emails_id' => $id_shopping,
                                                'users_id' => session('user')->id,
                                                'observation' => 'Recepción de Email'
                                            ];
                                            $historyM->save($dataHistory);
                                            $dataFiles = [
                                                'shopping_email_id' => $id_shopping,
                                                'name' => $attachment->id.'.zip',
                                            ];
                                            $emailFileModel = new ShoppingFiles();
                                            $emailFileModel->save($dataFiles);
                                        }
                                    }
    
                                }else if(!isset($checkEmail->email_id)){
                                    if (!is_dir(WRITEPATH.'uploads/document_reception/'.$company->identification_number)) {
                                        mkdir(WRITEPATH.'uploads/document_reception/'.$company->identification_number, 0777);
                                    }
                                    if (!is_dir(WRITEPATH.'uploads/document_reception/'.$company->identification_number.'/zip')) {
                                        mkdir(WRITEPATH.'uploads/document_reception/'.$company->identification_number.'/zip', 0777);
                                    }
                                    $attachment->setFilePath(WRITEPATH.'uploads/document_reception/'.$company->identification_number.'/zip/'.$attachment->id.'.zip');
                                    $attachment->saveToDisk();
                                    array_push($data, $attachment);
                                    $id_document = $this->_zip($attachment->id.'.zip');
                                    // var_dump($id_document); die();
                                    if($id_document == 'vacio'){
                                        $validador = true;
                                        $email = $mail->fromAddress;
                                        $customerController = new Customer();
                                        $customer = $customerController
                                            ->where(['email' => $email])
                                            ->orWhere(['email2' => $email])
                                            ->orWhere(['email3' => $email])
                                        ->asObject()->first();
                                        $invoiceCustomer = new Invoice();
                                        $invoiceData = [
                                            'companies_id' => Auth::querys()->companies_id,
                                            'type_documents_id' => 112,
                                            'invoice_status_id' => 19
                                        ];
                                        if(!empty($customer)){
                                            $invoiceData['customers_id'] = $customer->id;
                                        }else{
                                            $dataEmail2['name'] = $mail->fromName;
                                            $dataEmail2['from_address'] = $mail->fromAddress;
                                        }
                                        $invoiceCustomer->save($invoiceData);
                                        $id_invoices = $invoiceCustomer->getInsertID();
                                        $dataEmail2['invoices_id'] = $id_invoices;
                                        $emailController->set($dataEmail2)->where(['id' => $id_shopping])->update();
                                        // $emailController = new ShoppingEmail();
                                        // $emailController->save($dataEmail);
                                        $validador = true;
                                        // $id_shopping = $emailController->getInsertID();
                                        $historyM = new HistoryEmails();
                                        $dataHistory = [
                                            'shopping_emails_id' => $id_shopping,
                                            'users_id' => session('user')->id,
                                            'observation' => 'Recepción de Email'
                                        ];
                                        $historyM->save($dataHistory);
                                        $dataFiles = [
                                            'shopping_email_id' => $id_shopping,
                                            'name' => $attachment->id.'.zip',
                                        ];
                                        $emailFileModel = new ShoppingFiles();
                                        $emailFileModel->save($dataFiles);
                                    }else{
                                        if($id_document != false){
                                            $validador = true;
                                            $documentController = new Document();
                                            $document = $documentController->where(['id' => $id_document])->asObject()->first();
                                            $emailController->set(['invoices_id' => $document->invoice_id])->where(['id' => $id_shopping])->update();
                                            $historyM = new HistoryEmails();
                                            $dataHistory = [
                                                'shopping_emails_id' => $id_shopping,
                                                'users_id' => session('user')->id,
                                                'observation' => 'Recepción de Email'
                                            ];
                                            $historyM->save($dataHistory);
                                            $dataFiles = [
                                                'shopping_email_id' => $id_shopping,
                                                'name' => $attachment->id.'.zip',
                                            ];
                                            $emailFileModel = new ShoppingFiles();
                                            $emailFileModel->save($dataFiles);
                                        }
                                    }
                                }
                            }
                        }
                    }
                    foreach ($attachments as $attachment) {
                        if ($mail->hasAttachments()) {
                            if($attachment->mime !== 'application/zip; charset=binary') {
                                if(!$validador){
                                    $validador = true;
                                    $email = $mail->fromAddress;
                                    $customerController = new Customer();
                                    $customer = $customerController
                                        ->where(['email' => $email])
                                        ->orWhere(['email2' => $email])
                                        ->orWhere(['email3' => $email])
                                    ->asObject()->first();
                                    $invoiceCustomer = new Invoice();
                                    $invoiceData = [
                                        'companies_id' => Auth::querys()->companies_id,
                                        'type_documents_id' => 112,
                                        'invoice_status_id' => 19
                                    ];
                                    if(!empty($customer)){
                                        $invoiceData['customers_id'] = $customer->id;
                                    }else{
                                        $dataEmail2['name'] = $mail->fromName;
                                        $dataEmail2['from_address'] = $mail->fromAddress;
                                    }
                                    if(isset($checkEmail->email_id) && $checkEmail->email_id < $mailsId){
                                        $invoiceCustomer->save($invoiceData);
                                        $id_invoices = $invoiceCustomer->getInsertID();
                                        $dataEmail2['invoices_id'] = $id_invoices;
                                        $emailController->set($dataEmail2)->where(['id' => $id_shopping])->update();
                                        // $emailController = new ShoppingEmail();
                                        // $emailController->save($dataEmail);
                                        // $id_shopping = $emailController->getInsertID();
                                        $historyM = new HistoryEmails();
                                        $dataHistory = [
                                            'shopping_emails_id' => $id_shopping,
                                            'users_id' => session('user')->id,
                                            'observation' => 'Recepción de Email'
                                        ];
                                        $historyM->save($dataHistory);
                                        $dataFiles = [
                                            'shopping_email_id' => $id_shopping,
                                            'name' => $attachment->id.'.zip',
                                        ];
                                        $emailFileModel = new ShoppingFiles();
                                        $emailFileModel->save($dataFiles);
                                    }else if(!isset($checkEmail->email_id)){
                                        $invoiceCustomer->save($invoiceData);
                                        $id_invoices = $invoiceCustomer->getInsertID();
                                        $dataEmail2['invoices_id'] = $id_invoices;
                                        $emailController->set($dataEmail2)->where(['id' => $id_shopping])->update();
                                        // $emailController = new ShoppingEmail();
                                        // $emailController->save($dataEmail);
                                        // $id_shopping = $emailController->getInsertID();
                                        $historyM = new HistoryEmails();
                                        $dataHistory = [
                                            'shopping_emails_id' => $id_shopping,
                                            'users_id' => session('user')->id,
                                            'observation' => 'Recepción de Email'
                                        ];
                                        $historyM->save($dataHistory);
                                        $dataFiles = [
                                            'shopping_email_id' => $id_shopping,
                                            'name' => $attachment->id.'.zip',
                                        ];
                                        $emailFileModel = new ShoppingFiles();
                                        $emailFileModel->save($dataFiles);
                                    }
                                }else{
                                    if (!is_dir(WRITEPATH.'emails/'.$company->identification_number)) {
                                        mkdir(WRITEPATH.'emails/'.$company->identification_number, 0777);
                                    }
                                    $attachment->setFilePath(WRITEPATH.'emails/'.$company->identification_number.'/'.$attachment->name);
                                    $attachment->saveToDisk();
                                    $dataFiles = [
                                        'shopping_email_id' => $id_shopping,
                                        'name' => $attachment->name,
                                    ];
                                    $emailFileModel = new ShoppingFiles();
                                    $emailFileModel->save($dataFiles);
                                }
                            }
                        }
                    }
                }else{
                    $email = $mail->fromAddress;
                    $customerController = new Customer();
                    $customer = $customerController
                        ->where(['email' => $email])
                        ->orWhere(['email2' => $email])
                        ->orWhere(['email3' => $email])
                    ->asObject()->first();
                    $invoiceCustomer = new Invoice();
                    $invoiceData = [
                        'companies_id' => Auth::querys()->companies_id,
                        'type_documents_id' => 112,
                        'invoice_status_id' => 19
                    ];
                    if(!empty($customer)){
                        $invoiceData['customers_id'] = $customer->id;
                    }else{
                        $dataEmail['name'] = $mail->fromName;
                        $dataEmail['from_address'] = $mail->fromAddress;
                    }
                    if(isset($checkEmail->email_id) && $checkEmail->email_id < $mailsId){
                        $invoiceCustomer->save($invoiceData);
                        $id_invoices = $invoiceCustomer->getInsertID();
                        $dataEmail['invoices_id'] = $id_invoices;
                        $emailController = new ShoppingEmail();
                        $emailController->save($dataEmail);
                        $id_shopping = $emailController->getInsertID();
                        $historyM = new HistoryEmails();
                        $dataHistory = [
                            'shopping_emails_id' => $id_shopping,
                            'users_id' => session('user')->id,
                            'observation' => 'Recepción de Email'
                        ];
                        $historyM->save($dataHistory);
                    }else if(!isset($checkEmail->email_id)){
                        $invoiceCustomer->save($invoiceData);
                        $id_invoices = $invoiceCustomer->getInsertID();
                        $dataEmail['invoices_id'] = $id_invoices;
                        $emailController = new ShoppingEmail();
                        $emailController->save($dataEmail);
                        $id_shopping = $emailController->getInsertID();
                        $historyM = new HistoryEmails();
                        $dataHistory = [
                            'shopping_emails_id' => $id_shopping,
                            'users_id' => session('user')->id,
                            'observation' => 'Recepción de Email'
                        ];
                        $historyM->save($dataHistory);
                    }
                }
            }
            
            // var_dump($dataEmail);
            
        }
        
        // die();

       if(is_null($checkEmail) && isset($mailsId)) {
            $model = new CheckEmail();
            $checkEmailCreate = $model->save([
                'company_id'    => Auth::querys()->companies_id,
                'folder'        => 'INBOX',
                'date'          => $date,
                'email_id'      => $mailsId
            ]);
        }else if(!is_null($checkEmail) && isset($mailsId)){
            $model = new CheckEmail();
            $checkEmailUpdate = $model->where([
                'id'            =>   $checkEmail->id, 
                'company_id'    =>   Auth::querys()->companies_id
                ])
            ->set('date', $date)
            ->set('email_id', $mailsId)
            ->update();
        }

     
        if($origin == 0)
            return  redirect()->to(base_url('documents'))->with('success', 'Datos Actualizados Correctamente.');
        else
            return  redirect()->to(base_url('shopping'))->with('success', 'Datos Actualizados Correctamente.');
    }




    private function _zip($file)
    {
  	    $model = new Company();
        $data = $model->where(['id' => Auth::querys()->companies_id])
        ->asObject()
        ->first();

        helper('text');
        $documents = $this->zipExtraction($file,WRITEPATH.'uploads/document_reception/'.$data->identification_number.'/zip/');

 		$documentData = [
            'name'              =>  $file,
            'new_name'          =>  $file,
            'extension'         =>  'zip',
            'created_at'        =>  date('Y-m-d H:i:s'),
            'invoice_id'        =>  null,
            'companies_id'      =>  Auth::querys()->companies_id,
            'document_status_id'=>  1,
            'zip'               =>  $file
        ];
        try {
		    $files = decompress(WRITEPATH.'uploads/document_reception/'.$data->identification_number.'/zip/'.$file,
                WRITEPATH.'uploads/document_reception/'.$data->identification_number.'/zip/');
            $xml = false;
            foreach ($files as $key => $value) {
                $newFile = new File(WRITEPATH.'uploads/document_reception/'.$data->identification_number.'/zip/'.$value);
                switch($newFile->getExtension()) {
                    case 'xml':
                        $xml = true;
                        break;
                }
                unlink(WRITEPATH.'uploads/document_reception/'.$data->identification_number.'/zip/'.$value);
            }
            if(!$xml){
                return 'vacio';
            }
            $documentCreate = new Document();
            $documentCreate->save($documentData);
            $id_document = $documentCreate->getInsertID();

            $files = decompress(WRITEPATH.'uploads/document_reception/'.$data->identification_number.'/zip/'.$file,
            WRITEPATH.'uploads/document_reception/'.$data->identification_number.'/zip/');
                

            foreach ($files as $file){
                $newFile = new File(WRITEPATH.'uploads/document_reception/'.$data->identification_number.'/zip/'.$file);

                $associateDocument = new AssociateDocument();
                $name       = $newFile->getFilename();
                $newName    = $newFile->getRandomName();
                $extencion  = $newFile->getExtension();
                $associateDocument->save([
                    'documents_id' => $id_document,
                    'name'         => $name,
                    'new_name'     => $newName,
                    'extension'    => $extencion
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
            $documentReception = new DocumentReceptionController();
            $valid = $documentReception->validations($id_document, null, 1);
            if($valid == false)
                $id_document = false;
        }catch(\Exception $e) {
            
        }
        return $id_document;

       /* $documentPdf = [];
        $documents_string = implode(',', $documents);
        if([strpos($documents_string, '.xml')] !== false){
            foreach ($documents as $document) {
                $name =  random_string('alnum', 20);
                if(strstr($document, '.xml')) {
                    $documentData = [
                        'name'              =>  $document,
                        'new_name'          =>  $document,
                        'extension'         =>  'xml',
                        'created_at'        =>  date('Y-m-d H:i:s'),
                        'invoice_id'        =>  null,
                        'companies_id'      =>  Auth::querys()->companies_id,
                        'document_status_id'=>  1,
                        'zip'               =>  $file
                    ];
                    try {
                        $documentCreate = new Document();
                        $documentCreate->save($documentData);
                        $id = $documentCreate->getInsertID();
                        $documentReception = new DocumentReceptionController();
                        $documentReception->validations($id);
                        return $id;
                    }catch(\Exception $e) {
    
                    }
                }
                if(strstr($document, '.pdf')) {
                    $documentPdf = [
                        'name'              => $document,
                        'extension'         => 'pdf',
                        'new_name'          => $document,
                        'documents_id'      => '',
                    ];*/
                  /*  $companies      = new Company();
                    $client         = Services::curlrequest();
                    $token          = $companies->asObject()->find(Auth::querys()->companies_id);
                    $client->setHeader('Content-Type', 'application/json');
                    $client->setHeader('Accept', 'application/json');
                    $client->setHeader('Authorization', "Bearer " . $token->token);
        
                    $res = $client->post(getenv('API').'/ubl2.1/upload-file', [
                        'form_params' => [
                        'new_name'  => $documentPdf['new_name'],
                        'prefix'    => $documentPdf['extension'],
                        'file'      => base64_encode(file_get_contents(WRITEPATH . 'uploads/' .$document)),
                        ],
                    ]);
    
        
                    $json = json_decode($res->getBody());
                    //unlink(WRITEPATH . 'uploads/'.$documentPdf['new_name']);
                    if (isset($json->errors)) {
                        echo json_encode($json);
                        die();
                    }*/
          /*      }
            }
            if (isset($id)) {
                $assiateDocument        = new AssociateDocument();
                $documentPdf['documents_id']  = $id;
                $assiateDocument->save($documentPdf);
            }
        }else{
            return null;
        }*/
   
    }



    public function export()
    {
        $model  = new Invoice();
        $invoices = $model->select([
            'invoices.resolution',
            'line.description', 
            'line.price_amount', 
            'line.quantity',
            'invoices.created_at',
            'line.discount_amount',
            'invoices.type_documents_id',
            'line.line_extension_amount', 
            'customers.name as customer_name', 
            'type_document_identifications.name as type_document_identification_name',
            'customers.identification_number',
            'type_documents.name as type_document_name',
            '(SELECT tax_amount FROM line_invoice_taxs as tax JOIN  line_invoices on tax.line_invoices_id = line_invoices.id WHERE tax.taxes_id = 1 and line_invoices.id = line.id ) as iva',
            '(SELECT percent FROM line_invoice_taxs as tax JOIN  line_invoices on tax.line_invoices_id = line_invoices.id WHERE tax.taxes_id = 1 and line_invoices.id = line.id ) as percent_iva',
            '(SELECT tax_amount FROM line_invoice_taxs as tax JOIN  line_invoices on tax.line_invoices_id = line_invoices.id WHERE tax.taxes_id = 5 and line_invoices.id = line.id ) as reteiva',
            '(SELECT tax_amount FROM line_invoice_taxs as tax JOIN  line_invoices on tax.line_invoices_id = line_invoices.id WHERE tax.taxes_id = 6 and line_invoices.id = line.id ) as reterenta',
            '(SELECT tax_amount FROM line_invoice_taxs as tax JOIN  line_invoices on tax.line_invoices_id = line_invoices.id WHERE tax.taxes_id = 7 and line_invoices.id = line.id ) as reteica',
            'municipalities.name as municipality_name'
        ])
        ->join('line_invoices as line', 'invoices.id = line.invoices_id', 'left')
        ->join('customers', 'customers.id = invoices.customers_id', 'left')
        ->join('type_document_identifications', 'type_document_identifications.id = customers.type_document_identifications_id', 'left')
        ->join('type_documents', 'type_documents.id = invoices.type_documents_id', 'left')
        ->join('municipalities', 'municipalities.id = customers.municipality_id')
        ->whereIn('invoices.type_documents_id', [101, 102, 103, 104])
        ->where(['invoices.created_at >=' => '2022-05-01 00:00:00','invoices.created_at <=' => '2022-08-31 23:59:59'] )
        ->get()
        ->getResult();




        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator('Maarten Balliauw')
            ->setLastModifiedBy('Maarten Balliauw')
            ->setTitle('Reporte de Facturas Externas')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')
            ->setKeywords('office 2007 openxml php')
            ->setCategory('Test result file');

        $styleArray = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal'    => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'      => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFD7DBDD',
                ],

            ]
        ];


        $spreadsheet->getActiveSheet()->getStyle('A1:U1')->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(40);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('T')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('U')->setAutoSize(true);
        $spreadsheet->setActiveSheetIndex(0)
        ->setCellValue('A1', 'Tercero')
        ->setCellValue('B1', 'Tipo de identificación')
        ->setCellValue('C1', 'Número de identificación')
        ->setCellValue('D1', 'Tipo de documento')
        ->setCellValue('E1', 'Número de documento')
        ->setCellValue('F1', 'Producto / Servicio')
        ->setCellValue('G1', 'Cantidad')
        ->setCellValue('H1', 'Valor unitario del producto')
        ->setCellValue('I1', 'Descuento')
        ->setCellValue('J1', 'Valor total de los productos')
        ->setCellValue('K1', 'IVA (%)')
        ->setCellValue('L1', 'IVA ($)')
        ->setCellValue('M1', 'Retención en la fuente')
        ->setCellValue('N1', 'Retención de ICA')
        ->setCellValue('O1', 'Retención de IVA')
        ->setCellValue('P1', 'Total Factura')
        ->setCellValue('Q1', 'Fecha de Creacion')
        ->setCellValue('R1', 'Ciudad');

        $i = 2;
        foreach($invoices as $invoice) {
            if($invoice->type_documents_id == 101 || $invoice->type_documents_id == 102 || $invoice->type_documents_id == 104) {
                $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $i, $invoice->customer_name)
                ->setCellValue('B' . $i, $invoice->type_document_identification_name)
                ->setCellValue('C' . $i, $invoice->identification_number)
                ->setCellValue('D' . $i, $invoice->type_document_name)
                ->setCellValue('E' . $i, $invoice->resolution)
                ->setCellValue('F' . $i, $invoice->description)
                ->setCellValue('G' . $i, $invoice->quantity)
                ->setCellValue('H' . $i, $invoice->price_amount)
                ->setCellValue('I' . $i, ($invoice->discount_amount  == null ? 0 : $invoice->discount_amount))
                ->setCellValue('J' . $i, $invoice->line_extension_amount)
                ->setCellValue('K' . $i, $invoice->percent_iva)
                ->setCellValue('L' . $i, ($invoice->iva == null ? 0 : $invoice->iva))
                ->setCellValue('M' . $i, ($invoice->reterenta == null ? 0 : $invoice->reterenta))
                ->setCellValue('N' . $i, ($invoice->reteica == null ? 0 : $invoice->reteica))
                ->setCellValue('O' . $i, ($invoice->reteiva == null ? 0 : $invoice->reteiva))
                ->setCellValue('P' . $i, ($invoice->line_extension_amount + ($invoice->iva == null ? 0 : $invoice->iva)) - (($invoice->reterenta == null ? 0 : $invoice->reterenta) +   ($invoice->reteica == null ? 0 : $invoice->reteica)+ ($invoice->reteiva == null ? 0 : $invoice->reteiva)))
                ->setCellValue('Q' . $i, $invoice->created_at)
                ->setCellValue('R' . $i, $invoice->municipality_name);
                
            }else {
                $valor = ($invoice->line_extension_amount + ($invoice->iva == null ? 0 : $invoice->iva)) - (($invoice->reterenta == null ? 0 : $invoice->reterenta) +   ($invoice->reteica == null ? 0 : $invoice->reteica)+ ($invoice->reteiva == null ? 0 : $invoice->reteiva));
                $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $i, $invoice->customer_name)
                ->setCellValue('B' . $i, $invoice->type_document_identification_name)
                ->setCellValue('C' . $i, $invoice->identification_number)
                ->setCellValue('D' . $i, $invoice->type_document_name)
                ->setCellValue('E' . $i, $invoice->resolution)
                ->setCellValue('F' . $i, $invoice->description)
                ->setCellValue('G' . $i, '-'.$invoice->quantity)
                ->setCellValue('H' . $i, '-'.$invoice->price_amount)
                ->setCellValue('I' . $i, ($invoice->discount_amount  == null ? 0 : $invoice->discount_amount))
                ->setCellValue('J' . $i, '-'.$invoice->line_extension_amount)
                ->setCellValue('K' . $i, $invoice->percent_iva)
                ->setCellValue('L' . $i, ($invoice->iva == null ? 0 : '-'.$invoice->iva))
                ->setCellValue('M' . $i, ($invoice->reterenta == null ? 0 :  '-'.$invoice->reterenta))
                ->setCellValue('N' . $i, ($invoice->reteica == null ? 0 :  '-'.$invoice->reteica))
                ->setCellValue('O' . $i, ($invoice->reteiva == null ? 0 :  '-'.$invoice->reteiva))
                ->setCellValue('P' . $i,  '-'.$valor)
                ->setCellValue('Q' . $i, $invoice->created_at)
                ->setCellValue('R' . $i, $invoice->municipality_name);
            }
              $i++;
        }

        $spreadsheet->getActiveSheet()->setTitle('Reporte_Facturación_Detallado');
        $spreadsheet->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Reporte_Facturación_Detallado.xls"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');

        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $writer = IOFactory::createWriter($spreadsheet, 'Xls');
        $writer->save('php://output');
        exit;

        echo json_encode($invoices);die();
    }

   

    public function demo()
    {
        return view('upload_file/demo');
    }


    public function history()
    {
        return view('upload_file/history');
    }

    public function providers()
    {
        return view('upload_file/portal_proveedores');
    }

    public function show()
    {
        return view('upload_file/show');
    }

    public function finansiera()
    {
        return view('upload_file/finansiera');
    }

    public function contabilidad()
    {
        return view('upload_file/contabilidad');
    }

    public function showContabilidad()
    {
        return view('upload_file/show_contabilidad');
    }

    
}