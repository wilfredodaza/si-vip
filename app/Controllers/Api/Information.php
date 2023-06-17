<?php

namespace App\Controllers\Api;


use App\Models\Company;
use App\Models\GraphicEmail;
use App\Models\Invoice;
use CodeIgniter\RESTful\ResourceController;

class Information extends ResourceController
{

    protected $format = 'json';

    public function salesOfMonth($id = null)
    {
        $invoice = new Invoice();
        $invoices = $invoice->select('MONTH(invoices.created_at) as mes, sum(invoices.line_extesion_amount) as totals')
            ->where([
                'invoices.companies_id'     => Auth::querys()->role_id == 1 ? $id : Auth::querys()->companies_id,
                'invoices.created_at >='        => date('Y').'-01-01',
                'invoices.created_at <='        => date('Y').'-12-31',
                'invoices.invoice_status_id >'  => 1
            ])
            ->groupBy(['month(invoices.created_at)'])
            ->asObject()
            ->get()
            ->getResult();
        $data = [];
        $months = [];

        foreach ($invoices as $item) {
            array_push($months, $item->mes);
        }

        for ($i = 0; $i < 12; $i++) {
            if (in_array($i + 1, $months)) {
                $data[$i] = $invoices[array_search($i + 1, $months)];
            } else {
                $data[$i] = (object)['mes' => $i + 1, 'totals' => 0];
            }
        }

        return $this->respond(['status' => 200, 'data' => $data]);
    }

    public function salesOfProduct($id = null)
    {
        $invoice = new Invoice();
        $invoices = $invoice->select('MONTH(invoices.created_at) as mes, sum(line_invoices.line_extension_amount) as totals, products.name, products.id ')
            ->join('line_invoices', 'line_invoices.invoices_id = invoices.id')
            ->join('products', 'products.id = line_invoices.products_id')
            ->where([
                'invoices.companies_id' => Auth::querys()->role_id == 1 ? $id : Auth::querys()->companies_id,
                'invoices.created_at >=' =>  date('Y').'-01-01',
                'invoices.created_at <=' => date('Y').'-12-31'
            ])
            ->groupBy(['month(invoices.created_at), line_invoices.products_id'])
            ->asObject()
            ->get()
            ->getResult();


        $data = [];
        $products = [];
        $i = 0;
        foreach ($invoices as $item) {
            if (!in_array($item->id, $products)) {
                array_push($products, $item->id);
                $productsData[$item->id] = ['name' => $item->name];
            }
            $i++;
        }


        for ($i = 0; $i < 12; $i++) {
            $data[$i] = [];
            foreach ($products as $product) {
                $items = [];
                foreach ($invoices as $item) {
                    if ($item->mes == $i + 1 && $item->id == $product) {
                        $items = [
                            '_id' => $item->id,
                            'name' => $item->name,
                            'totals' => $item->totals,
                            'mes' => $item->mes
                        ];
                    } else {
                        if ($item->id == $product) {
                            $items = [
                                '_id' => $product,
                                'name' => $productsData[$product]['name'],
                                'totals' => 0,
                                'mes' => $i + 1
                            ];
                        }
                    }

                }
                array_push($data[$i], $items);

            }
        }

        return $this->respond(['status' => 200, 'data' => $data]);
    }

    public function salesOfCustomer($id = null)
    {
        $invoice = new Invoice();
        $invoices = $invoice->select('MONTH(invoices.created_at) as mes, sum(invoices.line_extesion_amount) as totals, customers.name, customers.id ')
            ->join('customers', 'customers.id = invoices.customers_id')
            ->where([
                'invoices.companies_id' => Auth::querys()->role_id == 1 ? $id : Auth::querys()->companies_id,
                'invoices.created_at >=' => date('Y').'-01-01',
                'invoices.created_at <=' =>	date('Y').'-12-31',
                'invoices.invoice_status_id >' => 1
            ])
            ->groupBy(['month(invoices.created_at), invoices.customers_id'])
            ->asObject()
            ->get()
            ->getResult();


        $data = [];
        $customers = [];
        $i = 0;
        foreach ($invoices as $item) {
            if (!in_array($item->id, $customers)) {
                array_push($customers, $item->id);
                $productsData[$item->id] = ['name' => $item->name];
            }
            $i++;
        }
        for ($i = 0; $i < 12; $i++) {
            $data[$i] = [];
            foreach ($customers as $customer) {
                $items = [];
                foreach ($invoices as $item) {


                    if ($item->mes == $i + 1 && $item->id == $customer) {

                        $items = [
                            '_id'       => $item->id,
                            'name'      => $item->name,
                            'totals'    => $item->totals,
                            'mes'       => $item->mes
                        ];
                        break;


                    } else {

                        if ($item->id == $customer) {
                            $items = [
                                '_id'       => $customer,
                                'name'      => $productsData[$customer]['name'],
                                'totals'    => 0,
                                'mes'       => $i + 1
                            ];
                        }
                    }
                }
                array_push($data[$i], $items);



            }
        }


        return $this->respond(['status' => 200, 'data' => $data]);
    }

