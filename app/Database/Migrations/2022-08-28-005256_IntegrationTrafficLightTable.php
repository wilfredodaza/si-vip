<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class IntegrationTrafficLightTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                         => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE ],
            'companies_id'               => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE ],
            'id_shopify'                 => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE ],
            'number_mfl'                 => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE ],
            'type_document_id'           => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE ],
            'integration_shopify_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => TRUE ],
            'observations'               => ['type' => 'TEXT' ],
            'uuid'                       => ['type' => 'VARCHAR', 'constraint' => 255 ],
            'status'                     => ['type' => "ENUM('aceptada', 'rechazada', 'devuelto', 'devuelto_prod')"],
            'check_return'               => ['type' => 'TINYINT', 'constraint' => 1, 'unsigned' => TRUE ],
            'created_at'                 => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'                 => ['type' => 'TIMESTAMP', 'null' => true],
            'deleted_at'                 => ['type' => 'TIMESTAMP', 'null' => true]
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('integration_traffic_light');
    }

    public function down()
    {
        $this->forge->dropTable('integration_traffic_light');
    }
}
