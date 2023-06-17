<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SubTypeWorkerTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                             => ['type' => 'INT', 'constraint' => 10, 'unsigned' => TRUE, 'auto_increment' => TRUE ],
            'name'                           => ['type' => 'VARCHAR', 'constraint' => 191],
            'code'                           => ['type' => 'CHAR', 'constraint' => 10],
            'created_at'                     => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at'                     => ['type' => 'TIMESTAMP', 'null' => true]
        ]);
        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('sub_type_workers');
    }

    public function down()
    {
        $this->forge->dropTable('sub_type_workers');
    }
}
