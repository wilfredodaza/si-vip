<?php


namespace App\Controllers;



use App\Controllers\Configuration\EmailController;
use Config\Services;
use App\Models\GraphicEmail;
use App\Models\Invoice;

class GraphicController extends BaseController
{
    public function setting() {
        return view('information/setting');
    }

    public function salesOfMonth()
    {
        return view('information/sales_of_month');
    }

    public function salesOfProduct()
    {
        return view('information/sales_of_product');
    }

    public function salesOfCustomer()
    {
        return view('information/sales_of_customer');
    }

    public function salesOfWallet()
    {
        return view('information/sales_of_wallet');
    }

    public function salesOfSeller()
    {
        return view('information/sales_of_seller');
    }

    public function salseOfWallet()
    {
        return view('information/sales_of_wallet');
    }


    public function _sendEmail(int $companiesId, string $emailUser)
    {

        $invoices       = new Invoice();
        $invoiceReal    = $invoices->select([
            'count(id) as count',
            'sum(line_extesion_amount) as total_mes',
        ])
        ->where([
            'MONTH(created_at)' => date('m'), 
            'companies_id'      => $companiesId,
            'idcurrency'        => 35,
            'invoice_status_id !=' => 1
            ])
        ->whereIn('type_documents_id', [1,2])
        ->groupBy(['MONTH(created_at)'])
        ->get()
        ->getResult();


        $invoices       = new Invoice();
        $invoiceRealCurrencys    = $invoices->select([
            'sum((line_extesion_amount * calculationrate)) as total',
        ])
        ->where([
            'MONTH(created_at)' => date('m'), 
            'companies_id'      => $companiesId,
            'invoice_status_id !=' => 1
            ])
        ->whereIn('type_documents_id', [1,2])
        ->groupBy(['MONTH(created_at)'])
        ->get()
        ->getResult();

        

     

    
        $invoices = new Invoice();
        $invoicePrevius = $invoices->select([
            'count(id) as count',
            'sum(line_extesion_amount) as total_mes',
            'invoice_status_id !=' => 1
        ])
        ->where([
            'MONTH(created_at)' => date('m') == 1 ? 12 : date('m') - 1 , 
            'companies_id'      =>  $companiesId
            ])
        ->groupBy(['MONTH(created_at)'])
        ->get()
        ->getResult();


        $invoices       = new Invoice();
        $invoicePreviusCurrencys    = $invoices->select([
            'count(id) as count',
            'sum((line_extesion_amount * calculationrate)) as total',
            'invoice_status_id !=' => 1
        ])
        ->where([
            'MONTH(created_at)' => date('m') == 1 ? 12 : date('m') - 1,  
            'companies_id'      => $companiesId,
            'idcurrency !='     => 35
            ])
        ->whereIn('type_documents_id', [1,2])
        ->groupBy(['MONTH(created_at)'])
        ->get()
        ->getResult();

     




        

        $invoice = new Invoice();
        $productsReal = $invoice
        ->select([
            'count(line_invoices.quantity) as quantity'
        ])
        ->join('line_invoices', 'invoices.id = line_invoices.invoices_id')
        ->where([
            'companies_id' => $companiesId, 
            'MONTH(invoices.created_at)' =>  date("m") 
        ])
        ->whereIn('type_documents_id', [1,2])
        ->get()
        ->getResult();

        
        $invoice = new Invoice();
        $productsPrevius = $invoice
        ->select([
            'count(line_invoices.quantity) as quantity'
        ])
        ->join('line_invoices', 'invoices.id = line_invoices.invoices_id')
	    ->where(['companies_id' =>$companiesId])
        ->whereIn('type_documents_id', [1,2])
        ->get()
        ->getResult();

       

        $customerArray = [];
        $customerPrevius = 0;
        $customers = new Invoice();
        $customersPrevius = $customers->select('customers_id')
        ->whereIn('type_documents_id', [1,2])
        ->where([
            'companies_id' => $companiesId,
            'invoices.created_at <' =>  date("Y-m-01 00:00:00"),
            ])
        ->get()
        ->getResult();

        foreach($customersPrevius as $item ):
           if(!in_array($item->customers_id, $customerArray)) {
                $customerPrevius += 1;
                array_push($customerArray, $item->customers_id);
           }
        endforeach;



        $customerNew = 0;
        $customers = new Invoice();
        $customersNew = $customers->select('customers_id')
        ->whereIn('type_documents_id', [1,2])
        ->where([
            'companies_id'                  => $companiesId,
            'MONTH(invoices.created_at)'    => date("m")
            ])
        ->get()
        ->getResult();

        foreach($customersNew as $item ):
           if(!in_array($item->customers_id, $customerArray)) {
                $customerNew += 1;
                array_push($customerArray, $item->customers_id);
           }
        endforeach;




    
   

        $wallet = new Invoice();
        $walletsTotal = $wallet
        ->select('sum(invoices.line_extesion_amount) as total')
        ->where([
            'invoices.status_wallet'    => 'Pendiente', 
            'companies_id'              => $companiesId,
            'idcurrency'             => 35
            ])
        ->whereIn('type_documents_id', [1,2])
        ->get()
        ->getResult();


        $wallet = new Invoice();
        $walletsTotalCurrency = $wallet
        ->select('sum((line_extesion_amount * calculationrate)) as total')
        ->where([
            'invoices.status_wallet'    => 'Pendiente', 
            'companies_id'              => $companiesId,
            'idcurrency !='                => 35
            ])
        ->whereIn('type_documents_id', [1,2])
        ->get()
        ->getResult();

            


        $wallet = new Invoice();
        $walletsCount = $wallet
        ->select([
            'customers_id',
            'count(invoices.id) as invoice'
        ])
        ->where(['invoices.status_wallet' => 'Pendiente', 'companies_id' => $companiesId ])
        ->whereIn('type_documents_id', [1,2])
        ->groupBy(['customers_id'])
        ->get()
        ->getResult();


        $email = new EmailController();
        $email->send('soporte@planetalab.xyz', 'Soporte MiFacturaLegal', $emailUser, 'MiFacturaLegal.COM - Indicadores BI de '.mes(date('m')).'.',  view('emails/report', [
            'invoiceReal'               => $invoiceReal, 
            'invoiceRealCurrency'       => $invoiceRealCurrencys,
            'invoicePrevius'            => $invoicePrevius,
            'invoicePreviusCurrency'    => $invoicePreviusCurrencys,
            'productReal'               => $productsReal,
            'productPrevius'            => $productsPrevius,
            'customerNew'               => $customerNew,
            'customerPrevius'           => $customerPrevius,
            'walletsCount'              => $walletsCount,
            'walletsTotal'              => $walletsTotal,
            'walletsTotalCurrency'      => $walletsTotalCurrency
            ]));
    }

