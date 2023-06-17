<?php


namespace App\Models;


use CodeIgniter\Model;

class Product extends Model
{

    protected $table            = 'products';
    protected $primaryKey       = 'id';
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'name',
        'code',
        'code_item',
        'valor',
        'value_one',
        'value_two',
        'value_three',
        'cost',
        'description',
        'unit_measures_id',
        'type_item_identifications_id',
        'reference_prices_id',
        'free_of_charge_indicator',
        'companies_id',
        'entry_credit',
        'entry_debit',
        'iva',
        'retefuente',
        'reteica',
        'reteiva',
        'account_pay',
        'brandname',
        'modelname',
        'foto',
        'category_id',
        'produc_valu_in',
        'produc_descu',
        'kind_product_id',
        'type_generation_transmition_id',
        'tax_iva',
        'provider_id',
        'gender_id',
        'group_id',
        'price_id',
        'sub_group_id',
        'material_id'
    ];

}