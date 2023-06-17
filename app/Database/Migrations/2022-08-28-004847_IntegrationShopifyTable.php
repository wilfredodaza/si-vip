<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class IntegrationShopifyTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                         => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE ],
            'companies_id'               => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE ],
            'resolucion_id'              => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE ],
            'name_shopify'               => ['type' => 'VARCHAR', 'constraint'=> 300],
            'token'                      => ['type' => 'VARCHAR', 'constraint'=> 300],
            'status'                     => ['type' => "ENUM('active', 'inactive')", 'default' => 'Active'],
            'status_invoice'             => ['type' => "ENUM('Borrador', 'Por pagar')"]
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('integration_shopify');
    }

    public function down()
    {
        $this->forge->dropTable('integration_shopify');
    }
}
