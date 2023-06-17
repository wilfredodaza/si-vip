<?php

namespace App\Database\Seeds;

use App\Models\Certificate;
use CodeIgniter\Database\Seeder;

class CertificateSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'companies_id'  =>   1,
            'name'          =>  'TOVAR-Y-CAMPANA-CONSULTORES-S-A-S-.p12',
            'password'      => 'omXtqHUfYTUPMIzm'
        ];

        $model = new Certificate();
        $model->insert($data);

    }
}
