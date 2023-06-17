<?php

namespace App\Database\Seeds;

use App\Models\Configuration;
use CodeIgniter\Database\Seeder;

class ConfigurationSeeder extends Seeder
{
    public function run()
    {
        $configuration = new Configuration();
        $data = [
            'name_app'          =>  'MiFacturaLegal.com',
            'icon_app'          =>  'apps',
            'email'             =>  'soporte@mifacturalegal.com',
            'logo_menu'         =>  'Captura4-b6463-250x170-2.jpg',
            'intro'             =>  null,
            'footer'            => '<small>&copy;2020 <a href="https://www.iplanetcolombia.com/">IPlanet Colombia S.A.S</a> Todos los derechos reservados.</small>',
            'alert_title'       =>  '',
            'alert_body'        => '',
            'status_alert'      => 'inactive'
        ];
        $configuration->insert($data);

    }
}
