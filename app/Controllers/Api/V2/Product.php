<?php

namespace App\Controllers\Api\V2;


use App\Controllers\Api\Auth;
use App\Controllers\HeadquartersController;
use App\Controllers\InventoryController;
use App\Traits\ValidationsTrait2;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use App\Models\AccountingAcount;
use App\Models\ProductsSerial;


class Product extends ResourceController
{

    use ValidationsTrait2;

    protected $modelName    = 'App\Models\Product';
    protected $format       = 'json';
    protected $controllerHeadquarters;
    protected $controllerInventory;

    public function __construct()
    {
        $this->controllerHeadquarters = new HeadquartersController();
        $this->controllerInventory = new InventoryController();
    }

    /**
     * List products in format JSON
     *
     * @return JSON
     */

    public function index()
    {
        // echo json_encode( $products );
        if ($this->request->getGet('tax_iva') !== null) {
            $tax_iva = 'R';
            if($this->request->getGet('tax_iva') == 'facturado'){
                $tax_iva = 'F';
            }
            // if($this->request->getGet('tax_iva') == 'ordeCompra' || $this->request->getGet('tax_iva') == 'R'){
                $products = $this->model
                    ->select(['products.*', 'products_details.cost_value'])
                ->where(['tax_iva' => $tax_iva, 'kind_product_id' => NULL])
                ->whereIN('companies_id', $this->controllerHeadquarters->idsCompaniesHeadquarters(Auth::querys()->companies_id))
                ->join('products_details', 'products_details.id_product = products.id and products_details.status = "active"', 'left')
                ->asObject()
                ->get()
                ->getResult();
            // }else{
            //     $products =  $this->controllerInventory->availabilityProductAccess(Auth::querys()->companies_id );
            //     $ids = [];
            //     $product_aux = []; 
            //     foreach ($products as $key => $product) {
            //         array_push($ids, $product->id);
            //         $product_aux[$product->id] = ($product->input + $product->inputTransfer) - ($product->output + $product->output);
            //     }
            //     if(count($products) > 0){
            //         $products = $this->model
            //             ->select(['products.*', 'products_details.cost_value'])
            //             ->where(['tax_iva' => $tax_iva, 'kind_product_id' => NULL])
            //             // ->whereIN('companies_id', $this->controllerHeadquarters->idsCompaniesHeadquarters(Auth::querys()->companies_id))
            //             ->whereIN('id', $ids)
            //             ->join('products_details', 'products_details.id_product = products.id and products_details.status = "active"', 'left')
            //             ->asObject()
            //             ->get()
            //             ->getResult();
            //     }else $products = [];
            // }
        }else{
            if($this->request->getGet('type_id') == 3){
                $products = $this->model
                    ->select(['products.*', 'products_details.cost_value'])
                    ->where(['kind_product_id' => 3])
                    ->whereIN('companies_id', $this->controllerHeadquarters->idsCompaniesHeadquarters(Auth::querys()->companies_id))
                    ->join('products_details', 'products_details.id_product = products.id and products_details.status = "active"', 'left')
                // ->whereIN('id', $ids)
                    ->asObject()
                    ->get()
                    ->getResult();
            }else{
                $products = $this->model
                    ->select(['products.*', 'products_details.cost_value'])
                    ->where(['tax_iva != ' => ''])
                    ->whereIN('companies_id', $this->controllerHeadquarters->idsCompaniesHeadquarters(Auth::querys()->companies_id))
                    ->join('products_details', 'products_details.id_product = products.id and products_details.status = "active"', 'left')
                // ->whereIN('id', $ids)
                    ->asObject()
                    ->get()
                    ->getResult();
            }
        }


        $arrayProducts = [];
        try {
            foreach ($products as $item) {
                $item->valor = ($this->request->getGet('type') !== null) ? $item->cost : $item->valor;
                $product = (object) [
                    'id'                            => $item->id,
                    'code'                          => $item->code,
                    'name'                          => $item->name,
                    'description'                   => $item->description,
                    'category'                      => $item->category_id,
                    'price_amount'                  => $item->valor,
                    'price_one'                     => $item->value_one,
                    'price_two'                     => $item->value_two,
                    'price_three'                   => $item->value_three,
                    'cost'                          => $item->cost_value ? $item->cost_value : $item->cost,
                    'unit_measure_id'               => $item->unit_measures_id,
                    'invoiced_quantity'             => 1,
                    'line_extension_amount'         => $item->valor,
                    'free_of_charge_indicator'      => $item->free_of_charge_indicator == 'true' ? true: false,
                    'type_item_identification_id'   => $item->type_item_identifications_id,
                    'base_quantity'                 => 1,
                    'type_generation_transmition_id'=> $item->type_generation_transmition_id,
                    'tax_totals'                    => [
                        $this->_accountingAccount($item->iva, 1, $item->valor),
                        $this->_accountingAccount($item->retefuente, 6, $item->valor),
                        $this->_accountingAccount($item->reteica, 7, $item->valor),
                    ],
                    // 'tax_totals' => [],
                    'tax_iva' => $item->tax_iva,
                    'max_quantity' => $this->controllerInventory->availabilityProduct($item->id, Auth::querys()->companies_id ),
                    'serials' => $this->getSerials($item->id)
                ];
                array_push($arrayProducts, $product);
                // if(count($product->tax_totals[1]) < 2){
                    // return $this->respond(['status' =>  200, 'code' => 200, 'data' => $item ], 500);
                // }
            }
        } catch (\Exception $e) {
            return $this->respond(['status' =>  200, 'code' => 200, 'data' => $e->getMessage() ], 500);
        }

        return $this->respond(['status' =>  200, 'code' => 200, 'data' => $arrayProducts ]);
    }

