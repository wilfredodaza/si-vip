<?php

namespace App\Database\Seeds;

use App\Models\Software;
use CodeIgniter\Database\Seeder;

class SoftwareSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'companies_id'          => 1,
            'identifier'            => '274efa72-5bf7-4c5c-b844-f046ad461482',
            'pin'                   => 30030,
            'identifier_payroll'    => '85d4f338-9bc8-4dc0-9996-9d5a805a8fc9',
            'pin_payroll'           => '30030'
        ];
        $model = new Software();
        $model->insert($data);
    }
}
