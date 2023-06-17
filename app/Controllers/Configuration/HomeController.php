<?php

namespace App\Controllers\Configuration;

use App\Controllers\Api\Auth;
use App\Controllers\BaseController;
use App\Models\Configuration;
use App\Models\Invoice;
use App\Models\LineInvoice;
use App\Models\Module;
use App\Models\TypeDocumentIdentifications;
use App\Models\Municipalities;
use App\Models\TypeRegimes;
use App\Models\TypeOrganizations;
use App\Models\Customer;
use App\Traits\SubscriptionTrait;


class HomeController extends BaseController
{
    use SubscriptionTrait;

	public function index()
	{





       /* $configurations = new Configuration();
        $configuration = $configurations->asObject()->first();
        if(session('user')->role_id != 5 && session('user')->role_id != 7) {
            return view('pages/home', ['indicator' =>  $this->total(), 'configuration' => $configuration]);
        }
  
        if(session('user')->role_id == 5 ) {
            $typeDocuments = new TypeDocumentIdentifications();
            $typeDocumentIdentification =  $typeDocuments->get()->getResult();


            $municipalities     = new Municipalities();
            $municipality       =  $municipalities->get()->getResult();

            $typeRegime         = new TypeRegimes();
            $typeRegimes        =  $typeRegime->get()->getResult();

            $typeOrganization   = new TypeOrganizations();
            $typeOrganizations  = $typeOrganization->get()->getResult();

            $customer = new Customer();
            $dataCustomer = $customer->where(['email' => session('user')->username ])->asObject()->first();

            

            $data = [
                'typeDocumentIdentification'        => $typeDocumentIdentification,
                'municipalities'                    => $municipality,
                'typeRegimes'                       => $typeRegimes,
                'typeOrganizations'                 => $typeOrganizations,
                'customer'                          => $dataCustomer,
                'configuration'                     => $configuration,
                'modules'                           => $modules
            ];
        }

        
        if( session('user')->role_id == 7) {
        
            $model = new Customer();
            $customer  = $model->select([
                'customers.id as customer_id',
                'customers.user_id',
                'customers.name as first_name',
                'customer_worker.second_name',
                'customer_worker.surname',
                'customer_worker.second_surname',
                'type_document_identifications.name as type_document_identification_name',
                'customers.identification_number',
                'municipalities.name as municipality_name',
                'customers.address',
                'customers.phone',
                'customers.email',
                'customer_worker.account_number',
                'banks.name as bank_name',
                'payment_methods.name payment_method_name',
                'bank_account_types.name as bank_account_type_name',
                'customer_worker.integral_salary',
                'customer_worker.admision_date',
                'type_workers.name as type_worker_name',
                'customer_worker.high_risk_pension',
                'type_contracts.name as type_contract_name',
                'sub_type_workers.name as sub_type_worker_name',
                'customer_worker.salary',
                'customer_worker.work',    
		        'customer_worker.holidays',
            	'customer_worker.court_date'
            ])
            ->join('customer_worker', 'customers.id = customer_worker.customer_id', 'left')
            ->join('municipalities', 'customers.municipality_id = municipalities.id', 'left')
            ->join('banks', 'banks.id = customer_worker.bank_id', 'left')
            ->join('type_document_identifications', 'type_document_identifications.id = customers.type_document_identifications_id' , 'left')
            ->join('payment_methods', 'payment_methods.id = customer_worker.payment_method_id', 'left' )
            ->join('bank_account_types', 'bank_account_types.id = customer_worker.bank_account_type_id' , 'left')
            ->join('type_contracts', 'type_contracts.id = customer_worker.type_contract_id', 'left' )
            ->join('type_workers', 'type_workers.id = customer_worker.type_worker_id', 'left' )
            ->join('sub_type_workers', 'sub_type_workers.id = customer_worker.sub_type_worker_id' , 'left')
            ->where(['customers.user_id' => Auth::querys()->id])
            ->asObject()
            ->first();

   
            $model = new Invoice();
            $invoice = $model->select(['count(invoices.id) as payroll_count'])
            ->where([
                'invoices.customers_id'         => $customer->customer_id,
                'invoices.companies_id'         => Auth::querys()->companies_id,
                'invoices.invoice_status_id'    => 14,
                'invoices.type_documents_id'    => 9
                ])
            ->asObject()
            ->first();

            
            if(is_null($customer) || is_null($invoice)) {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }

            

            $data = [
                'customer'      => $customer,
                'invoice'       => $invoice,
                'configuration' => $configuration,
                'modules'       => $modules
            ];

        }
*/

        $module = new Module();
        $modules = $module
            ->join('module_role', 'modules.id = module_role.module_id')
            ->where(['module_role.role_id' =>  Auth::querys()->role_id ])
            ->asObject()
            ->orderBy('position', 'asc')
            ->get()
            ->getResult();

		return  view('pages/home', ['modules' => $modules]);
	}

	public function about()
    {
        return view('pages/about');
    }

    

    public function products()
    {
        $lineInvoice = new LineInvoice();
        if(session('user')->companies_id != null) {

            $lineInvoice = $lineInvoice->select('count(line_invoices.products_id) as cant, products.name')
                ->join('products', 'line_invoices.products_id = products.id')
                ->join('invoices', 'invoices.id = line_invoices.invoices_id')
                ->where(['invoices.companies_id' => session('user')->companies_id])
                ->groupBy('line_invoices.products_id')
                ->orderBy('cant', 'desc')
                ->limit('5')
                ->get()
                ->getResult();


        }else {
            $lineInvoice = $lineInvoice->select('count(line_invoices.products_id) as cant, products.name')
                ->join('products', 'line_invoices.products_id = products.id')
                ->join('invoices', 'invoices.id = line_invoices.invoices_id')
                ->groupBy('line_invoices.products_id')
                ->orderBy('cant', 'desc')
                ->limit('5')
                ->get()
                ->getResult();
        }
        echo json_encode($lineInvoice);
        die();
    }

    public function customers()
    {
        $invoice = new Invoice();
        if(session('user')->companies_id != null) {
            $invoice = $invoice->select('count(invoices.customers_id) as cant, customers.name')
                ->join('customers', 'invoices.customers_id = customers.id')
                ->where(['invoices.companies_id' => session('user')->companies_id, 'customers.type_customer_id' => 1])
                ->groupBy('invoices.customers_id')
		        ->orderBy('cant', 'desc')
                ->limit('5')
                ->get()
                ->getResult();
        }else {
            $invoice = $invoice->select('count(invoices.customers_id) as cant, customers.name')
                ->join('customers', 'invoices.customers_id = customers.id')
                ->where(['customers.type_customer_id' => 1])
                ->groupBy('invoices.customers_id')
		        ->orderBy('cant', 'desc')
                ->limit('5')
                ->get()
                ->getResult();
        }
        echo json_encode($invoice);
        die();
    }
    

    
}
