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
use App\Models\InvoiceStatus;
use App\Models\LineInvoice;
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

class ProvidersController extends BaseController
{

    public function index()
    {



        // $emailController = new ShoppingEmail();
        // $invoicesTFM = new InvoiceTypeFiles();
        // $invoicestypeFiles = $invoicesTFM->where(['block' => 'SC'])->asObject()->get()->getResult();
        // $emails = $emailController
        //     ->select([
        //         // 'shopping_emails.*',
        //         'invoices.uuid',
        //         'invoices.resolution',
        //         'invoices.prefix',
        //         'invoices.payment_due_date',
        //         'invoices.id as invoices_id',
        //         'invoices.type_documents_id as type_documents_id_invoices',
        //         'invoices.invoice_status_id as status_id',
        //         'invoices.payable_amount as valor',
        //         'invoice_status.name as status_name',
        //         'invoice_status.description as status_description',
        //         'type_documents.name as type_documents_name',
        //     ])
        //     ->join('invoices','invoices.id = shopping_emails.invoices_id','left')
        //     ->join('type_documents', 'type_documents.id = invoices.type_documents_id', 'left')
        //     ->join('invoice_status', 'invoice_status.id = invoices.invoice_status_id')
        //     ->where(['shopping_emails.companies_id' => Auth::querys()->companies_id])
        //     ->whereNotIn('type_documents_id', [112,113]);
        //     // ->orderBy('id', 'desc');
        //     // if($status != 'todos'){
        //     //     if($status != 19) $emails = $emails->where(['invoice_status_id' => $status]);
        //     //     else if($status == 21){
        //         $emails = $emails
        //             ->select(['module_shopping.*', 'status_shopping.name as name_status_shop'])
        //             ->join('module_shopping', 'module_shopping.invoices_id = invoices.id', 'left')
        //             ->join('status_shopping', 'module_shopping.status_shopping_id = status_shopping.id', 'left')
        //         // ->where(['module_shopping.section_shopping_id' => 2]);
        //         ->where(['invoice_status_id' => 21]);
        // //     } 
        // //     else $emails = $emails->whereIn('invoice_status_id', [$status, 22]);
        // // }
        // $emails = $emails->get()->getResult();

        // var_dump($emails);die;



        $invoiceM = new Invoice();
        $invoiceStatus  = new InvoiceStatus();
        $statusT = $invoiceStatus->where(['block' => 'SC'])->whereNotIn('name', ['Pendientes'])->asObject()->get()->getResult();
        $emailController = new ShoppingEmail();
        $status = $emailController
            ->select([
                'invoice_status_id',
                'count(*) as total',
                '(select invoice_status.name from invoice_status where (invoice_status.id = invoices.invoice_status_id)) AS name',
                '(select Sum(payable_amount) from invoice_status where (invoice_status.id = invoices.invoice_status_id)) AS suma',
            ])
            ->join('invoices', 'shopping_emails.invoices_id = invoices.id')
            ->where(['type_documents_id >' => 100, 'shopping_emails.companies_id' => Auth::querys()->companies_id])
            ->whereNotIn('type_documents_id', [112,113])
            ->groupBy(['invoice_status_id'])
            ->orderBy('invoice_status_id', 'ASC')
        ->asobject()->get()->getResult();
        foreach ($status as $key => $statu) {
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
        $data = (object) [
            'id' => 'todos',
            'name' => 'Todas',
        ];
        array_push($statusT, $data);
        return view('provider/portal_proveedores', [
            'status' => $status,
            'estados' =>$statusT
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
                'invoices.prefix',
                'invoices.payment_due_date',
                'invoices.id as invoices_id',
                'invoices.type_documents_id as type_documents_id_invoices',
                'invoices.invoice_status_id as status_id',
                'invoices.payable_amount as valor',
                'invoice_status.name as status_name',
                'invoice_status.description as status_description',
                'type_documents.name as type_documents_name',
            ])
            ->join('invoices','invoices.id = shopping_emails.invoices_id','left')
            ->join('type_documents', 'type_documents.id = invoices.type_documents_id', 'left')
            ->join('invoice_status', 'invoice_status.id = invoices.invoice_status_id')
            ->where(['shopping_emails.companies_id' => Auth::querys()->companies_id])
            ->whereNotIn('type_documents_id', [112,113])
        ->orderBy('id', 'desc');
        if($status != 'todos'){
            if($status != 19 && $status != 21) $emails = $emails->where(['invoice_status_id' => $status]);
            else if($status == 21){
                $emails = $emails
                    ->select(['status_shopping.name as name_section_status'])
                    ->join('module_shopping', 'module_shopping.invoices_id = invoices.id', 'left')
                    ->join('status_shopping', 'module_shopping.status_shopping_id = status_shopping.id', 'left')
                    ->where(['module_shopping.section_shopping_id' => 2, 'invoice_status_id' => $status]);
            } 
            else $emails = $emails->whereIn('invoice_status_id', [$status, 22]);
        }
        $emails = $emails->get()->getResult();
        foreach ($emails as $key => $email) {
            $email->numero = ($key + 1);
            $email->files = $emailController->Files($email->invoices_id);
            $email->d_requiridos = '';
            $email->valor = '$'.number_format($email->valor, 0, ',', '.');
            $email->dian = !empty($email->uuid) ? '<i class="material-icons text-green green-text">check</i>' : '<i class="material-icons text-green red-text">close</i>';
            if($status != 21){
                if($email->status_id == 19) $color = 'blue lighten-5 indigo-text';
                else if($email->status_id == 20) $color = 'red lighten-5 red-text';
                else if($email->status_id == 21) $color = 'green lighten-5 green-text';
                else if($email->status_id == 22) $color = 'orange lighten-5 orange-text';
                $estado = $email->status_description;
            }else{
                if($email->name_section_status == 'Pagada') $color = 'green lighten-5 green-text';
                else if($email->name_section_status == 'Tesorería') $color = 'blue lighten-5 blue-text';
                else $color = 'purple lighten-5 purple-text';
                $estado = $email->name_section_status;
            }
            $email->status_name = '<span class="new badge '.$color.'  gradient-shadow" data-badge-caption="'.$estado.'"></span>';
            if(($email->type_documents_id_invoices != 112 && $email->type_documents_id_invoices != 113) && ($status == 19 || $status == 'todos') ){
                foreach($invoicestypeFiles as $type){
                    $validation = false;
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

            if(!empty($email->payment_due_date)){
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
                } else  $email->vence = $diff->days >= 1 ? $diff->format('%d').'D <i class="material-icons text-black black-text tiny">brightness_1</i>' : $diff->format('%h').'H <i class="material-icons text-red red-text tiny">brightness_1</i>';                ;
            }else{
                $email->vence = 'No aplica';
                $email->payment_due_date = 'No aplica';
            }
            $email->fecha_radicado = date('Y-m-d');
            $email->fecha_aux = date('Y-m-d');
            $email->action = '<a href="'.base_url(['shopping', 'history', $email->id, 2]).'" class="deep-purple-text"><i class="material-icons">email</i></a>';
            if($email->status_id == 21)$email->action .= '<a href="#!" onclick="retenciones(`'.$email->invoices_id.'`)" class=" modal-trigger light-blue-text"><i class="material-icons icon-small">monetization_on</i></a>';
            // if($status->)
            // if($email->status_id == 19 && ($email->type_documents_id_invoices != 112 && $email->type_documents_id_invoices != 113)){
            //     $email->action = "<a class='dropdown-trigger deep-purple-text' href='#!' data-target='drop_$email->id-$status'><i class='material-icons'>more_vert</i></a>";
            //     $email->action.='
            //         <ul id="drop_'.$email->id.'-'.$status.'" class="dropdown-content">
            //             <li><a href="#!" onclick="aceppt('.$email->invoices_id.', 1, `'.$status.'`)" class="green-text"><i class="material-icons">check</i> Aceptar</a></li>
            //             <li><a href="#!" onclick="aceppt('.$email->invoices_id.', 0, `'.$status.'`)" class="red-text "><i class="material-icons">close</i> Rechazar</a></li>
            //             <li><a href="'.base_url(['shopping', 'history', $email->id]).'" class="deep-purple-text "><i class="material-icons">email</i> Ver</a></li>
            //         </ul>';
            // }else{
            //     if((($email->type_documents_id_invoices == 112 || $email->type_documents_id_invoices == 113) && !empty($email->resolution)) || $email->status_id != 19){
            //         $email->action = '<a href="'.base_url(['shopping', 'history', $email->id]).'" class="deep-purple-text';
            //         if($status == 'todos') $email->action .=' tooltipped" data-position="bottom" data-tooltip="'.$email->status_description;
            //         $email->action .='"><i class="material-icons">email</i></a>';
            //     }else{
            //         $email->action = "<a class='dropdown-trigger deep-purple-text' href='#!' data-target='drop_res_$email->id-$status'><i class='material-icons'>more_vert</i></a>";
            //         $email->action.='
            //             <ul id="drop_res_'.$email->id.'-'.$status.'" class="dropdown-content">
            //                 <li><a href="#!" onclick="asignar(`'.$email->invoices_id.'`,`'.$status.'`)" class="green-text"><i class="material-icons">add</i> Asignar</a></li>
            //                 <li><a href="'.base_url(['shopping', 'history', $email->id]).'" class="deep-purple-text "><i class="material-icons">email</i> Ver</a></li>
            //             </ul>';
            //         // $email->action = '<a href="'.base_url(['shopping', 'history', $email->id]).'" class="deep-purple-text"><i class="material-icons">email</i></a>';
            //     }
            // }
        }
        return json_encode($emails);
    }

    public function table_taxes($id_invoice){
        $lineInvoiceM = new LineInvoice();
        $lines = $lineInvoiceM
            ->where(['invoices_id' => $id_invoice])
        ->asObject()->get()->getResult();
        $data =  [];
        foreach ($lines as $key => $line) {
            $line->rentas = $lineInvoiceM->Rentas($line->id);
            foreach ($line->rentas as $key => $renta) {
                switch ($renta->taxes_id) {
                    case 1:
                        if(empty($data[$renta->name]['valor'])) $data[$renta->name]['valor'] = 0;
                        $data[$renta->name]['valor'] = $data[$renta->name]['valor'] + $renta->tax_amount;
                        $data[$renta->name]['name'] = $renta->name;
                        break;
                    case 6:
                        if(empty($data[$renta->name]['valor'])) $data[$renta->name]['valor'] = 0;
                        $data[$renta->name]['valor'] = $data[$renta->name]['valor'] + $renta->tax_amount;
                        $data[$renta->name]['name'] = $renta->name;
                        break;
                    case 7:
                        if(empty($data[$renta->name]['valor'])) $data[$renta->name]['valor'] = 0;
                        $data[$renta->name]['valor'] = $data[$renta->name]['valor'] + $renta->tax_amount;
                        $data[$renta->name]['name'] = $renta->name;
                        break;
                }
            }

        }
        $data_aux = [];
        foreach ($data as $key => $value) {
            $object = (object) [
                'name' => $value['name'],
                'valor' => '$'.number_format($value['valor'], 0, ',', '.'),
            ];
            array_push($data_aux, $object);
        }

        return json_encode($data_aux);

    }

}