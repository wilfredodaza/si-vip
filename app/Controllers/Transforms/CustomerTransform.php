<?php

namespace App\Controllers\Transforms;

use App\Traits\TransformTrait;

/**
 *@author wilson andres bachiller ortiz
 *@email wbachiller@iplanetcolombia.com
 *@date  14/11/2020
 */

class CustomerTransform
{
    use TransformTrait;

    static public function request($data) {

        return  self::data($data, [
            'name'                          =>  'name',
            'identificationNumber'          =>  'identification_number',
            'typeDocumentIdentificationId'  =>  'type_document_identifications_id',
            'phone'                         =>  'phone',
            'address'                       =>  'address',
            'email'                         =>  'email',
            'merchantRegistration'          =>  'merchant_registration',
            'typeCustomerId'                =>  'type_customer_id',
            'typeRegimeId'                  =>  'type_regime_id',
            'municipalityId'                =>  'municipality_id',
            'typeOrganizationId'            =>  'type_organization_id'
        ]);
    }

    static public function respond($data)
    {
        return self::data($data, [
            'id'                                => '_id',
            'name'                              => 'name',
            'type_document_identifications_id'  =>  [
                'typeDocumentIdentifiacionId' => self::data($data, [
                    'type_document_identifications_id'      => '_id',
                    'type_document_identifications_name'    => 'name',
                    'type_document_identifications_code'    => 'code',
                ])
            ],
            'identification_number'             => 'identificationNumber',
            'dv'                                => 'dv',
            'phone'                             => 'phone',
            'address'                           => 'address',
            'email'                             => 'email',
            'email2'                            => 'email2',
            'email3'                            => 'email3',
            'merchant_registration'             => 'merchantRegistration',
            'type_customer_id'                  => [
                'typeCustomerId' => self::data($data, [
                    'type_customer_id' => '_id',
                    'type_customer_name' => 'name'
                ])
            ],
            'type_regime_id'                    => [
                'typeRegimeId' => self::data($data, [
                    'type_regimes_id' => '_id',
                    'type_regimes_name' => 'name',
                    'type_regimes_code' => 'code'
                ])
            ],
            'municipality_id'                   => [
                'municipalityId' => self::data($data, [
                    'municipalities_id'     => '_id',
                    'municipalities_name'   => 'name',
                    'municipalities_code'   => 'code'
                ])
            ],
            'type_organization_id'              => [
                'typeOrganizationId' => self::data($data, [
                    'type_organizations_id'      => '_id',
                    'type_organizations_name'    => 'name',
                    'type_organizations_code'    => 'code',
                ])
            ]
        ]);
    }


}