    public function salesOfSeller($id = null)
    {
        $invoice = new Invoice();
        $invoices = $invoice->select('MONTH(invoices.created_at) as mes, sum(invoices.line_extesion_amount) as totals, users.name, users.id ')
            ->join('users', 'users.id = invoices.seller_id')
            ->where([
                'invoices.companies_id'  => Auth::querys()->role_id == 1 ? $id : Auth::querys()->companies_id,
                'invoices.created_at >=' => date('Y').'-01-01',
                'invoices.created_at <=' => date('Y').'-12-31',
                'invoices.seller_id !='=> NULL
            ])
            ->groupBy(['month(invoices.created_at), invoices.seller_id'])
            ->asObject()
            ->get()
            ->getResult();


        $data = [];
        $customers = [];
        $i = 0;
        foreach ($invoices as $item) {
            if (!in_array($item->id, $customers)) {
                array_push($customers, $item->id);
                $productsData[$item->id] = ['name' => $item->name];
            }
            $i++;
        }
        for ($i = 0; $i < 12; $i++) {
            $data[$i] = [];
            foreach ($customers as $customer) {
                $items = [];
                foreach ($invoices as $item) {


                    if ($item->mes == $i + 1 && $item->id == $customer) {

                        $items = [
                            '_id'       => $item->id,
                            'name'      => $item->name,
                            'totals'    => $item->totals,
                            'mes'       => $item->mes
                        ];
                        break;


                    } else {

                        if ($item->id == $customer) {
                            $items = [
                                '_id'       => $customer,
                                'name'      => $productsData[$customer]['name'],
                                'totals'    => 0,
                                'mes'       => $i + 1
                            ];
                        }
                    }
                }
                array_push($data[$i], $items);



            }
        }
        return $this->respond(['status' => 200, 'data' => $data]);
    }

    public function salesOfWallet($id = null)
    {
        $invoice = new Invoice();
        $invoices = $invoice->select(' sum(invoices.payable_amount) as total, customers.name as customer, invoices.customers_id, count(invoices.customers_id) as invoices, sum(wallet.value) as pago
        ')
            ->join('wallet', 'invoices.id = wallet.invoices_id', 'left')
            ->join('customers', 'invoices.customers_id = customers.id')
            ->where([
                'invoices.companies_id'         => Auth::querys()->role_id == 1 ? $id : Auth::querys()->companies_id,
                'invoices.status_wallet'        => 'Pendiente',
                'invoices.invoice_status_id >'  => 1
            ])
            ->whereIn('invoices.type_documents_id', [1, 2])
            ->groupBy(['invoices.customers_id'])
            ->asObject()
            ->get()
            ->getResult();



        $invoicesCredit = new Invoice();
        $invoicesCredits = $invoicesCredit->select(' sum(invoices.payable_amount) as total, customers.name as customer, invoices.customers_id, count(invoices.customers_id) as invoices, sum(wallet.value) as pago
        ')
            ->join('wallet', 'invoices.id = wallet.invoices_id', 'left')
            ->join('customers', 'invoices.customers_id = customers.id')
            ->where([
                'invoices.companies_id'         => Auth::querys()->role_id == 1 ? $id : Auth::querys()->companies_id,
                'invoices.invoice_status_id >'  => 1,
            ])
            ->whereIn('invoices.type_documents_id', [4])
            ->groupBy(['invoices.customers_id'])
            ->asObject()
            ->get()
            ->getResult();





        $data = [];
        $i = 0;
        foreach ($invoices as $invoice) {
            $totalCredit = 0;
            foreach ($invoicesCredits as $invoicesCredit) {
                if($invoice->customers_id == $invoicesCredit->customers_id) {
                    $totalCredit = $invoicesCredit->total;

                }
            }
            $data[$i] = [
                'customer' => $invoice->customer,
                'invoices' => $invoice->invoices,
                'total' => ($invoice->total - ($invoice->pago + $totalCredit)) ];
            $i++;
        }

        return $this->respond(['status' => 200, 'data' => $data], 200);
    }

    public function index($id = null)
    {
        $companies = new Company();
        $company = $companies->select('graphic_email.setting_json')
            ->join('graphic_email', 'companies.id = graphic_email.companies_id')
            ->where(['companies_id' => Auth::querys()->role_id == 1 ? $id : Auth::querys()->companies_id,])
            ->asObject()
            ->get()
            ->getResult();

        if(count($company) > 0) {
            return $this->respond([
                'status'    => 200,
                'data'      => json_decode($company[0]->setting_json)
            ]);
        }else {
            return $this->respond([
                'status' => 200,
                'data' =>  [
                'day'               =>'Monday',
                'day_number'        => '',
                'hours'             => '12:00',
                'sale_of_customer'  => true,
                'sale_of_month'     => true,
                'sale_of_product'   => true,
                'sale_of_seller'    => true,
                'sale_of_wallet'    => true,
                'time'              => '2'
            ]]);
        }
    }

    public function create()
    {

        $graphicEmails  = new GraphicEmail();
        $graphicEmail   = $graphicEmails->where(['companies_id' => Auth::querys()->companies_id])->get()->getResult();
        $json           = $this->request->getJSON();


        $data = [
            'setting_json'      =>  json_encode($json),
            'companies_id'      =>  Auth::querys()->companies_id
        ];



        if(count($graphicEmail) > 0) {
            $graphicEmails->update(['companies_id' => Auth::querys()->companies_id], $data);
        } else {
            $graphicEmails->save($data);
        }
    }
}