    public function getSerials($id_product){
        $pSerialM = new ProductsSerial();
        // if(Auth::querys()->role_id != 15){
            $serilas = $pSerialM
            // has un select para las tablas products_serial y products_serial_detail.invoices_id y invoices.company_destination_id
            ->select(['products_serial.*', 'products_serial_detail.invoices_id', 'invoices.company_destination_id'])
            // has un where para el campo products_id sea igual a la vaiable que se envia
            // que el status sea igual a 1
            // y el company_destination_id sea igual al Auth::querys()->companies_id
            // y si el invoices.type_documents_id es igual a 115 que el invoice_status_id sea igual a 21
            ->where([
                'products_id' => $id_product,
                'products_serial.status' => 1,
                'invoices.company_destination_id' => Auth::querys()->companies_id,
                'invoices.type_documents_id !=' => 115])
            // ->where('invoices.type_documents_id !=', 115)
            ->orWhere('(invoices.type_documents_id = 115 and invoices.invoice_status_id = 21 and invoices.company_destination_id = '.Auth::querys()->companies_id.')')
            // Realiza un join a la tabla products_serial_detail teniendo como referencia al campo products_serial_id de la tabla products_serial
            // y que a su vez el campo invoices_id de products_serial_detail sea el mas grande
            ->join('products_serial_detail', 'products_serial_detail.products_serial_id = products_serial.id and products_serial_detail.invoices_id = (select max(invoices_id) from products_serial_detail where products_serial_id = products_serial.id)', 'left')
            // realiza un join a la tabla invoices teniendo como referencia al campo invoices_id de la tabla products_serial_detail y ya
            ->join('invoices', 'invoices.id = products_serial_detail.invoices_id', 'left')

            ->get()->getResult();
            return $serilas;
            // // Realiza un join a la tabla products_serial_detail teniendo como referencia al campo products_serial_id de la tabla products_serial
            // // y que a su vez el campo invoices_id de products_serial_detail sea el mas grande
            // ->join('products_serial_detail', 'products_serial_detail.products_serial_id = products_serial.id and products_serial_detail.invoices_id = (select max(invoices_id) from products_serial_detail where products_serial_id = products_serial.id)', 'left')
            // // realiza un join a la tabla invoices teniendo como referencia al campo invoices_id de la tabla products_serial_detail y ya
            // ->join('invoices', 'invoices.id = products_serial_detail.invoices_id', 'left')

            // ->get()->getResult();
            // return $serilas;
        // }
    }

