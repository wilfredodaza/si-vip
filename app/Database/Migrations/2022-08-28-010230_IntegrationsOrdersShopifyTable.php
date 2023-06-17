<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class IntegrationsOrdersShopifyTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                         => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE ],
            'integration_shopify_id'     => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE ],
            'company_id'                 => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE ],
            'shopify_id'                 => ['type' => 'VARCHAR', 'constraint' => 300],
            'shopify_number'             => ['type' => 'VARCHAR', 'constraint' => 300 ],
            'value'                      => ['type' => 'DECIMAL', 'constraint' => '20,2', 'null' => true],
            'status'                     => ['type' => 'VARCHAR', 'constraint' => 100 ],
            'create_at_shopify'          => ['type' => 'DATE' ]
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('integrations_orders_shopify');
    }

    public function down()
    {
        $this->forge->dropTable('integrations_orders_shopify');
    }
}
