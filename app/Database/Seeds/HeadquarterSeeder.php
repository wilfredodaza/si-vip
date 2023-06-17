<?php

namespace App\Database\Seeds;

use App\Models\Headquarters;
use CodeIgniter\Database\Seeder;

class HeadquarterSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['name' =>  'General'],
            ['name' =>  'Secundario']
        ];

        foreach ($data as $item):
            $headquarters = new Headquarters();
            $headquarters->insert($item);
        endforeach;
    }
}
