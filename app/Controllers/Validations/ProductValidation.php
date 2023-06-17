<?php


namespace App\Controllers\Validations;


use App\Traits\ValidationsTrait2;

class ProductValidation
{
    use ValidationsTrait2;

    public static function rules($data= [])
    {
        return  self::Validates((array) $data, [
            'code'                      => 'required|unique:Product=>code=>companies',
            'name'                      => 'required|max:250',
            'description'               => 'required',
            'unitMeasureId'             => 'required|number',
            'typeItemIdentificationId'  => 'required|number',
            'baseQuantity'              => 'required|number',
            'freeOfChargeIndicator'     => 'required',
            'referencePriceId'          => 'required|number',
            'value'                     => 'required|max:20',
            'entryCredit'               => 'required|number|max:11|isInValid:AccountingAcount=>id=>companies',
            'entryDebit'                => 'required|number|max:11|isInValid:AccountingAcount=>id=>companies',
            'iva'                       => 'required|number|max:11|isInValid:AccountingAcount=>id=>companies',
            'reteFuente'                => 'required|number|max:11|isInValid:AccountingAcount=>id=>companies',
            'reteICA'                   => 'required|number|max:11|isInValid:AccountingAcount=>id=>companies',
            'reteIVA'                   => 'required|number|max:11|isInValid:AccountingAcount=>id=>companies',
            'accountPay'                => 'required|number|max:11|isInValid:AccountingAcount=>id=>companies',
            'brandName'                 => 'required|max:250',
            'modelName'                 => 'required|max:250',
        ]);
    }
}