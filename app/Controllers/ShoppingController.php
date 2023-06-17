<?php

namespace App\Controllers;


use PhpImap\Mailbox;
use App\Models\ConfigurationMail;
use PhpImap\Exceptions\ConnectionException;
use App\Models\Document;
use App\Models\ShoppingEmail;
use App\Models\Invoice;
use App\Models\InvoiceStatus;
use App\Models\Companies;
use App\Models\AssociateDocument;
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


    public function index() 
    {
        // $emailController = new ShoppingEmail();
        // $emails = $emailController
        //     ->select([
        //         'shopping_emails.*',
        //         'invoices.uuid',
        //         'invoices.resolution',
        //         'invoices.prefix',
        //         'invoices.payment_due_date',
        //         'invoices.id as invoices_id',
        //         'invoices.type_documents_id as type_documents_id_invoices',
        //         'type_documents.name as type_documents_name',
        //         'customers.id as customer_id',
        //         'customers.name as customer_name',
        //         'companies.company as company_name',
        //         'companies.identification_number',
        //     ])
        //     ->join('invoices','invoices.id = shopping_emails.invoices_id','left')
        //     ->join('type_documents', 'type_documents.id = invoices.type_documents_id', 'left')
        //     ->join('customers', 'invoices.customers_id = customers.id', 'left')
        //     ->join('companies', 'companies.id = invoices.companies_id', 'left')
        //     ->where(['shopping_emails.companies_id' => Auth::querys()->companies_id])
        //     ->orderBy('id', 'desc')
        //     ->get()->getResult();
        // return var_dump($emails);
        $invoiceStatus = new InvoiceStatus();
        $status = $invoiceStatus->where(['block' => 'SC'])->asObject()->get()->getResult();
        $data = (object) [
            'id' => 'todos',
            'name' => 'Todas',
        ];
        array_push($status, $data);
        return view('shopping/index', [
            'estados' => $status
        ]);
    }

    public function tables($status){
        $emailController = new ShoppingEmail();
        $emails = $emailController
            ->select([
                'shopping_emails.*',
                'invoices.uuid',
                'invoices.resolution',
                'invoices.prefix',
                'invoices.payment_due_date',
                'invoices.id as invoices_id',
                'invoices.type_documents_id as type_documents_id_invoices',
                'invoices.invoice_status_id as status_id',
                'type_documents.name as type_documents_name',
                'customers.id as customer_id',
                'customers.name as customer_name',
                'companies.company as company_name',
                'companies.identification_number',
            ])
            ->join('invoices','invoices.id = shopping_emails.invoices_id','left')
            ->join('type_documents', 'type_documents.id = invoices.type_documents_id', 'left')
            ->join('customers', 'invoices.customers_id = customers.id', 'left')
            ->join('companies', 'companies.id = invoices.companies_id', 'left')
            ->where(['shopping_emails.companies_id' => Auth::querys()->companies_id])
            ->orderBy('id', 'desc');
            if($status != 'todos'){
                $emails = $emails->where(['invoice_status_id' => $status]);
            }
        $emails = $emails->get()->getResult();
        foreach ($emails as $key => $email) {
            $email->customer_name = !empty($email->customer_name) ? $email->customer_name : $email->name;
            $email->almacen = '<a class="waves-effect waves-light  modal-trigger" href="#modal1"><i class="material-icons text-green red-text">close</i></a>';
            $email->oc = '<a class="waves-effect waves-light  modal-trigger" href="#modal1"><i class="material-icons text-green red-text">close</i></a>';
            $email->remision = '<a class="waves-effect waves-light  modal-trigger" href="#modal1"><i class="material-icons text-green red-text">close</i></a>';
            $email->dian = !empty($email->uuid) ? '<i class="material-icons text-green green-text">check</i>' : '<i class="material-icons text-green red-text">close</i>';
            $fecha_now = new DateTime(date('Y-m-d H:i:s'));
            $fecha_payment = new DateTime($email->payment_due_date.' 23:59:59');
            $diff = $fecha_now->diff($fecha_payment);
            if($diff->days >= 1 && $diff->invert == 0)
                $email->vence = $diff->format('%d').'D <i class="material-icons text-green green-text tiny">brightness_1</i>';
            elseif($diff->days == 0 && $diff->invert == 0){
                if($diff->format('%H') >= 12)
                    $email->vence = $diff->format('%H').'H <i class="material-icons text-green green-text tiny">brightness_1</i>';
                elseif($diff->format('%H') <= 11 && $diff->format('%H') >= 4)
                    $email->vence = $diff->format('%H').'H <i class="material-icons text-yellow yellow-text tiny">brightness_1</i>';
                else
                    if($diff->format('%H') > 0)
                        $email->vence = $diff->format('%H').'H <i class="material-icons text-red red-text tiny">brightness_1</i>';
                    else
                        $email->vence = $diff->format('%i').'M <i class="material-icons text-red red-text tiny">brightness_1</i>';
            } else $email->vence = 'Fecha vencida';
            $email->action = '
                <div class="btn-group">
                    <button onclick="aceppt('.$email->invoices_id.', true)" class="btn green btn-small modal-trigger"><i class="material-icons">check</i></button>
                    <button onclick="aceppt('.$email->invoices_id.', false)" class="btn red btn-small  modal-trigger"><i class="material-icons">close</i></button>
                    <a href="'.base_url(['history', $email->id]).'" class="btn indigo btn-small"><i class="material-icons">add</i></a>    
                </div>';
        }
        return json_encode($emails);
    }

    public function history($id){
        $emailController = new ShoppingEmail();
        $mail = $emailController
            ->select([
                'shopping_emails.*',
                'invoices.uuid',
                'invoices.resolution',
                'invoices.prefix',
                'invoices.payment_due_date',
                'invoices.id as invoices_id',
                'invoices.type_documents_id as type_documents_id_invoices',
                'type_documents.name as type_documents_name',
                'customers.id as customer_id',
                'customers.name as customer_name',
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
        return view('shopping/history', [
            'mail' => $mail
        ]);
    }

    public function update(){
        $hola = ['hola'];
        return json_encode($hola);
    }
}