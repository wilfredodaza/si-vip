<?php


namespace App\Controllers\Api;

use App\Models\AccountingAcount;
use App\Models\Product;
use App\Traits\ApiTrait;
use App\Traits\TransformTrait;
use App\Traits\ValidationsTrait;
use CodeIgniter\RESTful\ResourceController;


class Products extends ResourceController
{

    use ApiTrait, TransformTrait, ValidationsTrait;


    protected $format = 'json';

    public function list()
    {
        try {
        $products   = new Product();
            $products->where(['companies_id' => Auth::querys()->companies_id])->get()->getResult();
            $items = $this->pagination($products, 'products')->where(['deleted_at' => null])->limit(5, 0)
                ->select('products.*, 
                    type_item_identifications.id as type_item_identifications_id,
                    type_item_identifications.code as type_item_identifications_code,
                    type_item_identifications.name as type_item_identifications_name,
                    type_item_identifications.code_agency as type_item_identifications_code_agency,
                    unit_measures.id as unit_measures_id,
                    unit_measures.name as unit_measures_name,
                    unit_measures.code as unit_measures_code,
                    reference_prices.id as reference_prices_id,
                    reference_prices.name as reference_prices_name,
                    reference_prices.code as reference_prices_code,
                ')
                ->join('type_item_identifications', 'products.type_item_identifications_id = type_item_identifications.id')
                ->join('unit_measures', 'products.unit_measures_id = unit_measures.id')
                ->join('reference_prices', 'products.reference_prices_id = reference_prices.id')
            ->get()
            ->getResult();


            $dates = [];
            foreach ($items as $item) {
                $data = $this->_transform($item);
                array_push($dates, $data);
            }


        return $this->respond([
                'status' =>  200,
                'message' => 'success',
                'data' => $dates,
                'pagination' => $this->paginate

            ], 200);

        } catch (\Exception $e) {
            return $this->respond([
                'status' => 400,
                'message' => 'Bat Request',
            ], 400);
        }

    }

    public function index()
    {
        $products = new Product();

        $items = $products->where(['companies_id' => Auth::querys()->companies_id])

            ->get()
            ->getResult();

        $data = [];
        $i = 0;

        foreach ($items as $key) {
            $data[$i]['product_id']                     = $key->id;
            $data[$i]['code']                           = $key->code;
            $data[$i]['name']                           = $key->name;
            $data[$i]['price_amount']                   = $key->valor;
            $data[$i]['description']                    = $key->description;
            $data[$i]['unit_measure_id']                = $key->unit_measures_id;
            $data[$i]['type_item_identification_id']    = $key->type_item_identifications_id;
            $data[$i]['base_quantity']                  = 1;
            $data[$i]['free_of_charge_indicator']       = $key->free_of_charge_indicator;
            $data[$i]['reference_price_id']             = $key->reference_prices_id;
            $data[$i]['value']                          = $key->valor;
            $data[$i]['iva'] = (double)$this->_accountingAccount($key->iva);
            $data[$i]['reteICA'] = (double)$this->_accountingAccount($key->reteica);
            $data[$i]['reteIVA'] = (double)0;
            $data[$i]['reteFuente'] = (double)$this->_accountingAccount($key->retefuente);
          	$data[$i]['foto']                           =  $key->foto;
          	$data[$i]['category_id'] = $key->category_id;
          	$data[$i]['produc_valu_in'] = $key->produc_valu_in;
            $data[$i]['produc_descu'] = $key->produc_descu;
            $i++;
        }

        /*return $this->respond([
            'status' => 200,
            'message' => 'ok',
            'data' => $data
        ], 200);*/
        return $this->respond($data, 200);
    }

    public function store()
    {
        $product = new Product();
        $data = $this->request->getJSON();



        $transform = self::data($data, [
            'code'                      => 'code',
            'name'                      => 'name',
            'description'               => 'description',
            'unitMeasureId'             => 'unit_measures_id',
            'typeItemIdentificationId'  => 'type_item_identifications_id',
            'freeOfChargeIndicator'     => 'free_of_charge_indicator',
            'referencePriceId'          => 'reference_prices_id',
            'value'                     => 'valor',
            'entryCredit'               => 'entry_credit',
            'entryDebit'                => 'entry_debit',
            'iva'                       => 'iva',
            'reteFuente'                => 'retefuente',
            'reteICA'                   => 'reteica',
            'reteIVA'                   => 'reteiva',
            'accountPay'                => 'account_pay',
            'brandName'                 => 'brandname',
            'modelName'                 => 'modelname'
        ]);
        $transform['companies_id'] = Auth::querys()->companies_id;
        if ($product->insert($transform)) {
            $id = $product->getInsertID();
            $products = new Product();
        $data = [];
            $product                                = $products->asObject()->find(['id' => $id]);
            $data['product_id']                     = $product[0]->id;
            $data['code']                           = $product[0]->code;
            $data['name']                           = $product[0]->name;
            $data['price_amount']                   = $product[0]->valor;
            $data['description']                    = $product[0]->description;
            $data['unit_measure_id']                = 70;
            $data['type_item_identification_id']    = $product[0]->type_item_identifications_id;
            $data['base_quantity']                  = 1;
            $data['discounts_id']                   = 1;
            $data['free_of_charge_indicator']       = $product[0]->free_of_charge_indicator;
            $data['reference_price_id']             = $product[0]->reference_prices_id;
            $data['value']                          = $product[0]->valor;
            $data['iva']                            = (double)$this->_accountingAccount($product[0]->iva);
            $data['reteICA']                        = (double)$this->_accountingAccount($product[0]->reteica);
            $data['reteIVA']                        = (double)0;
            $data['reteFuente']                     = (double)$this->_accountingAccount($product[0]->retefuente);
        }
        /* $data = $products->select('products.*,
                 type_item_identifications.id as type_item_identifications_id,
                 type_item_identifications.code as type_item_identifications_code,
                 type_item_identifications.name as type_item_identifications_name,
                 type_item_identifications.code_agency as type_item_identifications_code_agency,
                 unit_measures.id as unit_measures_id,
                 unit_measures.name as unit_measures_name,
                 unit_measures.code as unit_measures_code,
                 reference_prices.id as reference_prices_id,
                 reference_prices.name as reference_prices_name,
                 reference_prices.code as reference_prices_code,
             ')
             ->where(['products.companies_id' => Auth::querys()->companies_id, 'products.id' => $id])
             ->join('type_item_identifications', 'products.type_item_identifications_id = type_item_identifications.id')
             ->join('unit_measures', 'products.unit_measures_id = unit_measures.id')
             ->join('reference_prices', 'products.reference_prices_id = reference_prices.id')
             ->get()
             ->getResult();

        $data = $this->_transform($data[0]);
        $data['_id'] = $id;*/
        return $this->respond([
            'status'    => 201,
            'message'   => 'Created',
            'data' => $data
        ], 201);
    }

    public function edit($id = null)
    {
        $products = new Product();
        $data = $products->select('products.*, 
                    type_item_identifications.id as type_item_identifications_id,
                    type_item_identifications.code as type_item_identifications_code,
                    type_item_identifications.name as type_item_identifications_name,
                    type_item_identifications.code_agency as type_item_identifications_code_agency,
                    unit_measures.id as unit_measures_id,
                    unit_measures.name as unit_measures_name,
                    unit_measures.code as unit_measures_code,
                    reference_prices.id as reference_prices_id,
                    reference_prices.name as reference_prices_name,
                    reference_prices.code as reference_prices_code,
                ')
            ->where(['products.companies_id' => Auth::querys()->companies_id, 'products.id' => $id])
            ->join('type_item_identifications', 'products.type_item_identifications_id = type_item_identifications.id')
            ->join('unit_measures', 'products.unit_measures_id = unit_measures.id')
            ->join('reference_prices', 'products.reference_prices_id = reference_prices.id')
            ->get()
            ->getResult();


        if (count($data) > 0) {
            $data = $this->_transform($data[0]);
            return $this->respond([
                'status' => 200,
                'message' => 'ok',
                'data' => $data
            ], 200);

        } else {
            return $this->respond([
                'status' => 404,
                'message' => 'Not Found',
            ], 404);
        }

    }

    public function update($id = null)
    {

        $data = $this->request->getJSON();


        $validation = self::Validates((array)$data, [
            'code' => 'required|unique:Product=>code=>companies',
            'name' => 'required|max:250',
            'description' => 'required',
            'unitMeasureId' => 'required|number',
            'typeItemIdentificationId' => 'required|number',
            'baseQuantity' => 'required|number',
            'freeOfChargeIndicator' => 'required',//bolean
            'referencePriceId' => 'required|number',
            'value' => 'required|max:20',
            'entryCredit' => 'required|number|max:11|isInValid:AccountingAcount=>id=>companies',
            'entryDebit' => 'required|number|max:11|isInValid:AccountingAcount=>id=>companies',
            'iva' => 'required|number|max:11|isInValid:AccountingAcount=>id=>companies',
            'reteFuente' => 'required|number|max:11|isInValid:AccountingAcount=>id=>companies',
            'reteICA' => 'required|number|max:11|isInValid:AccountingAcount=>id=>companies',
            'reteIVA' => 'required|number|max:11|isInValid:AccountingAcount=>id=>companies',
            'accountPay' => 'required|number|max:11|isInValid:AccountingAcount=>id=>companies',
            'brandName' => 'required|max:250',
            'modelName' => 'required|max:250',
        ]);

        if ($validation->error) {
            return $this->respond([
                'status' => 400,
                'message' => 'Bat Request',
                'errors' => $validation
            ], 400);
        }

        $transform = self::data($data, [
            'code'                      => 'code',
            'name'                      => 'name',
            'description'               => 'description',
            'unitMeasureId'             => 'unit_measures_id',
            'typeItemIdentificationId'  => 'type_item_identifications_id',
            'baseQuantity'              => 'base_quantity',
            'freeOfChargeIndicator'     => 'free_of_charge_indicator',
            'referencePriceId'          => 'reference_prices_id',
            'value'                     => 'valor',
            'entryCredit'               => 'entry_credit',
            'entryDebit'                => 'entry_debit',
            'iva'                       => 'iva',
            'reteFuente'                => 'retefuente',
            'reteICA'                   => 'reteica',
            'reteIVA'                   => 'reteiva',
            'accountPay'                => 'account_pay',
            'brandName'                 => 'brandname',
            'modelName'                 => 'modelname'
        ]);


        $product = new Product();
        if (count($product->find(['id' => $id])) == 0) {
            return $this->respond([
                'status' => 404,
                'message' => 'Not found',
            ], 404);
        }
        $product = new Product();
        if ($product->update(['id' => $id, 'companies_id' => Auth::querys()->companies_id], $transform)) {
            $products = new Product();
            $data = $products->select('products.*, 
                    type_item_identifications.id as type_item_identifications_id,
                    type_item_identifications.code as type_item_identifications_code,
                    type_item_identifications.name as type_item_identifications_name,
                    type_item_identifications.code_agency as type_item_identifications_code_agency,
                    unit_measures.id as unit_measures_id,
                    unit_measures.name as unit_measures_name,
                    unit_measures.code as unit_measures_code,
                    reference_prices.id as reference_prices_id,
                    reference_prices.name as reference_prices_name,
                    reference_prices.code as reference_prices_code,
                ')
                ->where(['products.companies_id' => Auth::querys()->companies_id, 'products.id' => $id])
                ->join('type_item_identifications', 'products.type_item_identifications_id = type_item_identifications.id')
                ->join('unit_measures', 'products.unit_measures_id = unit_measures.id')
                ->join('reference_prices', 'products.reference_prices_id = reference_prices.id')
                ->get()
                ->getResult();

            if (count($data) > 0) {
                $data = $this->_transform($data[0]);
                return $this->respond([
                    'status' => 201,
                    'message' => 'Created',
                    'data' => $data
                ], 201);
            }
        }
    }

    public function delete($id = null)
    {
        $products = new Product();
        $data = $products->select('products.*, 
                    type_item_identifications.id as type_item_identifications_id,
                    type_item_identifications.code as type_item_identifications_code,
                    type_item_identifications.name as type_item_identifications_name,
                    type_item_identifications.code_agency as type_item_identifications_code_agency,
                    unit_measures.id as unit_measures_id,
                    unit_measures.name as unit_measures_name,
                    unit_measures.code as unit_measures_code,
                    reference_prices.id as reference_prices_id,
                    reference_prices.name as reference_prices_name,
                    reference_prices.code as reference_prices_code,
                ')
            ->where(['products.companies_id' => Auth::querys()->companies_id, 'products.id' => $id])
            ->join('type_item_identifications', 'products.type_item_identifications_id = type_item_identifications.id')
            ->join('unit_measures', 'products.unit_measures_id = unit_measures.id')
            ->join('reference_prices', 'products.reference_prices_id = reference_prices.id')
            ->get()
            ->getResult();

        if (count($data) > 0) {
            $transform = $this->_transform($data[0]);
            $products->delete($id);

            return $this->respond([
                'status'    => 201,
                'message'   => 'successfully removed',
                'data'      => $transform
            ], 201);
        } else {
            return $this->respond([
                'status' => 400,
                'message' => 'Not Found',
            ], 400);
        }
    }

    private function _accountingAccount($id)
    {
        if (!empty($id)) {
            $account = new AccountingAcount();
            $data = $account->asObject()->find(['id' => (int)$id]);
            if (count($data) > 0) {
                $value = $data[0]->percent;
            } else {
                $value = 0;
            }
        } else {
            $value = 0;
        }

        return $value;
    }

    private function _transform($data)
    {
        return self::data($data, [
            'id'                => '_id',
            'code'              => 'code',
            'valor'             => 'value',
            'name'              => 'name',
            'description'       => 'description',
            'brandname'         => 'brandName',
            'modelname'         => 'modelName',
            'unit_measures_id'  => [
                'unitMeasure'   => self::data($data, [
                        'unit_measures_id'      => '_id',
                        'unit_measures_name'    => 'name',
                        'unit_measures_code'    => 'code'
                    ]
                )],
            'type_item_identifications_id'      => [
                'typeItemIdentification'                    => self::data($data, [
                    'type_item_identifications_id'          => '_id',
                    'type_item_identifications_name'        => 'name',
                    'type_item_identifications_code'        => 'code',
                    'type_item_identifications_code_agency' => 'codeAgency'
                ])
            ],
            'reference_prices_id' => [
                'referencePriceId' => self::data($data, [
                    'reference_prices_id' => '_id',
                    'reference_prices_name' => 'name',
                    'reference_prices_code' => 'code',
                ])],
            'free_of_charge_indicator' => 'freeOfChangeIndicator',
            'entry_credit' => ['entryCredit' => self::data($this->_accountingAccountApi($data->entry_credit), [
                'id' => '_id',
                'name' => 'name',
                'code' => 'code',
                'percent' => 'percent',
                'nature' => 'nature',
                'status' => 'status',
                'created_at' => 'createdAt',
                'type_accounting_account_id' => ['typeAccountingAccount' => self::data($this->_accountingAccountApi($data->entry_credit), [
                    'id' => '_id',
                    'name' => 'name'
                ])]

            ])],
            'entry_debit' => ['entryDebit' => self::data($this->_accountingAccountApi($data->entry_debit), [
                'id' => '_id',
                'name' => 'name',
                'code' => 'code',
                'percent' => 'percent',
                'nature' => 'nature',
                'status' => 'status',
                'created_at' => 'createdAt',
                'type_accounting_account_id' => ['typeAccountingAccount' => self::data($this->_accountingAccountApi($data->entry_debit), [
                    'id' => '_id',
                    'name' => 'name'
                ])]

            ])],
            'iva' => ['iva' => self::data($this->_accountingAccountApi($data->iva), [
                'id' => '_id',
                'name' => 'name',
                'code' => 'code',
                'percent' => 'percent',
                'nature' => 'nature',
                'status' => 'status',
                'created_at' => 'createdAt',
                'type_accounting_account_id' => ['typeAccountingAccount' => self::data($this->_accountingAccountApi($data->iva), [
                    'id' => '_id',
                    'name' => 'name'
                ])]

            ])],
            'retefuente' => ['reteFuente' => self::data($this->_accountingAccountApi($data->retefuente), [
                'id' => '_id',
                'name' => 'name',
                'code' => 'code',
                'percent' => 'percent',
                'nature' => 'nature',
                'status' => 'status',
                'created_at' => 'createdAt',
                'type_accounting_account_id' => ['typeAccountingAccount' => self::data($this->_accountingAccountApi($data->retefuente), [
                    'id' => '_id',
                    'name' => 'name'
                ])]
            ])],
            'reteica' => ['reteICA' => self::data($this->_accountingAccountApi($data->reteica), [
                'id' => '_id',
                'name' => 'name',
                'code' => 'code',
                'percent' => 'percent',
                'nature' => 'nature',
                'status' => 'status',
                'created_at' => 'createdAt',
                'type_accounting_account_id' => ['typeAccountingAccount' => self::data($this->_accountingAccountApi($data->reteica), [
                    'id' => '_id',
                    'name' => 'name'
                ])]
            ])],
            'reteiva' => ['reteIVA' => self::data($this->_accountingAccountApi($data->reteiva), [
                'id' => '_id',
                'name' => 'name',
                'code' => 'code',
                'percent' => 'percent',
                'nature' => 'nature',
                'status' => 'status',
                'created_at' => 'createdAt',
                'type_accounting_account_id' => ['typeAccountingAccount' => self::data($this->_accountingAccountApi($data->reteiva), [
                    'id' => '_id',
                    'name' => 'name'
                ])]
            ])],
            'account_pay' => ['accountPay' => self::data($this->_accountingAccountApi($data->account_pay), [
                'id' => '_id',
                'name' => 'name',
                'code' => 'code',
                'percent' => 'percent',
                'nature' => 'nature',
                'status' => 'status',
                'created_at' => 'createdAt',
                'type_accounting_account_id' => ['typeAccountingAccount' => self::data($this->_accountingAccountApi($data->account_pay), [
                    'id' => '_id',
                    'name' => 'name'
                ])]
            ])],
        ]);
    }

    private function _accountingAccountApi($id)
    {
        if (!empty($id)) {

            $account = new AccountingAcount();
            $data = $account->select('
            accounting_account.*, 
            type_accounting_account.id as type_accounting_account_id,
            type_accounting_account.name as type_accounting_account_name
            ')
                ->where(['accounting_account.id' => $id])
                ->join('type_accounting_account', 'type_accounting_account.id = accounting_account.type_accounting_account_id')
                ->asObject()
                ->get()
                ->getResult();

            if (count($data) > 0) {

                return $data[0];
            } else {
                return null;
            }

        } else {
            return null;
        }
    }

}