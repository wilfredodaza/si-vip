<?php

namespace App\Controllers\Shopping;


use \App\Controllers\BaseController;
use PhpImap\Mailbox;
use App\Models\ConfigurationMail;
use PhpImap\Exceptions\ConnectionException;
use App\Models\Document;
use App\Models\ShoppingEmail;
use App\Models\HistoryEmails;
use App\Models\ShoppingFiles;
use App\Models\InvoiceFiles;
use App\Models\InvoiceTypeFiles;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\LineInvoice;
use App\Models\InvoiceStatus;
use App\Models\Companies;
use App\Models\TypeDocument;
use App\Models\AssociateDocument;
use App\Models\ModuleShopping;
use App\Models\TypeRejection;
use ZipArchive;
use App\Controllers\Xml\Xml;
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


use App\Controllers\EventController;

use \DateTime; 

class ShoppingController extends BaseController
{

    use ZipTrait;
    
    /**
     * @var Object $connection;
     * Info of Conection Email
     */

    private $mailbox;
    private $zip;

    
    /**
     * Conection Email
     * __construct()
     */

    public function __construct()
    {

    }

    /**
     * Despligue
     */


    public function index() 
    {

        // $data = EventController::event(17, 4, true);
        // var_dump(json_decode($data));
        // die();

        // $emailController = new ShoppingEmail();
        // $invoicesTFM = new InvoiceTypeFiles();
        // $invoicestypeFiles = $invoicesTFM->where(['block' => 'SC'])->asObject()->get()->getResult();
        // $emails = $emailController
        //     ->select([
        //         'shopping_emails.*',
        //         'invoices.uuid',
        //         'invoices.resolution',
        //         'invoices.created_at as created_invoice',
        //         'invoices.prefix',
        //         'invoices.payment_due_date',
        //         'invoices.id as invoices_id',
        //         'invoices.type_documents_id as type_documents_id_invoices',
        //         'invoices.invoice_status_id as status_id',
        //         'invoice_status.name as status_name',
        //         'invoice_status.description as status_description',
        //         'type_documents.name as type_documents_name',
        //         'type_documents.prefix as type_documents_prefix',
        //         'customers.id as customer_id',
        //         'customers.name as customer_name',
        //         'companies.company as company_name',
        //         'companies.identification_number',
        //         'document_status.id as id_doc_status',
        //         'document_status.description as desc_doc_status',
        //         'documents.id as id_document'
        //     ])
        //     ->join('invoices','invoices.id = shopping_emails.invoices_id','left')
        //     ->join('type_documents', 'type_documents.id = invoices.type_documents_id', 'left')
        //     ->join('invoice_status', 'invoice_status.id = invoices.invoice_status_id')
        //     ->join('customers', 'invoices.customers_id = customers.id', 'left')
        //     ->join('companies', 'companies.id = invoices.companies_id', 'left')
        //     ->join('documents', 'invoices.id = documents.invoice_id', 'left')
        //     ->join('document_status', 'document_status.id = documents.document_status_id', 'left')
        //     ->where(['shopping_emails.companies_id' => Auth::querys()->companies_id])
        // ->orderBy('id', 'desc');
        // $emails = $emails->get()->getResult();
        // foreach ($emails as $key => $email) {
        //     // if(!($email->type_documents_id_invoices == 112 || $email->type_documents_id_invoices == 113)){
        //         $date = date("Y-m-d H:i:s",strtotime($email->created_invoice.'+ 3 days'));
        //         $date_created = new DateTime($date);
        //         $now = new DateTime(date("Y-m-d H:i:s"));
        //         $diff = $now->diff($date_created);
        //         if($diff->days == 2 && $diff->invert == 0)
        //             $email->vence = $diff->days.'D <i class="material-icons text-green green-text tiny">brightness_1</i>';
        //         elseif($diff->days == 1 && $diff->invert == 0)
        //                 $email->vence = $diff->days.'D <i class="material-icons text-green orange-text tiny">brightness_1</i>';
        //         elseif($diff->days == 0 && $diff->invert == 0) $email->vence = $diff->days.'D <i class="material-icons text-green yellow-text tiny">brightness_1</i>';
        //         elseif($diff->invert == 1) $email->vence = 'Fecha <br> vencida';
        //     // }else $email->vence = 'No aplica';

        //     var_dump([$date_created,$now, $diff, $email->vence]);
        // }
        // return null;
        $invoiceTFM     = new InvoiceTypeFiles();
        $invoiceStatus  = new InvoiceStatus();
        $product        = new Product();
        $emailM         = new ShoppingEmail();
        $model          = new TypeRejection();
        $typeRejections = $model->asObject()->get()->getResult();
        $typereject = (object) [
            'name'  => 'Pendiente',
            'id'    => 00,
            'code'  => 00
        ];
        array_unshift( $typeRejections, $typereject);
        $products       = $product->where(['companies_id' => Auth::querys()->companies_id])->get()->getResult();
        $status         = $invoiceStatus->where(['block' => 'SC'])->whereNotIn('name', ['Pendientes'])->asObject()->get()->getResult();
        $statusI        = $invoiceStatus->where(['block' => 'SC'])->select([
                'invoice_status.*',
                '(select count(*) from invoices
                    INNER JOIN `shopping_emails` ON shopping_emails.invoices_id = invoices.id
                    where (invoice_status.id = invoices.invoice_status_id and shopping_emails.companies_id = '.Auth::querys()->companies_id.')
                ) AS total'
            ])
            ->groupBy(['id'])
            ->asObject()->get()->getResult();
        // var_dump($statusI);die;
        $types = $invoiceTFM->where(['block' => 'SC'])->asObject()->get()->getResult();
        $data = (object) [
            'id' => 'todos',
            'name' => 'Todas',
        ];
        array_push($status, $data);

