<?php


namespace App\Controllers;


use App\Controllers\Api\Auth;
use App\Models\Invoice;
use App\Models\TrackingCustomer;


class QuotationController extends BaseController
{
    public function index()
    {
        $invoice = new Invoice();
        $invoices = $invoice->select('*, invoice_status.name as status,
            type_documents.name  as type_document, 
            customers.name as customer, 
            invoices.id as id_invoice')
            ->like($this->_search(), isset($_GET['value']) ? $this->request->getGet('value') : '', 'both')
            ->join('type_documents', 'invoices.type_documents_id = type_documents.id')
            ->join('customers', 'invoices.customers_id = customers.id')
            ->join('invoice_status', 'invoices.invoice_status_id = invoice_status.id')
            ->where(['invoices.type_documents_id' => 100]);
        if (session('user')->role_id !== "1") {
            $invoices->where('invoices.companies_id', session('user')->companies_id);
        }
        $invoices->orderBy('invoices.id', 'desc');
        $data = [
            'invoices' => $invoices->paginate(10),
            'pager' => $invoices->pager,
        ];

        return view('quotation/index', $data);
    }

    public function create()
    {
        return view('quotation/create');
    }

    public function email($id)
    {

        $invoice = new Invoice();
        $invoices = $invoice->select('customers.email, customers.email2, customers.email3, customers.name, 
            invoices.resolution, companies.identification_number, companies.email as email_company')
            ->join('companies', 'invoices.companies_id = companies.id')
            ->join('customers', 'invoices.customers_id = customers.id')
            ->where(['invoices.companies_id' => Auth::querys()->companies_id, 'invoices.id' => $id])
            ->get()
            ->getResult();

        if(count($invoices) > 0 ){
            $client = \Config\Services::curlrequest();
            $res = $client->post(getenv('API').'/quotation/email',
                [
                    'form_params' => [
                        'email'                 => $invoices[0]->email ,
                        'email2'                => $invoices[0]->email2,
                        'email3'                => $invoices[0]->email3,
                        'name'                  => $invoices[0]->name,
                        'identification_number' => $invoices[0]->identification_number,
                        'resolution'            => $invoices[0]->resolution,
                        'email_company'         => $invoices[0]->email_company
                    ],
                    'headers' => [
                        'Accept' => 'application/json'
                    ],

                ]);

            $response = json_decode($res->getBody());

            if(isset($response->status) && $response->status == 200) {
                return redirect()->to(base_url('/quotation'))->with('success', $response->message);
            }else {
                return redirect()->to(base_url('/quotation'))->with('danger', 'El mensaje no pudo ser enviado');
            }

        }

    }

    public function edit($id)
    {
        return view('quotation/edit', ['id' => $id]);
    }

    private function _search()
    {
        if (isset($_GET['campo'])) {
            switch ($_GET['campo']) {
                case 'resolution':
                    $campo = 'invoices.resolution';
                    break;
                case 'Estado':
                    $campo = 'invoice_status.name';
                    break;
                case 'Cliente':
                    $campo = 'customers.name';
                    break;
                case 'Tipo de factura':
                    $campo = 'type_documents.name';
                    break;
            }

        } else {
            $campo = 'invoices.resolution';
        }
        return $campo;
    }

    public function close($id = null)
    {
        $invoice = new Invoice();
        $invoice->update(['id' => $id], ['invoice_status_id' => 6]);




        $tracking = new TrackingCustomer();
        $data = [
            'message'           => 'El usuario '.session('user')->username.' hace el cierre de la cotizacion el dia '.date('Y-m-d H:i:s'). '.',
            'username'          => session('user')->username,
            'created_at'        => date('Y-m-d H:i:s'),
            'table_id'          => $id,
            'companies_id'      => session('user')->companies_id,
            'type_tracking_id'  => 1,
        ];
        $tracking->save($data);

        return redirect()->to(base_url('quotation'))->with('success','La cotización fue cerrada exitosamente');
    }
}