    public function emailSales()
    {


        $graficEmail    = new GraphicEmail();
        $companies      = $graficEmail->join('companies' , 'companies.id =  graphic_email.companies_id')
        ->get()
        ->getResult();

       

         $i =0;
        foreach ($companies as $item) {

            /*$salesOfMonth               = $this->_querys(base_url().'/api/v1/graphic/sales_of_month/'.$item->companies_id);
            $salesOfProduct             = $this->_querys(base_url().'/api/v1/graphic/sales_of_product/'.$item->companies_id);
            $salesOfProductTwelve       = $this->_querys(base_url().'/api/v1/graphic/sales_of_product_twelve/'.$item->companies_id);
            $salesOfCustomer            = $this->_querys(base_url().'/api/v1/graphic/sales_of_customer/'.$item->companies_id);
            $salesOfCustomerMonth       = $this->_querys(base_url().'/api/v1/graphic/sales_of_customer_month/'.$item->companies_id);
            $salesOfCustomerPrevius     = $this->_querys(base_url().'/api/v1/graphic/sales_of_customer_month_previus/'.$item->companies_id);
            $salesOfCustomerAccumulated = $this->_querys(base_url().'/api/v1/graphic/sales_of_customer_month_accumulated/'.$item->companies_id);


            $salesOfSeller              = $this->_querys(base_url().'/api/v1/graphic/sales_of_seller/'.$item->companies_id);
            $salesOfSellerMonth         = $this->_querys(base_url().'/api/v1/graphic/sales_of_seller_month/'.$item->companies_id);
            $salesOfSellerPrevius       = $this->_querys(base_url().'/api/v1/graphic/sales_of_seller_previus/'.$item->companies_id);
            $salesOfSellerAccumulated   = $this->_querys(base_url().'/api/v1/graphic/sales_of_seller_accumulated/'.$item->companies_id);

            $salesOfWallet              = $this->_querys(base_url().'/api/v1/graphic/sales_of_wallet/'.$item->companies_id);
        


            $data = [
                'companies'                     => $item,
                'salesOfMonth'                  => $salesOfMonth,
                'salesOfProduct'                => $salesOfProduct,
                'salesOfCustomer'               => $salesOfCustomer,
                'salesOfSeller'                 => $salesOfSeller,
                'salesOfWallet'                 => $salesOfWallet,
                'salesOfCustomerMonth'          => $salesOfCustomerMonth,
                'salesOfCustomerPrevius'        => $salesOfCustomerPrevius,
                'salesOfProductTwelve'          => $salesOfProductTwelve,
                'salesOfCustomerAccumulated'    => $salesOfCustomerAccumulated,
                'salesOfSellerMonth'            => $salesOfSellerMonth,
                'salesOfSellerPrevius'          => $salesOfSellerPrevius,
                'salesOfSellerAccumulated'      => $salesOfSellerAccumulated,
                'setting'                       => $setting
            ];

            */

	
            $setting                    = $this->_querys(base_url().'/api/v1/graphic/setting/'.$item->companies_id);
	    
            if($setting->data->time == 1 && date('H') == explode(':', $setting->data->hours)[0]) {
        
                $this->_sendEmail($item->companies_id, $setting->data->email);
            }else if($setting->data->time == 2 && date('l') == $setting->data->day && date('H') == explode(':', $setting->data->hours)[0]) {
                $this->_sendEmail($item->companies_id, $setting->data->email);
            }else if($setting->data->time == 3 && date('d') == $setting->data->day_number && date('H') == explode(':', $setting->data->hours)[0]) {
                $this->_sendEmail($item->companies_id, $setting->data->email);
            }

          $i++;
        }
    }

    private function _null($data)
    {
      echo view('emails/information', $data);die();

       // echo $data['setting']->data->email; die();
     /*   $email = new  EmailController();
        $email->send('factura@planeta-internet.com','MiFacturaLegal.com', $data['setting']->data->email,'Informes de venta', view('emails/information', $data));
*/
    }

    private function _querys($url)
    {
      
       
        $token = $this->_autentication()->data->token;
     
        $client = Services::curlrequest();
        $client->setHeader('Content-Type', 'application/json');
        $client->setHeader('Accept', 'application/json');
        $client->setHeader('Authorization', "Bearer ".$token);
        $res = $client->get($url, []);
       return json_decode($res->getBody());
    }

    private function _autentication()
    {

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => base_url().'/api/auth',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{"username": "root","password": "123456789"}',
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        ]);

        $response = curl_exec($curl);

        curl_close($curl);
        return  json_decode($response);
    }

}