<?php

namespace App\Database\Seeds;

use App\Models\Company;
use CodeIgniter\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run()
    {
        $data = [
            'taxes_id'                                  => 1,
            'type_currencies_id'                        => 35,
            'type_liabilities_id'                       => 117,
            'type_organizations_id'                     => 1,
            'type_document_identifications_id'          => 6,
            'countries_id'                              => 46,
            'departments_id'                            => 5,
            'municipalities_id'                         => 149,
            'languages_id'                              => 80,
            'type_operations_id'                        => 10,
            'type_regimes_id'                           => 1,
            'type_environments_id'                      => 1,
            'type_environment_payroll_id'               => 1,
            'template_pdf_id'                           => 1,
            'type_company_id'                           =>  1,
            'company'                                   => 'MiFacturaLegal.COM',
            'identification_number'                     => '900782726',
            'dv'                                        => '8',
            'address'                                   => 'CARR 66 76 26 IN 3 AP 210',
            'email'                                     => 'emtovar1@gmail.com',
            'token'                                     => '4b1192bb68d97983211f24dd2b42218521f9631e63acdf17d0ea0e8bf9eac4ec',
            'phone'                                     => '3114600956',
            'testId'                                    =>  ''
        ];

        $model = new Company();
        $model->insert($data);

    }
}
