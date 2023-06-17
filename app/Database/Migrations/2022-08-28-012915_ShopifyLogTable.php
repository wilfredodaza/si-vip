<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ShopifyLogTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                         => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE ],
            'traffic_id'                 => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE ],
            'companies_id'               => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE ],
            'order_number'               => ['type' => 'INT', 'constraint' => 11 ],
            'message'                    => ['type' => 'TEXT'],
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('shopify_log');
    }

    public function down()
    {
        $this->forge->dropTable('shopify_log');
    }
}
