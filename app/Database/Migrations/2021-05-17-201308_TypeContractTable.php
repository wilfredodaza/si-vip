<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TypeContractTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                             => ['type' => 'INT', 'constraint' => 10, 'unsigned' => TRUE, 'auto_increment' => TRUE ],
            'name'                           => ['type' => 'VARCHAR', 'constraint' => 45],
            'code'                           => ['type' => 'VARCHAR', 'constraint' => 10],
            'created_at'                     => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'                     => ['type' => 'TIMESTAMP', 'null' => true]
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('type_contracts');
    }

    public function down()
    {
        $this->forge->dropTable('type_contracts');
    }
}
