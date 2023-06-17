<?php

namespace  App\Traits;

use App\Controllers\Api\Auth;
use App\Models\Invoice;
use App\Models\Subscription;
/**
 * IPlanetColombia
 * @date 10/05/2022
 * @author Wilson Andres Bachiller Ortiz
 * Trait encargado de los indicadores de la cantidad 
 * de documentos.
 */

trait SubscriptionTrait
{

     /**
      * @var Array[string] $typeDocumentIds
      * Tipo de documentos en base  de datos
      */

    private $typeDocumentIds = ['1', '2','3', '4', '5', '9', '10', '101', '102','103','104','105','106','107','108'];

    
     /**
      * @var Array[string] $documentsStatusIds
      * Estados de los documentos en base de datos
      */

    private $documentsStatusIds = [ '2', '3', '4', '7', '10', '14'];

    /**
     * Cantidad de documentos generados por el sistema
     * @return int 
     * 
     */
    private function  documents(string $startDate , string $endDate, $id = null): int
    {
        $model = new Invoice();
        return  $model
        ->where([
            'invoices.companies_id'     => $id ?? Auth::querys()->companies_id, 
            'invoices.created_at >='    => $startDate.' 00:00:00',  
            'invoices.created_at <='    => $endDate.' 23:59:59'
        ])
        ->whereIn('invoices.type_documents_id', $this->typeDocumentIds)
        ->whereIn('invoices.invoice_status_id', $this->documentsStatusIds)
        ->countAllResults();
    }

    /**
     * Cantidad de documentos de facturación
     * @var string $startDate fecha de inicio de la subcripción
     * @var string $endDate fecha final de la subcripcion
     * @return int cantidad de facturas de venta, exportacion y contingencia.
     */

    private function invoice(string $startDate , string $endDate, $id = null): int
    {
        $model = new Invoice();
        return  $model
        ->where([
            'invoices.companies_id'     => $id ?? Auth::querys()->companies_id, 
            'invoices.created_at >='    => $startDate.' 00:00:00',  
            'invoices.created_at <='    => $endDate.' 23:59:59'
        ])
        ->whereIn('invoices.type_documents_id', [1, 2, 3])
        ->whereIn('invoices.invoice_status_id', [2, 3, 4])
        ->countAllResults();
    }

    /**
     * Cantidad de documentos de nota credito
     * @var string $startDate fecha de inicio de la subcripción
     * @var string $endDate fecha final de la subcripcion
     * @return int cantidad de notas credito.
     */

    private function creditNote(string $startDate , string $endDate, $id = null): int
    {
        $model = new Invoice();
        return  $model
        ->where([
            'invoices.companies_id'     => $id ?? Auth::querys()->companies_id,  
            'invoices.created_at >='    => $startDate.' 00:00:00',  
            'invoices.created_at <='    => $endDate.' 23:59:59'
        ])
        ->whereIn('invoices.type_documents_id', [4])
        ->whereIn('invoices.invoice_status_id', [2, 3, 4])
        ->countAllResults();
    }


    /**
     * Cantidad de documentos de nota credito
     * @var string $startDate fecha de inicio de la subcripción
     * @var string $endDate fecha final de la subcripcion
     * @return int cantidad de facturad de venta, exportacion y contingencia.
     */

    private function debitNote(string $startDate , string $endDate, $id = null): int 
    {
        $model = new Invoice();
        return  $model
        ->where([
            'invoices.companies_id'     => $id ?? Auth::querys()->companies_id, 
            'invoices.created_at >='    => $startDate.' 00:00:00',  
            'invoices.created_at <='    => $endDate.' 23:59:59'
        ])
        ->whereIn('invoices.type_documents_id', [5])
        ->whereIn('invoices.invoice_status_id', [2, 3, 4])
        ->countAllResults();
    }


    /**
     * Cantidad de documentos en cartera
     * @var string $startDate fecha de inicio de la subscripcion
     * @var string $endDate fecha final de la subscripcion
     * @return int cantidad de documentos cargados en cartera
     */

    private function wallet(string $startDate , string $endDate, $id = null): int
    {
        $model = new Invoice();
        return $model->join('wallet', 'wallet.invoices_id = invoices.id')
        ->where([
            'invoices.companies_id'     => $id ?? Auth::querys()->companies_id, 
            'invoices.created_at >='    => $startDate.' 00:00:00',  
            'invoices.created_at <='    => $endDate.' 23:59:59',  
        ])
        ->whereIn('invoices.type_documents_id', $this->typeDocumentIds)
        ->countAllResults();
    }

    /**
     * Cantidad de documentos de nomina electronica
     * @var string $startDate fecha de inicio de la subscripcion
     * @var string $endDate fecha final de la subscripcion
     * @return int cantidad de documentos de nomina
     */

    private function payroll(string $startDate , string $endDate, $id = null): int
    {
        $model = new Invoice();
        return  $model
        ->where([
            'invoices.companies_id'     => $id ?? Auth::querys()->companies_id, 
            'invoices.created_at >='    => $startDate.' 00:00:00',  
            'invoices.created_at <='    => $endDate.' 23:59:59'
        ])
        ->whereIn('invoices.type_documents_id', [9])
        ->whereIn('invoices.invoice_status_id', [14])
        ->countAllResults();
    }


