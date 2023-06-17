<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class IntegrationShopifyConsolidationTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                         => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE ],
            'company_id'                 => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE ],
            'integration_shopify_id'     => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE ],
            'integrationTraffic'         => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE ],
            'name'                       => ['type' => 'TEXT' ],
            'created_at'                 => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'                 => ['type' => 'TIMESTAMP', 'null' => true],
            'deleted_at'                 => ['type' => 'TIMESTAMP', 'null' => true]
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('integration_shopify_consolidation');
    }

    public function down()
    {
        $this->forge->dropTable('integration_shopify_consolidation');
    }
}
