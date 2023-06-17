<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class IntegrationTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                         => ['type' => 'BIGINT', 'constraint' => 20, 'unsigned' => TRUE, 'auto_increment' => TRUE ],
            'name'                       => ['type' => 'VARCHAR', 'constraint' => 150 ],
            'icon'                       => ['type' => 'VARCHAR', 'constraint' => 150 ],
            'description'                => ['type' => 'TEXT'],
            'status'                     => ['type' => 'ENUM("Activa", "Inactivo")'],
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('integrations');
    }

    public function down()
    {
        $this->forge->dropTable('integrations');
    }
}