    public function create()
    {
        $input = $this->getRequestInput($this->request);
        $validate = $this->validateRequest($input, [
            'code'                      => 'required|max_length[45]',
            'name'                      => 'required|max_length[45]',
            'description'               => 'required|max_length[255]',
            'value'                     => 'required|max_length[45]',
            // 'entry_credit'              => 'required',
            // 'entry_debit'               => 'required',
            // 'iva'                       => 'required',
            // 'retefuente'                => 'required',
            // 'reteica'                   => 'required',
            // 'reteiva'                   => 'required',
            // 'account_pay'               => 'required',
            'brandname'                 => 'permit_empty|max_length[255]',
            'modelname'                 => 'permit_empty|max_length[255]',
            'photo'                     => 'permit_empty|max_length[255]'
        ]);

        if(!$validate) {
            return $this->respondHTTP422();
        }else {
            $accountAccounting = new AccountingAcount();
            $id = $accountAccounting->where(['code' => '0000000'])->asObject()->first();
            $json = $this->request->getJSON();
            $data = [
                'code'                          =>  $json->code,
                'name'                          =>  $json->name,
                'description'                   =>  $json->description,
                'valor'                         =>  $json->value,
                'free_of_charge_indicator'      =>  $json->free_of_charge_indicator ? 'false' : 'true',
                'unit_measures_id'              =>  70,
                'type_item_identifications_id'   =>  4,
                'companies_id'                  =>  Auth::querys()->companies_id,
                'entry_credit'                  =>  array_keys($this->_getAccountingAccount(1, 'Crédito'))[0],
                'entry_debit'                   =>  array_keys($this->_getAccountingAccount(1, 'Débito'))[0],
                'iva'                           =>  array_keys($this->_getAccountingAccount(2))[0],
                'retefuente'                    =>  array_keys($this->_getAccountingAccount(3))[0],
                'reteica'                       =>  array_keys($this->_getAccountingAccount(3))[0],
                'reteiva'                       =>  array_keys($this->_getAccountingAccount(3))[0],
                'account_pay'                   =>  array_keys($this->_getAccountingAccount(4))[0],
                'brandname'                     => $json->brandname,
                'modelname'                     => $json->modelname,
                'foto'                          => isset($json->photo) ?? $json->photo,
                'kind_product_id'               => $json->kind_product_id ?? 1,
                'type_generation_transmition_id'=> $json->type_generation_transmition_id ?? null,
                'reference_prices_id'           => 1,
                'cost'                          => $json->cost,
                'tax_iva'                       => 'F'
            ];
            $this->model->insert($data);

            $item = $this->model
                ->where(['id' => $this->model->getInsertID() ])
                ->asObject()
                ->first();

            $product = [
                'id'                            => $item->id,
                'code'                          => $item->code,
                'name'                          => $item->name,
                'description'                   => $item->description,
                'price_amount'                  => $item->valor,
                'unit_measure_id'               => $item->unit_measures_id,
                'invoiced_quantity'             => 1,
                'line_extension_amount'         => $item->valor,
                'free_of_charge_indicator'      => $item->free_of_charge_indicator == 'true' ? true: false,
                'type_item_identification_id'   => $item->type_item_identifications_id,
                'base_quantity'                 => 1,
                'type_generation_transmition_id'=> $item->type_generation_transmition_id,
                'tax_totals'                    => [
                    $this->_accountingAccount($item->iva, 1, $item->valor),
                    $this->_accountingAccount($item->retefuente, 6, $item->valor),
                    $this->_accountingAccount($item->reteica, 7, $item->valor),
                ]
            ];
            $data['iva'] = $id->id;
            $data['tax_iva'] = 'R';
            $this->model->insert($data);

            return $this->respond(['status' => 201,  'code' => 201 , 'data' => $product]);
        }

    }

    public function edit($id = null)
    {

    }

    public function update($id = null)
    {

    }

    public function delete($id = null)
    {

    }

    private function _accountingAccount(int $id, int $tax, float $priceAmount): array
    {
        $model = new AccountingAcount();
        $accounting = $model->asObject()->find($id);
        if(empty($accounting)){
            return [];
        }
        // return [$accounting];
        return [
            'tax_id'            => $tax,
			'tax_amount'        => ($accounting->percent * $priceAmount) / 100,
			'percent'           =>  $accounting->percent,
			'taxable_amount'    => $priceAmount
        ];
    }

    private function _getAccountingAccount(int $id, string $nature = '')
    {
        $account = new AccountingAcount();
        if($id == 1){
            $data = $account->where([
                'type_accounting_account_id'        => $id,
                'nature'                            => $nature,
            ])
                ->get()
                ->getResult();
        } else {
            $data = $account->where([
                'type_accounting_account_id'    => $id,
            ])
                ->get()
                ->getResult();
        }

        $info = [];
        foreach ($data as $item) {
            if ($id != 1  && $id != 4) {
                $info = array_merge($info, array($item->name . ' (' . $item->percent . '%' . ') ' => $item->id));
            } else {
                $info = array_merge($info, array($item->name => $item->id));
            }
        }
        $info  = array_flip($info);
        return $info;
    }
}