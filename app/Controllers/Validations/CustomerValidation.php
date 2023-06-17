<?php


namespace  App\Controllers\Validations;



use App\Traits\ValidationsTrait2;

class CustomerValidation
{
    use ValidationsTrait2;

    public static function rules($data= [])
    {
        return  self::Validates((array) $data, [
            'name'                          =>  'required',
            'identificationNumber'          =>  'required|min:7',
            'typeDocumentIdentificationId'  =>  'required|number|isInValid:TypeDocumentIdentifications=>id',
            'phone'                         =>  'required|number',
            'address'                       =>  'required',
            'email'                         =>  'required|email',
            'merchantRegistration'          =>  'number',
            'typeCustomerId'                =>  'request|number|isInValid:TypeCustomer=>id',
            'typeRegimeId'                  =>  'request|number|isInValid:TypeRegimes=>id',
            'municipalityId'                =>  'request|number|isInValid:Municipalities=>id',
            'typeOrganizationId'            =>  'request|number|isInValid:TypeOrganizations=>id'
        ]);
    }
}