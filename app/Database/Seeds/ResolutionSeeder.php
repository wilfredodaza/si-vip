<?php

namespace App\Database\Seeds;

use App\Models\Resolution;
use CodeIgniter\Database\Seeder;

class ResolutionSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'companies_id'          => 1,
                'type_documents_id'     => 1,
                'prefix'                =>  'SETP',
                'resolution'            => '18760000001',
                'resolution_date'       => '2019-01-19',
                'technical_key'         => 'fc8eac422eba16e22ffd8c6f94b3f40a6e38162c',
                'from'                  => '990000000',
                'to'                    => '995000000',
                'date_from'             => '2019-01-19',
                'date_to'               => '2030-01-19'
            ],
            [
                'companies_id'          => 1,
                'type_documents_id'     => 4,
                'prefix'                =>  'NC',
                'resolution'            => '1',
                'resolution_date'       => '2019-01-19',
                'technical_key'         => NULL,
                'from'                  => '1',
                'to'                    => '100',
                'date_from'             => '2019-01-19',
                'date_to'               => '2030-01-19'
            ],
            [
                'companies_id'          => 1,
                'type_documents_id'     => 5,
                'prefix'                => 'ND',
                'resolution'            => '1',
                'resolution_date'       => '2019-01-19',
                'technical_key'         => NULL,
                'from'                  => '1',
                'to'                    => '100000',
                'date_from'             => '2019-01-19',
                'date_to'               => '2030-01-19'
            ],
            [
                'companies_id'          => 1,
                'type_documents_id'     => 9,
                'prefix'                =>  'NI',
                'resolution'            => '1',
                'resolution_date'       => '2019-01-19',
                'technical_key'         => NULL,
                'from'                  => '1',
                'to'                    => '1000',
                'date_from'             => '2019-01-19',
                'date_to'               => '2030-01-19'
            ],
            [
                'companies_id'          => 1,
                'type_documents_id'     => 11,
                'prefix'                =>  'SEDS',
                'resolution'            => '18760000001',
                'resolution_date'       => '2022-07-27',
                'technical_key'         => NULL,
                'from'                  => '984000000',
                'to'                    => '985000000',
                'date_from'             => '2022-01-01',
                'date_to'               => '2022-12-31'
            ],
            [
                'companies_id'          => 1,
                'type_documents_id'     => 13,
                'prefix'                => 'NDS',
                'resolution'            => '1',
                'resolution_date'       => '2022-07-27',
                'technical_key'         => NULL,
                'from'                  => '1',
                'to'                    => '100',
                'date_from'             => '2022-01-01',
                'date_to'               => '2022-12-31'
            ],
        ];

        foreach ($data as $item):
            $model = new Resolution();
            $model->insert($item);
        endforeach;
    }
}
