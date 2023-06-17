<?php
/**
 * Clase encargada del manejo de la tabla de grocery crud de productos
 * @authosr Wilson Andres Bachiller Ortiz <wilson@mawii.com>
 * @date 01/08/2022
 * @version 1.0
 */

namespace App\Controllers\Tables;

use App\Controllers\Api\Auth;
use App\Models\AccountingAcount;
use App\Traits\Grocery;



class ProductTable
{
    use Grocery;

    /**
     * Campos que no deben ser vistos.
     * @var string[] $hidden
     */
    protected  $hidden = [
        'token',
        'created_at',
        'updated_at'
    ];

    /**
     * Columnas visibles en tabla de productos
     * @var string[]  $columns
     */
    protected  $columns = [
        'code',
        'name',
        'valor',
        'description'
    ];

    /**
     * Metodo encargado de guardar las realaciones de la tabla productos
     * @return void
     */
    protected function relations()
    {
        $this->crudTable->setRelation('companies_id', 'companies', 'company');
        $this->crudTable->setRelation('type_generation_transmition_id', 'type_generation_transmitions', 'name');
    }

    /**
     * Metodo encargado de las reglas de la tabla de productos
     * @return void
     */
    protected function rules()
    {
        $this->crudTable->setRule('code', 'required');
        $this->crudTable->setRule('name', 'required');
        $this->crudTable->setRule('valor', 'required');
        $this->crudTable->setRule('entry_credit', 'required');
        $this->crudTable->setRule('entry_debit', 'required');
        $this->crudTable->setRule('iva', 'required');
        $this->crudTable->setRule('retefuente', 'required');
        $this->crudTable->setRule('reteica', 'required');
        $this->crudTable->setRule('reteiva', 'required');
        $this->crudTable->setRule('account_pay', 'required');
    }

    /**
     * Metodo encargado de las tipo de campo que maneja el formulario
     * de productos.
     * @return void
     */
    protected function fieldType()
    {
        $this->crudTable->fieldType('entry_credit', 'dropdown_search', $this->_getAccountingAccount(1, 'Crédito'));
        $this->crudTable->fieldType('entry_debit', 'dropdown_search', $this->_getAccountingAccount(1, 'Débito'));
        $this->crudTable->fieldType('iva', 'dropdown_search', $this->_getAccountingAccount(2));
        $this->crudTable->fieldType('retefuente', 'dropdown_search', $this->_getAccountingAccount(3));
        $this->crudTable->fieldType('reteica', 'dropdown_search', $this->_getAccountingAccount(3));
        $this->crudTable->fieldType('reteiva', 'dropdown_search', $this->_getAccountingAccount(3));
        $this->crudTable->fieldType('account_pay', 'dropdown_search', $this->_getAccountingAccount(4));
        $this->crudTable->fieldType('reference_prices_id', 'hidden');
        $this->crudTable->fieldType('type_item_identifications_id', 'hidden');
        $this->crudTable->fieldType('foto', 'hidden');
        $this->crudTable->fieldType('category_id', 'hidden');
        $this->crudTable->fieldType('produc_valu_in', 'hidden');
        $this->crudTable->fieldType('produc_descu', 'hidden');
        $this->crudTable->fieldType('unit_measures_id', 'hidden');
        $this->crudTable->fieldType('type_generation_transmition_id', 'hidden');
        $this->crudTable->fieldType('kind_product_id', 'hidden');
        $this->crudTable->fieldType('deleted_at', 'hidden');
        $this->crudTable->fieldType('free_of_charge_indicator',  'dropdown_search', [ 'false' => 'No', 'true' => 'Si']);
        $this->crudTable->fieldType('valor', 'int');
    }


    /**
     * Callbacks encargado de modificar los datos del formulario de productos
     * @return void
     */
    protected function callback()
    {
        $this->crudTable->where(['companies_id' => Auth::querys()->companies_id]);
        if (session('user')->role_id == 2 || session('user')->role_id >= 3) {
            $this->crudTable->fieldType('companies_id', 'hidden');
            $this->crudTable->callbackAddForm(function ($data) {
                $data['reference_prices_id']                    = 1;
                $data['type_item_identifications_id']           = 4;
                $data['type_generation_transmition_id']         = null;
                $data['unit_measures_id']                       = 70;
                $data['companies_id']                           = Auth::querys()->companies_id;
                $data['brandname']                              = 'No Aplica';
                $data['modelname']                              = 'No Aplica';
                $data['category_id']                            = 1;
                $data['entry_credit']                           = array_keys($this->_getAccountingAccount(1, 'Crédito'))[0];
                $data['entry_debit']                            = array_keys($this->_getAccountingAccount(1, 'Débito'))[0];
                $data['iva']                                    = array_keys($this->_getAccountingAccount(2))[0];
                $data['retefuente']                             = array_keys($this->_getAccountingAccount(3))[0];
                $data['reteica']                                = array_keys($this->_getAccountingAccount(3))[0];
                $data['reteiva']                                = array_keys($this->_getAccountingAccount(3))[0];
                $data['account_pay']                            = array_keys($this->_getAccountingAccount(4))[0];
                return $data;
            });
        } else {
            $this->crudTable->columns(['code', 'name', 'valor', 'description', 'companies_id']);
            $this->crudTable->callbackAddForm(function ($data) {
                $data['reference_prices_id']                    = 1;
                $data['type_item_identifications_id']           = 1;
                $data['unit_measures_id']                       = 70;
                $data['type_generation_transmition_id']         = null;
                $data['kind_product_id']                        = 1;

                return $data;
            });
            $this->crudTable->callbackBeforeInsert(function ($data) {
                $data->data['valor'] = $this->_clearNumber($data->data['valor']);
                return $data;
            });
            $this->crudTable->callbackBeforeUpdate(function ($data) {
                $data->data['valor'] = $this->_clearNumber($data->data['valor']);
                return $data;
            });
        }
    }

    /**
     * Metodo de limipiar los datos ingresados en valor de producto
     * @param string $data valor del producto
     * @return array|string|string[]
     */
    private function _clearNumber(string $data)
    {
        $data = str_replace('-', '', $data);
        return $data;
    }

    /**
     * Metodo encargado de extraer el listado de cuentas contables
     * segun su tipo y naturaleza
     * @param int $id  id del tipo de cuenta contable
     * @param string $nature tipo de naturaleza a buscar
     * @return int[]|string[]
     */
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