    /**
     * Cantidad de documentos de nomina electronica de ajuste
     * @var string $startDate fecha de inicio de la subscripcion
     * @var string $endDate fecha final de la subscripcion
     * @return int cantidad de documentos de nomina de ajuste
     */

    private function adjustmentPayroll(string $startDate , string $endDate, $id = null): int 
    {
        $model = new Invoice();
        return  $model
        ->where([
            'invoices.companies_id'     => $id ?? Auth::querys()->companies_id, 
            'invoices.created_at >='    => $startDate.' 00:00:00',  
            'invoices.created_at <='    => $endDate.' 23:59:59'
        ])
        ->whereIn('invoices.type_documents_id', [10])
        ->whereIn('invoices.invoice_status_id', [14])
        ->countAllResults();
    }

    /**
     * Cantidad de documentos externos al facturador
     * @var string $startDate fecha de inicio de la subscripcion
     * @var string $endDate fecha final de la subscripcion
     * @return int cantidad de documentos de externos
     */

    private function externalDocument(string $startDate , string $endDate, $id = null): int
    {
        $model = new Invoice();
        return  $model
        ->where([
            'invoices.companies_id'     => $id ?? Auth::querys()->companies_id, 
            'invoices.created_at >='    => $startDate.' 00:00:00',  
            'invoices.created_at <='    => $endDate.' 23:59:59'
        ])
        ->whereIn('invoices.type_documents_id', [101, 102, 103, 104])
        ->whereIn('invoices.invoice_status_id', [7])
        ->countAllResults();
    }

    /**
     * Cantidad de documentos soporte
     * @var string $startDate fecha de inicio de la subscripcion
     * @var string $endDate fecha final de la subscripcion
     * @return int cantidad de documentos soporte
     */

    private function documentSupport(string $startDate , string $endDate, $id = null): int 
    {
        $model = new Invoice();
        return $model
        ->where([
            'invoices.companies_id'     => $id ?? Auth::querys()->companies_id, 
            'invoices.created_at >='    => $startDate.' 00:00:00',  
            'invoices.created_at <='    => $endDate.' 23:59:59'
        ])
        ->whereIn('invoices.type_documents_id', [105,106])
        ->whereIn('invoices.invoice_status_id', [10])
        ->countAllResults();
    }

    /**
     * Informacion de la subscripción
     * @var string $startDate fecha de inicio de la subscripcion
     * @var string $endDate fecha final de la subscripcion
     * @return Array
     */
    

    public function total($id = null): Object
    {
        $model          = new Subscription();
        $subscription   = $model
        ->select(['subscriptions.start_date', 'subscriptions.end_date', 'packages.name  as package_name', 'packages.quantity_document'])
        ->join('packages', 'packages.id = subscriptions.packages_id')
        ->where([
            'subscriptions.companies_id'    =>  $id ?? Auth::querys()->companies_id,
            'subscriptions.status'          => 'Activo',
        ])
        ->orderBy('subscriptions.id', 'desc')
        ->asObject()    
        ->first();


        if(Auth::querys()->role_id == 1) {
            $startDate  = isset($subscription->start_date) ? $subscription->start_date : '2000-01-01';
            $endDate    = isset($subscription) ? $subscription->end_date: '2500-01-01';
        } else {
            $startDate  = $subscription->start_date ?? '2000-01-01';
            $endDate    = isset($subscription) ? $subscription->end_date: date('Y-m-d');
            $currentDate   = strtotime(date("Y-m-d H:i:00",time()));
            $dateEntry  = strtotime($endDate." 21:00:00");

            if($currentDate > $dateEntry){
                $endDate    =  date('Y-m-d');
            }
        }
      

        return (Object) [
            'invoices'              => $this->invoice($startDate, $endDate, $id) ?? 0,
            'credit_note'           => $this->creditNote($startDate, $endDate, $id) ?? 0,
            'debit_note'            => $this->debitNote($startDate, $endDate, $id) ?? 0,
            'wallet'                => $wallet = $this->wallet($startDate, $endDate, $id) ?? 0,
            'payroll'               => $this->payroll($startDate, $endDate, $id) ?? 0,
            'adjustment_payroll'    => $this->adjustmentPayroll($startDate, $endDate, $id) ?? 0,
            'document_support'      => $this->documentSupport($startDate, $endDate, $id) ?? 0,
            'document_externo'      => $this->externalDocument($startDate, $endDate, $id) ?? 0,
            'total'                 => $total = $this->documents($startDate, $endDate, $id) + $wallet ?? 0 + $wallet, 
            'package_name'          => $subscription->package_name ?? 'No se encuentra registrado el paquete',
            'package_quantity'      => $quntity_package = $subscription->quantity_document ?? 0,
            'available'             => $quntity_package - $total,
            'start_date'            => $subscription->start_date ?? '0000-00-00',
            'end_date'              => $subscription->end_date ?? '0000-00-00'
        ];
    }
}






?>