        foreach ($statusI as $key => $statu) {
            if($statu->name == 'Aceptadas'){
                $statu->icon = 'check';
                $statu->color = 'gradient-45deg-green-teal';
            } else if($statu->name == 'Rechazadas'){
                $statu->icon = 'close';
                $statu->color = 'gradient-45deg-red-pink';
            } else if($statu->name == 'Pendientes'){
                $statu->icon = 'report_problem';
                $statu->color = 'gradient-45deg-amber-amber';
            } else{
                $statu->icon = 'priority_high';
                $statu->color = 'gradient-45deg-blue-indigo';
            }
        }

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

        $costCenterM = new \App\Models\CostCenter();
        $costCenter = $costCenterM->where([
            'companies_id'                  =>  Auth::querys()->companies_id,
        ])->get()->getResult();
        return view('shopping/index', [
            'estados'   => $status,
            'types'     => $types,
            // 'invoices'  => $invoices,
            'products'  => $products,

            // 'lineInvoices'  => $lineInvoices,
            'entryCredit'       => $entryCredit,
            'entryDebit'        => $entryDebit,
            'taxPay'            => $taxPay,
            'taxAdvance'        => $taxAdvance,
            'accountPay'        => $accountPay,
            'cost_center'       => $costCenter,
            'indicadores'       => $statusI,
            'typeRejections'    => $typeRejections
        ]);
    }

    public function tables($status){
        $emailController = new ShoppingEmail();
        $invoicesTFM = new InvoiceTypeFiles();
        $invoicestypeFiles = $invoicesTFM->where(['block' => 'SC'])->asObject()->get()->getResult();
        $emails = $emailController
            ->select([
                'shopping_emails.*',
                'invoices.uuid',
                'invoices.resolution',
                'invoices.created_at as created_invoice',
                'invoices.prefix',
                'invoices.payable_amount as valor',
                'invoices.payment_due_date',
                'invoices.id as invoices_id',
                'invoices.type_documents_id as type_documents_id_invoices',
                'invoices.invoice_status_id as status_id',
                'invoice_status.name as status_name',
                'invoice_status.description as status_description',
                'type_documents.name as type_documents_name',
                'type_documents.prefix as type_documents_prefix',
                'customers.id as customer_id',
                'customers.name as customer_name',
                'companies.company as company_name',
                'companies.identification_number',
                'document_status.id as id_doc_status',
                'document_status.description as desc_doc_status',
                'documents.id as id_document'
            ])
            ->join('invoices','invoices.id = shopping_emails.invoices_id','left')
            ->join('type_documents', 'type_documents.id = invoices.type_documents_id', 'left')
            ->join('invoice_status', 'invoice_status.id = invoices.invoice_status_id')
            ->join('customers', 'invoices.customers_id = customers.id', 'left')
            ->join('companies', 'companies.id = invoices.companies_id', 'left')
            ->join('documents', 'invoices.id = documents.invoice_id', 'left')
            ->join('document_status', 'document_status.id = documents.document_status_id', 'left')
            ->where(['shopping_emails.companies_id' => Auth::querys()->companies_id])
        ->orderBy('id', 'desc');
        if($status != 'todos'){
            if($status != 19) $emails = $emails->where(['invoice_status_id' => $status]);
            else $emails = $emails->whereIn('invoice_status_id', [$status, 22]);
        }
        $emails = $emails->get()->getResult();
        $type_documents = [];
        foreach ($emails as $key => $email) {
            
            $email->valor = '$'.number_format($email->valor, 0, ',', '.');
            $email->created_at = date('y-m-d H:i', strtotime($email->created_at));
            // if($status == 'todos'){
                $type_documents[$email->type_documents_id_invoices] = (object) ['name' => $email->type_documents_name, 'prefix' => $email->type_documents_prefix];
            // }else{}
            if(!empty($email->id_doc_status)){
                if($email->id_doc_status == 1 || $email->id_doc_status == 2) $color_doc = 'yellow';
                else if($email->id_doc_status == 3) $color_doc = 'green';
                else if($email->id_doc_status == 4) $color_doc = 'red';
                $email->resolution = '<p class="tooltipped"data-position="bottom" data-tooltip="'.$email->desc_doc_status.'">'.$email->resolution.'<i class="material-icons '.$color_doc.'-text tiny">brightness_1</i></p>';
            }
            $email->files = $emailController->Files($email->invoices_id);
            $email->d_requiridos = '';
            $email->customer_name = !empty($email->customer_name) ? '<p style="width:100px" class="tooltipped" data-position="bottom" data-tooltip="'.$email->customer_name.'"><span class="truncate">'.$email->customer_name.'</span></p>' : '';
            $email->company_name = '<p style="width:100px" class="tooltipped" data-position="bottom" data-tooltip="'.$email->company_name.'"><span class="truncate">'.$email->company_name;
            $email->dian = !empty($email->uuid) ? '<i class="material-icons text-green green-text">check</i>' : '<i class="material-icons text-green red-text">close</i>';
            if(!empty($email->id_doc_status)){
                foreach($invoicestypeFiles as $type){
                    $validation = null;
                    switch ($type->description) {
                        case 'almacen':
                                foreach ($email->files as $key => $file) {
                                    if($file->invoices_type_files_id == $type->id){
                                        $validation = $file;
                                        break;
                                    }
                                }
                                if(!empty($validation)){
                                    if($validation->status == 'Aceptado')
                                        $email->d_requiridos .= '<a class="waves-effect waves-light tooltipped" data-position="bottom" data-tooltip="'.$type->name.'" onclick="add_file(`'.$email->invoices_id.'`, `'.$email->id.'`, true)"><i class="material-icons text-green green-text">check</i></a>';
                                    if($validation->status == 'Rechazado')
                                    $email->d_requiridos .= '<a class="waves-effect waves-light  modal-trigger tooltipped" data-position="bottom" data-tooltip="'.$type->name.'<br>Archivo rechazado" onclick="add_file(`'.$email->invoices_id.'`, `'.$email->id.'`, false)"><i class="material-icons text-green red-text">error_outline</i></a>';
                                    if($validation->status == 'Pendiente')
                                        $email->d_requiridos .= '<a class="waves-effect waves-light tooltipped" data-position="bottom" data-tooltip="'.$type->name.'" onclick="file_pendiente(`'.$validation->id.'`,`'.$email->id.'`, `'.$status.'`)"><i class="material-icons text-green orange-text">priority_high</i></a>';
                                }else
                                    $email->d_requiridos .= '<a class="waves-effect waves-light  modal-trigger tooltipped" data-position="bottom" data-tooltip="'.$type->name.'" onclick="add_file(`'.$email->invoices_id.'`, `'.$email->id.'`, false)"><i class="material-icons text-green red-text">close</i></a>';
                            break;
                        case 'oc':
                                foreach ($email->files as $key => $file) {
                                    if($file->invoices_type_files_id == $type->id){
                                        $validation = $file;
                                        break;
                                    }
                                }
                                if(!empty($validation)){
                                    if($validation->status == 'Aceptado')
                                        $email->d_requiridos .= '<a class="waves-effect waves-light tooltipped" data-position="bottom" data-tooltip="'.$type->name.'" onclick="add_file(`'.$email->invoices_id.'`, `'.$email->id.'`, true)"><i class="material-icons text-green green-text">check</i></a>';
                                    if($validation->status == 'Rechazado')
                                    $email->d_requiridos .= '<a class="waves-effect waves-light  modal-trigger tooltipped" data-position="bottom" data-tooltip="'.$type->name.'<br>Archivo rechazado" onclick="add_file(`'.$email->invoices_id.'`, `'.$email->id.'`, false)"><i class="material-icons text-green red-text">error_outline</i></a>';
                                    if($validation->status == 'Pendiente')
                                        $email->d_requiridos .= '<a class="waves-effect waves-light tooltipped" data-position="bottom" data-tooltip="'.$type->name.'" onclick="file_pendiente(`'.$validation->id.'`,`'.$email->id.'`, `'.$status.'`)"><i class="material-icons text-green orange-text">priority_high</i></a>';
                                }else
                                    $email->d_requiridos .= '<a class="waves-effect waves-light  modal-trigger tooltipped" data-position="bottom" data-tooltip="'.$type->name.'" onclick="add_file(`'.$email->invoices_id.'`, `'.$email->id.'`, false)"><i class="material-icons text-green red-text">close</i></a>';
                            break;
                        case 'remision':
                                foreach ($email->files as $key => $file) {
                                    if($file->invoices_type_files_id == $type->id){
                                        $validation = $file;
                                        break;
                                    }
                                }
                                if(!empty($validation)){
                                    if($validation->status == 'Aceptado')
                                        $email->d_requiridos .= '<a class="waves-effect waves-light tooltipped" data-position="bottom" data-tooltip="'.$type->name.'" onclick="add_file(`'.$email->invoices_id.'`, `'.$email->id.'`, true)"><i class="material-icons text-green green-text">check</i></a>';
                                    if($validation->status == 'Rechazado')
                                    $email->d_requiridos .= '<a class="waves-effect waves-light  modal-trigger tooltipped" data-position="bottom" data-tooltip="'.$type->name.'<br>Archivo rechazado" onclick="add_file(`'.$email->invoices_id.'`, `'.$email->id.'`, false)"><i class="material-icons text-green red-text">error_outline</i></a>';
                                    if($validation->status == 'Pendiente')
                                        $email->d_requiridos .= '<a class="waves-effect waves-light tooltipped" data-position="bottom" data-tooltip="'.$type->name.'" onclick="file_pendiente(`'.$validation->id.'`,`'.$email->id.'`, `'.$status.'`)"><i class="material-icons text-green orange-text">priority_high</i></a>';
                                }else
                                    $email->d_requiridos .= '<a class="waves-effect waves-light  modal-trigger tooltipped" data-position="bottom" data-tooltip="'.$type->name.'" onclick="add_file(`'.$email->invoices_id.'`, `'.$email->id.'`, false)"><i class="material-icons text-green red-text">close</i></a>';
                            break;
                    }
                }
            }else{
                $email->dian = '';
            }
            if(!($email->type_documents_id_invoices == 112 || $email->type_documents_id_invoices == 113)){
                $date = date("Y-m-d H:i:s",strtotime($email->created_invoice.'+ 3 days'));
                $date_created = new DateTime($date);
                $now = new DateTime(date("Y-m-d H:i:s"));
                $diff = $now->diff($date_created);
                if($diff->days == 2 && $diff->invert == 0)
                    $email->vence = $diff->days.'D <i class="material-icons text-green green-text tiny">brightness_1</i>';
                elseif($diff->days == 1 && $diff->invert == 0)
                        $email->vence = $diff->days.'D <i class="material-icons text-green orange-text tiny">brightness_1</i>';
                elseif($diff->days == 0 && $diff->invert == 0) $email->vence = $diff->h.'H <i class="material-icons text-green yellow-text tiny">brightness_1</i>';
                elseif($diff->invert == 1) $email->vence = 'Fecha <br> vencida';
            }else $email->vence = 'No aplica';
            // $email->action =  $status == 'todos' && $email->status_id != 19 ? '<span class="my-badge badge blue lighten-4 blue-text mr-5">'.$email->status_name.'</span>':'';
            if(($email->status_id == 19 || $email->status_id == 22) && (!empty($email->id_doc_status))){
                $color_option = $email->status_id == 22 ? 'orange' : 'deep-purple';
                $email->action = "<a class='dropdown-trigger $color_option-text' href='#!' data-target='drop_$email->id-$status'><i class='material-icons'>more_vert</i></a>";
                if($email->id_doc_status == 2){
                    $email->action.='
                        <ul id="drop_'.$email->id.'-'.$status.'" class="dropdown-content">
                            <li><a href="#!" onclick="product('.$email->id_document.', `'.$status.'`)" class="green-text"><i class="material-icons">assignment</i>Asociar Producto/Servicio</a></li>
                            <li><a href="'.base_url(['shopping', 'history', $email->id, 1]).'" class="deep-purple-text "><i class="material-icons">email</i> Ver</a></li>
                        </ul>';
                }else{
                    $email->action.='
                        <ul id="drop_'.$email->id.'-'.$status.'" class="dropdown-content">
                            <li><a href="#!" onclick="aceppt('.$email->invoices_id.', 1, `'.$status.'`)" class="green-text"><i class="material-icons">check</i> Aceptar</a></li>
                            <li><a href="#!" onclick="aceppt('.$email->invoices_id.', 0, `'.$status.'`)" class="red-text "><i class="material-icons">close</i> Rechazar</a></li>
                            <li><a href="'.base_url(['shopping', 'history', $email->id, 1]).'" class="deep-purple-text "><i class="material-icons">email</i> Ver</a></li>
                        </ul>';
                }
            }else{
                if((($email->type_documents_id_invoices == 112 || $email->type_documents_id_invoices == 113) && !empty($email->resolution)) || $email->status_id != 19){
                    $email->action = '<a href="'.base_url(['shopping', 'history', $email->id, 1]).'" class="deep-purple-text';
                    if($status == 'todos') $email->action .=' tooltipped" data-position="bottom" data-tooltip="'.$email->status_description;
                    $email->action .='"><i class="material-icons">email</i></a>';
                }else{
                    $email->action = "<a class='dropdown-trigger deep-purple-text' href='#!' data-target='drop_res_$email->id-$status'><i class='material-icons'>more_vert</i></a>";
                    $email->action.='
                        <ul id="drop_res_'.$email->id.'-'.$status.'" class="dropdown-content">
                            <li><a href="#!" onclick="asignar(`'.$email->invoices_id.'`,`'.$status.'`)" class="green-text"><i class="material-icons">add</i> Asignar</a></li>
                            <li><a href="'.base_url(['shopping', 'history', $email->id, 1]).'" class="deep-purple-text "><i class="material-icons">email</i> Ver</a></li>
                        </ul>';
                    // $email->action = '<a href="'.base_url(['shopping', 'history', $email->id]).'" class="deep-purple-text"><i class="material-icons">email</i></a>';
                }
            }
        }
        $invoiceStatus  = new InvoiceStatus();
        $statusI = $invoiceStatus->where(['block' => 'SC'])->select([
            'invoice_status.*',
            '(select count(*) from invoices
            INNER JOIN `shopping_emails` ON shopping_emails.invoices_id = invoices.id
            where (invoice_status.id = invoices.invoice_status_id and shopping_emails.companies_id = '.Auth::querys()->companies_id.')
        ) AS total',])->asObject()->get()->getResult();
        
        $invoiceM       = new Invoice();
        $invoices = $invoiceM
            ->select('id,resolution, concat(prefix,"-", resolution) as name')
            ->where(['resolution !=' => null, 'type_documents_id >' => 100, 'companies_id' => Auth::querys()->companies_id])
            ->whereNotIn('type_documents_id', [112,113])
            ->orderBy('resolution', 'DESC')
        ->asObject()->get()->getResult();
        return json_encode(['tables' => $emails, 'indicadores' => $statusI,'invoices' => $invoices, 'type_document' => $type_documents]);
    }

    public function history($id, $module){
        $emailController    = new ShoppingEmail();
        $emailFilesModel    = new ShoppingFiles();
        $emailHistory       = new HistoryEmails();
        $invoiceFM          = new InvoiceFiles();
        $invoiceTFM         = new InvoiceTypeFiles();
        $model              = new Company();
        $company            = $model->where(['id' => Auth::querys()->companies_id])
            ->asObject()
        ->first();
        $mail = $emailController
          ->select([
              'shopping_emails.*',
              'invoices.uuid',
              'invoices.resolution',
              'invoices.prefix',
              'invoices.payment_due_date',
              'invoices.id as invoices_id',
              'invoices.type_documents_id as type_documents_id_invoices',
              'invoices.payable_amount as valor',
              'type_documents.name as type_documents_name',
              'customers.id as customer_id',
              'customers.name as customer_name',
              'customers.identification_number as customer_identification',
              'companies.company as company_name',
              'companies.identification_number',
          ])
          ->join('invoices','invoices.id = shopping_emails.invoices_id','left')
          ->join('type_documents', 'type_documents.id = invoices.type_documents_id', 'left')
          ->join('customers', 'invoices.customers_id = customers.id', 'left')
          ->join('companies', 'companies.id = invoices.companies_id', 'left')
          ->where(['shopping_emails.id' => $id])
          ->asObject()
        ->first();
        $files = $emailFilesModel->where(['shopping_email_id' => $mail->id])->asObject()->get()->getResult();
        $historys = $emailHistory
            ->select(['history_emails.*', 'users.name as name'])
            ->join('users', 'users.id = history_emails.users_id')
            ->where(['history_emails.shopping_emails_id' => $mail->id, 'file' => 'false'])
            ->orderBy('history_emails.id', 'DESC')
        ->asObject()->get()->getResult();
        $invoicesFiles = $invoiceFM->where(['invoices_id' => $mail->invoices_id])->asObject()->get()->getResult();
        $types = $invoiceTFM->where(['block' => 'SC'])->asObject()->get()->getResult();
        if($module == 1){
            $module = (object) [
                'id' => 1,
                'name' => 'shopping'
            ];
        }else if($module == 2){
            $module = (object) [
                'id' => 2,
                'name' => 'providers'
            ];
        }else{
            $module = (object) [
                'id' => 0,
                'name' => 'not_found'
            ];
        }
        return view('shopping/history', [
            'mail'          => $mail,
            'files'         => $files,
            'historys'      => $historys,
            'invoices_file' => $invoicesFiles,
            'types'         => $types,
            'company'       => $company,
            'module'        => $module
        ]);
    }

    public function update(){
        $id_invoice = $this->request->getPost('id_invoice');
        $value = (bool) $this->request->getPost('accept');
        $table = $this->request->getPost('table');
        // return json_encode([$table]);
        $invoiceController =  new Invoice();
        $emailM = new ShoppingEmail();
        $invoice = $invoiceController->select([
            'invoices.invoice_status_id',
            'invoices.resolution',
            'invoices.id'
            ])->where(['id' => $id_invoice])
        ->asObject()->first();
        if(!empty($invoice)){
            $email = $emailM->where(['invoices_id' => $id_invoice])->asObject()->first();
            if($value){
                $data = [
                    'invoice_status_id' => 21  //Aceptadas
                ];
                $moduleSM = new ModuleShopping();
                $historyM = new HistoryEmails();
                $dataHistory = [
                    'shopping_emails_id' => $email->id,
                    'users_id' => session('user')->id,
                    'observation' => 'Documento aceptado'
                ];
                $moduleSM->save(['section_shopping_id' => 2, 'status_shopping_id' => 1, 'invoices_id' => $id_invoice]);
                $historyM->save($dataHistory);
                $invoiceController->set($data)->where(['resolution' => $invoice->resolution, 'type_documents_id >' => 100])->update();
                return json_encode(['table' => $table]);
            }else{
                $type = $this->request->getPost('type');
                $observation = $this->request->getPost('observation');
                if($type != 0){
                    $data = [
                        'invoice_status_id' => 20 // Rechazadas
                    ];
                    $historyM = new HistoryEmails();
                    $dataHistory = [
                        'shopping_emails_id' => $email->id,
                        'users_id' => session('user')->id,
                        'observation' => !empty($observation) ? $observation : 'Documento rechazado'
                    ];
                    $message = 'Documento rechazado';
                }else{
                    $data = [
                        'invoice_status_id' => 22 // Pendiente
                    ];
                    $historyM = new HistoryEmails();
                    $dataHistory = [
                        'shopping_emails_id' => $email->id,
                        'users_id' => session('user')->id,
                        'observation' => !empty($observation) ? $observation : 'Documento pendiente'
                    ];
                    $message = 'Documento pendiente';
                }
                $historyM->save($dataHistory);
                $invoiceController->set($data)->where(['resolution' => $invoice->resolution, 'type_documents_id >' => 100])->update();
                return json_encode([
                    'table' => $table,
                    'message' => $message,
                ]);
            }
        }else{
          http_response_code(404);
          echo  json_encode([
              'status' => 404,
              'Message' => 'Not found'
          ], 404);
          die();
        }
    }

    public function file(){
        $data = $this->request->getPost();
        $file = $this->request->getFile('file');
        $url = $this->request->getPost('url');
        // var_dump($data);die();
        $invoicesFM = new InvoiceFiles();
        $historyM = new HistoryEmails();
        $invoiceTFM = new InvoiceTypeFiles();
        $model = new Company();
        $company = $model->where(['id' => Auth::querys()->companies_id])
            ->asObject()
        ->first();

        if(!empty($data['update'])){
            $file = $invoicesFM
                ->select(['invoices_files.*', 'invoices_type_files.name as name_type'])
                ->join('invoices_type_files', 'invoices_type_files.id = invoices_files.invoices_type_files_id')
                ->where(['invoices_files.id' => $data['id']])
            ->asObject()->first();
            if(!empty($file)){
                $invoicesFM->set(['status' => $data['status']])->where(['id' => $file->id])->update();
                $observation = 'Documento "'.$file->name_type.'" '.$data['status'];
                $historyM->save([
                    'observation' => $observation,
                    'shopping_emails_id' => $data['email_id'],
                    'users_id' => session('user')->id,
                ]);
                return json_encode(['update' => true]);
            }else{
                return json_encode(['update' => false]);
            }
        }
        
        $type = $invoiceTFM->where(['id' => $data['type']])->asObject()->first();
        if(!empty($file->getName())){
            if (!is_dir('invoices_files/')) {
                mkdir('invoices_files/', 0777);
            }
            if (!is_dir('invoices_files/'.$company->identification_number)) {
                mkdir('invoices_files/'.$company->identification_number, 0777);
            }
            $file->move('invoices_files/'.$company->identification_number, $file->getName());
            $dataFile = ['name' => $file->getName()];
        }
        else $dataFile = ['name' => null];
        if($data['exist'] == 'false'){ // Creamos
            $dataFile = [
                'invoices_id' => $data['invoices_id'],
                'invoices_type_files_id' => $data['type'],
                'name' => $dataFile['name'],
                'observation' => $data['observation'],
                'number' => $data['numero'],
                'status' => !empty($data['status']) ?  $data['status'] : 'Pendiente',
                'users_id' => session('user')->id
            ];
            $invoicesFM->save($dataFile);
            $observation = "Cargue del documento requerido '$type->name'";
            $dataHistory = [
                'shopping_emails_id' => $data['shopping_id'],
                'users_id' => session('user')->id,
                'observation' => $observation
            ];
            $historyM->save($dataHistory); // En la tabla history_email no se borran ni actualizan
            $dataHistory = [
                'shopping_emails_id' => $data['shopping_id'],
                'users_id' => session('user')->id,
                'observation' => $data['numero'].' - '.$data['observation'],
                'file' => 'true'
            ];
            $historyM->save($dataHistory); // En la tabla history_email no se borran ni actualizan
            return redirect()->to(base_url($url))->with('success', 'Archivo cargado con exito');
        }else{
            $dataFile = [
                'name' => $dataFile['name'],
                'status' => !empty($data['status']) ?  $data['status'] : 'Pendiente',
                'updated_at' => date('Y-m-d H:i:s'),
                'observation' => $data['observation'],
                'number' => $data['numero'],
                'users_id' => session('user')->id
            ];
            $invoicesFM->set($dataFile)->where(['invoices_id' => $data['invoices_id'], 'invoices_type_files_id ' => $data['type']])->update();
            $observation = "Actualización del documento requerido '$type->name'";
            $dataHistory = [
                'shopping_emails_id' => $data['shopping_id'],
                'users_id' => session('user')->id,
                'observation' => $observation
            ];
            $historyM->save($dataHistory); // En la tabla history_email no se borran ni actualizan
            $dataHistory = [
                'shopping_emails_id' => $data['shopping_id'],
                'users_id' => session('user')->id,
                'observation' => $data['numero'].' - '.$data['observation'],
                'file' => 'true'
            ];
            $historyM->save($dataHistory); // En la tabla history_email no se borran ni actualizan
            return redirect()->to($url)->with('success', 'Archivo actualizado con exito');
        }

    }

    public function table_history($id_shooping){
        $emailHistory       = new HistoryEmails();
        $historys = $emailHistory
            ->select(['history_emails.*', 'users.name as name'])
            ->join('users', 'users.id = history_emails.users_id')
            ->where(['history_emails.shopping_emails_id' => $id_shooping, 'file' => 'true'])
            ->orderBy('history_emails.id', 'DESC')
        ->asObject()->get()->getResult();
        foreach($historys as $key => $history){
            $history->id = ($key + 1);
        }
        return json_encode($historys);

    }

    public function assign(){
        $id_invoiceA    = $this->request->getPost('id');
        $id_invoice     = $this->request->getPost('id_invoice');
        $invoiceM       = new Invoice();
        $historyM       = new HistoryEmails();
        $emailM         = new ShoppingEmail();
        $invoiceStatus  = new InvoiceStatus();
        $email_actual   = $emailM->where(['invoices_id' => $id_invoiceA])->select('id, subject')->asObject()->first();
        $email_asigado  = $emailM->where(['invoices_id' => $id_invoice])->select('id')->asObject()->first();
        $invoice        = $invoiceM->where(['id' => $id_invoice])->asObject()->first();
        $invoiceM->set([
            'resolution' => $invoice->resolution,
            'invoice_status_id' => $invoice->invoice_status_id,
            'type_documents_id' => 113
        ])->where(['id' => $id_invoiceA])->update();
        $data_history = [
            'shopping_emails_id' => $email_actual->id,
            'users_id' => session('user')->id,
            'observation' => 'Email asignado al documento "'.$invoice->prefix.'-'.$invoice->resolution.'"'
        ];
        $historyM->save($data_history);
        $data_history['shopping_emails_id'] = $email_asigado->id;
        $data_history['observation'] = 'Asignacion del email "'.$email_actual->subject.'"';
        $historyM->save($data_history);
        $statusI = $invoiceStatus->where(['block' => 'SC'])->select(['invoice_status.*','(select count(*) from invoices where (invoice_status.id = invoices.invoice_status_id)) AS total',])->asObject()->get()->getResult();
        return json_encode([$id_invoiceA, $email_asigado, 'indicadores' => $statusI]);
    }

    public function download($name, $type){
        $companyM = new Company();
        $company = $companyM->where(['id' => Auth::querys()->companies_id])->asObject()->first();
        // var_dump($company->identification_number); die();
        $ruta = strstr($name, '.zip') ? WRITEPATH . '/uploads/document_reception/'.$company->identification_number.'/zip/'.$name:WRITEPATH . '/emails/'.$company->identification_number.'/'.$name;
        if(file_exists($ruta)){
            # Algunos encabezados que son justamente los que fuerzan la descarga
            header('Content-Type: application/octet-stream');
            header("Content-Transfer-Encoding: Binary");
            header("Content-disposition: attachment; filename=$name");
            # Leer el archivo y sacarlo al navegador
            readfile($ruta);
            # No recomiendo imprimir más cosas después de esto
        }else{
            http_response_code(404);
            echo  json_encode([
                'status' => 404,
                'Message' => 'Not found'
            ], 404);
            die();
        }
    }

    public function product($id_document, $status){
        $invoice = new Document();
        $invoices = $invoice
            ->asObject()
            ->find($id_document);
        $lineInvoice = new LineInvoice();
        $lineInvoices = $lineInvoice
            ->select(['line_invoices.*' , 'invoices.resolution'])
            ->where(['line_invoices.invoices_id' => $invoices->invoice_id])
            ->join('invoices', 'invoices.id = line_invoices.invoices_id')
            ->asObject()
            ->get()
            ->getResult();
        foreach ($lineInvoices as $key => $product) {
            $product->price_amount = '$ '. number_format($product->price_amount, '2', ',', '.');
            $product->line_extension_amount = '$ '. number_format($product->line_extension_amount, '2', ',', '.');
            $product->quantity = number_format($product->quantity, '2', ',', '.');
            if($product->upload != 'Cargado' && $product->upload != 'Sin Referencia'){
                $product->action = "<a class='dropdown-trigger deep-purple-text' href='#!' data-target='drop_$product->id-$id_document-$status'><i class='material-icons'>more_vert</i></a>";
                $product->action .= '
                <ul id="drop_'.$product->id.'-'.$id_document.'-'.$status.'" class="dropdown-content">
                    <li><a href="#!" onclick="add_product('.$product->id.', '.$id_document.', `'.strip_tags($product->description).'`, `'.$status.'`)" class="green-text"><i class="material-icons">shopping_cart</i> Asociar producto</a></li>
                    <li><a href="#!" onclick="created_product('.$product->id.', '.$id_document.', `'.strip_tags($product->description).'`, `'.$status.'`)" class="orange-text"><i class="material-icons">add_shopping_cart</i> Asociar producto nuevo</a></li>
                    <li><a href="#!" onclick="not_reference('.$product->id.', '.$id_document.', `'.strip_tags($product->description).'`, `'.$status.'`)" class="red-text "><i class="material-icons">remove_shopping_cart</i> No asociarle producto</a></li>
                </ul>';
            }else{
                $product->action = "Sin acciones";
            }

            if($product->upload == 'Cargado' || $product->upload ==  'Sin Referencia'){
                $product->upload = '<span class="new badge tooltipped " data-position="top" data-badge-caption="" data-tooltip="Producto Asociado correctamente">
                        '.$product->upload.'
                    </span>';
            }else{
                $product->upload = '<span class="new badge tooltipped orange lighten-5 orange-text" data-position="top" data-badge-caption="" data-tooltip="Producto sin asociar">
                    Sin cargar
                </span>';
            }
        }
        return json_encode($lineInvoices);
    }
}