<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class   ProductDetailTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                         => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE ],
            'id_product'                 => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE ],
            'id_invoices'                => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE ],
            'policy_type'                => ['type' => "ENUM('general', 'personalizado')"],
            'cost_value'                 => ['type' => 'DECIMAL', 'constraint' => '20,2'],
            'retail_value'               => ['type' => 'DECIMAL', 'constraint' => '20,2'],
            'wholesale_value'            => ['type' => 'DECIMAL', 'constraint' => '20,2'],
            'status'                     => ['type' => "ENUM('active', 'inactive')"],
            'created_at'                 => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'                 => ['type' => 'TIMESTAMP', 'null' => true],
            'deleted_at'                 => ['type' => 'TIMESTAMP', 'null' => true]
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('products_details');
    }

    public function down()
    {
        $this->forge->dropTable('products_details');
    }
}
