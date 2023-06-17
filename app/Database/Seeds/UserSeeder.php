<?php

namespace App\Database\Seeds;

use App\Models\User;
use CodeIgniter\Database\Seeder;

class UserSeeder extends  Seeder
{
    public function run()
    {
        $data = [
            [
                'name'          => 'Super Administrador',
                'email'         => 'iplanet@iplanetcolombia.com',
                'username'      => 'root',
                'password'      => password_hash('M49bx3kk!!', PASSWORD_DEFAULT),
                'status'        => 'active',
                'photo'         => '',
                'role_id'       => 1
            ],
            [
                'name'          => 'pruebas',
                'email'         => 'iplanet@iplanetcolombia.com',
                'username'      => 'pruebas',
                'password'      => password_hash('123456789', PASSWORD_DEFAULT),
                'status'        => 'active',
                'photo'         => '',
                'role_id'       => 2,
                'companies_id'  => 1
            ]
        ];

        foreach ($data as $item):
            $user = new User();
            $user->insert($item);
        endforeach;
    }
}